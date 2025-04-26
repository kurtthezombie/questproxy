<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useUserStore } from '@/stores/userStore'; //
import { useServiceStore } from '@/stores/serviceStore'; // Import the service store
import axios from 'axios';
import PortfolioCard from '@/components/portfolio/PortfolioCard.vue'; //
import ServiceDisplayCard from '@/components/ServiceDisplay.vue';
import toast from '@/utils/toast'; //
import dayjs from 'dayjs'; //

const route = useRoute();
const router = useRouter();
const userStore = useUserStore(); //
const serviceStore = useServiceStore(); // Use the service store

// --- Refs ---
const userId = ref(null); // <-- Use userId from route
const profileUsername = ref(null); // <-- This will be set *after* fetching user by ID
const profileData = ref(null);
const pilotDetails = ref(null);
const gamerDetails = ref(null);
const portfolios = ref([]);
const points = ref(0);
// const services = ref([]); // Removed local services ref
const categories = ref([]); // Keep local categories ref if needed for ServiceDisplayCard prop
const isLoading = ref(false); // Keep isLoading for overall page loading state
const error = ref(null);

// --- Computed Properties ---
const isOwnProfile = computed(() => {
  // Compare based on ID now, as that's the primary identifier from the route
  // Ensure both values are treated as numbers for comparison
  return userStore.isLoggedIn && userStore.userData?.id === Number(userId.value);
});

const loggedInUserRole = computed(() => userStore.userData?.role);

const initials = computed(() => {
  // Use profileUsername ref, which is populated after fetch
  return profileUsername.value
    ?.split(" ")
    .map(word => word[0])
    .join("")
    .toUpperCase() || '?';
});

const memberSince = computed(() => {
  if (!profileData.value?.created_at) return '';
  return dayjs(profileData.value.created_at).format('MMMM YYYY'); // Corrected format
});

// Computed property to check if the profile is a pilot profile
const isPilotProfile = computed(() => {
  return profileData.value?.role === 'pilot' || profileData.value?.role === 'game pilot';
});


// --- Main Data Fetching Function ---
const fetchUserProfile = async () => {
  // Get token directly from localStorage
  const authToken = localStorage.getItem('authToken');

  if (!userId.value) {
      console.error("fetchUserProfile called without a valid userId.");
      error.value = 'Cannot fetch profile: User ID is missing from route.';
      isLoading.value = false; // Set loading to false on error
      return;
  }
  // Check if token exists in localStorage
  if (!authToken) {
      console.error("fetchUserProfile called without an auth token in localStorage.");
      error.value = 'Authentication token is missing. Please log in.';
      isLoading.value = false; // Set loading to false on error
      return;
  }

  console.log(`Fetching profile for User ID: ${userId.value}`);
  console.log('Auth Token from localStorage:', authToken); // Log the token from localStorage

  isLoading.value = true; // Set loading state at the start

  try {
    // *** Use the /users/{id} endpoint ***
    // NOTE: Ensure your backend's UserController@show eager-loads the 'pilot' and 'gamer' relationships based on role
    const response = await axios.get(`http://127.0.0.1:8000/api/users/${userId.value}`, {
        headers: { Authorization: `Bearer ${authToken}` } // Use token from localStorage
    });

    // The user object is directly under 'user' key in the response for this endpoint based on UserController@show
    if (response.data && response.data.user) {
      profileData.value = response.data.user;
      console.log('Profile Data:', profileData.value); // Log profile data to inspect pilot/gamer relationships
      profileUsername.value = profileData.value.username; // <-- Set username after fetching

      // Reset related details before fetching new ones
      pilotDetails.value = null;
      gamerDetails.value = null;
      portfolios.value = [];
      points.value = 0;
      // services.value = []; // No longer need to reset local services
      serviceStore.services = []; // Clear services in the store when profile changes

      // Fetch related data based on role and the newly obtained username/IDs
      const role = profileData.value.role;
      // IMPORTANT: Access pilot/gamer IDs from the fetched profileData.value, assuming eager loading or structure
      const pilotId = profileData.value.pilot?.id;
      const gamerId = profileData.value.gamer?.id;
      const fetchedUserId = profileData.value.id; // Use the ID from the fetched data

      // Now that we have the username, we can fetch points
      const promises = [fetchPoints()];

      if ((role === 'game pilot' || role === 'pilot')) {
          if(pilotId) {
              console.log(`Identified Pilot ID: ${pilotId}. Attempting to fetch services.`); // Log pilot ID before fetching services
              promises.push(fetchPilotDetails(pilotId));
              // Use the serviceStore action to fetch pilot services
              promises.push(serviceStore.fetchServicesByPilot(pilotId));
          } else {
              console.warn(`User is a pilot but pilot details (including ID) are missing for user ID: ${userId.value}. Cannot fetch services.`);
              // This warning indicates a backend issue - the /api/users/{id} endpoint is not returning the pilot relationship.
              // Optionally set a message to the user
              // toast.warning("Pilot details missing. Cannot display services.");
          }
          // Use fetchedUserId for consistency
          promises.push(fetchPortfolios(fetchedUserId)); // Fetch portfolios using fetchedUserId

      } else if (role === 'gamer') { // Check for gamer role
          if (gamerId) {
              console.log(`Identified Gamer ID: ${gamerId}. Attempting to fetch gamer details.`); // Log gamer ID before fetching details
              promises.push(fetchGamerDetails(gamerId));
          } else {
               console.warn(`User is a gamer but gamer details (including ID) are missing for user ID: ${userId.value}. Cannot fetch gamer details.`);
               // This warning indicates a backend issue - the /api/users/{id} endpoint is not returning the gamer relationship.
               // Optionally set a message to the user
               // toast.warning("Gamer details missing. Cannot display preferences.");
          }
      }

      await Promise.all(promises);

    } else {
      throw new Error('User data not found in response');
    }
     error.value = null;
  } catch (err) {
    console.error('Error fetching user profile:', err);
     if (err.response) {
         if (err.response.status === 401) {
            error.value = 'Unauthorized. Please log in again.';
            // Optionally, clear the invalid token from localStorage and redirect to login
            // localStorage.removeItem('authToken');
            // router.push({ name: 'login' });
         } else if (err.response.status === 404) {
             error.value = `User with ID '${userId.value}' not found.`; // Update error message
         } else {
            error.value = `Failed to load user profile. Server responded with status ${err.response.status}.`;
         }
     } else if (err.request) {
        error.value = 'Failed to load user profile. No response from server.';
     } else {
        error.value = `Failed to load user profile: ${err.message}`;
     }
    toast.error(error.value);
  } finally {
      isLoading.value = false; // Ensure loading is set to false after fetch attempt
  }
};

