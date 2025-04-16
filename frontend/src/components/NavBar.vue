<template>
  <header class="bg-gray-900 sticky top-0 z-50 p-4 shadow-lg border-b border-green-900">
    <div class="container mx-auto flex justify-between items-center px-20">
      <!-- Logo Section -->
      <div class="flex items-center">
        <router-link to="/home" class="flex flex-row items-center">
          <img src="@/assets/img/qplogo3.png" alt="Logo" class="w-12 h-12">
          <span class="text-2xl font-bold text-white">QuestProxy</span>
        </router-link>
      </div>
      
      <nav class="flex items-center gap-0">
        <router-link to="/home" class="text-white hover:text-green-500 transition-colors duration-300">
          Home
        </router-link>
        <router-link to="/leaderboards" class="text-white hover:text-green-500 transition-colors duration-300">
          Leaderboard
        </router-link>
        <router-link v-if="role === 'game pilot'" to="/create-service"
          class="text-white hover:text-green-500 transition-colors duration-300">
          Service
        </router-link>
        <router-link v-if="role === 'game pilot'" to="/myportfolios"
          class="text-white hover:text-green-500 transition-colors duration-300">
          Portfolio
        </router-link>

        <!-- Avatar Dropdown Component -->
        <UserDropdown :username="username" :email="email" :role="role" @logout="callLogout" />
      </nav>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue';
import { useUserStore } from '@/stores/userStore';
import UserDropdown from '@/components/UserDropdown.vue';

const userStore = useUserStore();

const username = computed(() => userStore.userData?.username || '');
const email = computed(() => userStore.userData?.email || '');
const role = computed(() => userStore.userData?.role || '');

const callLogout = () => {
  userStore.clearUser(); // Clears user data
};
</script>
