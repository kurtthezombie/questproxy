<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useUserStore } from '@/stores/userStore';
import { useServiceStore } from '@/stores/serviceStore';
import axios from 'axios';
import PortfolioCard from '@/components/portfolio/PortfolioCard.vue';
import ServiceDisplayCard from '@/components/ServiceDisplay.vue';
import ReviewCard from '@/components/ReviewCard.vue';
import toast from '@/utils/toast';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime'; 
dayjs.extend(relativeTime);

const route = useRoute();
const router = useRouter();
const userStore = useUserStore();
const serviceStore = useServiceStore();

// --- Refs ---
const userId = ref(null); 
const profileUsername = ref(null);
const profileData = ref(null);
const pilotDetails = ref(null); 
const gamerDetails = ref(null);
const portfolios = ref([]);
const points = ref(0);
const categories = ref([]);
const isLoading = ref(true);
const error = ref(null);

// --- Reviews Refs ---
const pilotReviews = ref([]);
const isLoadingReviews = ref(false);
const reviewsError = ref(null);

// --- Computed Properties ---
const isOwnProfile = computed(() => {
  if (!userStore.isLoggedIn || !userStore.userData?.id || !userId.value) {
    return false;
  }
  return String(userStore.userData.id) === String(userId.value);
});

const profileRole = computed(() => profileData.value?.role);

const isPilotProfile = computed(() => {
  return profileRole.value === 'pilot' || profileRole.value === 'game pilot';
});


const pilotIdForReviews = computed(() => profileData.value?.pilot?.id);

const initials = computed(() => {
  return profileUsername.value?.split(" ").map(word => word[0]).join("").toUpperCase() || '?';
});

const memberSince = computed(() => {
  if (!profileData.value?.created_at) return '';
  return dayjs(profileData.value.created_at).format('MMMM D, YYYY');
});

// --- Reviews Display Logic ---
const reviewsLimit = ref(3); 
const showAllReviewsModal = ref(false); 

const displayedReviews = computed(() => {
  return pilotReviews.value.slice(0, reviewsLimit.value);
});

const hiddenReviews = computed(() => {
  return pilotReviews.value.slice(reviewsLimit.value);
});

const hasMoreReviews = computed(() => {
  return hiddenReviews.value.length > 0;
});

// --- Modal Functions ---
const openReviewsModal = () => {
  showAllReviewsModal.value = true;
};

const closeReviewsModal = () => {
  showAllReviewsModal.value = false;
};

// --- Services Display Logic ---
const servicesLimit = ref(3); 
const showAllServicesModal = ref(false);

const displayedServices = computed(() => {
  if (!serviceStore.services) return [];
  return serviceStore.services.slice(0, servicesLimit.value);
});

const hiddenServices = computed(() => {
   if (!serviceStore.services) return [];
  return serviceStore.services.slice(servicesLimit.value);
});

const hasMoreServices = computed(() => {
  return hiddenServices.value.length > 0;
});


// --- Service Modal Functions ---
const openServicesModal = () => {
  showAllServicesModal.value = true;
};

const closeServicesModal = () => {
  showAllServicesModal.value = false;
};


// --- Helper Fetching Functions ---

const fetchPilotDetails = async (pilotId) => {

    const authToken = localStorage.getItem('authToken');
    if (!pilotId || !authToken) return;
    try {
        const response = await axios.get(`http://127.0.0.1:8000/api/pilots/${pilotId}`, {
             headers: { Authorization: `Bearer ${authToken}` }
        });
        pilotDetails.value = response.data.pilot;
    } catch (err) {
        console.error(`Error fetching pilot details for pilot ID ${pilotId}:`, err);

    }
};

const fetchGamerDetails = async (gamerId) => {
    const authToken = localStorage.getItem('authToken');
    if (!gamerId || !authToken) return;
    try {
        const response = await axios.get(`http://127.0.0.1:8000/api/gamers/edit/${gamerId}`, {
             headers: { Authorization: `Bearer ${authToken}` }
        });
        gamerDetails.value = response.data.gamer;
    } catch (err) {
        console.error(`Error fetching gamer details for gamer ID ${gamerId}:`, err);

    }
};

