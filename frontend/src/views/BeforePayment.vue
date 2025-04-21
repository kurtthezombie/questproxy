<script setup>
import api from '@/utils/api';
import toast from '@/utils/toast';


const handlePayment = async () => {
  const bookingId = 1;

  const payload = {
    success_url: `http://localhost:5173/home`,
    cancel_url: `http://localhost:5173/payments/${bookingId}`
  };

  try {
    console.log(payload);
    const response = await api.post(`/payments/${bookingId}`, payload);
    
    const checkoutUrl = response.checkout_url;
    if (checkoutUrl) {
      window.location.href = checkoutUrl;
    } else {
      toast.error('No checkout URL received: ', response.data);
    }
    
  } catch (error) {
    toast.error('error occured my homie');
  }
}

</script>
<template>
  <div class="min-h-screen bg-gray-800 flex justify-center items-center">
    <button class="btn btn-lg btn-success" @click="handlePayment">To Payment</button>
  </div>
</template>