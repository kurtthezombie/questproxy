<template>
  <div class="max-w-4xl mx-auto p-4">
    <div class="grid grid-cols-12 gap-4">
      <!-- Left Section - Profile Info -->
      <div class="col-span-4 space-y-4">
        <div class="bg-white rounded-lg shadow p-4">
          <!-- Avatar and Username -->
          <div class="flex flex-col items-center space-y-2">
            <div class="w-24 h-24 rounded-full bg-gray-200 overflow-hidden">
              <img 
                :src="profileData.avatar || '/default-avatar.png'" 
                :alt="profileData.username" 
                class="w-full h-full object-cover"
              />
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
              <h3 class="font-medium">Description</h3>
              <button 
                v-if="isOwner" 
                @click="isEditing ? updateDescription() : isEditing = true"
                class="text-blue-500"
              >
                {{ isEditing ? 'Save' : 'Edit' }}
              </button>
            </div>
            <p v-if="!isEditing || !isOwner" class="text-gray-600 text-sm">
              {{ profileData.description || 'No description available' }}
            </p>
            <textarea 
              v-if="isEditing && isOwner"
              v-model="editableDescription"
              rows="3"
              class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            ></textarea>
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

    <!-- Report Modal -->
    <div v-if="showReportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Report User</h3>
          <button 
            @click="closeReportModal"
            class="text-gray-500 hover:text-gray-700"
          >
            ✕
          </button>
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Please describe your reason for reporting:
          </label>
          <textarea
            v-model="reportMessage"
            rows="4"
            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Enter your report message here..."
          ></textarea>
          <p v-if="reportError" class="mt-1 text-sm text-red-600">
            {{ reportError }}
          </p>
        </div>

        <div class="flex justify-end space-x-2">
          <button 
            @click="closeReportModal"
            class="px-4 py-2 text-sm border rounded hover:bg-gray-50"
          >
            Cancel
          </button>
          <button 
            @click="submitReport"
            class="px-4 py-2 text-sm bg-red-600 text-white rounded hover:bg-red-700"
            :disabled="!reportMessage.trim()"
          >
            Submit Report
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const props = defineProps({
  username: String,
  avatar: String,
  description: String,
  service: Object
});

const route = useRoute();
const loggedInUsername = ref('');
const currentUserId = ref(null);
const profileData = ref({
  username: '',
  avatar: '',
  description: '',
  service: null
});
const isOwner = ref(false);
const showReportModal = ref(false);
const reportMessage = ref('');
const reportError = ref('');
const isEditing = ref(false);
const editableDescription = ref('');

const fetchLoggedInUser = async () => {
  try {
    const token = localStorage.getItem('token');
    const response = await axios.get('http://127.0.0.1:8000/api/user', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    loggedInUsername.value = response.data.username;
    currentUserId.value = response.data.id;
    isOwner.value = loggedInUsername.value === route.params.username;
  } catch (error) {
    console.error('Error fetching logged in user:', error);
  }
};

const fetchProfileData = async () => {
  try {
    const token = localStorage.getItem('token');
    const username = route.params.username;
    const response = await axios.get(`http://127.0.0.1:8000/api/users/${username}`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    profileData.value = response.data;
    editableDescription.value = response.data.description || '';
  } catch (error) {
    console.error('Error fetching profile data:', error);
  }
};

const updateDescription = async () => {
  try {
    const portfolioData = {
      p_content: editableDescription.value,
      pilot_id: currentUserId.value
    };

    const response = await axios.post('http://127.0.0.1:8000/api/portfolios/create', portfolioData);

    if (response.data.success) {
      profileData.value.description = editableDescription.value;
      isEditing.value = false;
    } else {
      console.error('Portfolio update failed:', response.data.message);
      alert('Failed to update portfolio');
    }
  } catch (error) {
    console.error('Error updating portfolio:', error);
    alert('Error updating portfolio');
  }
};

const submitReport = async () => {
  if (!reportMessage.value.trim()) {
    reportError.value = 'Please enter a reason for reporting';
    return;
  }

  try {
    const reportData = {
      reason: reportMessage.value,
      reported_user_id: profileData.value.id 
    };

    const response = await axios.post('http://127.0.0.1:8000/api/reports/create', reportData);
    
    if (response.data) {
      showReportModal.value = false;
      reportMessage.value = '';
      reportError.value = '';
      alert('Report submitted successfully');
    }
  } catch (error) {
    console.error('Error submitting report:', error);
    reportError.value = 'Failed to submit report. Please try again.';
  }
};

const closeReportModal = () => {
  showReportModal.value = false;
  reportMessage.value = '';
  reportError.value = '';
};

onMounted(() => {
  fetchLoggedInUser();
  fetchProfileData();
});

watch(
  () => route.params.username,
  () => {
    fetchProfileData();
    fetchLoggedInUser();
  }
);
</script>
