<script setup>
import NavBar from '@/components/NavBar.vue';
import PortfolioCard from '@/components/portfolio/PortfolioCard.vue';
import { fetchUserByUsername, fetchPortfoliosByUser, fetchPointsByUsername } from '@/services/portfolio.service';
import toast from '@/utils/toast';
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const isLoading = ref(false);
const username = route.params.username;
const userData = ref(null);
const portfolios = ref([]);
const points = ref(0);

const initials = computed(() => {
  return username
    .split(" ")
    .map(word => word[0])
    .join("")
    .toUpperCase();
});

const fetchPoints = async (username) => {
  try {
    const data = await fetchPointsByUsername(username);
    console.log("Points: ", points.value);
    points.value = data;
  } catch (error) {
    console.error("Failed to fetch points: ", error);
    toast.error("Failed to fetch points");
  }
}

const getUserDataAndPortfolios = async () => {
  try {
    isLoading.value = true;

    const user = await fetchUserByUsername(username);

    if (!user) throw new Error("User not found");

    userData.value = user;

    const response = await fetchPortfoliosByUser(user.id);
    const baseURL = "http://127.0.0.1:8000/storage/";

    portfolios.value = response.portfolios.map((portfolio) => ({
      ...portfolio,
      p_content: baseURL + portfolio.p_content,
    }));
  } catch (error) {
    console.log(error);
    toast.error("Failed to fetch portfolio data");
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  getUserDataAndPortfolios(),
    fetchPoints(username)
});

</script>

<template>
  <NavBar />
  <div class="flex justify-center items-center min-h-screen max-w-full bg-gray-800 flex-col space-y-10 py-10">
    <div class="flex justify-center flex-col items-center">
      <div
        class="w-24 h-24 rounded-full bg-green-500 flex items-center justify-center text-5xl font-semibold text-white">
        {{ initials }}
      </div>
      <div class="text-center mt-5 font-bold text-5xl text-white">
        {{ username || 'Guest'}}
      </div>
      <div class="mt-5 flex items-center gap-2">
        <div class="bg-gray-300 text-green-900 px-3 py-1 rounded-full font-medium flex items-center">
          <span class="mr-1">Quest Points:</span> 
          <span class="font-bold">{{ points }}</span>
        </div>
      </div>
    </div>
    <div class="divider divider-accent text-white px-80">PORTFOLIO</div>
    <!-- where the cards show -->
    <div class="w-1/2 max-h-[600px] overflow-y-auto p-5">
      <div class="grid grid-cols-2 gap-y-5 gap-x-5 justify-items-center">
        <!-- Show skeleton loader when loading -->
        <template v-if="isLoading" class="flex justify-center items-center">
          <div class="flex justify-center items-center col-span-2 min-h-80">
            <span class="loading loading-bars loading-xl text-accent scale-[3]"></span>
          </div>
        </template>

        <!-- Show actual portfolios when loaded -->
        <template v-else>
          <PortfolioCard v-for="portfolio in portfolios" :key="portfolio.id" :username="username" :portfolio="portfolio"
            class="w-full" />
        </template>
      </div>
    </div>
  </div>
</template>