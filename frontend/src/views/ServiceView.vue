<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <NavBar/>

    <div v-if="serviceStore.loading"
         class="flex justify-center items-center mt-10">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
    </div>

    <div v-else-if="serviceStore.error"
         class="text-red-500 text-center mt-10">
      {{ serviceStore.error }}
    </div>

    <template v-else>
      <div class="container mx-auto py-5 max-w-7xl mt-5">
        <div class="relative bg-blue-800 bg-opacity-5 p-16 rounded-lg px-20 overflow-hidden">
          <div class="absolute top-0 left-0 w-full h-full bg-cover bg-center z-0">
            <div class="dust-container" v-once>
              <div
                v-for="index in 100"
                :key="index"
                class="dust"
                :style="generateParticleStyle(index)"
              ></div>
            </div>
          </div>
          <h1 class="text-5xl font-bold text-white text-left -ml-10">
            {{ getDisplayTitle }} <span class="text-green-400">Services</span>
          </h1>
          <div class="flex justify-between items-center -ml-10">
            <div class="max-w-[700px]">
              <h2 class="text-xl text-gray-300">
                Level up your {{ getDisplayTitle }} gameplay with professional boosting services from verified pilots.
              </h2>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120" width="120" height="120" class="ml-6 mr-20">
                <rect x="25" y="35" width="70" height="50" rx="10" ry="10" fill="none" stroke="#047857" stroke-width="7"/>
                <rect x="35" y="50" width="6" height="20" fill="#047857" rx="3" ry="3"/>
                <rect x="30" y="55" width="20" height="6" fill="#047857" rx="3" ry="3"/>
                <circle cx="70" cy="55" r="6" fill="#047857"/>
                <circle cx="80" cy="65" r="6" fill="#047857"/>
            </svg>
          </div>
          <div class="flex flex-wrap justify-start gap-4 -ml-10">
            <span class="bg-emerald-950 border border-emerald-600 text-emerald-500 text-xs font-semibold px-3 py-1 rounded-full hover:bg-gray-950 flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2L15 8L22 8.5L17 12.5L18 19.5L12 16.5L6 19.5L7 12.5L2 8.5L9 8L12 2Z" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              High Skill Level
            </span>
            <span class="bg-blue-950 border border-blue-600 text-blue-500 text-xs font-semibold px-3 py-1 rounded-full hover:bg-gray-950 flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2L4 6V12C4 15.2 7.3 17.6 10 19L12 21L14 19C16.7 17.6 20 15.2 20 12V6L12 2Z" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 12L11 14L15 10" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Trustworthy
            </span>
            <span class="bg-purple-950 border border-purple-700 text-purple-500 text-xs font-semibold px-3 py-1 rounded-full hover:bg-gray-950 flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="16" height="16">
                <circle cx="50" cy="50" r="48" fill="none" stroke="CurrentColor" stroke-width="4" />
                <polygon points="50,10 55,35 80,35 58,50 65,80 50,60 35,80 42,50 20,35 45,35"
                        fill="CurrentColor" />
              </svg>
              Skilled Pilots
            </span>
          </div>
        </div>
        <div class="relative w-full sm:w-full rounded-lg p-6 my-8 flex items-center">
          <input
              v-model="searchQuery"
              type="text"
              @keydown.enter="applySearch"
              placeholder="Search by game, description, pilot, days, or price..."
              class="bg-[#1e293b] text-gray-300 border border-gray-700 rounded-full pl-16 pr-4 py-4 h-13 shadow-md w-full focus:outline-none focus:border-4 focus:border-green-600 focus:text-gray-300 placeholder-gray-500"
          />
          <svg class="absolute left-4 top-1/2 -translate-y-1/2 opacity-70 ml-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 1.5em;">
              <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor">
                  <circle cx="11" cy="11" r="8"></circle>
                  <path d="m21 21-4.3-4.3"></path>
              </g>
          </svg>
          </div>
        <div v-if="!filteredServices.length && !serviceStore.loading" class="text-center mt-10 text-gray-400">
          No services found matching your criteria.
        </div>
        <div class="px-4 py-5">
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
            <ServiceDisplayCard
              v-for="service in filteredServices"
              :key="service.id"
              :service="service"
              :categories="serviceStore.categories"
              :isServiceHistory="false"
              class="w-full"
            />
          </div>
        </div>

      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useServiceStore } from '@/stores/serviceStore';
