<template>
  <NavBar />
  <div class="min-h-screen flex items-center justify-center bg-gray-900 text-white p-6">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg max-w-md w-full">
      <div class="text-center">
        <!-- Cancel Icon -->
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-900 mb-4">
          <svg class="h-10 w-10 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </div>
        
        <!-- Cancel Message -->
        <h2 class="text-2xl font-bold mb-2">
          Payment Cancelled
        </h2>
        <p class="text-gray-400 mb-6">
          Your payment process was cancelled. Your booking has not been confirmed.
        </p>
        
        <!-- Action Buttons -->
        <div class="flex flex-col space-y-3">
          <button 
            @click="tryAgain" 
            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors"
          >
            Try Again
          </button>
          <button 
            @click="goToHome" 
            class="w-full px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors"
          >
            Return to Home
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRoute, useRouter } from "vue-router"
import NavBar from "@/components/NavBar.vue"
import { ref, onMounted } from "vue"

const route = useRoute()
const router = useRouter()

const serviceId = ref(null)
const bookingId = ref(null)

onMounted(() => {
  serviceId.value = route.query.service_id || route.params.service_id || null
  bookingId.value = route.query.booking_id || route.params.booking_id || null
})

const tryAgain = () => {
  if (serviceId.value) {
    router.push(`/services/${serviceId.value}`)
  } else if (bookingId.value) {
    router.push(`/bookings/${bookingId.value}`)
  } else {
    router.push("/services")
  }
}

const goToHome = () => {
  router.push("/")
}

</script>
