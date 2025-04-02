<script setup>

import NavBar from '@/components/NavBar.vue';
import { ref } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const usernameToReport = ref(route.params.username);
const reason = ref('');

console.log(usernameToReport.value);

const submitReport = async () => {
  const reportData = {
    reason: reason.value,
    reported_user: usernameToReport.value,
  };

  console.log("Report Data: ", reportData);
}

</script>

<template>
  <NavBar />
  <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-6">
      <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">Report User</h1>

      <div class="mb-4">
        <p class="text-sm font-medium text-gray-700 mb-1">Reporting user:</p>
        <p class="text-lg font-semibold text-gray-900 p-2 bg-gray-100 rounded-md">{{ usernameToReport }}
        </p>
      </div>

      <form @submit.prevent="submitReport" class="space-y-4">
        <div>
          <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
          <textarea id="reason" v-model="reason"
            class="w-full textarea"
            rows="4" placeholder="Please explain why you are reporting this user" required></textarea>
        </div>

        <button type="submit"
          class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
          Submit Report
        </button>
      </form>
    </div>
  </div>
</template>