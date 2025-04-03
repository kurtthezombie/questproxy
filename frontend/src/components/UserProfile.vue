<template>
  <div class="max-w-4xl mx-auto p-4">
    <div class="grid grid-cols-12 gap-4">
      <!-- Left Section - Profile Info -->
      <div class="col-span-4 space-y-4">
        <div class="bg-white rounded-lg shadow p-4">
          <!-- Avatar and Username -->
          <div class="flex flex-col items-center space-y-2">
            <!-- Avatar -->
            <div 
              class="w-24 h-24 rounded-full bg-green-500 flex items-center justify-center text-white text-3xl font-bold"
            >
              {{ userInitial }}
            </div>
            <h2 class="text-xl font-semibold">{{ profileData.username }}</h2>

            <!-- Report Button - Only visible for non-owners -->
            <button 
              v-if="!isOwner"
              @click="showReportModal = true"
              class="w-full px-4 py-2 text-sm border rounded hover:bg-gray-50"
            >
              Report
            </button>
          </div>

          <!-- Description/Portfolio Section -->
          <div class="mt-4">
            <div class="flex justify-between items-center mb-2">
              <h3 class="font-medium"></h3>
              <div>
      
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Section - Service Display -->
      <div class="col-span-8">
        <div class="bg-white rounded-lg shadow h-full p-4">
          <div class="h-full flex items-center justify-center text-gray-500">
            {{ profileData.service || 'No services available' }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import loginService from '@/services/login-service';
import { useLoader } from '@/services/loader-service';
import { useRoute } from 'vue-router';

const { loadShow, loadHide } = useLoader();

const route = useRoute();
const profileData = ref({
  username: '',
  f_name: '',
  description: '',
  service: null,
});

const isOwner = ref(false);
const showReportModal = ref(false);
const isLoading = ref(false);
const currentUserId = ref(null);

const fetchLoggedInUser = async () => {
  const loader = loadShow();
  try {
    const response = await loginService.fetchUserData();
    currentUserId.value = response.id;
  } catch (error) {
    console.error('Error fetching logged-in user:', error);
  } finally {
    loadHide(loader);
  }
};



// Generate user initial
const userInitial = computed(() => {
  return profileData.value.f_name?.charAt(0)?.toUpperCase() || '?';
});


onMounted(() => {

  fetchLoggedInUser();
});
</script>
