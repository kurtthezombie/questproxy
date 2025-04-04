<script setup>
import {  reactive, ref, onMounted } from 'vue';
import NavBar from '@/components/NavBar.vue';
import { fetchMyReports } from '@/services/report.service';
import toast from '@/utils/toast';
import ReportDetailsModal from '@/components/reports/ReportDetailsModal.vue';

const reports = ref([]);
const pagination = reactive({
  currentPage: 1,
  lastPage: 1,
});

const loading = ref(false);

const isModalOpen = ref(false);
const selectedReport = ref(null);

const fetchReports = async (page = 1) => {
  try {
    loading.value = true;

    const data = await fetchMyReports(page);
    console.log(data);
    reports.value = data.data;
    pagination.currentPage = data.current_page;
    pagination.lastPage = data.last_page;
  } catch (error) {
    toast.error('Failed to fetch reports.');
  } finally {
    loading.value = false;
  }
}

// Call the fetch function when the component is mounted
onMounted(() => {
  fetchReports();
});

// Format date function
const formatDate = (date) => {
  return new Date(date).toLocaleDateString(); // Format it to a desired format
};

const openModal = (report) => {
  selectedReport.value = report;
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};
</script>

<template>
  <div>
    <NavBar />
    <div class="bg-gray-800 min-h-screen">
      <div class="container mx-auto py-8 px-4 max-w-2xl">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
          <!-- Header -->
          <div class="p-6 border-b">
            <h1 class="text-2xl font-semibold">My Reports</h1>
            <p class="text-gray-500 mt-1">
              View and manage all the reports you've submitted
            </p>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="text-center p-6">
            <div class="space-y-4">
              <div v-for="n in 3" :key="n" class="skeleton h-40 bg-gray-300 animate-pulse mx-auto"></div>
            </div>
            <span class="loading loading-spinner loading-xl mt-5"></span>
          </div>

          <div v-else-if="reports.length === 0"
            class="text-center text-gray-500 py-20 flex items-center justify-center h-full">No reports made yet.</div>
          <!-- Cards in Single Column -->
          <div v-else class="p-6">
            <div class="space-y-4">
              <div v-for="report in reports" :key="report.id"
                class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <div class="p-4 border-b bg-gray-50">
                  <div class="flex justify-between items-start">
                    <h3 class="font-medium">{{ report.reportedUser }}</h3>
                    <span :class="{
                      'px-2 py-1 text-xs font-semibold rounded-full ml-2': true,
                      'bg-green-100 text-green-800': report.status === 'resolved',
                      'bg-red-100 text-red-800': report.status === 'rejected',
                      'bg-blue-100 text-blue-800': report.status === 'pending'
                    }">
                      {{ report.status }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-500 mt-1">
                    {{ formatDate(report.dateReported) }}
                  </p>
                </div>

                <div class="p-4">
                  <p class="text-gray-700">{{ report.reason }}</p>
                  <div class="mt-4 flex justify-end">
                    <button
                      class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200"
                      @click="openModal(report)">
                      View Details
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- pagination -->
          <div class="flex justify-end mb-3 px-6" v-if="pagination.lastPage > 1">
            <div class="join">
              <button class="join-item btn" :disabled="pagination.currentPage === 1" @click="fetchReports(pagination.currentPage - 1)">
                «
              </button>
              <button class="join-item btn">
                {{ pagination.currentPage }}
              </button>
              <button class="join-item btn" :disabled="pagination.currentPage === pagination.lastPage" @click="fetchReports(pagination.currentPage + 1)">
                »
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <ReportDetailsModal :isModalOpen="isModalOpen" :report="selectedReport" @close="closeModal" />
  </div>
</template>