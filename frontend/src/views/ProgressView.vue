<script setup>

import ProgressCard from '@/components/progress/ProgressCard.vue';
import { fetchBookingData, fetchProgressLogs } from '@/services/progress.service';
import toast from '@/utils/toast';
import dayjs from 'dayjs';
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useUserStore } from '@/stores/userStore';
import AddProgressForm from '@/components/progress/AddProgressForm.vue';
import NavBar from '@/components/NavBar.vue';

const route = useRoute();
const bookingId = computed(() => route.params.bookingId);

const userStore = useUserStore();
const loggedUserId = userStore?.userData?.id;

const booking = ref(null);
const progress = ref([]);
const isLoading = ref(false);

const formRef = ref();

const openForm = () => {
  formRef.value?.open();
};

const handleFetchBookingData = async () => {
  try {
    const bookingData = await fetchBookingData(bookingId.value);
    booking.value = bookingData;
  } catch (error) {
    toast.error(error);
  }
}

const handleFetchProgressLogs = async () => {
  try {
    const progressData = await fetchProgressLogs(bookingId.value);
    console.log("Progress: ",progressData);
    progress.value = progressData;
  } catch (error) {
    toast.error(error);
  }
}

const loadData = async () => {
  isLoading.value = true;
  try {
    await handleFetchBookingData();
    await handleFetchProgressLogs();
  } finally {
    isLoading.value = false;
  }
};

const isPilot = computed(() => {
  if (!booking.value?.service?.pilot?.user?.id) return false;
  return loggedUserId === booking.value?.service?.pilot?.user?.id;
});

// Watch for changes in the bookingId
watch(bookingId, () => {
  loadData();
});

onMounted(() => {
  loadData();
});
</script>
<template>
  <NavBar />
  <div class="bg-gray-900 min-h-screen text-white p-6">
    <div v-if="isLoading" class="flex justify-center items-center min-h-[60vh]">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
    </div>
    <div v-else class="flex flex-col gap-y-6 max-w-4xl mx-auto">
      <!-- Header Section -->
      <div class="bg-gray-800 shadow-md rounded-2xl p-6 relative">
        <!-- Status Pill -->
        <span class="absolute top-4 right-4 px-4 py-2 text-xs font-semibold text-gray-800 rounded-full" :class="booking?.status === 'in_progress' ? 'bg-orange-500' : 'bg-green-500'">
          {{ booking?.status === 'in_progress' ? 'In Progress' : 'Completed' }}
        </span>
        <h2 class="text-2xl font-semibold">{{ booking?.service?.category?.title }}</h2>
        <p class="text-gray-300 mt-2">{{ booking?.service?.description }}</p>
        <p class="text-sm text-gray-500 mt-1">Started on: <span class="font-medium">{{ dayjs(booking?.instruction?.start_date).format('MMMM D YYYY') }}</span></p>
        <p class="text-sm text-gray-500 mt-1">Client: <span class="font-medium">{{ booking?.client?.username }}</span></p>
        <!-- Client Username -->
      </div>

      <div class="flex flex-col space-y-4 justify-center items-center">
        <div class="radial-progress text-green-400" :style="`--value:${booking?.progress}; --size:8rem;`"
          :aria-valuenow="booking?.progress" role="progressbar">
          {{ booking?.progress }}%
        </div>
        <div class="text-green-500">
          Progress
        </div>
      </div>
      <div>
        <!-- Show Update button if logged-in user is the pilot -->
        <div class="mt-4 flex justify-center" v-if="isPilot">
          <AddProgressForm 
            ref="formRef" 
            :booking-id="booking?.id"
             @progress-added="handleFetchProgressLogs"
            />
          <button @click="openForm" class="btn bg-green-500 hover:bg-lime-400 px-4 py-2 shadow-none border-none rounded-lg transition">
            Add Progress Item
          </button>
        </div>
      </div>
      <div v-if="!progress.length" class="text-center bg-gray-800 rounded-lg p-5">
        No progress update added yet...
      </div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6 max-h-[768px] overflow-y-auto pr-2">
        <ProgressCard 
          v-for="log in progress"
          :key="log.id"
          :progress-log="log"
          :is-pilot="isPilot"
          @progress-deleted="handleFetchProgressLogs"
        />
      </div>
    </div>
    
  </div>
</template>