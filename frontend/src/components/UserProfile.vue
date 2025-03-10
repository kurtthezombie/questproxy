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
                :src="userAvatar || 'src/assets/default-avatar.png'"
                @error="handleAvatarError"
                :alt="profileData.username || 'User avatar'"
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
              <div>
                <button 
                  v-if="isOwner" 
                  @click="isEditing ? saveDescription() : startEditing()"
                  class="text-blue-500 mr-2"
                  :disabled="isLoading"
                >
                  {{ isEditing ? 'Update' : 'Edit' }}
                </button>
                <button 
                  v-if="isEditing && isOwner"
                  @click="cancelEditing"
                  class="text-gray-500"
                >
                  Cancel
                </button>
              </div>
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
import { ref, onMounted } from 'vue';
import axios from 'axios';
import loginService from '@/services/login-service';

const userAvatar = ref('');
const isOwner = ref(false);
const isEditing = ref(false);
const editableDescription = ref('');
const showReportModal = ref(false);
const reportMessage = ref('');
const reportError = ref('');
const isLoading = ref(false);



const profileData = ref({
  username: '',
  avatar: '',
  description: '',
  service: null
});

const generateUserAvatar = (userId) => {
  const avatarNumber = (userId % 6) + 1;
  return `src/assets/avatarimg/avatar${avatarNumber}.png`;
};

const fetchLoggedInUser = async () => {
  try {
    const token = localStorage.getItem('authToken');
    if (!token) {
      throw new Error('Authentication token is missing');
    }
    const response = await loginService.fetchUserData();
    currentUserId.value = response.id;
    loggedInUsername.value = response.username;
  } catch (error) {
    console.error('Error fetching logged-in user:', error);
    // Handle redirection to login if needed
  }
};


const fetchProfileData = async () => {
  try {
    const userId = route.params.userId; 
    const data = await loginService.fetchUserDataById(userId);
    profileData.value = data;
    userAvatar.value = data.avatar || generateUserAvatar(userId);
    isOwner.value = data.id === currentUserId.value;
  } catch (error) {
    console.error('Error fetching profile data:', error);
  }
};

const startEditing = () => {
  isEditing.value = true;
  editableDescription.value = profileData.value.description;
};

const saveDescription = async () => {
  isLoading.value = true;
  try {
    const response = await axios.put(`http://127.0.0.1:8000/api/users/${profileData.value.id}/description`, {
      description: editableDescription.value,
    }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('authToken')}`,
      },
    });
    profileData.value.description = response.data.description;
    isEditing.value = false;
  } catch (error) {
    console.error('Error saving description:', error);
  } finally {
    isLoading.value = false;
  }
};

const cancelEditing = () => {
  isEditing.value = false;
};

const closeReportModal = () => {
  showReportModal.value = false;
  reportMessage.value = '';
  reportError.value = '';
};

const submitReport = async () => {
  try {
    const reportedUserId = 42; 
    const reason = "Inappropriate behavior";

    const result = await loginService.reportUser({ reportedUserId, reason });
    alert(result.message); 
  } catch (error) {
    console.error("Error submitting report:", error);
  }
};

onMounted(() => {
  fetchProfileData();
  fetchLoggedInUser();
});

</script>

<style>
/* Add any additional styles here */
</style>