// --- Helper Fetching Functions ---

const fetchPilotDetails = async (pilotId) => {
    const authToken = localStorage.getItem('authToken'); // Get token from localStorage
    if (!pilotId || !authToken) return; // Check for both ID and token
    try {
        const response = await axios.get(`http://127.0.0.1:8000/api/pilots/${pilotId}`, {
             headers: { Authorization: `Bearer ${authToken}` } // Use token from localStorage
        });
        pilotDetails.value = response.data.pilot;
    } catch (err) {
        console.error('Error fetching pilot details:', err);
        toast.warning('Could not load pilot-specific details.');
    }
};

const fetchGamerDetails = async (gamerId) => {
    const authToken = localStorage.getItem('authToken'); // Get token from localStorage
    if (!gamerId || !authToken) {
        console.warn("fetchGamerDetails: Missing gamerId or authToken.");
        return; // Check for both ID and token
    }
    console.log(`Fetching details for Gamer ID: ${gamerId}`); // Log gamer ID
    try {
        const response = await axios.get(`http://127.0.0.1:8000/api/gamers/edit/${gamerId}`, {
             headers: { Authorization: `Bearer ${authToken}` } // Use token from localStorage
        });
        console.log('fetchGamerDetails Response Status:', response.status); // Log status
        console.log('fetchGamerDetails Response Data:', response.data); // Log data

        // Assuming the gamer details are directly in response.data.gamer
        gamerDetails.value = response.data.gamer;
        console.log('Gamer Details ref updated:', gamerDetails.value); // Log gamerDetails ref value

    } catch (err) {
        console.error('Error fetching gamer details:', err);
        toast.warning('Could not load gamer-specific details.');
    }
};

// Reverted fetchPortfolios function
const fetchPortfolios = async (fetchedUserId) => {
    const authToken = localStorage.getItem('authToken'); // Get token from localStorage
    if (!fetchedUserId || !authToken) return; // Check for both ID and token
    try {
        const response = await axios.get(`http://127.0.0.1:8000/api/portfolios/user/${fetchedUserId}`, { // Use ID here
            headers: { Authorization: `Bearer ${authToken}` } // Use token from localStorage
        });
        const baseURL = "http://127.0.0.1:8000/storage/";
        portfolios.value = response.data.portfolios.map(p => ({
            ...p,
            p_content: baseURL + p.p_content
        }));
    } catch (err) {
        console.error('Error fetching portfolios:', err);
        toast.warning('Could not load portfolios.');
    }
};


