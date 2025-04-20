<template>
  <NavBar />
  <div class="min-h-screen flex items-center justify-center bg-gray-900 text-white p-6">
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl w-full flex flex-col md:flex-row">
      <!-- Service Details -->
      <div class="flex-1 pr-0 md:pr-6 mb-6 md:mb-0">
        <h2 class="text-2xl font-bold">
          <span v-if="loading">Loading service details...</span>
          <span v-else>{{ service ? formatGameTitle(service.game) : 'Service' }}</span>
        </h2>

        <p class="text-gray-400 mt-2">
          <span v-if="loading" class="animate-pulse">Fetching description...</span>
          <span v-else>{{ service?.description || 'No description available' }}</span>
        </p>

        <div class="mt-4 space-y-2">
          <p><strong>Price:</strong> 
            <span v-if="loading" class="animate-pulse">₱...</span>
            <span v-else>{{ service ? formatPrice(service.price) : '₱0.00' }}</span>
          </p>

          <p><strong>Duration:</strong> 
            <span v-if="loading" class="animate-pulse">Loading...</span>
            <span v-else>
              {{ formatDuration(service?.duration) }}
            </span>
          </p>

          <p><strong>Status:</strong>
            <span :class="service?.availability ? 'text-green-400' : 'text-red-400'">
              {{ service?.availability ? 'Available' : 'Not Available' }}
            </span>
          </p>
        </div>
      </div>

      <!-- Payment Action -->
      <div class="flex flex-col justify-center items-center w-full md:w-1/3">
        <button 
          class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg text-lg w-full transition-colors"
          @click="proceedToPayment"
          :disabled="loading || !service?.availability"
        >
          <span v-if="loading">Loading...</span>
          <span v-else-if="!service?.availability">Not Available</span>
          <span v-else>Book & Pay Now</span>
        </button>
        
        <div v-if="error" class="mt-4 p-3 bg-red-900/50 text-red-300 rounded-lg text-sm w-full">
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