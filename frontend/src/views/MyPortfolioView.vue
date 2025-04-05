<script setup>
import { ref, computed, onMounted, watchEffect } from 'vue';
import { useUserStore } from '@/stores/userStore';
import NavBar from '@/components/NavBar.vue';
import CreateModal from "@/components/portfolio/CreateModal.vue";
import PortfolioCard from '@/components/portfolio/PortfolioCard.vue';
import toast from '@/utils/toast';
import { fetchPointsByUsername, fetchPortfolios } from '@/services/portfolio.service';
import EditPortfolioModal from '@/components/portfolio/EditPortfolioModal.vue';

const userStore = useUserStore();
const userName = userStore.userData?.username;
const isModalOpen = ref(false);
const isLoading = ref(false);
const isEditModalOpen = ref(false);
const selectedPortfolio = ref(null);
const portfolios = ref([]);
const points = ref(0);

const loadPortfolios = async (isSubmit = false) => {
    if (!userStore.userData?.pilot_id) return;

    isLoading.value = true;

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

            toast.info(message);
        } else {
            portfolios.value = [];
            toast.info("No portfolios found.");
        }
    } catch (error) {
        console.error("Failed to fetch portfolios: ", error);
        toast.error("Failed to fetch portfolios");
    } finally {
        isLoading.value = false;
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

const openEditModal = (portfolio) => {
    selectedPortfolio.value = portfolio;
    isEditModalOpen.value = true;
};

const handlePortfolioDeleted = (deletedId) => {
    portfolios.value = portfolios.value.filter(p => p.id !== deletedId);
};

const fetchPoints = async (myname) => {
  try {
    const data = await fetchPointsByUsername(myname);
    console.log("Points: ", points.value);
    points.value = data;
  } catch (error) {
    console.error("Failed to fetch points: ", error);
    toast.error("Failed to fetch points");
  }
}

onMounted(() => {
    fetchPoints(userName);
    loadPortfolios();
});

</script>

<template>
    <NavBar />
    <!--container-->
    <div class="flex justify-center min-h-screen max-w-full bg-gray-800">
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
                <div class="text-center mt-5 font-bold text-xl text-white">
                    {{ userStore.userData?.username || 'Guest' }}'s portfolio
                </div>
                <div class="mt-5 flex items-center gap-2">
                    <div class="bg-gray-300 text-green-900 px-3 py-1 rounded-full font-medium flex items-center">
                        <span class="mr-1">Quest Points:</span>
                        <span class="font-bold">{{ points }}</span>
                    </div>
                </div>
            </div>
            <hr class="w-80 h-1 mx-auto my-4 bg-gray-100 border-0 rounded-sm md:my-10 dark:bg-gray-700 w-full">

            <div class="flex w-100 flex-col items-center justify-center">
                <button class="btn btn-neutral text-green-300 mx-auto hover:shadow-green-300 hover:shadow-md mb-5"
                    @click="isModalOpen = true">
                    + Add portfolio item
                </button>
                <div class="w-full max-h-[600px] overflow-y-auto px-5">
                    <div class="grid grid-cols-2 gap-y-5 gap-x-5 justify-items-center">
                        <!-- Show skeleton loader when loading -->
                        <template v-if="isLoading" class="flex justify-center items-center">
                            <div class="flex justify-center items-center col-span-2 min-h-80">
                                <span class="loading loading-bars loading-xl text-accent scale-[3]"></span>
                            </div>
                        </template>

                        <!-- Show actual portfolios when loaded -->
                        <template v-else>
                            <PortfolioCard v-for="portfolio in portfolios" :key="portfolio.id" :username="userName"
                                :portfolio="portfolio" class="w-full" @deleted="handlePortfolioDeleted"
                                @edit="openEditModal" />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- modal component -->
    <CreateModal v-model="isModalOpen" @submit="handleSubmit" />
    <EditPortfolioModal v-show="isEditModalOpen" v-model="isEditModalOpen" :portfolio="selectedPortfolio"
        @close="isEditModalOpen = false" @update="loadPortfolios" />
</template>