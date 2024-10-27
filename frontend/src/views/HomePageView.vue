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

      <!-- Categories Section -->
      <section>
      <h2 class="text-2xl font-semibold mb-4">Trending Categories</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <!-- Category Titles -->
        <div 
          v-for="category in filteredCategories" 
          :key="category.id"
          class="bg-gray-800 p-4 rounded-lg shadow-lg hover:bg-green-500 transition-colors duration-300 cursor-pointer"
          @click="$router.push({ name: 'ServiceView', params: { title: category.title } })"
        >
          <h3 class="text-xl font-semibold">{{ category.title }}</h3>
        </div>
      </div>
    </section>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import loginService from '@/services/login-service';
import UserDropdown from '@/components/UserDropdown.vue';
import { useLoader } from '@/services/loader-service';

const { loadShow, loadHide } = useLoader();

const router = useRouter();
const searchQuery = ref('');
const username = ref('');
const email = ref('');
const role = ref('');
const categories = ref([]);

const fetchCategories = async () => {
  const loader = loadShow();
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/categories');
    categories.value = response.data.categories;
    console.log("Fetched categories:", categories.value); 
  } catch (error) {
    console.error('Error fetching categories:', error);
  } finally {
    loadHide(loader);
  }
};

const filteredCategories = computed(() => {
  return categories.value.filter(category => 
    category.title.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const navigateToServiceView = (categoryTitle) => {
  router.push({ name: 'ServiceView', params: { title: categoryTitle } });
};


const fetchUserData = async () => {
  const loader = loadShow();
  try {
    const userData = await loginService.fetchUserData(); 
    username.value = userData.username;
    role.value = userData.role || 'User';  
  } catch (error) {
    console.error('Error fetching user data:', error);
    router.push({ name: 'login' });
  } finally {
    loadHide(loader);
  }
};

onMounted(() => {
  fetchUserData();
  fetchCategories();  // Load categories when component is mounted
});

const callLogout = () => {
  loginService.logout();
  router.push({ name: 'login' });
};
</script>
