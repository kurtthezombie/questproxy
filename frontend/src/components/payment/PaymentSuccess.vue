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
import { ref, onMounted } from "vue"
import { useRoute, useRouter } from "vue-router"
import NavBar from "@/components/NavBar.vue"
import { verifyPayment } from "@/services/payment-service"
import axios from "axios"

const route = useRoute()
const router = useRouter()

const transactionId = ref(route.query.transaction_id || route.params.transaction_id || "N/A")
const serviceName = ref("Your Service")
const amount = ref("₱0.00")
const loading = ref(true)
const error = ref(null)
const bookingId = ref(route.params.booking_id || null)

// Initialize refs outside onMounted to ensure they are always defined
const paymentVerificationResult = ref(null)
const bookingDetails = ref(null)
const serviceDetails = ref(null)

onMounted(async () => {
  try {
    if (transactionId.value !== "N/A") {
      // Verify payment and get details
      paymentVerificationResult.value = await verifyPayment(transactionId.value)

      // If we have a booking ID, fetch booking and service details
      if (bookingId.value) {
        try {
          const authToken = localStorage.getItem("authToken")
          const bookingResponse = await axios.get(`http://127.0.0.1:8000/api/bookings/${bookingId.value}`, {
            headers: {
              Authorization: `Bearer ${authToken}`,
              "Content-Type": "application/json",
            },
          })

          bookingDetails.value = bookingResponse.data.data.booking

          if (bookingDetails.value && bookingDetails.value.service_id) {
            const serviceResponse = await axios.get(
              `http://127.0.0.1:8000/api/services/${bookingDetails.value.service_id}`,
            )

            serviceDetails.value = serviceResponse.data.data.service
            if (serviceDetails.value) {
              serviceName.value = serviceDetails.value.name || serviceDetails.value.game || "Game Service"
              amount.value = `₱${parseFloat(serviceDetails.value.price).toFixed(2)}`
            }
          }
        } catch (err) {
          console.error("Error fetching booking details:", err)
          error.value = "Error fetching booking details."
        }
      }
    }
  } catch (err) {
    error.value = err.message || "Failed to verify payment"
    console.error("Payment verification error:", err)
  } finally {
    loading.value = false
  }
})

const goToBookings = () => {
  router.push("/bookings")
}

const goToHome = () => {
  router.push("/")
}

</script>