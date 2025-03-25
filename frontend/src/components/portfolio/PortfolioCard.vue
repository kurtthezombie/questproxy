<script setup>
import dayjs from 'dayjs';
import toast from '@/utils/toast';
import { computed, ref } from 'vue';
import { deletePortfolio } from '@/services/portfolio.service';
import relativeTime from 'dayjs/plugin/relativeTime';

dayjs.extend(relativeTime);

const emit = defineEmits(["deleted","edit"]);

const props = defineProps({
    portfolio: Object,
});

const showModal = ref(false);
const showEditModal = ref(false);
const selectedImage = ref("");
const selectedCaption = ref("");
const selectedPortfolio = ref(null);

const formattedTime = computed(() =>
    props.portfolio?.created_at ? dayjs(props.portfolio.created_at).fromNow() : "Unknown time"
);

const openImage = (imageSrc) => {
    selectedImage.value = imageSrc;
    selectedCaption.value = props.portfolio.caption || "";
    showModal.value = true;
};

const openEditModal = (selected) => {
    selectedPortfolio.value = selected;
    showEditModal.value = true;
    emit("edit", selected);
};

const closeModal = () => {
    showModal.value = false;
};

const handleDelete = async (portfolioId) => {
    try {
        await deletePortfolio(portfolioId);
        emit('deleted', portfolioId);
        toast.success("Portfolio deleted successfully");
    } catch (error) {
        toast.error("Failed to delete portfolio");
    }
}

</script>

<template>
    <div class="flex justify-center ">
        <div
            class="mt-3 w-3/4 bg-gray-200 p-3 rounded-sm shadow-md hover:scale-105 transition-transform duration-300 ease-in-out cursor-pointer rounded-xl hover:shadow-lg hover:shadow-lime-500
            flex flex-col justify-between min-h-[300px]
            ">
            <!-- Portfolio Image -->
            <div class="flex justify-center rounded-md">
                <!-- Edit & Delete Buttons (Top Right of Image) -->
                <div class="absolute top-2 right-2 flex space-x-2">
                    <button 
                        class="p-2 bg-gray-400 text-white rounded-md shadow-md hover:bg-blue-700 transition"
                        @click="openEditModal(portfolio)"
                        >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <button 
                        @click="handleDelete(portfolio.id)"
                        class="p-2 bg-gray-400 text-white rounded-md shadow-md hover:bg-red-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </div>
                <img v-if="portfolio.p_content" :src="portfolio.p_content"
                    class="w-full aspect-[4/3] object-cover rounded-md" 
                    alt="Portfolio Image"
                    @click="openImage(portfolio.p_content)" />
                <span v-else class="text-white font-semibold">No Image</span>
            </div>
            <hr>
            <!-- Portfolio Caption -->
             <div class="mt-auto">
                <div class="mt-3 text-start text-xl text-gray-800 font-medium">
                {{ portfolio?.caption || "" }}
                </div>
                <div class="mt-1 text-sm text-gray-600">{{ formattedTime }}</div>
             </div>
            
        </div>
        <!-- Image Preview Modal -->
        <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-90 mt-10">
            <div class="relative p-4 rounded-lg shadow-lg max-w-3xl">
                <button @click="closeModal"
                    class="absolute top-5 right-5 text-gray-200 bg-gray-500 bg-opacity-50 p-3 text-2xl font-bold hover:bg-gray-400 hover:bg-opacity-30 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
                <img :src="selectedImage" class="w-full max-h-[80vh] object-contain rounded-md" alt="Preview Image" />
                <h6 class="text-2xl text-white">{{ selectedCaption || "" }}</h6>
            </div>
        </div>
    </div>
</template>
