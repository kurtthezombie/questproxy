<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
      <div class="bg-white p-8 rounded-lg shadow-lg text-center">
        <!-- Success Message -->
        <div v-if="isSuccess">
          <h1 class="text-2xl font-bold text-green-500 mb-4">Payment Successful!</h1>
          <p class="text-gray-700">Thank you for your purchase. Your booking has been confirmed.</p>
        </div>
  
        <!-- Cancel Message -->
        <div v-else>
          <h1 class="text-2xl font-bold text-red-500 mb-4">Payment Cancelled</h1>
          <p class="text-gray-700">Your payment was cancelled. Please try again if you wish to complete the booking.</p>
        </div>
  
        <!-- Go to Dashboard Button -->
        <button @click="goToDashboard" :class="buttonClass">
          Go to Dashboard
        </button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed, onMounted, ref } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import axios from 'axios';
  import { useUserStore } from '@/stores/userStore';
  
  const route = useRoute();
  const router = useRouter();
  const userStore = useUserStore();
  
  const isSuccess = ref(false);
  const isLoading = ref(true);
  
  // Verify payment status with PayMongo
  const verifyPayment = async (transactionId) => {
    try {
      const authToken = userStore.token || localStorage.getItem('authToken');
      const response = await axios.get(
        `http://127.0.0.1:8000/api/payments/success/${transactionId}`,
        {
          headers: {
            Authorization: `Bearer ${authToken}`,
          },
        }
      );
  
      if (response.data && response.data.success) {
        isSuccess.value = true;
      } else {
        isSuccess.value = false;
      }
    } catch (error) {
      console.error('Error verifying payment:', error);
      isSuccess.value = false;
    } finally {
      isLoading.value = false;
    }
  };
  
  onMounted(() => {
    const transactionId = route.query.transaction_id;
    if (transactionId) {
      verifyPayment(transactionId);
    } else {
      isSuccess.value = route.query.status === 'success';
      isLoading.value = false;
    }
  });
  
  const goToDashboard = () => {
    router.push({ name: 'dashboard' });
  };
  
  const buttonClass = computed(() => {
    return isSuccess.value
      ? 'mt-6 px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600'
      : 'mt-6 px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600';
  });
  </script>