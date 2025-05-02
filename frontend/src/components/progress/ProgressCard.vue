<script setup>
import { deleteProgress } from '@/services/progress.service';
import toast from '@/utils/toast';
import dayjs from 'dayjs';
import { ref, onMounted, onBeforeUnmount } from 'vue';

const baseURL = "http://127.0.0.1:8000/storage/";
const props = defineProps({
  progressLog: {
    type: Object,
    required: true
  },
  isPilot: {
    type: Boolean,
    required: true,
  }
});

const emit = defineEmits(['progress-deleted']);

const showLightbox = ref(false);


const openLightbox = () => {
  showLightbox.value = true;
};

const closeLightbox = () => {
  showLightbox.value = false;
};

const handleKeyDown = (e) => {
  if (e.key === 'Escape') {
    closeLightbox();
  }
};

const handleDeleteProgress = async (id) => {
  try {
    await deleteProgress(id);
    emit('progress-deleted');
    toast.success('Progress item deleted.');
  } catch (error) {
    toast.error('Failed to delete progress item.');
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown);
});

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleKeyDown);
});

</script>

<template>
  <!-- Progress card -->
  <div class="bg-gray-800 shadow rounded-2xl overflow-hidden relative">
    <!-- Trash Icon -->
    <button v-if="isPilot" @click="handleDeleteProgress(props.progressLog.id)" class="absolute top-2 right-2 text-white hover:text-red-400 transition" aria-label="Delete">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
      </svg>
    </button>
    <div class="w-full h-48 bg-gray-600 cursor-pointer" @click="openLightbox">
      <img
        :src="props.progressLog.image_path ? baseURL + props.progressLog.image_path : 'https://via.placeholder.com/400x300'"
        alt="Progress"
        class="w-full h-full object-cover"
      />
    </div>
    <div class="p-4">
      <p class="text-sm text-gray-400 mt-1">{{ props.progressLog.description }}</p>
      <p class="text-xs text-gray-500">Added on: 
        <span class="font-medium">{{ dayjs(props.progressLog.created_at).format('MMMM D YYYY') }}</span>
      </p>
    </div>
  </div>

  <!-- Lightbox modal -->
  <div
    v-if="showLightbox"
    class="fixed inset-0 z-50 bg-black bg-opacity-80 flex items-center justify-center"
  >
    <!-- Close button -->
    <button
      @click="closeLightbox"
      class="absolute top-4 right-4 text-white hover:text-red-400 transition"
      aria-label="Close"
    >
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M6 18 18 6M6 6l12 12" />
      </svg>
    </button>

    <!-- Full-size image -->
    <img
      :src="props.progressLog.image_path ? baseURL + props.progressLog.image_path : 'https://via.placeholder.com/800x600'"
      alt="Full view"
      class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-lg"
    />
  </div>
</template>
