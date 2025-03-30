<template>
  <NavBar :username="username" :email="email" :role="role" :callLogout="callLogout" />

  <div class="flex items-center justify-center min-h-screen bg-gray-900">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-6">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">
        Complete Your Payment for {{ getGameTitle }}
      </h2>

      <!-- Error Message -->
      <div v-if="error" class="mb-4 p-4 bg-red-50 text-red-500 rounded-md">
        {{ error }}
      </div>

      <!-- Loading Spinner -->
      <div v-else-if="loading" class="flex justify-center items-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
      </div>

      <!-- Service Details -->
      <div v-else-if="service" class="space-y-4">
        <p><strong>Game Title:</strong> {{ getGameTitle }}</p>
        <p><strong>Description:</strong> {{ service.description }}</p>
        <p><strong>Price:</strong> ₱{{ formatPrice(service.price) }}</p>
        <p><strong>Duration:</strong> {{ formatDuration(service.duration) }}</p>
        <p><strong>Availability:</strong> {{ service.availability ? 'Available' : 'Not Available' }}</p>
      </div>

      <!-- Buy Now Button -->
      <button
        class="w-full bg-red-500 text-white py-2 rounded-lg mt-4"
        @click="openBookingModal"
        :disabled="loading"
      >
        Buy Now
      </button>

      <!-- Cancel Button -->
      <button
        class="w-full bg-gray-500 text-white py-2 rounded-lg mt-2"
        @click="cancelPayment"
      >
        Cancel
      </button>

      <!-- Booking Modal -->
      <Booking
        v-if="isBookingModalOpen"
        :isModalOpen="isBookingModalOpen"
        @close="closeBookingModal"
        :serviceId="route.params.serviceId"
        @submitBooking="handleBookingSubmit"
      />
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import { useServiceStore } from '@/stores/serviceStore';
import { useUserStore } from '@/stores/userStore';
import NavBar from '@/components/NavBar.vue';
import Booking from '@/components/BookingView.vue';

const serviceStore = useServiceStore();
const userStore = useUserStore();
const router = useRouter();
const route = useRoute();

const loading = ref(false);
const error = ref(null);
const isBookingModalOpen = ref(false);

const service = computed(() => serviceStore.service);
const username = ref(userStore.userData?.username || '');
const email = ref(userStore.userData?.email || '');
const role = ref(userStore.userData?.role || '');

onMounted(() => {
  checkAuth();
  fetchService();
});

const fetchService = async () => {
  const serviceId = route.params.serviceId;
  if (!serviceId) {
    error.value = 'Service ID is missing in route parameters.';
    return;
  }

  try {
    loading.value = true;
    error.value = null;

    const response = await axios.get(`http://127.0.0.1:8000/api/services/${serviceId}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('authToken')}`,
      },
    });

    console.log('Fetched service response:', response.data); // Debugging

    if (response.data && response.data.data) {
      serviceStore.setService(response.data.data); // Assuming `data` contains the service
      console.log('Stored service:', serviceStore.service);
    } else {
      error.value = 'Invalid service data received.';
    }
  } catch (err) {
    error.value = 'Failed to fetch service details. Please try again.';
    console.error('Error fetching service:', err);
  } finally {
    loading.value = false;
  }
};


// Get the game title from categories or fallback to the service game name
const getGameTitle = computed(() => {
  return service.value?.game || 'Unknown Game';
});

// Format duration to a readable date string
const formatDuration = (duration) => {
  if (!duration) return 'Not specified';
  const date = new Date(duration);
  return date.toLocaleString();
};

// Format price to a readable currency string
const formatPrice = (price) => {
  return Number(price).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
};

// Open the booking modal
const openBookingModal = () => {
  isBookingModalOpen.value = true;
};

// Close the booking modal
const closeBookingModal = () => {
  isBookingModalOpen.value = false;
};

// Handle the final booking submission from the modal
const handleBookingSubmit = async (bookingData) => {
  try {
    loading.value = true;

    // Send payment request to the backend
    const paymentResponse = await axios.post(
      `http://127.0.0.1:8000/api/payments/${bookingData.bookingId}`,
      {
        amount: serviceStore.service.price,
        transaction_data: bookingData.transactionData,
      },
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('authToken')}`,
        },
      }
    );

    if (paymentResponse.data) {
      alert('Payment completed successfully!');
      closeBookingModal();
      router.push({
        name: 'TransactionView',
        params: { transactionId: paymentResponse.data.transaction_id },
      });
    }
  } catch (err) {
    console.error('Error completing payment:', err);
    alert('Failed to complete payment.');
  } finally {
    loading.value = false;
  }
};

// Cancel payment and go back
const cancelPayment = () => {
  router.go(-1);
};

// Check if the user is authenticated
const checkAuth = () => {
  if (!localStorage.getItem('authToken')) {
    router.push({ name: 'login' });
  }
};

// Logout function
const callLogout = () => {
  userStore.clearUser();
  serviceStore.clearServices();
  localStorage.removeItem('authToken');
  localStorage.removeItem('tokenType');
  router.push({ name: 'login' });
};
</script>