import { useUserStore } from '@/stores/userStore';
import NavBar from '@/components/NavBar.vue';
import ServiceDisplayCard from '@/components/ServiceDisplay.vue';
import '@/assets/css/style.css'; 

const router = useRouter();
const serviceStore = useServiceStore();
const userStore = useUserStore();

// --- User State  ---
const username = computed(() => userStore.userData?.username || '');
const email = computed(() => userStore.userData?.email || '');
const role = computed(() => userStore.userData?.role || '');

// --- Route Param for Category  ---
const categoryGame = ref(router.currentRoute.value.params.title || 'all');

// --- Search Query Refs ---
const searchQuery = ref(''); 
const appliedSearchQuery = ref(''); 


// --- Computed Property for Display Title  ---
const getDisplayTitle = computed(() => {
  if (!categoryGame.value || categoryGame.value.toLowerCase() === 'all') {
    return 'All';
  }
  const category = serviceStore.categories?.find(
    cat => cat.game?.toLowerCase() === categoryGame.value.toLowerCase()
  );
  return category ? category.title : categoryGame.value;
});


// --- Modified filteredServices computed property ---
const filteredServices = computed(() => {
  let services = serviceStore.services || [];


  if (categoryGame.value && categoryGame.value.toLowerCase() !== 'all') {
    services = services.filter(service =>
      service && service.game && service.game.toLowerCase() === categoryGame.value.toLowerCase()
    );
  }

  const query = appliedSearchQuery.value.toLowerCase().trim();
  if (query) {
    const queryNumber = Number(query);
    const isNumberSearch = !isNaN(queryNumber);

    services = services.filter(service => {
      const gameTitle = service.game ? (serviceStore.categories.find(cat => cat.game === service.game)?.title || service.game) : '';

      // Check for text match in relevant fields
      const textMatch =
        gameTitle.toLowerCase().includes(query) ||
        (service.description && service.description.toLowerCase().includes(query)) ||
        (service.pilot_username && service.pilot_username.toLowerCase().includes(query));

      let numberMatch = false;
      if (isNumberSearch) {
        numberMatch =
          (service.duration === queryNumber) ||
          (service.price === queryNumber);
      }

      return textMatch || numberMatch;
    });
  }

  return services;
});


// --- Authentication Check  ---
const checkAuth = () => {
  if (!localStorage.getItem('authToken')) {
    router.push({ name: 'login' });
  }
};

// --- Service Fetching  ---
const fetchServices = async () => {
  try {
    await serviceStore.fetchUserServices();
    console.log('Fetched services:', serviceStore.services);
  } catch (error) {
    console.error('Error fetching services:', error);
    serviceStore.error = 'Failed to load services.'; 
  }
};

// --- Logout Function  ---
const callLogout = () => {
  userStore.clearUser();
  serviceStore.clearServices();
  localStorage.removeItem('authToken');
  localStorage.removeItem('tokenType');
  router.push({ name: 'login' });
};

// --- Method to apply search on Enter key ---
const applySearch = () => {
    appliedSearchQuery.value = searchQuery.value;
};

// --- Method to clear the search ---
const clearSearch = () => {
    searchQuery.value = ''; 
    appliedSearchQuery.value = ''; 
};

// --- Lifecycle Hooks (Existing) ---
onMounted(async () => {
  checkAuth();
  await serviceStore.fetchCategories(); 
  await fetchServices(); 
});

// --- Dust Particle Function ---
const generateParticleStyle = (index) => {
  const left = Math.random() * 100;
  const top = Math.random() * 100;
  const size = Math.random() * 3 + 2;
  const duration = Math.random() * 10 + 5;
  const delay = Math.random() * 5;
  return {
    left: `${left}%`,
    top: `${top}%`,
    width: `${size}px`,
    height: `${size}px`,
    animationDuration: `${duration}s`,
    animationDelay: `${delay}s`,
  };
};
</script>