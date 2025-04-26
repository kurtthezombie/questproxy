<script setup>
import dayjs from 'dayjs';
import { onMounted, reactive, ref, watch } from 'vue';
import { useLoader } from '@/services/loader-service';
import { useServiceStore } from '@/stores/serviceStore';
import api from '@/utils/api';

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
const serviceStore = useServiceStore();
const emit = defineEmits(['close']);
const { loadShow, loadHide } = useLoader();
const confirmationVisible = ref(false);
const credentials = reactive({
  username: '',
  password: '',
})
const showDetails = ref(false);
const revealDetails = () => showDetails.value = true;
const hideDetails = () => showDetails.value = false;

const toggleDetails = () => {
  showDetails.value = !showDetails.value;
};

const closeModal = () => {
 emit('close');
};

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

const fetchCredentials = async (bookingId) => {
  const loader = loadShow();
  try {
    const response = await api.get(`/bookings/instructions/${bookingId}`);

    console.log(response);

    credentials.username = response.instruction.credentials_username || '';
    credentials.password = response.instruction.credentials_password || '';

    console.log("Fetched credentials:", credentials);
  } catch (error) {
    console.error("Error fetching booking credentials:", error);
  } finally {
    loadHide(loader);
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

watch(() => props.isModalOpen, (isOpen) => {
  if (isOpen && props.selectedBooking) {
    fetchCredentials(props.selectedBooking.id);
  }
});

watch(() => props.selectedBooking, (newVal) => {
  console.log('Selected booking changed:', newVal);
});

const capitalizeFirstLetter = (status) => {
  if (!status) return status;
  return status.charAt(0).toUpperCase() + status.slice(1).toLowerCase();
};
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
          <p class="text-base font-medium">{{ selectedBooking.client?.username || 'Unknown' }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-400">Status</p>
          <span :class="{
              'bg-blue-400 text-white': selectedBooking.status === 'completed',
              'bg-yellow-500 text-white': selectedBooking.status === 'pending',
              'bg-red-400 text-white': selectedBooking.status === 'cancelled'
            }" class="inline-block px-2 py-1 text-xs text-gray-700 font-semibold rounded-full">
            {{ capitalizeFirstLetter(selectedBooking.status) }}
          </span>

        </div>
        <div>
          <p class="text-sm text-gray-400">Created At</p>
          <p class="text-base font-medium">{{ dayjs(selectedBooking.created_at).format('MMMM D, YYYY h:mm A') }}</p>
        </div>
      </div>

      <div v-if="selectedBooking?.status === 'pending'" class="mt-5 border border-gray-700 rounded-lg p-4 flex flex-col space-y-2">
        <!-- Button to toggle details -->
        <label for="username">Username</label>
        <input :type="showDetails ? 'text' : 'password'" class="bg-[#1e293b] p-2 rounded-lg" :value=credentials.username
          readonly>
        <label for="password">Password</label>
        <input :type="showDetails ? 'text' : 'password'" class="bg-[#1e293b] p-2 rounded-lg" :value=credentials.password
          readonly>
        <button @mousedown="revealDetails" 
          @mouseup="hideDetails" 
          @mouseleave="hideDetails"
          @touchstart.prevent="revealDetails" 
          @touchend="hideDetails"
          class="bg-gray-800 hover:bg-gray-700 text-white p-2 rounded-lg flex justify-center w-full">
          <svg v-if="!showDetails" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
          </svg>
        </button>
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
        <button v-if="selectedBooking?.status === 'pending'" @click="showConfirmationDialog"
          class="bg-blue-500 hover:bg-green-700 btn text-white border-none shadow-none">
          Mark as Completed
        </button>
        <button class="btn text-white btn-neutral hover:bg-red-700" @click="closeModal">Close</button>
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
</template>