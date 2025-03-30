<template>
  <div v-if="isModalOpen" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg max-w-lg w-full">
      <h3 class="text-xl font-semibold mb-4">Order Details</h3>

      <!-- Confidentiality Message -->
      <div class="bg-blue-50 p-4 rounded-lg mb-4 text-sm text-blue-800">
        <p>Your information will be kept confidential and used solely for the purpose of completing your order. We do not share your data with third parties.</p>
      </div>

      <!-- Additional Notes Textarea -->
      <textarea
        v-model="additionalNotes"
        placeholder="Add your additional notes here..."
        class="w-full p-2 border rounded-lg mb-4"
      ></textarea>

      <!-- Username Input -->
      <input
        v-model="credentialsUsername"
        type="text"
        placeholder="Username"
        class="w-full p-2 border rounded-lg mb-4"
      />

      <!-- Password Input -->
      <input
        v-model="credentialsPassword"
        type="text"
        placeholder="Password"
        class="w-full p-2 border rounded-lg mb-4"
      />

      <!-- Submit Booking and Payment Button -->
      <button
        :disabled="isLoading"
        :class="{'opacity-50 cursor-not-allowed': isLoading}"
        class="w-full bg-green-500 text-white py-2 rounded-lg"
        @click="submitBooking"
      >
        {{ isLoading ? 'Processing...' : 'Buy' }}
      </button>

      <!-- Cancel Button -->
      <button
        :disabled="isLoading"
        class="mt-2 w-full bg-gray-300 text-gray-700 py-2 rounded-lg"
        @click="closeBookingModal"
      >
        Cancel
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useUserStore } from '@/stores/userStore';

const props = defineProps({
  isModalOpen: Boolean,
  serviceId: String,
});

const emit = defineEmits(['close', 'submitBooking']);

const userStore = useUserStore();

const additionalNotes = ref('');
const credentialsUsername = ref('');
const credentialsPassword = ref('');
const isLoading = ref(false);

const closeBookingModal = () => {
  emit('close');
};

const submitBooking = async () => {
  isLoading.value = true;

  try {
    const authToken = userStore.token || localStorage.getItem('authToken');

    await axios.post(
      'http://127.0.0.1:8000/api/bookings/store',
      {
        client_id: userStore.userData.id,
        service_id: props.serviceId,
        additional_notes: additionalNotes.value,
        credentials_username: credentialsUsername.value,
        credentials_password: credentialsPassword.value,
      },
      {
        headers: {
          Authorization: `Bearer ${authToken}`,
        },
      }
    );

    const paymentResponse = await axios.post(
      `http://127.0.0.1:8000/api/payments/${props.serviceId}`, 
      {
        success_url: `${window.location.origin}/payment-result?status=success&transaction_id={CHECKOUT_SESSION_ID}`,
        cancel_url: `${window.location.origin}/payment-result?status=cancel`,
      },
      {
        headers: {
          Authorization: `Bearer ${authToken}`,
        },
      }
    );

    // Step 3: Redirect to PayMongo checkout URL
    if (paymentResponse.data && paymentResponse.data.data && paymentResponse.data.data.checkout_url) {
      window.location.href = paymentResponse.data.data.checkout_url;
    } else {
      console.error('Invalid payment response structure:', paymentResponse.data);
    }
  } catch (err) {
    console.error('Booking submission error:', err);
  } finally {
    isLoading.value = false;
  }
};
</script>