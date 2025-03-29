<script setup>
import NavBar from '@/components/NavBar.vue';
import PortfolioCard from '@/components/portfolio/PortfolioCard.vue';
import { fetchUserByUsername, fetchPortfoliosByUser } from '@/services/portfolio.service';
import toast from '@/utils/toast';
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const isLoading = ref(false);
const username = route.params.username;
const userData = ref(null);
const portfolios = ref([]);

const initials = computed(() => {
      return username
            .split(" ")
            .map(word => word[0])
            .join("")
            .toUpperCase();
});

const getUserDataAndPortfolios = async () => {
      try {
            isLoading.value = true;

            const user = await fetchUserByUsername(username);

            if (!user) throw new Error("User not found");

            userData.value = user;

            const response = await fetchPortfoliosByUser(user.id);

            portfolios.value = response.portfolios;

            console.log("Extracted Portfolios:");
            portfolios.value.forEach((portfolio, index) => {
            console.log(`Portfolio ${index + 1}:`, portfolio);
            });
      } catch (error) {
            console.log(error);
            toast.error("Failed to fetch portfolio data");
      } finally {
            isLoading.value = false;
      }
};

onMounted(getUserDataAndPortfolios);

</script>

<template>
      <NavBar />
      <div class="flex justify-center items-center min-h-screen max-w-full bg-gray-800 flex-col space-y-10">
            <div class="flex justify-center flex-col items-center">
                  <div
                        class="w-24 h-24 rounded-full bg-green-500 flex items-center justify-center text-5xl font-semibold text-white">
                        {{ initials }}
                  </div>
                  <div class="text-center mt-5 font-bold text-5xl text-white">{{ username || 'Guest'
                        }}</div>
            </div>
            <div class="divider divider-accent text-white px-80">PORTFOLIO</div>
      </div>
      <!-- where the cards show -->
      <div class="flex w-full flex-col p-5 items-center justify-center">
            <div class="grid grid-cols-2 gap-y-3 gap-x-0 justify-items-center">
                  <!-- Show skeleton loader when loading -->
                  <template v-if="isLoading" class="flex justify-center items-center">
                        <div class="flex justify-center items-center col-span-2 min-h-80">
                              <span class="loading loading-bars loading-xl text-accent scale-[3]"></span>
                        </div>
                  </template>

                  <!-- Show actual portfolios when loaded -->
                  <template v-else>
                        <PortfolioCard v-for="portfolio in portfolios" :key="portfolio.id" :portfolio="portfolio"
                              class="w-full" />
                  </template>
            </div>
      </div>
</template>