<template>
  <div class="p-6 bg-gray-900 min-h-screen">
    <div class="max-w-5xl mx-auto bg-gray-800 p-6 rounded-lg shadow-lg flex flex-col md:flex-row">
      <!-- Left Section: Profile & Portfolio -->
      <div class="w-full md:w-1/3 pr-0 md:pr-4 mb-6 md:mb-0">
        <div class="flex flex-col items-center">
          <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center text-white text-2xl font-bold">
            {{ userInitial }}
          </div>
          <h2 class="mt-2 text-xl font-bold text-white">{{ profileData.username || 'Loading...' }}</h2>
          <p class="text-sm text-gray-400">{{ }}</p>
          
          <!-- Pilot Service Badge -->
          <div v-if="isPilot" class="mt-1">
            <span class="bg-blue-500 text-white text-xs font-medium px-2.5 py-0.5 rounded">
              Game Pilot
            </span>
          </div>
          
          <button 
            class="mt-2 px-4 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition"
            :disabled="!profileLoaded"
            @click="goToPortfolio"
          >
            Portfolio
          </button>
        </div>

        <!-- Portfolio Preview -->
        <div class="mt-4 flex items-center justify-center">
          <div v-for="(portfolio, index) in portfolios.slice(0, 2)" :key="index">
            <img :src="portfolio.p_content" alt="Portfolio Image" class="w-12 h-12 bg-gray-600 rounded-md mr-2 object-cover">
          </div>
          <div v-if="portfolios.length > 2" class="w-12 h-12 bg-gray-700 rounded-md flex items-center justify-center text-white text-lg cursor-pointer" @click="goToPortfolio">
            +{{ portfolios.length - 2 }}
          </div>
          <div v-if="portfolios.length === 0" class="text-gray-400 text-sm">
            No portfolio items
          </div>
        </div>
        
        <!-- Pilot Service Details (if applicable) -->
        <div v-if="isPilot && pilotService" class="mt-6 p-3 bg-gray-700 rounded-md">
          <h3 class="font-semibold text-blue-300 mb-2">Pilot Service Details</h3>
          <div class="grid grid-cols-2 gap-2 text-sm">
            <p class="text-gray-400">Experience:</p>
            <p class="font-medium text-white">{{ pilotService.experience || 'N/A' }} years</p>
            
            <p class="text-gray-400">Status:</p>
            <p :class="`font-medium ${pilotService.active ? 'text-green-400' : 'text-red-400'}`">
              {{ pilotService.active ? 'Active' : 'Inactive' }}
            </p>
            
            <p class="text-gray-400">Rating:</p>
            <div class="flex items-center">
              <span class="text-yellow-400 mr-1">★</span>
              <span class="text-white">{{ pilotService.rating || '4.5' }}/5</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Section: Services -->
      <div class="w-full md:w-2/3 pl-0 md:pl-4">
        <h3 class="text-lg text-center font-semibold text-white mb-4">Offered Services</h3>
        <div v-if="serviceStore.loading" class="flex justify-center items-center h-32">
          <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
        </div>
        <div v-else-if="serviceStore.services.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <ServiceCard 
            v-for="service in serviceStore.services" 
            :key="service.id" 
            :service="service"
            :categories="serviceStore.categories"
            @click="handleServiceClick(service)"
          />
        </div>
        <div v-else class="flex justify-center items-center h-32 text-gray-400 text-lg">
          <template v-if="isPilot">No services currently offered</template>
          <template v-else>This user does not offer any services</template>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useUserStore } from '@/stores/userStore'; 
import { useServiceStore } from '@/stores/serviceStore'; 
import loginService from '@/services/login-service';
import userService from '@/services/user-service';
import { fetchPortfoliosByUser } from '@/services/portfolio.service';
import { useLoader } from '@/services/loader-service';
import ServiceCard from '@/components/ServiceDisplay.vue'; 
import toast from '@/utils/toast';
import axios from 'axios';

const { loadShow, loadHide } = useLoader();


const router = useRouter();
const route = useRoute();
const userStore = useUserStore();
const serviceStore = useServiceStore(); 

const profileData = ref({});
const portfolios = ref([]);
const username = ref('');
const role = ref('User');
const profileLoaded = ref(false);
const pilotService = ref(null);

