<template>
  <header class="bg-gray-900 sticky top-0 z-50 p-4 shadow-lg border-b border-green-900 relative">
    <div class="mx-auto w-full px-4 md:px-8 xl:px-[160px] 2xl:px-[300px] flex justify-between items-center">
      
      <!-- Logo Section -->
      <div class="flex items-center">
        <router-link to="/home" class="flex items-center gap-2">
          <img src="@/assets/img/qplogo3.png" alt="Logo" class="w-12 h-12" />
          <span class="text-2xl font-bold text-white">QuestProxy</span>
        </router-link>
      </div>

      <!-- Hamburger button (mobile) -->
      <button @click="toggleMenu" class="text-white lg:hidden focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <!-- Desktop Nav -->
      <nav class="hidden lg:flex items-center gap-">
        <router-link to="/home" class="text-white hover:text-green-500 transition">Home</router-link>
        <router-link v-if="role === 'gamer'" to="/pilot-matching" class="text-white hover:text-green-500 transition">Pilot Matching</router-link>
        <router-link to="/mybookings" class="text-white hover:text-green-500 transition">My Bookings</router-link>
        <router-link v-if="role === 'game pilot'" to="/services-history" class="text-white hover:text-green-500 transition">Service</router-link>
        <router-link v-if="role === 'game pilot'" to="/myportfolios" class="text-white hover:text-green-500 transition">Portfolio</router-link>
        <UserDropdown :username="username" :email="email" :role="role" @logout="callLogout" />
      </nav>
    </div>

    <!-- Mobile Nav -->
    <div v-if="isMenuOpen" class="lg:hidden px-4 py-3 space-y-2 flex flex-col items-center justify-center">
      <div class="w-full flex justify-end">
        <button class="p-1 border-none text-green-500" @click="toggleMenu">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="flex justify-center items-center flex-col w-full bg-blue-800 bg-opacity-5 text-center rounded-md">
        <router-link to="/home" class="block text-white hover:text-green-400 w-full p-2 border-b border-gray-800">Home</router-link>
        <router-link to="/mybookings" class="block text-white hover:text-green-400 w-full p-2 border-b border-gray-800">My Bookings</router-link>
        <router-link v-if="role === 'game pilot'" to="/services-history" class="block text-white hover:text-green-400 w-full p-2 border-b border-gray-800">Service</router-link>
        <router-link v-if="role === 'game pilot'" to="/myportfolios" class="block text-white hover:text-green-400 w-full p-2 border-b border-gray-800">Portfolio</router-link>
        <router-link :to="{ name: 'userprofile', params: { id } }" class="block text-white hover:text-green-400 w-full p-2 border-b border-gray-800">My Profile</router-link>
        <router-link to="/leaderboards" class="block text-white hover:text-green-400 w-full p-2 border-b border-gray-800">Leaderboard</router-link>
        <router-link to="/account-settings" class="block text-white hover:text-green-400 w-full p-2 border-b border-gray-800">Settings</router-link>
        <router-link to="/payment-history" class="block text-white hover:text-green-400 w-full p-2 border-b border-gray-800">Payment History</router-link>
        <button @click="callLogout" class="block text-white hover:text-red-500 w-full p-2 border-b border-gray-800 bg-red-800 bg-opacity-50">Sign Out</button>
      </div>      
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useUserStore } from '@/stores/userStore';
import UserDropdown from '@/components/UserDropdown.vue';
import loginService from '@/services/login-service';
import router from '@/router';

const userStore = useUserStore();


const username = computed(() => userStore.userData?.username || '');
const email = computed(() => userStore.userData?.email || '');
const role = computed(() => userStore.userData?.role || '');
const id = userStore.userData?.id || '';

const isMenuOpen = ref(false);
const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};

const callLogout = async () => {
  userStore.clearUser();
  await loginService.logout();
  router.push({ name: 'login' });
};

</script>
