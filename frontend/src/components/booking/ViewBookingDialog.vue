<script setup>
import dayjs from 'dayjs';
import { ref, watch } from 'vue';
import { useLoader } from '@/services/loader-service';
import { useServiceStore } from '@/stores/serviceStore';

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

watch(() => props.selectedBooking, (newVal) => {
  console.log('Selected booking changed:', newVal);
});
</script>

<template>
  <!-- Modal for booking details -->
  <dialog id="bookingModal" class="modal" :open="isModalOpen">
    <div class="modal-box bg-gray-800 rounded-xl shadow-xl p-6 text-white">
      <h3 class="text-lg font-bold">Booking Details</h3>

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
            'bg-green-400': selectedBooking.status === 'completed',
            'bg-orange-400': selectedBooking.status === 'pending',
            'bg-red-400': selectedBooking.status === 'cancelled'
          }" class="inline-block px-2 py-1 text-xs text-gray-700 font-semibold rounded-full">
            {{ selectedBooking.status }}
          </span>
        </div>
        <div>
          <p class="text-sm text-gray-400">Created At</p>
          <p class="text-base font-medium">{{ dayjs(selectedBooking.created_at).format('MMMM D, YYYY h:mm A') }}</p>
        </div>
      </div>

      <!-- Booking Instruction/Note Section -->
      <div class="mt-6 bg-gray-700 rounded-lg p-4">
        <p class="text-sm text-gray-300 mb-1 font-semibold">Note from user:</p>
        <!-- Ensure selectedBooking and instruction exist before displaying additional notes -->
        <p v-if="props.selectedBooking?.instruction" class="text-sm text-gray-100 italic">
          {{ props.selectedBooking.instruction.additional_notes || 'None' }}
        </p>
        <p v-else class="text-sm text-gray-100 italic">No additional notes available</p>
      </div>

      <!-- Footer (Close Button) -->
      <div class="modal-action mt-6">
        <button 
          v-if="selectedBooking?.status === 'pending'"
          @click="showConfirmationDialog"
          class="bg-green-800 hover:bg-green-700 btn text-white border-none shadow-none">
          Mark as Completed
        </button>
        <button class="btn text-white btn-neutral hover:bg-blue-700" @click="closeModal">Close</button>
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
        <button @click="confirmMarkAsCompleted" class="btn bg-green-800 hover:bg-green-700 text-white border-none shadow-none">Yes</button>
        <!-- No button to cancel the action -->
        <button @click="cancelMarkAsCompleted" class="btn bg-red-800 hover:bg-red-700 text-white border-none shadow-none">No</button>
      </div>
    </div>
  </dialog>
</template>