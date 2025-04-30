<script setup>

import toast from "@/utils/toast.js";
import { ref, onMounted, onUnmounted } from 'vue';
import { createPortfolio } from '@/services/portfolio.service';

defineProps(["modelValue"]);
const emit = defineEmits(["update:modelValue", "submit"]);

const file = ref(null);
const previewUrl = ref(null);
const caption = ref("");
const isLoading = ref(false);

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

const submitForm = async () => {
    isLoading.value = true;
    if (!file.value) return;

    const originalFile = file.value;
    const cleanFileName = originalFile.name.replace(/\s+/g, '_');

    const renamedFile = new File([originalFile], cleanFileName, { type: originalFile.type });


    const formData = new FormData();
    formData.append("p_content", renamedFile);
    formData.append("caption", caption.value);

    try {
        const response = await createPortfolio(formData);

        toast.success("Portfolio item added successfully");
        emit("submit", { file: renamedFile, caption: caption.value });  // Emit the submit event to the parent
        closeModal();
    } catch (error) {
        console.log("Error: ", error);
        toast.error(`Failed to add portfolio item: ${error.response?.data?.message}`);
    } finally {
        isLoading.value = false;
    }
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
            <div v-if="modelValue" class="fixed inset-0 flex items-center justify-center bg-black/50"
                @click.self="closeModal">
                <div class="card bg-gray-900 p-6 shadow-lg w-96 ">
                    <!--Header-->
                    <div class="flex justify-between items-center">
                        <h2 class="card-title text-white">Add Portfolio Item</h2>
                        <button @click="closeModal"
                            class="text-gray-300 hover:text-gray-800 cursor-pointer rounded-full hover:bg-gray-200 p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d='M6 18 18 6M6 6l12 12' />
                            </svg>
                        </button>
                    </div>

                    <!-- Divider -->
                    <hr class="my-3 border-gray-700" />

                    <!-- Content -->
                    <div class="space-y-4">
                        <!-- Image Preview -->
                        <div v-if="previewUrl" class="flex justify-center">
                            <img :src="previewUrl" alt="Image Preview" class="max-w-full max-h-40 rounded-lg">
                        </div>

                        <!-- Upload Image Button -->
                        <label class="block btn btn-soft px-4 py-2 text-center flex items-center justify-center gap-2"
                            :class="file ? 'w-full' : 'btn-circle w-12 h-12 p-0 mx-auto'">
                            <input type="file" class="hidden" @change="handleFileUpload" />
                            <span v-if="file">{{ file.name }}</span>
                            <div v-else>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d='M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15m0-3-3-3m0 0-3 3m3-3V15' />
                                </svg>
                            </div>
                        </label>

                        <!-- Caption Input -->
                        <input v-model="caption" type="text" placeholder="Caption"
                            class="input w-full bg-[#1e293b] text-white shadow-none border border-gray-700" />

                        <!-- Submit Button-->
                        <button @click="submitForm"
                            class="w-full btn bg-green-600 border border-green-600 shadow-none text-white hover:bg-green-700"
                            :disabled="isLoading">
                            <!-- Show loading spinner when isLoading is true -->
                            <span v-if="isLoading" class="loading loading-spinner"></span>
                            <!-- Show text when not loading -->
                            <span v-else>Submit</span>
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>