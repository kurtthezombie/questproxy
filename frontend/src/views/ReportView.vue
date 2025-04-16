<script setup>

import NavBar from '@/components/NavBar.vue';
import { submitReport } from '@/services/report.service';
import toast from '@/utils/toast';
import { ref } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const usernameToReport = ref(route.params.username);
const reason = ref('');
const selectedReasons = ref([]);
const otherReasonText = ref('');
const loading = ref(false);

const handleSubmitReport = async () => {
  if (selectedReasons.value.length === 0) {
    toast.error('Please select at least one reason for reporting.');
    return;
  }

  // Validate that other reason has text if "Other" is selected
  if (selectedReasons.value.includes('Other') && !otherReasonText.value.trim()) {
    toast.error('Please provide details for the "Other" reason.');
    return;
  }

  const reasons = selectedReasons.value.map(reason =>
    reason === 'Other' ? otherReasonText.value : reason
  );

  try {
    loading.value = true;

    const reportData = {
      reason: reasons.join(', '), 
      reported_user: usernameToReport.value,
    };

    const result = await submitReport(reportData);
    selectedReasons.value = [];
    otherReasonText.value = ''; 
    toast.success('Report submitted successfully!');
  } catch (error) {
    loading.value = false;

    console.error('Error submitting report:', error);
    toast.error('Failed to submit report. Please try again later.');
  } finally {
    loading.value = false;
  }
}

</script>

<template>
  <NavBar />
  <div class="min-h-screen bg-gray-800 flex flex-col items-center justify-center p-6">
    <div class="w-full max-w-lg bg-white rounded-lg shadow-md p-8 my-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">Report User</h1>

      <div class="mb-8">
        <p class="text-sm font-medium text-gray-700 mb-2">Reporting user:</p>
        <p class="text-lg font-semibold text-gray-900 p-3 bg-gray-100 rounded-md">{{ usernameToReport }}</p>
      </div>

      <form @submit.prevent="handleSubmitReport" class="space-y-6">
        <div class="space-y-3">
          <label for="reason" class="block text-base font-medium text-gray-700">Reason for report</label>
          <p class="text-sm text-gray-500">Please select the reason why you're reporting this user.</p>

          <!-- Report reason options with v-model to track selection -->
          <div class="flex items-center">
            <input type="checkbox" id="harassment" name="reportReason" value="Harassment/Abuse" v-model="selectedReasons"
              class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300" />
            <label for="harassment" class="ml-3 block text-sm font-medium text-gray-700">
              Harassment/Abuse
            </label>
          </div>

          <div class="flex items-center">
            <input type="checkbox" id="spam" name="reportReason" value="Spam" v-model="selectedReasons"
              class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300" />
            <label for="spam" class="ml-3 block text-sm font-medium text-gray-700">
              Spam
            </label>
          </div>

          <div class="flex items-center">
            <input type="checkbox" id="offensive" name="reportReason" value="Offensive Content" v-model="selectedReasons"
              class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300" />
            <label for="offensive" class="ml-3 block text-sm font-medium text-gray-700">
              Offensive Content
            </label>
          </div>

          <div class="flex items-center">
            <input type="checkbox" id="fraudulent" name="reportReason" value="Fraudulent Behavior" v-model="selectedReasons"
              class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300" />
            <label for="fraudulent" class="ml-3 block text-sm font-medium text-gray-700">
              Fraudulent Behavior
            </label>
          </div>

          <div class="flex items-center">
            <input type="checkbox" id="username" name="reportReason" value="Inappropriate Username"
              v-model="selectedReasons" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300" />
            <label for="username" class="ml-3 block text-sm font-medium text-gray-700">
              Inappropriate Username
            </label>
          </div>

          <div class="flex items-center">
            <input type="checkbox" id="impersonation" name="reportReason" value="Impersonation" v-model="selectedReasons"
              class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300" />
            <label for="impersonation" class="ml-3 block text-sm font-medium text-gray-700">
              Impersonation
            </label>
          </div>

          <div class="flex items-center">
            <input type="checkbox" id="inaccurate" name="reportReason" value="Inaccurate Information"
              v-model="selectedReasons" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300" />
            <label for="inaccurate" class="ml-3 block text-sm font-medium text-gray-700">
              Inaccurate Information
            </label>
          </div>

          <div class="flex items-center">
            <input type="checkbox" id="other" name="reportReason" value="Other" v-model="selectedReasons"
              class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300" />
            <label for="other" class="ml-3 block text-sm font-medium text-gray-700">
              Other
            </label>
          </div>

          <!-- Textbox appears only when "Other" is selected -->
          <div v-if="selectedReasons.includes('Other')" class="mt-4">
            <label for="otherReason" class="block text-sm font-medium text-gray-700">Please provide details:</label>
            <textarea id="otherReason" v-model="otherReasonText"
              class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
              rows="6" placeholder="Please explain why you are reporting this user"></textarea>
          </div>
        </div>

        <div class="pt-4">
          <p class="text-sm text-gray-500 mb-6">
            Your report will be reviewed by our moderation team. We take all reports seriously and will take appropriate
            action according to our community guidelines.
          </p>

          <button type="submit"
            :class="['w-full', 'bg-red-600', 'hover:bg-red-700', 'text-white', 'font-medium', 'py-3', 'px-4', 'rounded-md', 'transition', 'duration-200', 'text-base']"
            :disabled="loading">
            <span v-if="loading" class="loading loading-spinner"></span>
            <span v-else>Submit Report</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>