// Fetch Points now uses the profileUsername ref, which is set after the initial fetch
const fetchPoints = async () => {
  const authToken = localStorage.getItem('authToken'); // Get token from localStorage
  if (!profileUsername.value || !authToken) { // Check if username and token are available
       console.warn("Cannot fetch points yet, username or token not available.");
       return;
  }
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/portfolios/user/points/${profileUsername.value}`, {
        headers: { Authorization: `Bearer ${authToken}` } // Use token from localStorage
    });
    points.value = response.data.points ?? 0;
  } catch (err) {
    console.error("Failed to fetch points: ", err);
    toast.error("Failed to fetch points");
  }
};

// Removed local fetchPilotServices function

const fetchCategories = async () => { /* ... keep implementation ... */
    try {
        const response = await axios.get(`http://127.0.0.1:8000/api/categories`);
        categories.value = response.data.categories || [];
    } catch (err) {
        console.error('Error fetching categories:', err);
    }
};

// --- Lifecycle Hooks and Watchers ---

onMounted(() => {
  fetchCategories();
  // No need to call fetchServicesByPilot or fetchGamerDetails here, they are called in fetchUserProfile
});

// *** Watch for route param 'id' changes ***
watch(() => route.params.id, async (newId) => {
  if (newId && newId !== userId.value) { // Check if ID is present and different
    console.log(`Route ID changed to: ${newId}`);
    isLoading.value = true; // Keep isLoading for initial fetch
    error.value = null;
    userId.value = newId; // Update the userId ref *before* fetching
    profileUsername.value = null; // Reset username when ID changes

    // Reset data
    profileData.value = null;
    pilotDetails.value = null;
    gamerDetails.value = null; // Reset gamer details
    portfolios.value = [];
    points.value = 0;
    serviceStore.services = []; // Clear services in the store when profile changes

    // Call the main fetch function using the new ID
    await fetchUserProfile();

    // isLoading.value = false; // Moved to finally block in fetchUserProfile
  } else if (!newId) {
      console.log("Route ID is undefined or null.");
       error.value = "User ID parameter missing in URL.";
       isLoading.value = false; // Stop loading if ID is missing
  }
}, { immediate: true }); // Run immediately


// --- Methods ---
const goToAccountSettings = () => { /* ... keep implementation ... */
    router.push({ name: 'account-settings' });
}

const goToMyPortfolio = () => { /* ... keep implementation ... */
    router.push({ name: 'MyPortfolio' });
}

const goToReportUser = () => { /* ... keep implementation ... */
    // Ensure we have the username before navigating to report page
    if(profileUsername.value) {
        router.push({ name: 'ReportView', params: { username: profileUsername.value } });
    } else {
        toast.error("Cannot report user: username not loaded.");
    }
}

// Method to navigate to the public portfolio view
const goToPublicPortfolio = () => {
    if (profileUsername.value) {
        router.push({ name: 'PortfolioView', params: { username: profileUsername.value } });
    } else {
        toast.error("Cannot view portfolio: username not loaded.");
    }
}
</script>

