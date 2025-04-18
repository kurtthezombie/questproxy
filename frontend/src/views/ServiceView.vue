<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <NavBar/>
    
    <!-- Loading State -->
    <div v-if="serviceStore.loading" 
         class="flex justify-center items-center mt-10">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="serviceStore.error" 
         class="text-red-500 text-center mt-10">
      {{ serviceStore.error }}
    </div>

    <!-- Content -->
    <template v-else>
      <div class="container mx-auto py-5 max-w-7xl mt-5">
        <div class="relative bg-blue-800 bg-opacity-5 p-16 rounded-lg px-20 overflow-hidden">
          <div class="absolute top-0 left-0 w-full h-full bg-cover bg-center z-0">
            <div class="dust-container">
              <!-- Generate 100 dust particles -->
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
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 2C6 4 8 4 12 2C16 4 18 4 20 2C20 6 20 8 20 12C20 15 16 18 12 20C8 18 4 15 4 12C4 8 4 6 4 2Z" />
            </svg>
              Verified Pilots
            </span>
          </div>
        </div>

        <!-- No Services Message -->
        <div v-if="!filteredServices.length" class="text-center mt-10 text-gray-400">
          No services available for this category.
        </div>

        <div class="px-4 py-5">
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
    <ServiceDisplay
      v-for="service in filteredServices"
      :key="service.id"
      :service="service"
      :categories="serviceStore.categories"
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
import ServiceDisplay from '@/components/ServiceDisplay.vue';
import '@/assets/css/style.css';

const router = useRouter();
const serviceStore = useServiceStore();
const userStore = useUserStore();

const username = computed(() => userStore.userData?.username || '');
const email = computed(() => userStore.userData?.email || '');
const role = computed(() => userStore.userData?.role || '');

const categoryGame = ref(router.currentRoute.value.params.title || 'all');

const getDisplayTitle = computed(() => {
  if (!categoryGame.value || categoryGame.value.toLowerCase() === 'all') {
    return 'All';
  }
  const category = serviceStore.categories?.find(
    cat => cat.game?.toLowerCase() === categoryGame.value.toLowerCase()
  );
  return category ? category.title : categoryGame.value;
});


const filteredServices = computed(() => {
  if (!categoryGame.value || categoryGame.value.toLowerCase() === 'all') {
    return serviceStore.services;
  }
  return serviceStore.services.filter(service => 
    service && service.game && service.game.toLowerCase() === categoryGame.value.toLowerCase()
  );
});


const checkAuth = () => {
  if (!localStorage.getItem('authToken')) {
    router.push({ name: 'login' });
  }
};

const fetchServices = async () => {
  try {
    await serviceStore.fetchUserServices();
    console.log('Fetched services:', serviceStore.services); 
  } catch (error) {
    console.error('Error fetching services:', error);
  }
};


const callLogout = () => {
  userStore.clearUser();
  serviceStore.clearServices();
  localStorage.removeItem('authToken');
  localStorage.removeItem('tokenType');
  router.push({ name: 'login' });
};

onMounted(async () => {
  checkAuth();
  await serviceStore.fetchCategories();
  await fetchServices();
});

// Function to generate random styles for dust particles
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