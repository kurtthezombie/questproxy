<template>
  <NavBar :username="username" :email="email" :role="role" :callLogout="callLogout" />

  <div class="flex items-center justify-center min-h-screen bg-gray-900">
    <!-- Payment Card -->
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-6">
      <div>
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
          Complete Your Payment for {{ getGameTitle }}
        </h2>
      </div>

      <div v-if="error" class="mb-4 p-4 bg-red-50 text-red-500 rounded-md">
        {{ error }}
      </div>

      <div v-else-if="loading" class="flex justify-center items-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
      </div>

      <div v-else class="space-y-4">
        <p v-if="service"><strong>Description:</strong> {{ service.description }}</p>
        <p v-if="service"><strong>Price:</strong> ₱{{ formatPrice(service.price) }}</p>
        <p v-if="service"><strong>Duration:</strong> {{ formatDuration(service.duration) }}</p>
        <p v-if="service"><strong>Availability:</strong> {{ service.availability ? 'Available' : 'Not Available' }}</p>
        <p class="text-gray-600">
          Click below to proceed with the payment. You will be prompted to complete booking details.
        </p>
      </div>

      <!-- Initial Buy Now Button -->
      <button
        class="w-full bg-red-500 text-white py-2 rounded-lg mt-4"
        @click="openBookingModal"
        :disabled="loading"
      >
        Buy Now
      </button>

      <!-- Booking Modal -->
      <Booking 
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

const loading = ref(false);
const error = ref(null);
const route = useRoute();

const service = computed(() => serviceStore.service);
const username = ref('');
const email = ref('');
const isBookingModalOpen = ref(false);

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
    await serviceStore.fetchServiceById(serviceId);
  } catch (err) {
    error.value = 'Error fetching service data';
  } finally {
    loading.value = false;
  }
};

const getGameTitle = computed(() => {
  if (serviceStore.categories.length && serviceStore.service) {
    const category = serviceStore.categories.find(
      (cat) => cat.game === serviceStore.service.game
    );
    return category ? category.title : serviceStore.service.game;
  }
  return serviceStore.service?.game || 'Unknown Game';
});

const formatDuration = (duration) => {
  if (!duration) return 'Not specified';
  const date = new Date(duration);
  return date.toLocaleString();
};

const formatPrice = (price) => {
  return Number(price).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
};

const openBookingModal = () => {
  isBookingModalOpen.value = true;
};

const closeBookingModal = () => {
  isBookingModalOpen.value = false;
};

// Handle the final booking submission from the modal
const handleBookingSubmit = async (bookingData) => {
  try {
    loading.value = true;
    // Make API call to complete payment
    const paymentResponse = await axios.post(`http://127.0.0.1:8000/api/payments/${bookingData.bookingId}`, {
      amount: serviceStore.service.price,
      transaction_data: bookingData.transactionData
    }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('authToken')}`
      }
    });
    if (paymentResponse.data) {
      alert('Payment completed successfully!');
      closeBookingModal();
      router.push({ name: 'TransactionView', params: { transactionId: paymentResponse.data.transaction_id } });
    }
  } catch (err) {
    console.error('Error completing payment:', err);
    alert('Failed to complete payment.');
  } finally {
    loading.value = false;
  }
};

function checkAuth() {
  if (!localStorage.getItem('authToken')) {
    router.push({ name: 'login' });
  }
}

const callLogout = () => {
  userStore.clearUser();
  serviceStore.clearServices();
  localStorage.removeItem('authToken');
  localStorage.removeItem('tokenType');
  router.push({ name: 'login' });
};
</script>