const userInitial = computed(() => {
  if (!profileData.value?.f_name) return '?';
  return `${profileData.value.f_name.charAt(0)}${profileData.value.l_name?.charAt(0) || ''}`.toUpperCase();
});

// Check if user is a pilot
const isPilot = computed(() => {
  return role.value === 'Game Pilot';
});

// Fetch user data
const fetchUserData = async () => {
  const loader = loadShow();
  try {
    const userData = await loginService.fetchUserData();
    username.value = userData.username;
    // Adjust role display
    role.value = userData.role === 'gamer' ? 'Gamer' : userData.role === 'game pilot' ? 'Game Pilot' : 'User';
  } catch (error) {
    console.error('Error fetching user data:', error);
    router.push({ name: 'login' }); 
  } finally {
    loadHide(loader);
  }
};

// Fetch profile data based on user ID in route params
const fetchProfileData = async () => {
  try {
    const userId = route.params.id;
    const profile = await userService.getUserProfile(userId);
    profileData.value = profile;

    username.value = profile.username || "Loading...";
    
    // Check if user has pilot service details
    if (profile.role === 'game pilot') {
      role.value = 'Game Pilot';
      
      // Set pilot service details if available
      pilotService.value = {
        experience: profile.pilot_experience || 0,
        active: profile.pilot_active !== undefined ? profile.pilot_active : true,
        rating: profile.pilot_rating || '4.5'
      };
    }
    
    profileLoaded.value = true;
  } catch (error) {
    console.error('Error fetching profile:', error);
  }
};

// Fetch user services if role is pilot using the service store
const fetchUserServices = async () => {
  try {
    const userId = route.params.id;
    // Use role value directly from computed
    if (role.value === 'Game Pilot') {
      await serviceStore.fetchServicesByPilot(userId);
    } else {
      serviceStore.clearServices(); // Clear any existing services if not a pilot
    }
  } catch (error) {
    console.error('Error fetching user services:', error);
    serviceStore.clearServices();
  }
};

// Fetch categories using the service store
const fetchCategories = async () => {
  try {
    await serviceStore.fetchCategories();
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

// Fetch portfolio images
const fetchPortfolios = async () => {
  try {
    const userId = route.params.id;
    const response = await fetchPortfoliosByUser(userId);
    const baseURL = "http://127.0.0.1:8000/storage/";

    portfolios.value = response.portfolios?.map(portfolio => ({
      ...portfolio,
      p_content: baseURL + portfolio.p_content,
    })) || [];
  } catch (error) {
    console.error("Error fetching portfolio:", error);
    portfolios.value = [];
  }
};

// Handle service click
const handleServiceClick = (service) => {
  if (!service.availability) {
    toast.warning("This service is currently not accepting bookings.");
    return;
  }
  router.push({
    name: 'PaymentView', 
    params: { serviceId: service.id }, 
    state: { service: service } 
  });
};

// Go to portfolio page
const goToPortfolio = () => {
  // Get user ID from route parameters
  const userId = route.params.id;
  
  // If we have profile data loaded, use it
  if (profileLoaded.value && profileData.value && profileData.value.username) {
    const isOwnProfile = currentUser.value && 
                       currentUser.value.username === profileData.value.username;

    if (isOwnProfile) {
      router.push({ name: 'MyPortfolio' });
    } else {
      router.push({ name: 'PortfolioView', params: { username: profileData.value.username } });
    }
    return;
  }
  
  if (currentUser.value && currentUser.value.id && userId === currentUser.value.id.toString()) {
    router.push({ name: 'MyPortfolio' });
    return;
  }
 
  if (userId) {
    router.push({ name: 'PortfolioView', params: { username: `user-${userId}` } });
    return;
  }
  
  router.push({ name: 'MyPortfolio' });
  
  toast.info("Redirecting to portfolio with limited information. Some features may be unavailable.");
};

watch(() => route.params.id, async () => {
  serviceStore.loading = true;
  await fetchUserData();
  await fetchProfileData();
  await fetchCategories();
  await fetchUserServices();
  await fetchPortfolios();
}, { immediate: true });

// Fetch initial data when mounted
onMounted(async () => {
  serviceStore.loading = true;
  await fetchUserData();
  await fetchProfileData();
  await fetchCategories(); 
  await fetchUserServices();
  await fetchPortfolios();
});
</script>