<script setup>

import { defineProps, defineEmits, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
  isModalOpen: Boolean,
  report: Object,
})

const emit = defineEmits(['close']);

const closeModal = () => {
  emit('close');
}

const handleEscape = (event) => {
  if (event.key === 'Escape') {
    closeModal();
  }
};

onMounted(() => {
  // Add the event listener when the modal is open
  if (props.isModalOpen) {
    window.addEventListener('keydown', handleEscape);
  }
});

onUnmounted(() => {
  // Clean up the event listener
  window.removeEventListener('keydown', handleEscape);
});

watch(() => props.isModalOpen, (newVal) => {
  // If modal opens or closes, update the event listener accordingly
  if (newVal) {
    window.addEventListener('keydown', handleEscape);
  } else {
    window.removeEventListener('keydown', handleEscape);
  }
});
</script>

<template>
  <Teleport to="body">
    <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0"
      enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100"
      leave-to-class="opacity-0">
      <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full overflow-hidden" @click.stop
          :class="{ 'animate-modal-in': isModalOpen }">
          <!-- Modal Header -->
          <div class="p-4 sm:p-6 border-b flex items-start justify-between">
            <h3 class="text-lg font-semibold">Report Details</h3>
            <button @click="closeModal"
              class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-200 rounded-full p-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-x">
                <path d="M18 6 6 18" />
                <path d="m6 6 12 12" />
              </svg>
              <span class="sr-only">Close</span>
            </button>
          </div>

          <!-- Modal Content - Simplified -->
          <div class="p-4 sm:p-6">
            <div class="space-y-4">
              <div>
                <h4 class="text-sm font-medium text-gray-500">Reported User</h4>
                <p class="mt-1 font-medium">{{ report?.reportedUser }}</p>
              </div>

              <div>
                <h4 class="text-sm font-medium text-gray-500">Status</h4>
                <div class="mt-1">
                  <span :class="{
                    'px-2 py-1 text-xs font-semibold rounded-full inline-block': true,
                    'bg-green-100 text-green-800': report?.status === 'resolved',
                    'bg-red-100 text-red-800': report?.status === 'rejected',
                    'bg-blue-100 text-blue-800': report?.status === 'pending'
                  }">
                    {{ report?.status }}
                  </span>
                </div>
              </div>

              <div>
                <h4 class="text-sm font-medium text-gray-500">Reason for Report</h4>
                <p class="mt-1">{{ report?.reason }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>