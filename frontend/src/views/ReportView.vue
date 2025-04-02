<script setup>

import NavBar from '@/components/NavBar.vue';
import { submitReport } from '@/services/report.service';
import toast from '@/utils/toast';
import { ref } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const usernameToReport = ref(route.params.username);
const reason = ref('');

console.log(usernameToReport.value);

const handleSubmitReport = async () => {
  try {
    const reportData = {
      reason: reason.value,
      reported_user: usernameToReport.value,
    };

    console.log("Report Data: ", reportData);

    const result = await submitReport(reportData);
    console.log("Report Result: ", result);

    toast.success('Report submitted successfully!');
  } catch (error) {
    console.error('Error submitting report:', error);
    toast.error('Failed to submit report. Please try again later.');
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
          <p class="text-sm text-gray-500">Please provide detailed information about why you're reporting this user.</p>
          <textarea id="reason" v-model="reason"
            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
            rows="8" placeholder="Please explain why you are reporting this user" required></textarea>
        </div>

        <div class="pt-4">
          <p class="text-sm text-gray-500 mb-6">
            Your report will be reviewed by our moderation team. We take all reports seriously and will take appropriate
            action according to our community guidelines.
          </p>

          <button type="submit"
            class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-md transition duration-200 text-base">
            Submit Report
          </button>
        </div>
      </form>
    </div>
  </div>
</template>