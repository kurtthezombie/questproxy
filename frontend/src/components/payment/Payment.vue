<template>
  <div>
    <button
      class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-3 rounded-md text-lg transition-all shadow-lg w-full mb-2"
      @click="handlePayment"
      :disabled="paymentLoading"
    >
      <span v-if="paymentLoading">Processing Payment...</span>
      <span v-else>Proceed to Payment ({{ formatPrice(service?.price) }})</span>
    </button>

    <button
      v-if="confirmedBooking?.id"
      class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-md text-lg transition-all shadow-lg w-full mb-2"
      @click="checkPaymentStatus"
      :disabled="statusLoading"
    >
      <span v-if="statusLoading">Checking Status...</span>
      <span v-else>Check Payment Status</span>
    </button>

    <button
      @click="cancelBooking"
      class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white w-full"
    >
      Cancel Booking
    </button>

    <div v-if="error" class="mt-4 bg-red-800/30 text-red-300 p-3 rounded-md text-sm">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { initiatePayment } from '@/services/payment-service';
import axios from 'axios';

const props = defineProps({
  confirmedBooking: Object,
  service: Object,
});

const emit = defineEmits(['cancel-booking']);
const paymentLoading = ref(false);
const statusLoading = ref(false);
const error = ref(null);

console.log('Confirmed Booking:', props.confirmedBooking);
const handlePayment = async () => {
if (!props.confirmedBooking?.id) {
  error.value = 'No booking found to proceed with payment.';
  console.error('Missing booking ID');
  return;
}

paymentLoading.value = true;
error.value = null;

try {
  const authToken = localStorage.getItem('authToken');
  if (!authToken) throw new Error('You must be logged in to make a payment.');

  console.log('Initiating payment for booking:', props.confirmedBooking.id);

  const checkoutUrl = await initiatePayment(
    props.confirmedBooking.id,
    `http://localhost:5173/home`, // Success URL
    `http://localhost:5173/payments/${props.confirmedBooking.id}`, // Cancel URL
    authToken
  );

  if (!checkoutUrl) {
    throw new Error('No payment URL received from server');
  }

  window.location.href = checkoutUrl;
} catch (err) {
  console.error('Payment error details:', {
    error: err,
    response: err.response?.data,
    status: err.response?.status,
  });

  error.value =
    err.response?.data?.message ||
    err.message ||
    'Payment processing failed';
} finally {
  paymentLoading.value = false;
}
};
  
  const checkPaymentStatus = async () => {
    statusLoading.value = true;
    error.value = null;
  
    try {
      const authToken = localStorage.getItem('authToken');
      if (!authToken) throw new Error('You must be logged in to check payment status.');
  
      const response = await axios.get(
        `http://127.0.0.1:8000/api/bookings/${props.confirmedBooking.id}`,
        {
          headers: { Authorization: `Bearer ${authToken}` },
        }
      );
  
      const booking = response.data.data || response.data;
  
      if (booking.status === 'completed' || booking.status === 'paid') {
        alert('Payment is already completed!');
      } else if (booking.payment_url) {
        window.location.href = booking.payment_url;
      } else {
        alert(`Current payment status: ${booking.status || 'pending'}`);
      }

      if (booking.value && booking.value.service) {
        // safe to use booking.value.service
      } else {
        // show a loading spinner or error message
      }
    } catch (err) {
      console.error('Error checking payment status:', err);
      error.value = err.response?.data?.message || err.message || 'Failed to check payment status.';
    } finally {
      statusLoading.value = false;
    }
  };
  
  const cancelBooking = () => {
    emit('cancel-booking');
  };
  
  const formatPrice = (price) =>
    price ? `₱${Number(price).toLocaleString('en-US', { minimumFractionDigits: 2 })}` : '₱0.00';
  </script>
  
