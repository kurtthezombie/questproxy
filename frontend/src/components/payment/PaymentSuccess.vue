<template>
  <NavBar />
  <div class="min-h-screen flex items-center justify-center bg-gray-900 text-white px-4 py-5">
    <div class="bg-blue-800 bg-opacity-5 p-8 rounded-xl border border-gray-700 w-full max-w-md">
      <div v-if="loading" class="text-center py-10">
        <svg class="animate-spin h-10 w-10 text-blue-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="mt-4">Verifying your payment...</p>
      </div>

      <div v-else-if="paymentVerified" class="text-center">
        <div class="mb-6 text-green-500">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h2 class="text-2xl font-bold mb-2">Payment Successful!</h2>
        <p class="text-gray-300 mb-6">Your booking is now confirmed.</p>
        
        <div class="bg-gray-800 p-4 rounded-lg mb-6 text-left">
          <div class="flex justify-between mb-2">
            <span class="text-gray-400">Amount:</span>
            <span class="font-bold">₱{{ (payment.amount / 100).toFixed(2) }}</span>
          </div>
          <div class="flex justify-between mb-2">
            <span class="text-gray-400">Booking ID:</span>
            <span>{{ payment.booking_id }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-400">Paid via:</span>
            <span class="capitalize">{{ payment.method || 'Card' }}</span>
          </div>
        </div>
        
        <router-link 
          to="/services-history" 
          class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-md transition-all w-full text-center block"
        >
          View My Bookings
        </router-link>
      </div>

      <div v-else class="text-center">
        <div class="mb-6 text-red-500">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </div>
        <h2 class="text-2xl font-bold mb-2">Payment Issue</h2>
        <p class="text-gray-300 mb-6">{{ error || "We couldn't verify your payment." }}</p>
        
        <div class="flex gap-3">
          <router-link 
            to="/services" 
            class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-4 py-2 rounded-md transition-all flex-1 text-center"
          >
            Browse Services
          </router-link>
          <router-link 
            to="/services-history" 
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-md transition-all flex-1 text-center"
          >
            Check Bookings
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import NavBar from '@/components/NavBar.vue';

const route = useRoute();
const payment = ref({});
const loading = ref(true);
const paymentVerified = ref(false);
const error = ref(null);

onMounted(async () => {
  try {
    // First get the payment record using booking ID from route
    const authToken = localStorage.getItem('authToken');
    if (!authToken) throw new Error('Session expired');

    // 1. Find payment record by booking ID
    const paymentsResponse = await axios.get(
      `http://127.0.0.1:8000/api/payments?booking_id=${route.params.id}`,
      {
        headers: { Authorization: `Bearer ${authToken}` }
      }
    );

    const paymentRecord = paymentsResponse.data.data.payment[0];
    if (!paymentRecord) throw new Error('Payment record not found');

    // 2. Verify payment with PayMongo
    const verificationResponse = await axios.get(
      `http://127.0.0.1:8000/api/payments/success/${paymentRecord.transaction_id}`,
      {
        headers: { Authorization: `Bearer ${authToken}` }
      }
    );

    payment.value = paymentRecord;
    paymentVerified.value = true;
    
  } catch (err) {
    error.value = err.response?.data?.message || 
                err.message || 
                'Payment verification failed';
    console.error('Payment error:', err);
  } finally {
    loading.value = false;
  }
});
</script>