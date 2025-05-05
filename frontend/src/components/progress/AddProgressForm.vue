<script setup>
import { createProgressUpdate } from '@/services/progress.service';
import toast from '@/utils/toast';
import { ref } from 'vue';

const modal = ref(null);
const isSubmitting = ref(false);

const props = defineProps({
  bookingId: {
    type: Number,
    required: true,
  },
  handleFetchProgressLogs: {
    type: Function,
    required: true,
  }
});

console.log(props?.bookingId);
const emit = defineEmits(['progress-added']);

const form = ref({
  description: '',
  image: null,
});

const imagePreview = ref(null);

const handleFile = (e) => {
  const file = e.target.files[0];
  form.value.image = file;  // Store file in the form data
  if (file) {
    imagePreview.value = URL.createObjectURL(file);  // Generate image preview URL
  } else {
    imagePreview.value = null;  // Clear the preview if no file is selected
  }
};

const handleSubmit = async () => {
  if (!form.value.image) return;

  isSubmitting.value = true;
  const originalFile = form.value.image;
  const cleanFileName = originalFile.name.replace(/\s+/g, '_');
  const renamedFile = new File([originalFile], cleanFileName, { type: originalFile.type });

  const formData = new FormData();
  formData.append('image', renamedFile);
  formData.append('description', form.value.description);
  formData.append('booking_id', props.bookingId);

  try {
    await createProgressUpdate(formData);
    // Refresh progress logs
    emit('progress-added');
  } catch (error) {
    toast.error('Progress addition failed');
    console.error(error);
  } finally {
    isSubmitting.value = false;
    close();
  }
};

const resetFields = () => {
  form.value.description = '';
  form.value.image = null;
  imagePreview.value = null;
  // Reset the file input element manually by accessing it via ref (optional)
  if (modal.value) {
    const fileInput = modal.value.querySelector('input[type="file"]');
    if (fileInput) {
      fileInput.value = '';  // Clear the file input value
    }
  }
};

const open = () => modal.value?.showModal();
const close = () => {
  modal.value?.close();
  resetFields();  // Reset fields when modal closes
};

defineExpose({ open });
</script>

<template>
  <dialog ref="modal" class="modal">
    <div class="modal-box bg-gray-800 text-white space-y-6 max-w-md">
      <h3 class="font-bold text-lg">Add Progress Item</h3>

      <form @submit.prevent="handleSubmit">
        <!-- Image preview -->
        <div v-if="imagePreview" class="mb-4">
          <img :src="imagePreview" alt="Preview" class="w-full rounded-lg max-h-64 object-contain" />
        </div>
        <input type="file" @change="handleFile" class="file-input file-input-success w-full mb-4 bg-gray-700" accept="image/*"/>
        
        <input v-model="form.description" class="input input-bordered w-full mb-4 bg-gray-700" placeholder="Description" />
        <button type="submit" class="btn btn-md btn-success w-full" :disabled="isSubmitting">
          <span v-if="isSubmitting" class="loading loading-spinner"></span>
          {{ isSubmitting ? 'Adding Progress...' : 'Add Progress' }}
        </button>
      </form>

      <div class="modal-action">
        <button class="btn" @click="close" :disabled="isSubmitting">Close</button>
      </div>
    </div>
  </dialog>
</template> 
