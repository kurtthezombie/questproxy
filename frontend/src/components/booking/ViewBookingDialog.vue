<script setup>
import dayjs from 'dayjs';
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { useLoader } from '@/services/loader-service';
import { useServiceStore } from '@/stores/serviceStore';
import axios from 'axios';
import { generatePDF } from '@/services/agreement.service';

const props = defineProps({
  selectedBooking: {
    type: Object,
    default: null
  },
  isModalOpen: {
    type: Boolean,
    default: false
  }
})

const backendUrl = import.meta.env.VITE_BACKEND_URL || 'http://127.0.0.1:8000/';

const serviceStore = useServiceStore();
const emit = defineEmits(['close']);
const { loadShow, loadHide } = useLoader();
const confirmationVisible = ref(false);
const progressModalVisible = ref(false);

const openProgressModal = () => {
  if (props.selectedBooking?.progress !== undefined) {
    progressValue.value = props.selectedBooking.progress; // Set initial progress to progressValue
    originalProgress.value = props.selectedBooking.progress; // Store the original progress
  }
  progressModalVisible.value = true;
};

const closeProgressModal = () => {
  progressModalVisible.value = false;
};

const closeModal = () => {
  emit('close');
};
const progressValue = ref(0);
const originalProgress = ref(0);

const markAsCompleted = async (bookingId) => {
  const loader = loadShow();
  try {
    await serviceStore.markBookingAsCompleted(bookingId);
    await serviceStore.fetchBookingsByPilot();
    closeModal();
  } catch (error) {
    console.error("Error marking booking as completed:", error);
  } finally {
    loadHide(loader);
  }
};

const saveProgress = async () => {
  if (props.selectedBooking?.id) {
    await updateBookingProgress(props.selectedBooking.id, progressValue.value);
    console.log('Saving progress:', progressValue.value);
    props.selectedBooking.progress = progressValue.value;

    closeProgressModal();
  }
}

const updateBookingProgress = async (bookingId, progress) => {
  const loader = loadShow();

  try {
    const token = localStorage.getItem('authToken');
    if (!token) throw new Error('No authentication token found');

    const response = await axios.put(
      `${backendUrl}api/bookings/${bookingId}/progress`,
      { progress: progress },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        }
      }
    );

    // Optionally, fetch updated bookings or handle the UI update accordingly
    await serviceStore.fetchBookingsByPilot();

    // Close the modal after the update
    closeProgressModal();
  } catch (error) {
    // Handle any errors
    console.error("Error updating progress:", error);
  } finally {
    loadHide(loader);
  }
};

const generateContractPdf = () => {
  if (props.selectedBooking && props.selectedBooking.id) {
    console.log("HELLO BOOKING ID: ",props.selectedBooking.service_id);
    const serviceId = props.selectedBooking.service_id;
    const bookingId = props.selectedBooking.id;
    const form = {
      commLink: props.selectedBooking.instruction.communication_link,  // Assuming communication link is in instruction
      additional_notes: props.selectedBooking.instruction.additional_notes,  // Additional notes
      start_date: props.selectedBooking.instruction.start_date,  // Start date from instruction
    };

    generatePDF(serviceId, bookingId, form);
  } else {
    console.error("Selected booking data is not available!");
  }
}

const showConfirmationDialog = () => {
  confirmationVisible.value = true;
};

const confirmMarkAsCompleted = () => {
  if (props.selectedBooking?.id) {  // Ensure selectedBooking exists and has an id
    markAsCompleted(props.selectedBooking.id);  // Call the function to mark booking as completed
    confirmationVisible.value = false;  // Hide the confirmation dialog
  }
};

const cancelMarkAsCompleted = () => {
  confirmationVisible.value = false;  // Just close the confirmation dialog
};

const canMarkAsCompleted = computed(() => {
  console.log('Progress Value:', progressValue.value);
  return progressValue.value === 100;
});

watch(() => props.selectedBooking, (newVal) => {
  if (newVal && newVal.hasOwnProperty('progress')) {
    progressValue.value = newVal.progress;
    originalProgress.value = newVal.progress;
  } else {
    console.log('Progress not available for selected booking');
  }
});
</script>

