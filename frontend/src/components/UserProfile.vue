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
                :src="avatar" 
                :alt="username" 
                class="w-full h-full object-cover"
              />
            </div>
            <h2 class="text-xl font-semibold">{{ username }}</h2>
            
            <!-- Report Button - Always visible for non-owners -->
            <button 
              v-if="!isOwner"
              @click="showReportModal = true"
              class="w-full px-4 py-2 text-sm border rounded hover:bg-gray-50"
            >
              Report
            </button>
          </div>

          <!-- Description Section - Editable -->
          <div class="mt-4">
            <div class="flex justify-between items-center mb-2">
              <h3 class="font-medium">Description</h3>
              <button 
                @click="isEditing = !isEditing"
                class="text-blue-500"
              >
                {{ isEditing ? 'Save' : 'Edit' }}
              </button>
            </div>
            <p v-if="!isEditing" class="text-gray-600 text-sm">{{ description }}</p>
            <textarea 
              v-if="isEditing"
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
            {{ service }}
          </div>
        </div>
      </div>
    </div>

    <!-- Report Modal with Message Input -->
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
import { ref, watch } from 'vue'; 
import axios from 'axios';

const props = defineProps({
  username: String,
  avatar: String,
  description: String,
  service: String,
  reportedUserId: Number 
});

const showReportModal = ref(false);
const reportMessage = ref('');
const reportError = ref('');

const closeReportModal = () => {
  showReportModal.value = false;
  reportMessage.value = ''; 
  reportError.value = ''; 
};

const submitReport = async () => {
  const reportData = {
    reason: reportMessage.value.trim(),
    reported_user_id: props.reportedUserId 
  };

  try {
    const token = localStorage.getItem('token'); 

    const response = await axios.post('http://127.0.0.1:8000/api/reports/create', reportData, {
      headers: {
        Authorization: `Bearer ${token}`, 
      },
    });
    
    console.log('Report submitted:', response.data);
    closeReportModal();
  } catch (error) {
    console.error('Error submitting report:', error);
    reportError.value = 'Failed to submit report. Please try again later.';
  }
};
</script>
