<script setup>
import { ref, computed, onMounted, watchEffect } from 'vue';
import { useUserStore } from '@/stores/userStore';
import NavBar from '@/components/NavBar.vue';
import CreateModal from "@/components/portfolio/CreateModal.vue";
import PortfolioCard from '@/components/portfolio/PortfolioCard.vue';
import toast from '@/utils/toast';
import { fetchPointsByUsername, fetchPortfolios } from '@/services/portfolio.service';
import EditPortfolioModal from '@/components/portfolio/EditPortfolioModal.vue';
import dayjs from 'dayjs';
import '@/assets/css/style.css'; 

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

const getMemberSince = (createdAt) => {
  if (!createdAt) return '';
  const date = dayjs(createdAt);
  return date.format('YYYY');
};

// Function to generate random styles for dust particles
const generateParticleStyle = (index) => {
  const left = Math.random() * 100;
  const top = Math.random() * 100;
  const size = Math.random() * 3 + 2;
  const duration = Math.random() * 10 + 5;
  const delay = Math.random() * 5;
  
  return {
    left: `${left}%`,
    top: `${top}%`,
    width: `${size}px`,
    height: `${size}px`,
    animationDuration: `${duration}s`,
    animationDelay: `${delay}s`,
  };
};
</script>

<template>
    <NavBar />
    <!--container-->
    <div class="flex justify-center min-h-screen max-w-full bg-gray-900">
        <div class="flex flex-col p-10 w-full max-w-5xl mx-auto">
            <div class="gradient-bg flex justify-center flex-col items-center">
                <div class="dust-container">
                    <!-- Generate 100 dust particles -->
                    <div 
                        v-for="index in 100" 
                        :key="index" 
                        class="dust"
                        :style="generateParticleStyle(index)"
                    ></div>
                </div>
                <div
                    class="w-[130px] h-[130px] rounded-full bg-green-500 flex items-center justify-center text-7xl font-semibold text-white">
                    {{ initials }}
                </div>
                <div class="text-center mt-5 font-bold text-3xl text-white">
                    {{ userStore.userData?.username || 'Guest' }}'s portfolio
                </div>
                <div class="mt-5 flex items-center gap-2">
                    <div class="bg-emerald-950 text-emerald-400 px-3 py-1 rounded-full text-xs flex items-center border border-emerald-800">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="yellow">
                            <path d="M12 2L15 8L22 8.5L17 12.5L18 19.5L12 16.5L6 19.5L7 12.5L2 8.5L9 8L12 2Z"/>
                        </svg>
                        <span class="font-semibold mr-1 ml-1">Quest Points:</span>
                        <span class="font-bold">{{ points }}</span>
                    </div>
                    <div class="flex items-center gap-1 text-blue-400 font-semibold text-xs leading-tight bg-blue-950 px-3 py-1 rounded-full border border-blue-700">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A10.97 10.97 0 0112 15c2.21 0 4.25.672 5.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Member since {{ getMemberSince(userStore.userData?.created_at) }}
                    </div>
                </div>
            </div>
            <!-- Portfolio Gallery -->
            <div class="flex w-full justify-between mt-20 px-4">
                <div class="text-left">
                    <h2 class="text-2xl font-bold text-white">Portfolio Gallery</h2>
                    <p class="text-gray-400">Showcase of achievements</p>
                </div>
                <div class="text-right">
                    <button class="bg-emerald-600 text-white mb-5 p-2 pb-2 rounded"
                        @click="isModalOpen = true">
                        + Add portfolio item
                    </button>
                </div>
            </div>

            <!-- Portfolio Cards -->
            <div class="mt-10 w-full max-h-[600px] overflow-y-auto px-5">
                <div class="grid grid-cols-2 gap-y-5 gap-x-5 justify-items-center">
                    <template v-if="isLoading">
                        <div class="flex justify-center items-center col-span-2 min-h-80">
                            <span class="loading loading-bars loading-xl text-accent scale-[3]"></span>
                        </div>
                    </template>
                    <template v-else>
                        <PortfolioCard v-for="portfolio in portfolios" :key="portfolio.id" :username="userName"
                            :portfolio="portfolio" class="w-full" @deleted="handlePortfolioDeleted"
                            @edit="openEditModal" />
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- modal component -->
    <CreateModal v-model="isModalOpen" @submit="handleSubmit" />
    <EditPortfolioModal v-show="isEditModalOpen" v-model="isEditModalOpen" :portfolio="selectedPortfolio"
        @close="isEditModalOpen = false" @update="loadPortfolios" />
</template>

