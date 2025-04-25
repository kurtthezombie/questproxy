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
                <svg class="h-[1.5em] opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor">
                  <circle cx="11" cy="11" r="8"></circle>
                  <path d="m21 21-4.3-4.3"></path>
                </g>
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
        <div class="relative mt-3">
          <!-- Tab Buttons Container -->
          <div class="flex justify-start items-center space-x-8 px-5">
            <button 
              class="flex rounded transition-colors duration-200 items-center pl-[100px] pr-20 py-5"
              :class="activeTab === 'services' 
                ? 'text-green-400 bg-[#1e293b]' 
                : 'text-gray-500 hover:text-white hover:bg-gray-600'"
              @click="activeTab = 'services'">
              <svg class="w-5 h-5 mr-2" 
                :class="activeTab === 'services' ? 'text-green-400' : 'text-gray-500'" 
                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" 
                  d="M9 12h6m-7 4h8m1-10V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2M4 7h16a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V9a2 2 0 012-2z" />
              </svg>
              Available Services 
              <span class="bg-emerald-500 text-white rounded-full px-2 ml-1 mr-10">
                {{ filteredUserServices.length }}
              </span>
            </button>

            <button 
              class="flex rounded transition-colors duration-200 items-center pl-[100px] pr-20 py-5"
              :class="activeTab === 'bookings' 
                ? 'text-green-400 bg-[#1e293b]' 
                : 'text-gray-500 hover:text-white hover:bg-gray-600'"
              @click="showBookings">
              <svg class="w-5 h-5 mr-2" 
                :class="activeTab === 'bookings' ? 'text-green-400' : 'text-gray-500'" 
                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" 
                  d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
              </svg>
              My Bookings 
              <span class="bg-emerald-500 text-white rounded-full px-2 ml-1 mr-10">
                {{ serviceStore.myBookings?.length || 0 }}
              </span>
            </button>

            <button 
              class="flex rounded transition-colors duration-200 items-center pl-[100px] pr-[100px] py-5"
              :class="activeTab === 'history' 
                ? 'text-green-400 bg-[#1e293b]' 
                : 'text-gray-500 hover:text-white hover:bg-gray-600'"
              @click="activeTab = 'history'">
              <svg class="w-5 h-5 mr-2" 
                :class="activeTab === 'history' ? 'text-green-400' : 'text-gray-500'" 
                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" 
                  d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Service History 
              <span class="bg-emerald-500 text-white rounded-full px-2 ml-1 mr-10">
                {{ serviceHistory.length }}
              </span>
            </button>
          </div>  
        </div>
        
        <!-- Tab Content -->
        <div class="px-4 py-6">
          <!-- Services Tab -->
          <div v-if="activeTab === 'services'">
            <div v-if="!filteredUserServices.length" class="text-center mt-10 text-gray-400">
              No services available.
            </div>
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
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

          <!-- Bookings Tab -->
          <div v-if="activeTab === 'bookings'">
            <div v-if="serviceStore.bookingsLoading" class="flex justify-center items-center py-10">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
            </div>
            <div v-else-if="serviceStore.bookingsError" class="text-red-500 text-center py-10">
              {{ serviceStore.bookingsError }}
            </div>
            <div v-else-if="!serviceStore.myBookings?.length" class="text-center text-gray-400 py-10">
              No bookings found for your services.
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="booking in serviceStore.myBookings" :key="booking.id" 
                   class="bg-blue-900 bg-opacity-20 p-4 rounded-xl border border-gray-700 hover:border-green-400 transition-all">
                <div class="flex justify-between items-start">
                  <h3 class="text-xl font-bold text-white mt-1">
                    {{ booking.service?.game || 'Unknown Service' }}
                  </h3>
                  <span :class="{
                    'bg-emerald-500 font-bold': booking.status === 'completed',
                    'bg-blue-500 font-bold': booking.status === 'pending',
                    'bg-red-500 font-bold': booking.status === 'cancelled'
                  }" class="text-xs px-2 py-1 rounded-full text-white capitalize mt-2 mr-1">
                    {{ booking.status }}
                  </span>
                </div>
                
                <p class="text-gray-300 mt-1 mb-5 ">
                  {{ booking.service?.description || 'No description available' }}
                </p>
                
                <div class="mt-3 text-gray-300">
                  <div class="flex items-center mb-5">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Client: {{ booking.client?.username || 'Unknown' }}
                  </div>
                  
                  <div class="flex justify-between items-center mt-2">
                    <span class="text-2xl font-bold text-green-400">
                      ₱{{ booking.service?.price?.toLocaleString() || '0' }}
                    </span>
                    <span class="text-gray-400">
                      {{ timeAgo(booking.created_at) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- History Tab -->
          <div v-if="activeTab === 'history'">
            <div v-if="!serviceHistory.length" class="text-center text-gray-400 py-10">
              No service history found.
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="history in serviceHistory" :key="history.id" 
                   class="bg-gray-800 p-4 rounded-xl border border-gray-700 hover:border-green-400 transition-all">
                <h2 class="text-xl font-semibold text-white mb-2">
                  {{ history.service?.name || 'Unknown Service' }}
                </h2>
                <p class="text-sm text-gray-400 mb-1">
                  <strong>Client:</strong> {{ history.client?.username || 'Unknown' }}
                </p>
                <p class="text-sm text-gray-400 mb-1">
                  <strong>Status:</strong> 
                  <span :class="{
                    'text-green-400': history.status === 'completed',
                    'text-yellow-400': history.status === 'pending',
                    'text-red-400': history.status === 'cancelled'
                  }" class="capitalize">">
                    {{ history.status }}
                  </span>
                </p>
                <p class="text-sm text-gray-400">
                  <strong>Completed At:</strong> {{ formatDate(history.updated_at) }}
                </p>
              </div>
            </div>
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
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';

dayjs.extend(relativeTime)

const router = useRouter();
const serviceStore = useServiceStore();
const userStore = useUserStore();
const searchQuery = ref('');
const activeTab = ref('services');

// Computed properties
const username = computed(() => userStore.userData?.username || '');
const email = computed(() => userStore.userData?.email || '');
const role = computed(() => userStore.userData?.role || '');

const filteredUserServices = computed(() => {
  const userId = userStore.userData?.pilot_id;
  return userId ? serviceStore.services.filter(service => service.pilot_id === userId) : [];
});

const serviceHistory = computed(() => {
  return serviceStore.myBookings?.filter(booking => 
    ['completed', 'expired'].includes(booking.status)
  ) || [];
});

const filteredAndSearchedServices = computed(() => {
  if (!searchQuery.value) {
    return filteredUserServices.value;
  }
  const query = searchQuery.value.toLowerCase();
  return filteredUserServices.value.filter(service =>
    service.name.toLowerCase().includes(query) ||
    service.description.toLowerCase().includes(query)
  );
});

// Methods
const showBookings = async () => {
  activeTab.value = 'bookings';
  if (!serviceStore.myBookings?.length) {
    await serviceStore.fetchBookingsByPilot();
  }
};

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleString();
};

const handleServiceDeleted = (deletedId) => {
  serviceStore.services = serviceStore.services.filter(service => service.id !== deletedId);
};

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
  } catch (error) {
    console.error("Error fetching data:", error);
  }
};


onMounted(async () => {
  if (checkAuth()) {
    await fetchData();
  }
});

const timeAgo = (dateString) => {
  return dayjs(dateString).fromNow()
}


</script>