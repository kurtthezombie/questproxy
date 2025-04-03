<template>
  <div class="min-h-screen bg-gray-900">
    <header class="bg-gray-800 sticky top-0 z-50 p-4 flex justify-between items-center">
      <div class="flex items-center">
        <img src="@/assets/img/qplogo3.png" alt="Logo" class="w-12 h-12" />
        <span class="text-2xl font-bold text-green-500">QuestProxy</span>
      </div>
      <nav class="flex space-x-6">
        <router-link to="/home" class="text-white hover:text-green-500">Home</router-link>
        <router-link to="/leaderboards" class="text-white hover:text-green-500">Leaderboard</router-link>
        <router-link v-if="role === 'game pilot'" to="/create-service" class="text-white hover:text-green-500">Service</router-link>
        <UserDropdown :username="username" :email="email" :role="role" :callLogout="callLogout" />
      </nav>
    </header>
    <div>
      <UserProfile />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import UserProfile from '@/components/UserProfile.vue';
import UserDropdown from '@/components/UserDropdown.vue';
import loginService from '@/services/login-service';
import { useLoader } from '@/services/loader-service';

const { loadShow, loadHide } = useLoader();

const username = ref('');
const email = ref('');
const role = ref('');

const callLogout = () => {
  userStore.clearUser();
  serviceStore.clearServices();
  localStorage.removeItem('authToken');
  localStorage.removeItem('tokenType');
  router.push({ name: 'login' });
};

const fetchUserData = async () => {
  const loader = loadShow();
  try {
    const userData = await loginService.fetchUserData();
    username.value = userData.username;
    email.value = userData.email;
    role.value = userData.role;
  } catch (error) {
    console.error('Error fetching user data:', error);
  } finally {
    loadHide(loader);
  }
};

onMounted(() => {
  fetchUserData();
});
</script>