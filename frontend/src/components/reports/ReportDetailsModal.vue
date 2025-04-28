<template>
  <Teleport to="body">
    <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0"
      enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100"
      leave-to-class="opacity-0">
      <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-60 z-40 flex items-center justify-center p-4">
        <div class="bg-[#1E293B] rounded-lg shadow-xl max-w-md w-full overflow-hidden border border-gray-700 text-white"
          @click.stop :class="{ 'animate-modal-in': isModalOpen }">
          <div class="p-4 sm:p-6 border-b border-gray-700 flex items-start justify-between relative">
            <button @click="closeModal"
              class="absolute top-4 left-4 text-gray-400 hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-600 rounded-full p-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              <span class="sr-only">Go back</span>
            </button>

            <h3 class="text-lg font-semibold text-white text-center w-full">Report Details</h3>

            <button @click="closeModal"
              class="text-gray-400 hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-600 rounded-full p-1 absolute top-4 right-4">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-x">
                <path d="M18 6 6 18" />
                <path d="m6 6 12 12" />
              </svg>
              <span class="sr-only">Close</span>
            </button>
          </div>

          <div class="p-4 sm:p-6">
            <div class="space-y-4">
              <div>
                <h4 class="text-sm font-medium text-gray-400">Reported User</h4>
                <p class="mt-1 font-medium text-gray-200">{{ report?.reportedUser }}</p>
              </div>

              <div>
                <h4 class="text-sm font-medium text-gray-400">Status</h4>
                <div class="mt-1">
                  <span :class="{
                    'px-2 py-1 text-xs font-semibold rounded-full inline-block border': true,
                    'bg-green-950 text-green-400 border-green-800': report?.status === 'resolved',
                    'bg-red-950 text-red-400 border-red-800': report?.status === 'rejected',
                    'bg-blue-950 text-blue-400 border-blue-800': report?.status === 'pending'
                  }">
                    {{ report?.status }}
                  </span>
                </div>
              </div>

              <div>
                <h4 class="text-sm font-medium text-gray-400">Reason for Report</h4>
                <p class="mt-1 text-gray-200">{{ report?.reason }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>

import { onMounted, onUnmounted, watch } from 'vue';

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
  if (props.isModalOpen) {
    window.addEventListener('keydown', handleEscape);
  }
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleEscape);
});

watch(() => props.isModalOpen, (newVal) => {
  if (newVal) {
    window.addEventListener('keydown', handleEscape);
  } else {
    window.removeEventListener('keydown', handleEscape);
  }
});
</script>
