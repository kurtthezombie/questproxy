<template>
    <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md mx-4">
            <h2 class="text-xl font-bold mb-4 text-center dark:text-white">Confirm Your Booking</h2>
            <p class="text-gray-600 dark:text-gray-300 text-center mb-6">
                Your information is secure and encrypted.
            </p>
  
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">Game Account Username</label>
                    <input v-model="form.credentials_username" type="text"
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Enter game username" required />
                </div>
  
                <div>
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">Game Account Password</label>
                    <div class="relative">
                        <input v-model="form.credentials_password" :type="showPassword ? 'text' : 'password'"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Enter game password" required />
                        <button @click="showPassword = !showPassword" type="button"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 dark:text-gray-400">
                            {{ showPassword ? 'Hide' : 'Show' }}
                        </button>
                    </div>
                </div>
  
                <div>
                    <label class="block text-sm font-medium mb-1 dark:text-gray-300">Additional Notes</label>
                    <textarea v-model="form.additional_notes"
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Special instructions" rows="3"></textarea>
                </div>
            </div>
  
            <div v-if="error" class="mt-4 p-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-300 text-sm rounded-lg">
                {{ error }}
            </div>
  
            <div class="mt-6 flex justify-between">
                <button @click="closeModal"
                    class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white">
                    Cancel
                </button>
                <button @click="submitBooking" :disabled="loading || !formValid"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 flex items-center justify-center gap-2">
                    <span v-if="!loading">Confirm & Pay</span>
                    <svg v-else class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span v-if="loading">Processing...</span>
                </button>
            </div>
        </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed } from "vue";
  import axios from "axios";
  import { useUserStore } from "@/stores/userStore";
  
  const props = defineProps({
    isOpen: Boolean,
    serviceId: {
        type: Number,
        required: true,
    },
    closeModal: {
        type: Function,
        required: true,
    },
  });
  
  const emit = defineEmits(["booking-confirmed"]);
  
  const userStore = useUserStore();
  const form = ref({
    credentials_username: "",
    credentials_password: "",
    additional_notes: "",
  });
  const loading = ref(false);
  const error = ref(null);
  const showPassword = ref(false);
  
  const formValid = computed(() => {
    return form.value.credentials_username && form.value.credentials_password;
  });
  
  const submitBooking = async () => {
  loading.value = true;
  error.value = null;

  try {
    const authToken = localStorage.getItem("authToken");
    if (!authToken || !userStore.userData?.id) {
      throw new Error("Please log in to complete your booking");
    }

    const response = await axios.post(
      "http://127.0.0.1:8000/api/bookings/store",
      {
        client_id: userStore.userData.id,
        service_id: props.serviceId,
        ...form.value,
        status: "pending_payment",
      },
      {
        headers: {
          Authorization: `Bearer ${authToken}`,
          "Content-Type": "application/json",
        },
      }
    );

    console.log('Booking created:', response.data); // Add this for debugging
    
    // Emit the complete booking data
    emit('booking-confirmed', response.data.booking);
    props.closeModal();
    
  } catch (err) {
    console.error("Booking error:", err);
    error.value = err.response?.data?.message || err.message || "Failed to create booking";
  } finally {
    loading.value = false;
  }
};

  </script>
