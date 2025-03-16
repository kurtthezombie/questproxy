<script setup>

import { ref, defineProps, defineEmits, onMounted, onUnmounted } from 'vue';

defineProps(["modelValue"]);
const emit = defineEmits(["update:modelValue", "submit"]);

const file = ref(null);
const previewUrl = ref(null);
const caption = ref("");

const closeModal = () => {
    file.value = null;
    previewUrl.value = null;
    caption.value = "";
    emit("update:modelValue", false);
};

const handleFileUpload = (event) => {
    const selectedFile = event.target.files[0];

    if (!selectedFile) return;

    if (selectedFile) {
        file.value = selectedFile;
        previewUrl.value = URL.createObjectURL(selectedFile); // Generate preview URL
    }
};

const submitForm = () => {
    emit("submit", { file: file.value, caption: caption.value });
    closeModal();
};

const closeOnEscape = (event) => {
    if (event.key === "Escape") closeModal();
};

onMounted(() => window.addEventListener("keydown", closeOnEscape));
onUnmounted(() => window.removeEventListener("keydown", closeOnEscape));
</script>

<template>
    <Teleport to="body">
        <transition name="fade">
            <div
                v-if="modelValue"
                class="fixed inset-0 flex items-center justify-center bg-black/50"
                @click.self="closeModal"
            >
                <div class="bg-white p-6 rounded-lg shadow-lg w-96 border-green-500 border">
                    <!--Header-->
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold">Add Portfolio Item</h2>
                        <button @click="closeModal" class="text-gray-500 hover:text-gray-800 cursor-pointer rounded-full hover:bg-gray-200 p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Divider -->
                     <hr class="my-3 border-gray-300" />

                     <!-- Content -->
                      <div class="space-y-4">
                        <!-- Image Preview -->
                        <div v-if="previewUrl" class="flex justify-center">
                            <img :src="previewUrl" alt="Image Preview" class="max-w-full max-h-40 rounded-lg">
                        </div>

                        <!-- Upload Image Button -->
                         <label class="block w-full px-4 py-2 text-center bg-purple-500 text-white rounded-lg cursor-pointer hover:bg-purple-600">
                            <input type="file" class="hidden" @change="handleFileUpload" />
                            {{  file ? file.name : "Upload Image" }}
                         </label>

                         <!-- Caption Input -->
                          <input 
                            v-model="caption"
                            type="text"
                            placeholder="Caption"
                            class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                          />

                          <!-- Submit Button-->
                          <button
                            @click="submitForm"
                            class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600"
                          >
                            Submit
                        </button>
                      </div>
                </div>
            </div>
        </transition>
    </Teleport>
</template>

<style scoped>

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s;
}

.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

</style>