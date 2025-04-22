<script setup>
import {  reactive, ref, onMounted, computed } from 'vue';
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

const searchQuery = ref("");
const currentFilter = ref('all');

const filteredReports = computed(() => {
  // Filter reports by status first
  let filtered = currentFilter.value === 'all' ? reports.value : reports.value.filter(report => report.status === currentFilter.value);

  // Then filter by the search query (matching reported user or reason)
  if (searchQuery.value.trim()) {
    filtered = filtered.filter(report => 
      report.reportedUser.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
      report.reason.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  }
  return filtered;
});
</script>

<template>
  <div>
    <NavBar />
    <div class="min-h-screen bg-gray-900 py-5 px-4">
      <div class="max-w-3xl mx-auto w-full mt-5">
        <div class="flex items-center gap-2 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
              class="w-7 h-7 text-green-400 translate-y-[2px]" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 2C6 4 8 4 12 2C16 4 18 4 20 2C20 6 20 8 20 12C20 15 16 18 12 20C8 18 4 15 4 12C4 8 4 6 4 2Z" />
          </svg>
          <h2 class="text-4xl font-bold text-white">
            My Reports
          </h2>
        </div>
        <h3 class="text-base text-gray-400 mb-5 -mt-2">View all reports you've submitted</h3>
        
        <!-- Search Section -->
        <div class="relative w-full max-w-7xl mb-5">
          <div class="flex items-center space-x-2 w-full">
            <div class="relative w-full max-w-7xl">
              <div class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-300 rounded-full p-2 focus-within:border-2 focus-within:border-green-400">
                <svg class="h-[1.2em] opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                  <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                  </g>
                </svg>
              </div>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search reports.."
                class="bg-[#1e293b] text-gray-300 text-sm border border-gray-700 rounded-md pl-12 pr-2 py-3 h-15 shadow-md w-full focus:outline-none focus:border-4 focus:border-green-600 focus:text-gray-300"
              />
            </div>
          </div>
        </div>

        <div class="flex mb-5 bg-gray-900 p-1 rounded-sm shadow-md">
          <button
            v-for="status in ['all', 'pending', 'resolved', 'rejected']"
            :key="status"
            @click="currentFilter = status"
            :class="[ 
              'flex-1 px-6 py-2 text-sm font-medium transition',  // Make buttons equally spaced
              currentFilter === status
                ? 'bg-green-500 text-white' 
                : 'bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700',
              'first:rounded-l-md last:rounded-r-md'
            ]"
          >
            {{ status.charAt(0).toUpperCase() + status.slice(1) }}
          </button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center p-6">
          <div class="space-y-4">
            <div v-for="n in 3" :key="n" class="skeleton h-40 bg-gray-300 animate-pulse mx-auto"></div>
          </div>
          <span class="loading loading-spinner loading-xl mt-5"></span>
        </div>

        <div v-else-if="reports.length === 0"
          class="text-center text-gray-500 py-20 flex items-center justify-center h-full">No reports made yet.
        </div>

        <!-- Cards in Single Column -->
        <div v-else class="space-y-4">
          <div v-for="report in filteredReports" :key="report.id"
            class="border border-gray-700 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-center">
              <!-- Left side: Report Info -->
              <div class="p-4">
                <div class="flex items-start mb-1">
                  <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                  </svg>
                  <h3 class="font-medium text-white mr-2">{{ report.reportedUser }}</h3>
                  <span :class="{
                    'px-2 py-1 text-xs font-semibold rounded-full ml-2 flex items-center justify-center text-center': true,
                    'bg-green-900 text-green-300': report.status === 'resolved',
                    'bg-red-900 text-red-300': report.status === 'rejected',
                    'bg-blue-900 text-blue-300': report.status === 'pending'
                  }">
                    {{ report.status }}
                  </span>
                </div>

                <p class="text-sm text-gray-400 mb-2 ml-1">{{ formatDate(report.dateReported) }}</p>
                <div class="flex flex-wrap gap-2">
                  <div
                    v-for="(reason, index) in report.reason.split(',')"
                    :key="index"
                    class="text-xs font-medium border border-gray-700 rounded-2xl px-3 py-1 bg-gray-900 bg-opacity-20 text-gray-300"
                  >
                    {{ reason.trim() }}
                  </div>
                </div>
              </div>

              <!-- Right side: Button -->
              <div class="pr-4">
                <button
                  class="px-4 py-1 text-sm font-medium text-white border border-gray-600 rounded-md hover:bg-gray-700 transition"
                  @click="openModal(report)">
                  View Details
                </button>
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