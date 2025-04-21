<template>
  <button 
    @click="initiatePayment"
    class="pay-button"
    :disabled="loading"
  >
    Complete Payment (₱{{ formatPrice(servicePrice) }})
  </button>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useUserStore } from '@/stores/userStore';

const props = defineProps({
  serviceId: {
    type: Number,
    required: true
  }
});

const emit = defineEmits(['payment-complete']);

const userStore = useUserStore();
const loading = ref(false);
const error = ref(null);
const servicePrice = ref(0); 

const authToken = localStorage.getItem('authToken');

onMounted(async () => {
  try {
    const res = await axios.get(
      `http://127.0.0.1:8000/api/services/${props.serviceId}`
    );
    servicePrice.value = res.data.data.service.price;
  } catch (err) {
    console.error('Failed to fetch service price:', err);
  }
});

const initiatePayment = async () => {
  loading.value = true;
  error.value = null;

  try {
    const latestBookingRes = await axios.get(
      `http://127.0.0.1:8000/api/bookings/latest?service_id=${props.serviceId}&user_id=${userStore.userData.id}`,
      {
        headers: { Authorization: `Bearer ${authToken}` }
      }
    );

    const bookingId = latestBookingRes.data.data.booking.id; 
    const paymentResponse = await axios.post(
      `http://127.0.0.1:8000/api/payments/${bookingId}`,
      {
        success_url: `${window.location.origin}/payment/success/${bookingId}`,
        cancel_url: `${window.location.origin}/payment/cancel/${bookingId}`
      },
      {
        headers: {
          Authorization: `Bearer ${authToken}`,
          'Content-Type': 'application/json'
        }
      }
    );

    const checkoutUrl = paymentResponse.data.data.checkout_url;
    window.location.href = checkoutUrl;

  } catch (err) {
    console.error('Payment failed:', err);
    error.value = err.response?.data?.message || err.message || 'Payment failed';
  } finally {
    loading.value = false;
  }
};

const formatPrice = (price) => {
  return Number(price).toFixed(2);
};
</script>
