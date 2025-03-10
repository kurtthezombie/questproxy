<template>
  <div v-if="isModalOpen" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg max-w-lg w-full">
      <h3 class="text-xl font-semibold mb-4">Order Details</h3>
      
      <!-- Additional Notes Textarea -->
      <textarea v-model="additionalNotes" placeholder="Add your additional notes here..." class="w-full p-2 border rounded-lg mb-4"></textarea>

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
        type="password"
        placeholder="Password"
        class="w-full p-2 border rounded-lg mb-4"
      />
      
      <!-- Submit Booking and Payment Button -->
      <button class="w-full bg-green-500 text-white py-2 rounded-lg" @click="submitBooking">
        Buy
      </button>
      
      <!-- Cancel Button -->
      <button class="mt-2 w-full bg-gray-300 text-gray-700 py-2 rounded-lg" @click="closeBookingModal">
        Cancel
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useUserStore } from '@/stores/userStore';

// Props passed from PaymentView.vue
const props = defineProps({
  isModalOpen: Boolean,
  serviceId: String
});

const emit = defineEmits(['close', 'submitBooking']);

const userStore = useUserStore();

const additionalNotes = ref('');
const credentialsUsername = ref('');
const credentialsPassword = ref('');

const closeBookingModal = () => {
  emit('close');  
};

const submitBooking = async () => {
  try {
    const authToken = localStorage.getItem('authToken');

    const bookingResponse = await axios.post('http://127.0.0.1:8000/api/bookings/store', {
      client_id: userStore.userData.id,
      service_id: props.serviceId,
      additional_notes: additionalNotes.value,
      credentials_username: credentialsUsername.value,
      credentials_password: credentialsPassword.value,
    }, {
      headers: {
        Authorization: `Bearer ${authToken}`, 
      }
    });

    if (bookingResponse.data) {
      const bookingData = {
        bookingId: bookingResponse.data.booking.id,
        transactionData: {
          amount: bookingResponse.data.price,
          service_id: props.serviceId
        }
      };
      emit('submitBooking', bookingData);
      closeBookingModal();
    }
  } catch (err) {
    console.error(err);
    alert('Failed to submit booking.');
  }
};
</script>
