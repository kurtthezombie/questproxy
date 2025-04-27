<script setup>
import NavBar from '@/components/NavBar.vue';
import { submitReport } from '@/services/report.service';
import toast from '@/utils/toast';
import { ref } from 'vue';
// Import useRouter to handle navigation
import { useRoute, useRouter } from 'vue-router'; // Import useRouter

const route = useRoute();
const router = useRouter(); // Get the router instance
const usernameToReport = ref(route.params.username);
const reason = ref(''); // This ref seems unused, consider removing if not needed elsewhere
const selectedReasons = ref([]);
const otherReasonText = ref('');
const loading = ref(false);

const handleSubmitReport = async () => {
  // --- Validation ---
  if (selectedReasons.value.length === 0) {
    toast.error('Please select at least one reason for reporting.');
    return;
  }

  // Validate that other reason has text if "Other" is selected
  if (selectedReasons.value.includes('Other') && !otherReasonText.value.trim()) {
    toast.error('Please provide details for the "Other" reason.');
    return;
  }

  // --- Prepare Data ---
  // Combine selected reasons, using otherReasonText if 'Other' is chosen
  const reasons = selectedReasons.value.map(reasonValue =>
    reasonValue === 'Other' ? otherReasonText.value.trim() : reasonValue
  );

  const reportData = {
    reason: reasons.join(', '), // Join reasons into a comma-separated string
    reported_user: usernameToReport.value,
  };

  // --- Submit Report ---
  try {
    loading.value = true;

    // Call the API service to submit the report
    const result = await submitReport(reportData); // Assuming submitReport handles the API call

    // --- Success Handling ---
    // Reset form fields
    selectedReasons.value = [];
    otherReasonText.value = '';
    toast.success('Report submitted successfully!');

    // Redirect to the homepage after successful submission
    router.push({ name: 'homepage' }); // Use the route name defined in your router config (index.txt)

  } catch (error) {
    // --- Error Handling ---
    // Log the error for debugging
    console.error('Error submitting report:', error);

    // Show user-friendly error message
    // Check for specific error details if available from the backend response
    const errorMessage = error?.response?.data?.message || 'Failed to submit report. Please try again later.';
    toast.error(errorMessage);

  } finally {
    // Ensure loading state is turned off regardless of success or failure
    loading.value = false;
  }
}

</script>

<template>
  <NavBar />
  <div class="min-h-screen bg-gray-900 flex flex-col items-center justify-center p-6 -mt-5">
    <div class="w-full max-w-lg border border-gray-700 rounded-lg shadow-lg overflow-hidden bg-gray-800 bg-opacity-60 backdrop-blur-sm">
      <div class="bg-gray-800 bg-opacity-50 p-4 pt-6 border-b border-gray-700">
        <h1 class="text-3xl font-bold text-white ml-4">Report User</h1>
      </div>

      <div class="p-8">
        <div class="mb-8">
          <p class="text-sm font-medium text-gray-400 mb-2">Reporting user:</p>
          <p class="text-lg font-semibold p-3 bg-blue-900 bg-opacity-30 border border-gray-700 text-white rounded-md shadow-sm">{{ usernameToReport || 'Loading...' }}</p>
        </div>

        <form @submit.prevent="handleSubmitReport" class="space-y-6">
          <div class="space-y-3">
            <label class="block text-base font-medium text-gray-200">Reason for report</label>
            <p class="text-sm text-gray-400">Please select all applicable reasons.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
              <div v-for="reasonOpt in ['Harassment/Abuse', 'Spam', 'Offensive Content', 'Fraudulent Behavior', 'Inappropriate Username', 'Impersonation', 'Inaccurate Information', 'Other']" :key="reasonOpt" class="flex items-center">
                <input
                  type="checkbox"
                  :id="reasonOpt.toLowerCase().replace(/[^a-z0-9]/g, '')"
                  :name="reasonOpt.toLowerCase().replace(/[^a-z0-9]/g, '')"
                  :value="reasonOpt"
                  v-model="selectedReasons"
                  class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-500 rounded bg-gray-700 cursor-pointer" />
                <label :for="reasonOpt.toLowerCase().replace(/[^a-z0-9]/g, '')" class="text-white ml-3 block text-sm font-medium cursor-pointer">
                  {{ reasonOpt }}
                </label>
              </div>
            </div>

            <div v-if="selectedReasons.includes('Other')" class="mt-4 transition-all duration-300 ease-in-out">
              <label for="otherReason" class="block text-sm text-gray-300 mb-1">Please provide details:</label>
              <textarea
                id="otherReason"
                v-model="otherReasonText"
                class="w-full px-4 py-3 border border-gray-600 bg-[#1e293b] text-white rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 shadow-inner"
                rows="4"
                placeholder="Explain the reason for reporting..."
                required>
              </textarea>
            </div>
          </div>

          <div class="pt-4">
            <p class="text-xs text-gray-400 mb-6">
              Your report will be reviewed by our moderation team. We take all reports seriously and will take appropriate action according to our community guidelines. Thank you for helping keep our community safe.
            </p>

            <button
              type="submit"
              :class="[
                'w-full flex justify-center items-center',
                'bg-red-600 hover:bg-red-700',
                'text-white font-medium',
                'py-3 px-4 rounded-md',
                'transition duration-200 ease-in-out',
                'text-base shadow-md',
                'disabled:opacity-50 disabled:cursor-not-allowed'
              ]"
              :disabled="loading || selectedReasons.length === 0 || (selectedReasons.includes('Other') && !otherReasonText.trim())">
              <span v-if="loading" class="loading loading-spinner loading-sm mr-2"></span>
              <span>{{ loading ? 'Submitting...' : 'Submit Report' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Add custom styles if needed, Tailwind handles most */
input[type="checkbox"] {
  /* Slightly larger checkbox for easier clicking */
  transform: scale(1.1);
}
</style>