const fetchPortfolios = async (fetchedUserId) => {
    const authToken = localStorage.getItem('authToken');
    if (!fetchedUserId || !authToken) return;
    try {
        const response = await axios.get(`http://127.0.0.1:8000/api/portfolios/user/${fetchedUserId}`, {
            headers: { Authorization: `Bearer ${authToken}` }
        });
        const baseURL = "http://127.0.0.1:8000/storage/";
        portfolios.value = Array.isArray(response.data?.portfolios)
         ? response.data.portfolios.map(p => ({
            ...p,
            p_content: p.p_content ? baseURL + p.p_content : null
           }))
         : [];
    } catch (err) {
        console.error(`Error fetching portfolios for user ID ${fetchedUserId}:`, err);
 
    }
};

const fetchPoints = async () => {
  const authToken = localStorage.getItem('authToken');
  if (!profileUsername.value || !authToken || !isPilotProfile.value) {
       points.value = 0;
       return;
  }
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/portfolios/user/points/${profileUsername.value}`, {
        headers: { Authorization: `Bearer ${authToken}` }
    });
    points.value = response.data.points ?? 0;
  } catch (err) {
    console.error(`Error fetching points for ${profileUsername.value}: `, err);
    points.value = 0;
   
  }
};

const fetchCategories = async () => {
    try {
        const response = await axios.get(`http://127.0.0.1:8000/api/categories`);
        categories.value = response.data.categories || [];
    } catch (err) {
        console.error('Error fetching categories:', err);
    }
};

// --- Fetch Reviews for Pilot Profile  ---

const fetchPilotReviews = async (pilotId) => {
    if (!pilotId) {
        console.warn("Cannot fetch reviews without pilot ID.");
        reviewsError.value = 'Pilot ID not found for fetching reviews.';
        pilotReviews.value = [];
        return;
    }
    console.log(`Attempting to fetch reviews directly for pilot ID: ${pilotId}...`);
    isLoadingReviews.value = true;
    reviewsError.value = null;
    const authToken = localStorage.getItem('authToken');
    if (!authToken) {
        reviewsError.value = 'Authentication token missing.';
        isLoadingReviews.value = false;
        return;
    }

    try {
 
        const response = await axios.get(`http://127.0.0.1:8000/api/pilots/${pilotId}/reviews`, {
             headers: { Authorization: `Bearer ${authToken}` }
        });


        pilotReviews.value = response.data?.reviews || [];

        if (!Array.isArray(pilotReviews.value)) {
            console.warn("Fetched reviews data is not an array:", pilotReviews.value);
            pilotReviews.value = [];
            throw new Error("Invalid review data format received from backend.");
        }

        if (pilotReviews.value.length === 0) {
             reviewsError.value = 'No reviews found for this pilot yet.';
             console.log(`No reviews found for pilot ${pilotId}.`);
        } else {
             console.log(`Reviews fetched successfully for pilot ${pilotId}:`, pilotReviews.value);
             reviewsError.value = null; 
        }

    } catch (error) {
        console.error(`Error fetching reviews for pilot ${pilotId}: `, error);
        if (error.response?.status === 404) {
             reviewsError.value = 'No reviews found for this pilot yet.'; 
             console.log(`No reviews found for pilot ${pilotId} (404).`);
             pilotReviews.value = [];
        } else {
            reviewsError.value = 'Failed to load reviews.';
            toast.error(reviewsError.value); 
            pilotReviews.value = [];
        }
    } finally {
        isLoadingReviews.value = false;
    }
};


