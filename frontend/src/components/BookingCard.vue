<template>
  <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
      <!-- Form Header -->
      <h2 class="text-xl font-bold mb-4 text-center">Confirm Your Booking</h2>
      <p class="text-gray-600 text-center mb-6">
        Your information is secure and encrypted.
      </p>

      <!-- Form Inputs -->
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Game Account Username</label>
          <input
            v-model="form.credentials_username"
            type="text"
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            placeholder="Enter game username"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Game Account Password</label>
          <input
            v-model="form.credentials_password"
            type="text"
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            placeholder="Enter game password"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Additional Notes</label>
          <textarea
            v-model="form.additional_notes"
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            placeholder="Special instructions (optional)"
            rows="3"
          ></textarea>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="mt-4 text-red-500 text-sm">
        {{ error }}
      </div>

      <!-- Action Buttons -->
      <div class="mt-6 flex justify-between">
        <button
          @click="closeModal"
          class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400"
        >
          Cancel
        </button>
        <button
          @click="submitBooking"
          :disabled="loading"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
        >
          <span v-if="!loading">Confirm & Pay</span>
          <span v-else>Processing...</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
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
      error: null
    };
  },
  methods: {
    async submitBooking() {
      this.loading = true;
      this.error = null;

      try {
        const authToken = localStorage.getItem("authToken");
        if (!authToken) throw new Error("No authentication token found");

        const userStore = useUserStore();
        if (!userStore.userData?.id) throw new Error("User data not available");

        // 1. Debug: Log the payload before sending
        console.log("Submitting booking with:", {
          client_id: userStore.userData.id,
          service_id: this.serviceId,
          ...this.form
        });

        // 2. Create Booking
        const bookingResponse = await axios.post(
          "http://127.0.0.1:8000/api/bookings/store",
          {
            client_id: userStore.userData.id,
            service_id: this.serviceId,
            credentials_username: this.form.credentials_username,
            credentials_password: this.form.credentials_password,
            additional_notes: this.form.additional_notes || "No notes provided" // Ensure non-null
          },
          {
            headers: { Authorization: `Bearer ${authToken}` }
          }
        );

        console.log("Booking response:", bookingResponse.data);

        // 3. Get the booking ID from response
        let bookingId;
        if (bookingResponse.data.status === 200) {
          // Handle both response structures
          bookingId = bookingResponse.data.data?.booking?.id || 
                     bookingResponse.data.booking_id;
        }

        if (!bookingId) throw new Error("No booking ID received");

        // 4. Initiate Payment
        console.log("Initiating payment for booking:", bookingId);
        const paymentResponse = await axios.post(
          `http://127.0.0.1:8000/api/payments/${bookingId}`,
          {
            success_url: `${window.location.origin}/payment-success`,
            cancel_url: `${window.location.origin}/payment-cancel`
          },
          {
            headers: { Authorization: `Bearer ${authToken}` }
          }
        );

        console.log("Payment response:", paymentResponse.data);

        // 5. Handle payment response
        if (paymentResponse.data.status === 201) {
          const paymongoUrl = paymentResponse.data.data?.checkout_url;
          if (!paymongoUrl) throw new Error("No PayMongo URL received");

          console.log("Redirecting to:", paymongoUrl);
          window.location.href = paymongoUrl;
        } else {
          throw new Error(paymentResponse.data.message || "Payment failed");
        }

      } catch (error) {
        console.error("Full error details:", {
          error: error,
          response: error.response?.data,
          config: error.config
        });
        
        this.error = error.response?.data?.message || 
                   error.message || 
                   "Payment processing failed";
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>