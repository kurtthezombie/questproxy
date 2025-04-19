<script setup>
import { updatePortfolio } from '@/services/portfolio.service';
import toast from '@/utils/toast';
import { computed, ref, reactive, onMounted, onUnmounted, watchEffect } from 'vue';

const props = defineProps({
    modelValue: Boolean,
    portfolio: Object,
});
const loadingBtn = ref(false);
const emit = defineEmits(["update:modelValue", "close", "update"]);

const form = reactive({
    caption: props.portfolio?.caption || "",
    imagePreview: props.portfolio?.p_content || "",
    newImage: null
});

watchEffect(() => {
    if (props.modelValue && props.portfolio) {
        form.caption = props.portfolio?.caption || "";
        form.imagePreview = props.portfolio?.p_content || "";
        form.newImage = null;
    }
});

const closeModal = () => {
    emit("update:modelValue", false);
};

const closeOnEscape = (event) => {
    if (event.key === "Escape") closeModal();
};

const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.newImage = file;
        form.imagePreview = URL.createObjectURL(file);
    }
};

const handleUpdatePortfolio = async () => {
    if (!form.caption.trim()) {
        toast.error("Caption is required");
        return;
    }

    const id = props.portfolio?.id;
    if (!id) {
        toast.error("Portfolio ID is missing!");
        return;
    }

    //set formData
    const formData = new FormData();
    formData.append("_method", "PUT");
    formData.append("caption",form.caption);

    if(form.newImage) {
        formData.append("p_content", form.newImage);
    }
    
    loadingBtn.value = true;

    try{
        const response = await updatePortfolio(id, formData);
        toast.success("Portfolio updated successfully!");

        console.log("Portfolio successfully updated, emitting update event.");
        emit("update");

        closeModal();
    } catch (error) {
        toast.error("Failed to update portfolio");
    } finally {
        loadingBtn.value = false;
    }
};

const isUnchanged = computed(() => {
    return form.caption === props.portfolio.caption && !form.newImage;
});

//closing behavior
onMounted(() => window.addEventListener("keydown", closeOnEscape));
onUnmounted(() => window.removeEventListener("keydown", closeOnEscape));
</script>

<template>
    <div>
        <Teleport to="body">
            <transition name="fade">
                <div v-if="modelValue" class="fixed inset-0 flex items-center justify-center bg-black/50"
                    @click.self="closeModal">
                    <div class="card bg-gray-900 p-6 shadow-lg w-96">
                        <!-- Header -->
                        <div class="flex justify-between items-center">
                            <h2 class="card-title text-white">Edit Portfolio Item</h2>
                            <button @click="closeModal"
                                class="text-white hover:text-gray-800 cursor-pointer rounded-full hover:bg-red-200 p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d='M6 18 18 6M6 6l12 12' />
                                </svg>
                            </button>
                        </div>

                        <hr class="my-3 border-gray-700" />

                        <!-- image preview -->
                        <div class="mb-4 flex flex-col justify-center items-center">
                            <img :src="form.imagePreview" alt="Portfolio Image"
                                class="w-full max-h-48 object-cover rounded-md border border-gray-700" />
                            <label class="btn mt-4 text-white bg-green-600 shadow-none border border-green-600 font-thin">
                                Change Image
                                <input type="file" accept="image/*" class="hidden" @change="handleImageChange">
                            </label>
                        </div>

                        <!-- Caption Input -->
                        <div class="mb-4">
                            <label class="block font-semibold text-white">Caption</label>
                            <input :value="form.caption" @input="form.caption = $event.target.value" type="text"
                                class="input input-bordered w-full mt-1 bg-[#1e293b] text-white shadow-none border border-gray-700" />
                        </div>

                        <!-- Cancel Button -->
                        <div class="flex justify-end gap-x-1">
                            <button @click="emit('close')" class="btn text-white bg-gray-700 border font-thin border-gray-700 shadow-none">Cancel</button>
                            <button 
                                @click="handleUpdatePortfolio"
                                class="btn bg-green-600 border border-green-900 shadow-none text-white font-thin hover:bg-green-700 flex items-center gap-2"
                                :disabled="isUnchanged || loadingBtn">
                                <span v-if="loadingBtn" class="loading loading-spinner"></span>
                                <span v-if="!loadingBtn">Update</span>
                            </button>
                        </div>
                    </div>
                </div>
            </transition>
        </Teleport>
    </div>
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