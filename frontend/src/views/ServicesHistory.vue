<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <NavBar/>
    <div class="container mx-auto py-5 max-w-7xl">

      <!-- Loading State -->
      <div v-if="serviceStore.loading" class="flex justify-center items-center mt-10">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
      </div>

      <!-- Error State -->
      <div v-else-if="serviceStore.error" class="text-red-500 text-center mt-10">
        {{ serviceStore.error }}
      </div>

      <!-- Content -->
      <template v-else>
        <div class="flex justify-between items-center px-4 mt-10 mb-4">
          <div>
            <h1 class="text-5xl font-bold text-white">My Services</h1>
            <p class="text-gray-300 text-lg mt-3">Manage your gaming services and bookings</p>
          </div>
          <div v-if="role === 'game pilot'">
            <router-link to="/create-service" class="inline-block">
              <button class="bg-emerald-500 hover:bg-emerald-600 text-black py-2 px-4 rounded-md transition-colors duration-200">
                &#43; Create New Service
              </button>
            </router-link>
          </div>
        </div>

        <!-- Search Section -->
        <div class="relative w-full max-w-7xl">
          <div class="flex items-center mt-8 space-x-4 px-4 w-full">
            <div class="relative w-full max-w-7xl">
              <div class="absolute left-2 top-1/2 -translate-y-1/2 bg-blue-900 bg-opacity-50 rounded-full p-2 focus-within:border-2 focus-within:border-green-400">
                <svg 
                  xmlns="http://www.w3.org/2000/svg" 
                  class="h-7 w-7 text-white"
                  fill="none" 
                  viewBox="0 0 24 24" 
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M10 3a7 7 0 015.65 11.35l4.35 4.35M15 10a5 5 0 10-10 0 5 5 0 0010 0z" />
                </svg>
              </div>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search services.."
                class="bg-blue-800 bg-opacity-5 text-gray-300 border border-gray-700 rounded-full pl-16 pr-4 py-4 h-15 shadow-md w-full focus:outline-none focus:border-4 focus:border-green-600 focus:text-gray-300"
              />
            </div>
          </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="relative ml-4 mt-3 mr-4">
          <!-- Tab Buttons Container -->
          <div class="flex justify-start items-center px-4 space-x-6 py-2 border-b border-gray-700">
            <button class="flex items-center text-green-400 pb-2 hover:text-emerald-400">
              <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-7 4h8m1-10V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2M4 7h16a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V9a2 2 0 012-2z" />
              </svg>
              Available Services 
              <span class="bg-emerald-500 text-white rounded-full px-2 ml-1 mr-10"></span>
            </button>
            <button class="flex items-center text-gray-500 hover:text-white">
              <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
              </svg>
              My Bookings 
              <span class="bg-gray-600 text-white rounded-full px-2 ml-1 mr-10"></span>
            </button>
            <div class="flex items-center space-x-2 mb-1">
              <button class="flex items-center text-gray-500 hover:text-white">
                <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Service History 
                <span class="bg-gray-600 text-white rounded-full px-2 ml-1"></span>
              </button>
            </div>
          </div>

          <!-- Underline Outside the Container -->
          <div class="absolute top-full h-[2px] bg-emerald-400 rounded" style="width: 200px;"></div>
        </div>

        <!-- No Services Message -->
        <div v-if="!filteredUserServices.length" class="text-center mt-10 text-gray-400">
          No services available.
        </div>

        <!-- Services Grid -->
        <div class="px-4 py-5">
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
            <ServiceDisplay
              v-for="service in filteredAndSearchedServices"
              :key="service.id"
              :service="service"
              :categories="serviceStore.categories"
              :isServiceHistory="true"
              @serviceDeleted="handleServiceDeleted"
              class="w-full"
            />
          </div>
        </div>

      </template>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useServiceStore } from '@/stores/serviceStore';
import { useUserStore } from '@/stores/userStore';
import NavBar from '@/components/NavBar.vue';
import ServiceDisplay from '@/components/ServiceDisplay.vue';

const router = useRouter();
const serviceStore = useServiceStore();
const userStore = useUserStore();
console.log("ID: ", userStore.userData.pilot_id)
const searchQuery = ref(''); // Used to bind the search input

const username = computed(() => userStore.userData?.username || '');
const email = computed(() => userStore.userData?.email || '');
const role = computed(() => userStore.userData?.role || '');

const filteredUserServices = computed(() => {
  const userId = userStore.userData?.pilot_id;
  return userId ? serviceStore.services.filter(service => service.pilot_id === userId) : [];
});

// Computed property to filter the services based on searchQuery
const filteredAndSearchedServices = computed(() => {
  if (!searchQuery.value) {
    return filteredUserServices.value;
  }
  return filteredUserServices.value.filter(service =>
    service.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    service.description.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const checkAuth = () => {
  if (!localStorage.getItem('authToken')) {
    router.push({ name: 'login' });
    return false;
  }
  return true;
};

const fetchData = async () => {
  const pilot_id = userStore.userData?.pilot_id;
  if (!pilot_id) {
    console.error("Pilot ID not found.");
    return;
  }
  
  try {
    await serviceStore.fetchServicesByPilot(pilot_id); 
    await serviceStore.fetchCategories();
    console.log("Fetched services:", serviceStore.services);
  } catch (error) {
    console.error("Error fetching data:", error);
  }
};

const handleServiceDeleted = (deletedId) => {
  serviceStore.services = serviceStore.services.filter(service => service.id !== deletedId);
};

const callLogout = () => {
  userStore.clearUser();
  serviceStore.clearServices();
  localStorage.removeItem('authToken');
  localStorage.removeItem('tokenType');
  router.push({ name: 'login' });
};

onMounted(async () => {
  if (checkAuth()) {
    await fetchData();
  }
});
</script>
