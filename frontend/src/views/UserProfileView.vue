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
        <router-link v-if="role" to="/create-service" class="text-white hover:text-green-500">Service</router-link>
        <UserDropdown :username="username" :email="email" :role="role" :callLogout="callLogout" />
      </nav>
    </header>
    <div>
      <UserProfile
      :username="username"
      :avatar="profileData.avatar"
      :description="description"
      :service="profileData.service"
      @report="handleReport"
    />
  </div>
  </div>
</template>


<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import loginService from '@/services/login-service';
import UserProfile from '@/components/UserProfile.vue';
import UserDropdown from '@/components/UserDropdown.vue'; 
import axios from 'axios';


const username = ref(''); 
const email = ref(''); 
const role = ref(''); 


const profileData = ref({});
const description = ref('');


const route = useRoute();

const fetchUserData = async () => {
  try {
    const userData = await loginService.fetchUserData(); 
    username.value = userData.username; 
  } catch (error) {
    console.error('Error fetching user data:', error);
  }
};


const handleReport = (reportMessage) => {
  
  console.log('Report submitted:', reportMessage);
};

onMounted(() => {
  fetchUserData();
});


const callLogout = async () => {
  await loginService.logout();
  router.push({ name: 'login' });
};
</script>
