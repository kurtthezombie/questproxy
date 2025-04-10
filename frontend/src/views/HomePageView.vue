<template>
  <!-- Header -->
  <NavBar/>
  <div class="min-h-screen bg-gray-900 text-white p-5">
    <!-- Main Dashboard -->
    <div class="container mx-auto py-5 max-w-7xl">
      <!-- Header Section -->
      <div>
        <div class="p-6 bg-gray-800 rounded-xl shadow-md flex flex-col mb-8 border border-gray-600">
          <h1 class="text-3xl mb-2 font-bold">
            Welcome, <span class="text-green-400">{{ username }}</span>
          </h1>
          <h3 class="mb-5 text-gray-400">
            Ready to enhance your gaming experience today?
          </h3>
        </div>
        <!-- Search Bar -->
        <div class="relative w-full max-w-7xl">
          <div class="absolute left-2 top-1/2 -translate-y-1/2 bg-gray-700 rounded-full p-2 focus-within:border-2 focus-within:border-green-400">
            <svg 
              xmlns="http://www.w3.org/2000/svg" 
              class="h-7 w-7 text-white"
              fill="none" 
              viewBox="0 0 24 24" 
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M10 3a7 7 0 015.65 11.35l4.35 4.35M15 10a5 5 0 10-10 0 5 5 0 0010 0z" />
            </svg>
          </div>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search game"
            class="bg-gray-800 text-gray-300 border border-gray-600 rounded-full pl-16 pr-4 py-4 h-15 shadow-md w-full focus:outline-none focus:border-4 focus:border-green-600 focus:text-gray-300"
          />
        </div>
      </div>
      <!-- Categories Section -->
      <div class="py-10">
        <section class="max-w-7xl mx-auto">
          <h2 class="text-2xl font-bold mb-10">Trending Categories</h2>
          <div v-if="!categories.length" class="text-center mt-10 text-gray-400">
              Loading categories...
          </div>
          <!-- Category Cards -->
          <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-10 justify-items-center">
            <div 
              v-for="category in filteredCategories" 
              :key="category.id"
              class="bg-gray-800 p-6 rounded-xl border border-gray-600 group hover:border-green-400 hover:shadow-green-500/50 hover:-translate-y-2 transition-all duration-300 cursor-pointer flex flex-col items-center justify-center w-[290px] min-h-[120px]">
              <!-- Category Title -->
              <h3 class="font-bold text-gray-300 text-center text-lg group-hover:font-bold group-hover:text-green-400 group-hover:text-xl">
                {{ category.title }}
              </h3>
            </div>
          </div>
        </section>
      </div>
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
import { useUserStore } from '@/stores/userStore';
import { useServiceStore } from '@/stores/serviceStore';

const { loadShow, loadHide } = useLoader();

const router = useRouter();
const searchQuery = ref('');
const username = ref('');
const email = ref('');
const role = ref('');
const categories = ref([]);

const userStore = useUserStore();
const serviceStore = useServiceStore();

const fetchCategories = async () => {
  const loader = loadShow();
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/categories');
    categories.value = response.data.categories || [];
    console.log("Fetched categories:", categories.value); 
  } catch (error) {
    console.error('Error fetching categories:', error);
    categories.value = [];
  } finally {
    loadHide(loader);
  }
};

const filteredCategories = computed(() => {
  return categories.value.filter(category => 
    category?.title?.toLowerCase().includes(searchQuery.value.toLowerCase())
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
