<template>
  <NavBar />
  <div class="min-h-screen flex items-center justify-center bg-gray-900 text-white p-6">
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl w-full flex">
      <!-- Service Details -->
      <div class="flex-1 pr-6">
        <h2 class="text-2xl font-bold">
          <span v-if="loading">Loading service details...</span>
          <span v-else>{{ service ? service.game.replace('_', ' ').toUpperCase() : 'Service' }}</span>
        </h2>

        <p class="text-gray-400 mt-2">
          <span v-if="loading" class="animate-pulse">Fetching description...</span>
          <span v-else>{{ service && service.description ? service.description : 'No description available' }}</span>
        </p>

        <div class="mt-4">
          <p><strong>Price:</strong> 
            <span v-if="loading" class="animate-pulse">₱...</span>
            <span v-else>{{ service ? formatPrice(service.price) : '₱0.00' }}</span>
          </p>

          <p><strong>Duration:</strong> 
            <span v-if="loading" class="animate-pulse">Loading...</span>
            <span v-else>
              {{ service ? formatDuration(service.duration) : 'N/A' }}
            </span>
          </p>
        </div>
      </div>

      <!-- Buy Button -->
      <div class="flex flex-col justify-center items-center w-1/3">
        <button 
          class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg text-lg w-full"
          @click="proceedToPayment"
          :disabled="loading"
        >
          {{ loading ? "Loading..." : "Buy Service" }}
        </button>
      </div>
    </div>
  </div>

  <!-- Booking Modal -->
  <BookingCard 
    :isOpen="isModalOpen"
    :serviceId="route.params.serviceId"
    :closeModal="closeModal"
  />
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import NavBar from "@/components/NavBar.vue";
import BookingCard from "@/components/BookingCard.vue"; // Updated Import

const route = useRoute();
const service = ref(null);  
const loading = ref(true);  
const isModalOpen = ref(false); // Controls modal visibility

const formatPrice = (price) => {
  return price ? `₱${Number(price).toLocaleString("en-US", { minimumFractionDigits: 2 })}` : "₱0.00";
};

// Fetch service details
const fetchServiceDetails = async () => {
  try {
    loading.value = true; 
    const authToken = localStorage.getItem("authToken");
    const tokenType = localStorage.getItem("tokenType");

    if (!authToken || !tokenType) {
      console.error("No authentication token found.");
      return;
    }

    console.log("Fetching Service ID:", route.params.serviceId);
    const response = await axios.get(`http://127.0.0.1:8000/api/services/${route.params.serviceId}`, {
      headers: { Authorization: `${tokenType} ${authToken}` }
    });

    if (response.data.service) {
      service.value = response.data.service;
      console.log("Service Data Loaded:", service.value);
    } else {
      console.error("No service data found in response.");
    }

  } catch (error) {
    console.error("Error fetching service details:", error);
  } finally {
    loading.value = false; 
  }
};

// Open the modal when clicking Buy Service
const proceedToPayment = () => {
  console.log("Opening booking modal...");
  isModalOpen.value = true;
};

// Close modal
const closeModal = () => {
  isModalOpen.value = false;
};

// Format duration
const formatDuration = (datetime) => {
  if (!datetime) return "N/A";
  
  const [date, time] = datetime.split(" "); 
  const [hours, minutes] = time.split(":"); 

  return `${date} (${hours}h ${minutes}m)`;
};

onMounted(fetchServiceDetails);
</script>