// --- Main Data Fetching Function ---
const fetchUserProfile = async () => {
  const authToken = localStorage.getItem('authToken');

  if (!userId.value) { error.value = 'User ID is missing.'; isLoading.value = false; return; }
  if (!authToken) { error.value = 'Authentication token is missing.'; isLoading.value = false; return; }

  console.log(`Fetching profile for User ID: ${userId.value}`);
  isLoading.value = true;
  error.value = null;

  profileData.value = null; pilotDetails.value = null; gamerDetails.value = null;
  portfolios.value = []; points.value = 0; serviceStore.services = [];
  pilotReviews.value = []; reviewsError.value = null;

  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/users/${userId.value}`, {
        headers: { Authorization: `Bearer ${authToken}` }
    });

    if (!response.data?.user) throw new Error('User data not found');
    profileData.value = response.data.user;
    profileUsername.value = profileData.value.username;
    console.log('Profile Data fetched:', profileData.value);


    const role = profileData.value.role;
    const specificPilotId = profileData.value.pilot?.id; 
    const specificGamerId = profileData.value.gamer?.id; 
    const fetchedUserId = profileData.value.id; 

    const promises = [];

    if (role === 'pilot' || role === 'game pilot') {
        console.log("Profile is Pilot. Fetching points, details, services, portfolio, reviews...");
        promises.push(fetchPoints()); 
        if (specificPilotId) {
            promises.push(fetchPilotDetails(specificPilotId)); 
            promises.push(serviceStore.fetchServicesByPilot(specificPilotId)); 
            promises.push(fetchPilotReviews(specificPilotId));
        } else {
             console.warn("Pilot ID not found in profile data, cannot fetch pilot-specific details or reviews.");
             reviewsError.value = "Could not fetch reviews: Pilot ID missing.";
        }
        promises.push(fetchPortfolios(fetchedUserId)); 

    } else if (role === 'gamer') {
         console.log("Profile is Gamer. Fetching details...");
        if (specificGamerId) {
            promises.push(fetchGamerDetails(specificGamerId)); 
        }
    }

    if (promises.length > 0) {
        await Promise.all(promises);
        console.log("All profile data promises resolved.");
    }
    error.value = null; 

  } catch (err) {
    console.error('Error during user profile fetch process:', err);
     error.value = `Failed to load profile: ${err.message}`;
     if (err.response) {
         error.value = `Failed to load profile (${err.response.status}).`;
         if (err.response.status === 401) error.value = 'Unauthorized.';
         else if (err.response.status === 404) error.value = `User not found.`;
     } else if (err.request) {
        error.value = 'No response from server.';
     }
    
  } finally {
      isLoading.value = false;
      console.log("Profile fetching complete.");
  }
};


// --- Lifecycle Hooks and Watchers ---
onMounted(() => { fetchCategories(); });

watch(() => route.params.id, (newId, oldId) => {
  if (newId && newId !== oldId) {
    userId.value = newId;
    fetchUserProfile(); 
  } else if (!newId) {
      error.value = "User ID parameter missing."; isLoading.value = false; profileData.value = null;
  }
}, { immediate: true });


// --- Methods ---
const goToAccountSettings = () => router.push({ name: 'account-settings' });
const goToMyPortfolio = () => router.push({ name: 'MyPortfolio' });
const goToReportUser = () => {
    if(profileUsername.value) router.push({ name: 'ReportView', params: { username: profileUsername.value } });
    else toast.error("Username not loaded.");
}
const goToPublicPortfolio = () => {
    if (profileUsername.value) router.push({ name: 'PortfolioView', params: { username: profileUsername.value } });
    else toast.error("Username not loaded.");
}

</script>

<template>
  <div class="min-h-screen bg-gray-900 text-white p-4 md:p-10">
    <div v-if="isLoading" class="flex justify-center items-center h-screen">
      <span class="loading loading-bars loading-xl text-accent scale-[3]"></span>
    </div>

    <div v-else-if="error" class="text-center py-20">
      <h2 class="text-2xl text-red-500 font-semibold">Error Loading Profile</h2>
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
            <p class="text-gray-400 capitalize">{{ profileRole?.replace('game ', '') }}</p>
            <div class="mt-3 flex flex-wrap justify-center md:justify-start items-center gap-2 md:gap-4 text-sm">
              <div v-if="isPilotProfile" class="bg-emerald-950 text-emerald-400 px-3 py-1 rounded-full flex items-center border border-emerald-800">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="yellow" class="mr-1"><path d="M12 2L15 8L22 8.5L17 12.5L18 19.5L12 16.5L6 19.5L7 12.5L2 8.5L9 8L12 2Z"/></svg>
                <span class="font-semibold">Points:</span><span class="font-bold ml-1">{{ points }}</span>
              </div>
              <div class="flex items-center gap-1 text-blue-400 font-semibold bg-blue-950 px-3 py-1 rounded-full border border-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A10.97 10.97 0 0112 15c2.21 0 4.25.672 5.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                Member since {{ memberSince }}
              </div>
            </div>
          </div>
        </div>

        <div class="bg-gray-850 border-t border-gray-700 p-4 flex flex-wrap justify-center md:justify-end gap-3">
          <button v-if="isOwnProfile" @click="goToAccountSettings" class="btn btn-sm btn-outline btn-info">Account Settings</button>
          <button v-if="isOwnProfile && isPilotProfile" @click="goToMyPortfolio" class="btn btn-sm btn-outline btn-success">Manage Portfolio</button>
          <button v-if="!isOwnProfile && isPilotProfile" @click="goToPublicPortfolio" class="btn btn-sm btn-outline btn-primary">View Portfolio</button>
          <button v-if="!isOwnProfile" @click="goToReportUser" class="btn btn-sm btn-outline btn-warning">Report User</button>
        </div>
      </section>

      <section v-if="pilotDetails || gamerDetails" class="bg-gray-800 rounded-lg shadow-lg overflow-hidden border border-gray-700">
        <div class="border-b border-gray-700 p-4"><h2 class="text-2xl font-semibold">Details</h2></div>
        <div class="p-6">
          <div v-if="pilotDetails" class="space-y-6">
             <div class="bg-gray-750 p-4 rounded-lg"><h3 class="font-semibold text-gray-400 text-sm uppercase mb-2">Skills</h3><p class="text-gray-200 whitespace-pre-wrap">{{ pilotDetails.skills || 'No skills listed.' }}</p></div>
             <div class="bg-gray-750 p-4 rounded-lg"><h3 class="font-semibold text-gray-400 text-sm uppercase mb-2">Bio</h3><p class="text-gray-200 whitespace-pre-wrap">{{ pilotDetails.bio || 'No bio available.' }}</p></div>
          </div>
          <div v-else-if="gamerDetails" class="bg-gray-750 p-4 rounded-lg"><h3 class="font-semibold text-gray-400 text-sm uppercase mb-2">Gamer Preference</h3><p class="text-gray-200">{{ gamerDetails.gamer_preference || 'No preference specified.' }}</p></div>
        </div>
      </section>

      <section v-if="isPilotProfile" class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold">Services Offered</h2>
             <button
                v-if="hasMoreServices"
                @click="openServicesModal"
                class="btn btn-sm btn-outline btn-secondary"
            >
                Show All {{ serviceStore.services.length }} Services
            </button>
        </div>

        <div v-if="serviceStore.loading" class="flex justify-center items-center py-10 bg-gray-800 rounded-lg border border-gray-700">
            <span class="loading loading-dots loading-lg text-accent"></span>
        </div>
        <div v-else-if="serviceStore.services.length === 0" class="text-center text-gray-500 py-10 bg-gray-800 rounded-lg border border-gray-700">
            This pilot is not currently offering any services.
        </div>
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            <ServiceDisplayCard
                v-for="service in displayedServices"
                :key="service.id"
                :service="service"
                :categories="categories"
                :is-service-history="false"
            />
            </div>
      </section>


      <section v-if="isPilotProfile" class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold">Reviews Received</h2>
            <button
                v-if="hasMoreReviews"
                @click="openReviewsModal"
                class="btn btn-sm btn-outline btn-secondary"
            >
                Show All {{ pilotReviews.length }} Reviews
            </button>
        </div>

        <div v-if="isLoadingReviews" class="flex justify-center items-center py-10 bg-gray-800 rounded-lg border border-gray-700">
            <span class="loading loading-dots loading-lg text-accent"></span>
        </div>
        <div v-else-if="reviewsError" class="text-center text-red-400 py-10 bg-gray-800 rounded-lg border border-red-700">
            <p>{{ reviewsError }}</p>
            <button v-if="pilotIdForReviews" @click="fetchPilotReviews(pilotIdForReviews)" class="btn btn-sm btn-outline btn-warning mt-2">Retry Reviews</button>
        </div>
        <div v-else-if="pilotReviews.length === 0" class="text-center text-gray-500 py-10 bg-gray-800 rounded-lg border border-gray-700">
            This pilot hasn't received any reviews yet.
        </div>
        <div v-else class="space-y-4">
            <ReviewCard v-for="review in displayedReviews" :key="review.id" :review="review" />
        </div>
      </section>

      <section v-if="isPilotProfile" class="space-y-4">
          <div class="flex items-center justify-between mb-4">
              <h2 class="text-2xl font-semibold">Portfolio Gallery</h2>
              </div>

          <div v-if="portfolios.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
              <template v-for="(portfolio, index) in portfolios" :key="portfolio.id">
                  <PortfolioCard
                      v-if="index < 2 || portfolios.length <= 3"
                      :username="profileUsername"
                      :portfolio="portfolio"
                      :hide-actions="true" />
              </template>

              <div
                  v-if="portfolios.length > 3"
                  class="relative flex items-center justify-center bg-gray-800 rounded-lg shadow-lg overflow-hidden border border-gray-700 cursor-default"
                  >
                  <div class="absolute inset-0 bg-black opacity-50"></div> <div class="relative z-10 text-white text-4xl font-bold">
                      +{{ portfolios.length - 2 }} </div>
                  <div class="absolute bottom-4 text-gray-300 text-sm">
                      More Images
                  </div>
              </div>
          </div>

          <div v-else class="text-center text-gray-500 py-10 bg-gray-800 rounded-lg border border-gray-700">
              This pilot hasn't added any portfolio items yet.
          </div>
      </section>


      <div v-if="!isPilotProfile && !gamerDetails" class="text-center text-gray-500 py-10 bg-gray-800 rounded-lg border border-gray-700">
         Profile details loaded. No specific sections applicable for this role.
       </div>
    </div>

    <div v-if="showAllReviewsModal" class="modal modal-open">
        <div class="modal-box max-w-3xl bg-gray-800 text-white border border-gray-700">
            <h3 class="font-bold text-lg mb-4">All Reviews for {{ profileUsername }}</h3>

            <div class="space-y-4 max-h-96 overflow-y-auto pr-2 -mr-2">
                <ReviewCard v-for="review in pilotReviews" :key="'modal-' + review.id" :review="review" />
            </div>

            <div class="modal-action">
                <button @click="closeReviewsModal" class="btn btn-secondary">Close</button>
            </div>
        </div>
    </div>

    <div v-if="showAllServicesModal" class="modal modal-open">
        <div class="modal-box max-w-3xl bg-gray-800 text-white border border-gray-700">
            <h3 class="font-bold text-lg mb-4">All Services by {{ profileUsername }}</h3>

            <div class="service-modal-list flex flex-col items-center space-y-4 max-h-96 overflow-y-auto pr-2 -mr-2 pb-0">
                <ServiceDisplayCard
                    v-for="service in serviceStore.services"
                    :key="'modal-service-' + service.id"
                    :service="service"
                    :categories="categories"
                    :is-service-history="false"
                />
            </div>

            <div class="modal-action">
                <button @click="closeServicesModal" class="btn btn-secondary">Close</button>
            </div>
        </div>
    </div>
  </div>
</template>

<style scoped>
.whitespace-pre-wrap { white-space: pre-wrap; }
.bg-gray-750 { background-color: rgba(55, 65, 81, 0.6); }
.bg-gray-850 { background-color: rgb(31 41 55 / 0.7); }
.loading { margin: auto; }
.btn { transition: all 0.2s ease-in-out; }
.btn:hover { transform: translateY(-1px); filter: brightness(1.1); }


</style>