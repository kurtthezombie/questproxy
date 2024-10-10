<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <!-- Header -->
    <header class="bg-gray-800 sticky top-0 z-50 p-4 flex justify-between items-center shadow-lg border-b-4 border-green-500">
      <div class="flex items-center">
        <img src="@/assets/img/qp_logo2.png" alt="Logo" class="w-12 h-12">
        <span class="text-2xl font-bold text-white-500">QuestProxy</span>
      </div>
      <nav class="flex space-x-6">
        <a href="#" class="text-white hover:text-green-500 transition-colors duration-300">Ned Bayot</a>
        <a href="#" class="text-white hover:text-green-500 transition-colors duration-300">Canoy Pisot</a>
        <a href="#" class="text-white hover:text-green-500 transition-colors duration-300">Edging</a>
        <button @click="callLogout" class=" text-white hover:bg-red-500 hover:text-red-900 font-semibold py-2 px-4 rounded transition-colors duration-300">
          Logout
        </button>
      </nav>
    </header>

    <!-- Main Dashboard -->
    <div class="container mx-auto py-10">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Welcome, BAYOT!</h1>
        <!-- Search Bar porn included-->
        <div class="relative w-64">
          <input
            type="text"
            placeholder="Find games NIG"
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
          <!-- Game Card Types Template -->
          <div class="bg-gray-800 p-4 rounded-lg shadow-lg hover:bg-green-500 transition-colors duration-300">
            <img src="@/assets/img/WOW.webp" alt="World of Warcraft" class="mb-4 rounded-md object-cover w-36 h-36">
            <h3 class="text-xl font-semibold">World of Warcraft</h3>
          </div>

          <div class="bg-gray-800 p-4 rounded-lg shadow-lg hover:bg-green-500 transition-colors duration-300">
            <img src="@/assets/img/Dota2.webp" alt="Dota 2" class="mb-4 rounded-md object-cover w-36 h-36">
            <h3 class="text-xl font-semibold">Dota 2</h3>
          </div>

          <div class="bg-gray-800 p-4 rounded-lg shadow-lg hover:bg-green-500 transition-colors duration-300">
            <img src="@/assets/img/gta5.webp" alt="GTA 5 Online" class="mb-4 rounded-md object-cover w-36 h-36">
            <h3 class="text-xl font-semibold">GTA 5 Online</h3>
          </div>

          <div class="bg-gray-800 p-4 rounded-lg shadow-lg hover:bg-green-500 transition-colors duration-300">
            <img src="https://via.placeholder.com/150" alt="Diablo 4" class="mb-4 rounded-md object-cover w-36 h-36">
            <h3 class="text-xl font-semibold">Diablo 4</h3>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import loginService from '@/services/login-service';
import { useRouter } from 'vue-router';

const router = useRouter();

const callLogout = () => {
  console.log('LOGOUT function CALLED from GamePilotView');
  loginService.logout();

  router.push({ name: 'login' });
};
</script>

<style scoped>
/* Custom Styles */
</style>
