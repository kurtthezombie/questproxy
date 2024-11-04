<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <!-- Header -->
    <NavBar :username="username" :email="email" :role="role" :callLogout="callLogout" />

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
          @click="$router.push({ name: 'ServiceView', params: { title: category.game } })"
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
import { useLoader } from '@/services/loader-service';
import NavBar from '@/components/NavBar.vue';

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
  fetchCategories();  
});

const callLogout = () => {
  userStore.clearUser();
  serviceStore.clearServices();
  localStorage.removeItem('authToken');
  localStorage.removeItem('tokenType');
  router.push({ name: 'login' });
};

</script>
