<script setup>
import api from '@/utils/api';
import toast from '@/utils/toast';
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';


const route = useRoute();
const router = useRouter();
const id = route.params.id;

const loading = ref(false);
const isBeating = ref(false);
const paymentStatus = ref(null);

const animateHeart = () => {
  isBeating.value = true;
  setTimeout(() => {
    isBeating.value = false;
  }, 100);
}

const handleVerifyPayment = async (id) => {
  try{
    const response = await api.get(`payments/success/${id}`);

    paymentStatus.value = response.data.payment_status;

    if (paymentStatus.value === 'paid') {
      loading.value = false;
    } else {
      loading.value = false; 
      toast.error('Payment not verified. Please try again later.');
    }
  } catch (error){
    loading.value = false; // Stop loading on error
    console.error('Error verifying payment:', error);
    toast.error('Something went wrong. Please try again later.');
  }
}

const handleContinue = () => {
  router.push('/home');
}


onMounted(() => {
  loading.value = true;
  handleVerifyPayment(id);
});
</script>

<template>
  <div class="min-h-screen bg-gray-900 flex items-center justify-center text-white">
    <div class="card bg-gray-800 max-w-md w-full flex flex-col items-center justify-center">
      <div class="card-body text-center w-full">
        <div v-if="loading" class="space-y-3">
          <div class="card-title text-2xl font-bold flex justify-center">
            Verifying Payment...
          </div>
          <div class="animate-pulse text-sm text-gray-400">
            <p>Please wait while we confirm your transaction</p>
          </div>
          <div class="w-16 h-16 border-4 border-green-400 border-t-transparent rounded-full mx-auto animate-spin"></div>
        </div>
        <div v-else-if="paymentStatus === 'paid'" class="space-y-3">
          <div class="card-title text-2xl font-bold flex justify-center text-green-400">Payment Verified!</div>
          <div class="text-sm text-gray-400">Thanks for purchasing a service! Come again.</div>
          <div class="flex justify-center mt-4">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-16 text-green-400 cursor-pointer transition-transform duration-100"
            :class="{ 'scale-125': isBeating }"
            @click="animateHeart"
            >
              <path
                d="m9.653 16.915-.005-.003-.019-.01a20.759 20.759 0 0 1-1.162-.682 22.045 22.045 0 0 1-2.582-1.9C4.045 12.733 2 10.352 2 7.5a4.5 4.5 0 0 1 8-2.828A4.5 4.5 0 0 1 18 7.5c0 2.852-2.044 5.233-3.885 6.82a22.049 22.049 0 0 1-3.744 2.582l-.019.01-.005.003h-.002a.739.739 0 0 1-.69.001l-.002-.001Z" />
            </svg>
          </div>
        </div>

        <div v-else class="space-y-3">
          <div class="card-title text-2xl font-bold flex justify-center text-red-400">Payment Not Verified</div>
          <div class="text-sm text-gray-400">Unfortunately, we couldn't verify your payment. Please try again later.</div>
        </div>

        <div class="card-actions">
          <button class="btn btn-success w-full mt-4" :disabled="loading" @click="handleContinue">Continue</button>
        </div>
      </div>
    </div>
  </div>
</template>