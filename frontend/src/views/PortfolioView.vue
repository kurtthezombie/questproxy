<script setup>
import { ref, computed, onMounted } from 'vue';
import { useUserStore } from '@/stores/userStore';
import NavBar from '@/components/NavBar.vue';
import CreateModal from "@/components/portfolio/CreateModal.vue";
import PortfolioCard from '@/components/portfolio/PortfolioCard.vue';
import toast from '@/utils/toast';
import { fetchPortolios } from '@/services/portfolio.service';

const userStore = useUserStore();
const userName = userStore.userData?.username;
const role = computed(() => userStore.userData?.role || '');
const isModalOpen = ref(false);
const portfolios = ref([]);

const loadPortfolios = async () => {
    if (!userStore.userData?.id) return;
    try {
        const response = await fetchPortfolios(userStore.userData.id);
        portfolios.value = response.data || [];
    } catch (error) {
        console.error("Failed to fetch portfolios: ", error);
        toast.error("Failed to fetch portfolios");
    }
}

const handleSubmit = (data) => {
    console.log("Uploaded file: ", data.file);
    console.log("Caption: ", data.caption);

    isModalOpen.value = false
};

const props = defineProps({
    username: String,
    image: String,
});

const initials = computed(() => {
    return userName
        .split(" ")
        .map(word => word[0])
        .join("")
        .toUpperCase();
});

onMounted(loadPortfolios);

</script>

<template>
    <NavBar />
    <!--container-->
    <div class="flex justify-center max-w-full bg-base-100">
        <div class="flex w-1/2 flex-col items-center p-10">
            <div class="flex justify-center flex-col items-center">
                <!-- If image exists, show it -->
                <img v-if="props.image" :src="props.image" alt="Profile Image"
                    class="w-24 h-24 rounded-full object-cover" />

                <!-- If no image, show default circle with initials -->
                <div v-else
                    class="w-24 h-24 rounded-full bg-gray-300 flex items-center justify-center text-5xl font-semibold text-white">
                    {{ initials }}
                </div>
                <div class="text-center mt-5 font-bold text-xl">{{ userStore.userData?.username || 'Guest' }}'s portfolio</div>
            </div>
            <hr class="w-80 h-1 mx-auto my-4 bg-gray-100 border-0 rounded-sm md:my-10 dark:bg-gray-700">

            <div class="mt-10 flex w-100 flex-col bg-blue-200 p-5">
                <button class="btn btn-circle w-14 h-14 p-2 bg-lime-200" @click="isModalOpen = true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>

                </button>
                <div class="flex flex-col items-center">
                    <!-- Use PortfolioCard component -->
                    <PortfolioCard 
                        v-for="portfolio in portfolios" 
                        :key="portfolio.id" 
                        :portfolio="portfolio"
                        :onEdit="editPortfolio"
                        :onDelete="deletePortfolio" 
                    />
                </div>
            </div>
        </div>
    </div>
    <!-- modal component -->
    <CreateModal v-model="isModalOpen" @submit="handleSubmit" />
</template>