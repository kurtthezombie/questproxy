<script setup>
import { ref, computed, onMounted } from 'vue';
import { useUserStore } from '@/stores/userStore';
import NavBar from '@/components/NavBar.vue';
import CreateModal from "@/components/portfolio/CreateModal.vue";
import PortfolioCard from '@/components/portfolio/PortfolioCard.vue';
import toast from '@/utils/toast';
import { fetchPortfolios } from '@/services/portfolio.service';

const userStore = useUserStore();
const userName = userStore.userData?.username;
const isModalOpen = ref(false);
const portfolios = ref([]);

const loadPortfolios = async (isSubmit = false) => {
    if (!userStore.userData?.pilot_id) return;

    try {
        const response = await fetchPortfolios(userStore.userData?.pilot_id);
        console.log("Response: ", response);

        const baseURL = "http://127.0.0.1:8000/storage/";

        if (response?.length) { 
            portfolios.value = response.map((portfolio) => ({
                ...portfolio,
                p_content: baseURL + portfolio.p_content,
            }));

            const message = isSubmit ? "Portfolios refreshed" : "Portfolios fetched successfully";

            toast.success(message);
        } else {
            portfolios.value = [];
            toast.info("No portfolios found.");
        }
    } catch (error) {
        console.error("Failed to fetch portfolios: ", error);
        toast.error("Failed to fetch portfolios");
    }
}

const handleSubmit = (data) => {
    isModalOpen.value = false;
    loadPortfolios(true);
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

const handlePortfolioDeleted = (deletedId) => {
    portfolios.value = portfolios.value.filter(p => p.id !== deletedId);
};

onMounted(() => {
    loadPortfolios();
});

</script>

<template>
    <NavBar />
    <!--container-->
    <div class="flex justify-center max-w-full bg-gray-800">
        <div class="flex w-1/2 flex-col items-center p-10">
            <div class="flex justify-center flex-col items-center">
                <!-- If image exists, show it -->
                <img v-if="props.image" :src="props.image" alt="Profile Image"
                    class="w-24 h-24 rounded-full object-cover" />
                <!-- If no image, show default circle with initials -->
                <div v-else
                    class="w-24 h-24 rounded-full bg-green-500 flex items-center justify-center text-5xl font-semibold text-white">
                    {{ initials }}
                </div>
                <div class="text-center mt-5 font-bold text-xl text-white">{{ userStore.userData?.username || 'Guest' }}'s portfolio</div>
            </div>
            <hr class="w-80 h-1 mx-auto my-4 bg-gray-100 border-0 rounded-sm md:my-10 dark:bg-gray-700 w-full">

            <div class="flex w-100 flex-col p-5 items-center justify-center">
                <button class="btn btn-circle w-14 h-14 p-2 mx-auto hover:bg-gray-300 hover:shadow-green-300 hover:shadow-md" @click="isModalOpen = true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>
                <div class="grid grid-cols-2 gap-y-3 gap-x-0 justify-items-center">
                    <!-- Use PortfolioCard component -->
                    <PortfolioCard 
                        v-for="portfolio in portfolios" 
                        :key="portfolio.id" 
                        :portfolio="portfolio"
                        class="w-full"
                        @deleted="handlePortfolioDeleted"
                    />
                </div>
            </div>
        </div>
    </div>
    <!-- modal component -->
    <CreateModal v-model="isModalOpen" @submit="handleSubmit" />
</template>