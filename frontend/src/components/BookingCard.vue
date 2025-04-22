<template>
  <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md mx-4">
      <!-- Form Header -->
      <h2 class="text-xl font-bold mb-4 text-center dark:text-white">Confirm Your Booking</h2>
      <p class="text-gray-600 dark:text-gray-300 text-center mb-6">
        Your information is secure and encrypted.
      </p>

      <!-- Form Inputs -->
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1 dark:text-gray-300">Game Account Username</label>
          <input
            v-model="form.credentials_username"
            type="text"
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            placeholder="Enter game username"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-1 dark:text-gray-300">Game Account Password</label>
          <div class="relative">
            <input
              v-model="form.credentials_password"
              :type="showPassword ? 'text' : 'password'"
              class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              placeholder="Enter game password"
              required
            />
            <button
              @click="showPassword = !showPassword"
              type="button"
              class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 dark:text-gray-400"
            >
              {{ showPassword ? 'Hide' : 'Show' }}
            </button>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1 dark:text-gray-300">Additional Notes</label>
          <textarea
            v-model="form.additional_notes"
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            placeholder="Special instructions (optional)"
            rows="3"
          ></textarea>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="mt-4 p-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-300 text-sm rounded-lg">
        {{ error }}
      </div>

      <!-- Action Buttons -->
      <div class="mt-6 flex justify-between">
        <button
          @click="closeModal"
          class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white"
        >
          Cancel
        </button>
        <button
          @click="submitBooking"
          :disabled="loading || !formValid"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 flex items-center justify-center gap-2"
        >
          <span v-if="!loading">Confirm & Pay</span>
          <svg v-else class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span v-if="loading">Processing...</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useUserStore } from "@/stores/userStore";

export default {
  props: ["isOpen", "serviceId", "closeModal"],
  data() {
    return {
      form: {
        credentials_username: "",
        credentials_password: "",
        additional_notes: ""
      },
      loading: false,
      error: null,
      showPassword: false
    };
  },
  computed: {
    formValid() {
      return this.form.credentials_username.trim() && this.form.credentials_password.trim();
    }
  },
  methods: {
    async submitBooking() {
      this.loading = true;
      this.error = null;

      try {
        // 1. Get auth token and user data
        const authToken = localStorage.getItem("authToken");
        if (!authToken) throw new Error("No authentication token found");

        const userStore = useUserStore();
        if (!userStore.userData?.id) throw new Error("User data not available");

        // 2. Create booking
        const bookingResponse = await axios.post(
          "http://127.0.0.1:8000/api/bookings/store",
          {
            client_id: userStore.userData.id,
            service_id: this.serviceId,
            credentials_username: this.form.credentials_username,
            credentials_password: this.form.credentials_password,
            additional_notes: this.form.additional_notes || "No notes provided"
          },
          {
            headers: { Authorization: `Bearer ${authToken}` }
          }
        );

        // 3. Get booking ID from response
        let bookingId;
        if (bookingResponse.data.status === 200 || bookingResponse.status === 200) {
          const responseData = bookingResponse.data;
          bookingId = responseData?.data?.booking?.id || responseData?.booking?.id || null;
        }

        if (!bookingId) throw new Error("Failed to get booking ID");

        // 4. Initiate payment with PayMongo
        const successUrl = `${window.location.origin}/payments/success?booking_id=${bookingId}`;
        const cancelUrl = `${window.location.origin}/bookings/${bookingId}?canceled=true`;

        const paymentResponse = await axios.post(
          `http://127.0.0.1:8000/api/payments/${bookingId}`,
          {
            success_url: successUrl,
            cancel_url: cancelUrl
          },
          {
            headers: { Authorization: `Bearer ${authToken}` }
          }
        );

        // 5. Redirect to PayMongo checkout
        if (paymentResponse.data.data?.checkout_url) {
          window.location.href = paymentResponse.data.data.checkout_url;
        } else {
          throw new Error("No checkout URL received");
        }

      } finally {
        this.loading = false;
      }
    }
  }
};
</script>