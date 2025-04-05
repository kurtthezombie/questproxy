<template>
  <div class="p-6 bg-gray-900 min-h-screen">
    <div class="max-w-5xl mx-auto bg-gray-800 p-6 rounded-lg shadow-lg flex">
      <!-- Left Section: Profile & Portfolio -->
      <div class="w-1/3 pr-4">
        <div class="flex flex-col items-center">
          <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center text-white text-2xl font-bold">
            {{ userInitial }}
          </div>
          <h2 class="mt-2 text-xl font-bold text-white">{{ profileData.username || 'Loading...' }}</h2>
          <button 
            class="mt-2 px-4 py-1 bg-blue-500 text-white rounded"
            @click="goToPortfolio"
          >
            Portfolio
          </button>
        </div>

        <!-- Portfolio Preview -->
        <div class="mt-4 flex items-center">
          <div v-for="(portfolio, index) in portfolios.slice(0, 2)" :key="index">
            <img :src="portfolio.p_content" alt="Portfolio Image" class="w-12 h-12 bg-gray-600 rounded-md mr-2">
          </div>
          <div v-if="portfolios.length > 2" class="w-12 h-12 bg-gray-700 rounded-md flex items-center justify-center text-white text-lg">
            +
          </div>
        </div>
      </div>

      <!-- Right Section: Services -->
      <div class="w-2/3 pl-4">
        <h3 class="text-lg text-center font-semibold text-white mb-4">Offered Services</h3>
        <div v-if="userServices.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <ServiceCard 
            v-for="service in userServices" 
            :key="service.id" 
            :service="service"
            :categories="categories"
            @click="handleServiceClick(service)"
          />
        </div>
        <div v-else class="flex justify-center items-center h-32 text-gray-400 text-lg">
          No services available
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useUserStore } from '@/stores/userStore'; 
import loginService from '@/services/login-service';
import userService from '@/services/user-service';
import serviceService from '@/services/service-service';
import { fetchPortfoliosByUser } from '@/services/portfolio.service';
import { useLoader } from '@/services/loader-service';
import ServiceCard from '@/components/ServiceDisplay.vue'; 

const { loadShow, loadHide } = useLoader();

const router = useRouter();
const route = useRoute();
const userStore = useUserStore();

const profileData = ref({});
const userServices = ref([]);
const portfolios = ref([]);
const username = ref('');
const role = ref('User');
const profileLoaded = ref(false);
const categories = ref([]); // Add categories data

const userInitial = computed(() => {
  if (!profileData.value?.f_name) return '?';
  return `${profileData.value.f_name.charAt(0)}${profileData.value.l_name?.charAt(0) || ''}`.toUpperCase();
});

// Fetch categories
const fetchCategories = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/categories');
    categories.value = response.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

// Fetch user data
const fetchUserData = async () => {
  const loader = loadShow();
  try {
    const userData = await loginService.fetchUserData();
    username.value = userData.username;
    role.value = userData.role || 'User';
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
    profileData.value = await userService.getUserProfile(userId);
    profileLoaded.value = true; 
  } catch (error) {
    console.error('Error fetching profile:', error);
  }
};

// Fetch user services
const fetchUserServices = async () => {
  try {
    const userId = route.params.id;
    userServices.value = await serviceService.fetchUserDataById(userId);
  } catch (error) {
    console.error('Error fetching user services:', error);
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
  if (!profileLoaded.value) {
    console.error("Profile data not loaded yet, cannot redirect.");
    return;
  }

  if (!profileData.value || !profileData.value.username) {
    console.error("Username is missing, cannot redirect.");
    return;
  }

  const isOwnProfile = profileData.value.username === username.value;

  if (isOwnProfile) {
    router.push({ name: 'MyPortfolio' });
  } else {
    router.push({ name: 'PortfolioView', params: { username: profileData.value.username } });
  }
};

// Watch for changes in the route and fetch the data
watch(() => route.params.id, async () => {
  await fetchUserData();
  await fetchProfileData();
  await fetchUserServices();
  await fetchPortfolios();
  await fetchCategories(); 
}, { immediate: true });

// Fetch initial data when mounted
onMounted(async () => {
  await fetchUserData();
  await fetchProfileData();
  await fetchUserServices();
  await fetchPortfolios();
  await fetchCategories(); 
});
</script>