<template>
  <!-- Modal for booking details -->
  <dialog id="bookingModal" class="modal" :open="isModalOpen">
    <div class="modal-box bg-gray-900 border border-gray-700 rounded-xl shadow-xl p-6 text-white">
      <h3 class="text-2xl font-bold">Booking Details</h3>

      <!-- Booking Info -->
      <div v-if="selectedBooking" class="mt-4 space-y-4">
        <div>
          <p class="text-sm text-gray-400">Service</p>
          <p class="text-base font-medium">{{ selectedBooking.service?.description || 'Unknown Service' }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-400">Client</p>
          <p class="text-base font-medium ">{{ selectedBooking.client?.username || 'Unknown' }}</p>
        </div>
        <div class="space-y-2">
          <p class="text-sm text-gray-400">Game</p>
          <p class="text-base font-medium">{{ selectedBooking.service?.category.title }}</p>
        </div>
        <div class="space-y-2">
          <p class="text-sm text-gray-400">Communication Link</p>
          <p class="text-sm font-medium bg-gray-700 p-2 rounded-lg">{{ selectedBooking.instruction.communication_link ||
            'None' }}</p>
        </div>
        <div class="space-y-2">
          <p class="text-sm text-gray-400">Start Date</p>
          <p class="text-sm font-medium bg-gray-700 p-2 rounded-lg"> {{
            dayjs(selectedBooking.instruction.start_date).format('MMMM D, YYYY') || 'null'}}
          </p>
        </div>
        <div>
          <p class="text-sm text-gray-400">Status</p>
          <span :class="{
            'bg-blue-400 text-white': selectedBooking.status === 'completed',
            'bg-yellow-500 text-white': selectedBooking.status === 'in_progress',
            'bg-red-400 text-white': selectedBooking.status === 'cancelled'
          }" class="inline-block px-2 py-1 text-xs text-gray-700 font-semibold rounded-full">
            {{ selectedBooking.status === 'in_progress' ? 'in progress' : selectedBooking.status }}
          </span>

        </div>
        <div>
          <p class="text-sm text-gray-400">Created At</p>
          <p class="text-base font-medium">{{ dayjs(selectedBooking.created_at).format('MMMM D, YYYY h:mm A') }}</p>
        </div>
      </div>

      <!-- Booking Progress Bar -->
      <div class="mt-6 space-y-2">
        <p class="text-sm text-gray-400">Progress</p>
        <!-- Radial Progress Bar -->
        <div class="relative flex items-center justify-center">
          <div class="radial-progress text-green-400 hover:cursor-pointer"
            :style="{ '--value': selectedBooking?.progress || 0 }" aria-valuenow="70" role="progressbar"
            @click="openProgressModal">
            {{ selectedBooking?.progress }}%
          </div>
        </div>
        <div class="flex justify-center">
          <p class="text-green-400 text-sm">Complete</p>
        </div>
      </div>

      <!-- Booking Instruction/Note Section -->
      <div class="mt-6 bg-gray-700 rounded-lg p-4">
        <p class="text-sm text-gray-300 mb-1 font-semibold">Instructions from user:</p>
        <!-- Ensure selectedBooking and instruction exist before displaying additional notes -->
        <p v-if="props.selectedBooking?.instruction" class="text-sm text-gray-100 italic">
          {{ props.selectedBooking.instruction.additional_notes || 'None' }}
        </p>
        <p v-else class="text-sm text-gray-100 italic">No additional notes available</p>
      </div>

      <!-- Footer (Close Button) -->
      <div class="modal-action mt-6">
        <button class="btn" @click="generateContractPdf">
          Download Agreement
        </button>
        <button v-if="selectedBooking?.status === 'in_progress'" @click="showConfirmationDialog"
          class="bg-blue-500 hover:bg-green-700 btn text-white border-none shadow-none" :disabled="!canMarkAsCompleted">
          Mark as Completed
        </button>
        <button class="btn text-white bg-gray-600 border-none shadow-none hover:bg-red-600"
          @click="closeModal">Close</button>
      </div>
    </div>
  </dialog>
  <!-- End Modal -->

  <!-- Confirmation Dialog (for confirming "Mark as Completed" action) -->
  <dialog id="confirmationDialog" class="modal" :open="confirmationVisible">
    <div class="modal-box bg-gray-800 rounded-xl shadow-xl p-6 text-white">
      <h3 class="text-lg font-bold">Are you sure?</h3>
      <p class="mt-4 text-gray-400">You are about to mark this booking as completed. Do you want to proceed?</p>
      <div class="modal-action mt-6">
        <!-- Yes button to confirm the action -->
        <button @click="confirmMarkAsCompleted"
          class="btn bg-green-800 hover:bg-green-700 text-white border-none shadow-none">Yes</button>
        <!-- No button to cancel the action -->
        <button @click="cancelMarkAsCompleted"
          class="btn bg-red-800 hover:bg-red-700 text-white border-none shadow-none">No</button>
      </div>
    </div>
  </dialog>

  <dialog id="progressModal" class="modal" :open="progressModalVisible">
    <div class="modal-box bg-gray-900 border border-gray-700 rounded-xl shadow-xl p-6 text-white">
      <h3 class="text-2xl font-bold">Progress Update</h3>

      <div class="mt-4 space-y-2">
        <p class="text-sm text-gray-400">Current Progress</p>
        <input type="range" min="0" max="100" :value="progressValue"
          @input="e => progressValue = parseInt(e.target.value)" class="range range-success range-sm w-full"
          id="progressSlider" />
        <div class="flex justify-between mt-2">
          <span class="text-sm" id="progressValue">{{ progressValue }}%</span>
        </div>
      </div>

      <div class="modal-action mt-6">
        <button id="saveButton" class="btn text-white bg-green-800 hover:bg-green-600 border-none shadow-none"
          @click="saveProgress" :disabled="progressValue === originalProgress">
          Save Progress
        </button>
        <button class="btn text-white bg-gray-600 hover:bg-gray-700 border-none shadow-none"
          @click="closeProgressModal">Close</button>
      </div>
    </div>
  </dialog>
</template>