<template>
  <div class="min-h-screen bg-gray-900 text-white p-4 md:p-10">
    <div v-if="isLoading" class="flex justify-center items-center h-screen">
      <span class="loading loading-bars loading-xl text-accent scale-[3]"></span>
    </div>

    <div v-else-if="error" class="text-center py-20">
      <h2 class="text-2xl text-red-500 font-semibold">Error</h2>
      <p>{{ error }}</p>
      <div class="mt-4 flex justify-center gap-3">
        <button @click="router.push({ name: 'homepage' })" class="btn btn-primary">Go Home</button>
        <button v-if="userId" @click="fetchUserProfile" class="btn btn-secondary">Retry</button>
      </div>
    </div>

    <div v-else-if="profileData" class="max-w-5xl mx-auto space-y-8">
      <section class="bg-gray-800 rounded-lg shadow-lg overflow-hidden border border-gray-700">
        <div class="p-6 md:p-8 flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6">
          <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-green-600 flex items-center justify-center text-4xl md:text-5xl font-semibold text-white flex-shrink-0">
            {{ initials }}
          </div>

          <div class="flex-grow text-center md:text-left">
            <h1 class="text-3xl md:text-4xl font-bold">{{ profileData.username }}</h1>
            <p class="text-gray-400 capitalize">{{ profileData.role?.replace('game ', '') }}</p>

            <div class="mt-3 flex flex-wrap justify-center md:justify-start items-center gap-2 md:gap-4 text-sm">
              <div class="bg-emerald-950 text-emerald-400 px-3 py-1 rounded-full flex items-center border border-emerald-800">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="yellow" class="mr-1">
                  <path d="M12 2L15 8L22 8.5L17 12.5L18 19.5L12 16.5L6 19.5L7 12.5L2 8.5L9 8L12 2Z"/>
                </svg>
                <span class="font-semibold">Points:</span>
                <span class="font-bold ml-1">{{ points }}</span>
              </div>

              <div class="flex items-center gap-1 text-blue-400 font-semibold bg-blue-950 px-3 py-1 rounded-full border border-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A10.97 10.97 0 0112 15c2.21 0 4.25.672 5.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Member since {{ memberSince }}
              </div>
            </div>
          </div>
        </div>

        <div class="bg-gray-850 border-t border-gray-700 p-4 flex flex-wrap justify-center md:justify-end gap-3">
          <button v-if="isOwnProfile" @click="goToAccountSettings" class="btn btn-sm btn-outline btn-info">
            Account Settings
          </button>
          <button v-if="isOwnProfile && isPilotProfile" @click="goToMyPortfolio" class="btn btn-sm btn-outline btn-success">
            Manage Portfolio
          </button>
          <button v-if="!isOwnProfile && isPilotProfile" @click="goToPublicPortfolio" class="btn btn-sm btn-outline btn-primary">
            View Portfolio
          </button>
          <button v-if="!isOwnProfile" @click="goToReportUser" class="btn btn-sm btn-outline btn-warning">
            Report User
          </button>
        </div>
      </section>

      <section v-if="pilotDetails || gamerDetails" class="bg-gray-800 rounded-lg shadow-lg overflow-hidden border border-gray-700">
        <div class="border-b border-gray-700 p-4">
          <h2 class="text-2xl font-semibold">Details</h2>
        </div>

        <div class="p-6">
          <div v-if="pilotDetails" class="space-y-6">
            <div class="bg-gray-750 p-4 rounded-lg">
              <h3 class="font-semibold text-gray-400 text-sm uppercase mb-2">Skills</h3>
              <p class="text-gray-200 whitespace-pre-wrap">{{ pilotDetails.skills || 'No skills listed.' }}</p>
            </div>
            <div class="bg-gray-750 p-4 rounded-lg">
              <h3 class="font-semibold text-gray-400 text-sm uppercase mb-2">Bio</h3>
              <p class="text-gray-200 whitespace-pre-wrap">{{ pilotDetails.bio || 'No bio available.' }}</p>
            </div>
          </div>

          <div v-else-if="gamerDetails" class="bg-gray-750 p-4 rounded-lg">
            <h3 class="font-semibold text-gray-400 text-sm uppercase mb-2">Gamer Preference</h3>
            <p class="text-gray-200">{{ gamerDetails.gamer_preference || 'No preference specified.' }}</p>
          </div>

          <div v-else class="text-gray-500 text-center py-4">
            No specific details available for this role.
          </div>
        </div>
      </section>

      <section v-if="isPilotProfile" class="space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-semibold">Portfolio Gallery</h2>
        </div>

        <div v-if="portfolios.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
          <PortfolioCard
            v-for="portfolio in portfolios"
            :key="portfolio.id"
            :username="profileUsername"
            :portfolio="portfolio"
          />
        </div>
        <div v-else class="text-center text-gray-500 py-10 bg-gray-800 rounded-lg border border-gray-700">
          This pilot hasn't added any portfolio items yet.
        </div>
      </section>

      <section v-if="isPilotProfile" class="space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-semibold">Services Offered</h2>
        </div>

        <div v-if="serviceStore.services.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
          <ServiceDisplayCard
            v-for="service in serviceStore.services"
            :key="service.id"
            :service="service"
            :categories="categories"
            :is-service-history="false"
          />
        </div>
        <div v-else-if="serviceStore.loading" class="flex justify-center items-center py-10 bg-gray-800 rounded-lg border border-gray-700">
          <span class="loading loading-dots loading-lg text-accent"></span>
        </div>
        <div v-else class="text-center text-gray-500 py-10 bg-gray-800 rounded-lg border border-gray-700">
          This pilot is not currently offering any services.
        </div>
      </section>

      <div v-if="!isPilotProfile && !gamerDetails && !pilotDetails" class="text-center text-gray-500 py-10 bg-gray-800 rounded-lg border border-gray-700">
        Profile details loaded. No portfolio or services applicable for this role.
      </div>
    </div>

    <div v-else-if="!isLoading && !error" class="text-center py-20">
      <p class="text-gray-500">Profile data could not be loaded.</p>
    </div>
  </div>
</template>

<style scoped>
.whitespace-pre-wrap {
  white-space: pre-wrap;
}

/* Additional styles for better visual organization */
.bg-gray-750 {
  background-color: rgba(31, 41, 55, 0.5);
}

.bg-gray-850 {
  background-color: rgba(22, 30, 40, 1);
}
</style>
