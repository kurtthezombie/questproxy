<template>
  <NavBar />
  <div class="min-h-screen flex items-center justify-center bg-gray-900 text-white px-4 py-5">
    <div class="bg-blue-800 bg-opacity-5 p-8 rounded-xl border border-gray-700 w-[400px] h-[500px] flex flex-col -mt-20">

      <!-- Service Info -->
      <div class="flex-1 overflow-y-auto">
        <div class="flex justify-between items-center mb-2"> 
          <div>
            <a href="/services-history">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
              </svg>
            </a>
          </div>         
          <div class="text-sm px-2 rounded-2xl bg-emerald-500 ml-auto border border-gray-700">
            <span :class="service?.availability ? 'text-white  font-semibold' : 'bg-red-500 text-white'">
              {{ service?.availability ? 'Available' : 'Not Available' }}
            </span>
          </div>
        </div>
        <h3 class="text-4xl font-bold text-white">
          <span v-if="loading">Loading...</span>
          <span v-else>{{ service ? formatGameTitle(service.game) : 'Service' }}</span>
        </h3>
        <p class="text-gray-300 text-md mb-6 leading-relaxed mt-1">
          <span v-if="loading" class="animate-pulse">Fetching description...</span>
          <span v-else>{{ service?.description || 'No description available' }}</span>
        </p>
      </div>
      
      <div class="grid grid-cols-1 text-">
        <div class="grid text-left">
          <div class="flex items-center bg-gray-800 bg-opacity-60 px-3 py-2 rounded-md">
            <!-- SVG Icon -->
            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex flex-col">
              <!-- Duration label -->
              <span class="text-gray-400 text-sm">Duration</span>
              <!-- Loading or Duration value -->
              <span v-if="loading" class="animate-pulse text-white">Loading...</span>
              <span v-else class="text-white font-bold text-sm">{{ formatDuration(service?.duration) }}</span>
            </div>
          </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-700 dark:border-gray-700 my-1"></div>
              
        <div class=" pb-2 mt-2 ">
          <div class="flex flex-col items-end">
            <span class="text-gray-400 text-sm">Price</span>
            <span v-if="loading" class="animate-pulse text-green-500 text-3xl mb-5">₱...</span>
            <span v-else class="font-bold text-green-400 text-3xl mb-5">
              {{ service ? formatPrice(service.price) : '₱0.00' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Action Area -->
      <div class="mt-auto">
        <button 
          class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold px-6 py-3 rounded-md text-lg transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed w-full"
          @click="proceedToPayment"
          :disabled="loading || !service?.availability">
          <span v-if="loading">Loading...</span>
          <span v-else-if="!service?.availability">Not Available</span>
          <span v-else>Book Service</span>
        </button>

        <div v-if="error" class="mt-4 bg-red-800/30 text-red-300 p-3 rounded-md text-sm">
          {{ error }}
        </div>
      </div>
    </div>
  </div>

  <!-- Booking Modal -->
  <BookingCard 
    v-if="service"
    :isOpen="isModalOpen"
    :serviceId="service.id"
    :service="service"
    :closeModal="closeModal"
    @bookingConfirmed="handleBookingConfirmation"
  />
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import NavBar from "@/components/NavBar.vue";
import BookingCard from "@/components/BookingCard.vue"; 

const route = useRoute();
const router = useRouter();
const service = ref(null);  
const loading = ref(true);  
const isModalOpen = ref(false);
const error = ref(null);

const formatPrice = (price) => {
  return price ? `₱${Number(price).toLocaleString("en-US", { minimumFractionDigits: 2 })}` : "₱0.00";
};

const formatGameTitle = (game) => {
  if (!game) return 'Service';
  return game.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
};

const formatDuration = (duration) => {
  if (!duration) return 'N/A';
  
  // Handle both numeric (days) and datetime strings
  if (typeof duration === 'number') {
    return `${duration} ${duration === 1 ? 'day' : 'days'}`;
  }

  try {
    // Try to parse as datetime if it's a string
    const date = new Date(duration);
    if (!isNaN(date)) {
      return date.toLocaleString();
    }
    return duration; // Return as-is if not a valid date
  } catch {
    return duration; // Fallback
  }
};

// Fetch service details
const fetchServiceDetails = async () => {
  try {
    loading.value = true;
    error.value = null;
    const authToken = localStorage.getItem("authToken");
    const tokenType = localStorage.getItem("tokenType");

    if (!authToken || !tokenType) {
      throw new Error("Please login to book services");
    }

    const response = await axios.get(
      `http://127.0.0.1:8000/api/services/${route.params.serviceId}`,
      {
        headers: { Authorization: `${tokenType} ${authToken}` }
      }
    );

    if (response.data?.service) {
      service.value = response.data.service;
    } else {
      throw new Error("Service details not found");
    }

  } catch (err) {
    console.error("Service fetch error:", err);
    error.value = err.response?.data?.message || err.message || "Failed to load service details";
    // Redirect back if service doesn't exist
    if (err.response?.status === 404) {
      router.push({ name: 'services' });
    }
  } finally {
    loading.value = false;
  }
};

const proceedToPayment = () => {
  if (!service.value?.availability) {
    error.value = "This service is not currently available for booking";
    return;
  }
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

const handleBookingConfirmation = (bookingId) => {
  console.log("Booking confirmed with ID:", bookingId);
  // You can add additional handling here if needed
  closeModal();
};

onMounted(() => {
  if (!route.params.serviceId) {
    router.push({ name: 'services' });
    return;
  }
  fetchServiceDetails();
});

</script>