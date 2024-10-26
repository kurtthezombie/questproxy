<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <!-- Header -->
    <header class="bg-gray-800 sticky top-0 z-50 p-4 flex justify-between items-center shadow-lg border-b-4 border-green-500">
      <div class="flex items-center">
        <img src="@/assets/img/qplogo3.png" alt="Logo" class="w-12 h-12">
        <span class="text-2xl font-bold text-green-500">QuestProxy</span>
      </div>
      <nav class="flex space-x-6">
        <router-link to="/leaderboards" class="text-white hover:text-green-500 transition-colors duration-300">
          Leaderboard
        </router-link>
        <router-link 
          v-if="role === 'game pilot'" 
          to="/create-service" 
          class="text-white hover:text-green-500 transition-colors duration-300">
          Service
        </router-link>

        <!-- Avatar Dropdown Component -->
        <UserDropdown 
          :username="username" 
          :email="email" 
          :role="role" 
          :callLogout="callLogout"
        />
      </nav>
    </header>

    <!-- Main Dashboard -->
    <div class="container mx-auto py-10">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Welcome, {{ username }}</h1>
        <!-- Search Bar -->
        <div class="relative w-64">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Find games"
            class="bg-white text-black rounded-full px-4 py-2 pl-10 shadow-lg w-full"
          />
          <div class="absolute left-3 top-2 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m8 0l-3-3 3-3M9 19l3-3-3-3m8 0l-3 3 3 3M19 9l3 3-3 3M21 21v-3h-6v3h6z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Game Types -->
      <section>
        <h2 class="text-2xl font-semibold mb-4">Trending Games</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <!-- Game Cards -->
          <div 
            v-for="game in filteredGames" 
            :key="game.name"
            class="bg-gray-800 p-4 rounded-lg shadow-lg hover:bg-green-500 transition-colors duration-300">
            <img :src="game.image" :alt="game.name" class="mb-4 rounded-md object-cover w-36 h-36">
            <h3 class="text-xl font-semibold">{{ game.name }}</h3>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import loginService from '@/services/login-service';
import { useRouter } from 'vue-router';
import UserDropdown from '@/components/UserDropdown.vue';


const router = useRouter();
const searchQuery = ref('');
const username = ref('');
const email = ref('');
const role = ref('');

import WOWImage from '@/assets/img/WOW.webp';
import Dota2Image from '@/assets/img/Dota2.webp';
import GTA5Image from '@/assets/img/gta5.webp';
import Diablo4Image from '@/assets/img/diablo4.webp';

const games = ref([
  { name: 'World of Warcraft', image: WOWImage },
  { name: 'Dota 2', image: Dota2Image },
  { name: 'GTA 5 Online', image: GTA5Image },
  { name: 'Diablo 4', image: Diablo4Image },
]);

const filteredGames = computed(() => {
  return games.value.filter(game => 
    game.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});


const fetchUserData = async () => {
  try {
    const userData = await loginService.fetchUserData(); 
    username.value = userData.username;
    role.value = userData.role || 'User';  
  } catch (error) {
    console.error('Error fetching user data:', error);
  }
};

onMounted(() => {
  fetchUserData();
});

const callLogout = () => {
  loginService.logout();
  router.push({ name: 'login' });
};
</script>