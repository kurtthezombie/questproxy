<template>
  <!-- Header -->
  <NavBar/>
  <div class="min-h-screen bg-gray-900 text-white p-5">
    <!-- Main Dashboard -->
    <div class="container mx-auto py-5 max-w-7xl">

      <!-- Welcome Section -->
      <div class="relative w-full py-6 rounded-xl shadow-md flex flex-col gap-8 overflow-hidden">
        
        <!-- Dust Background -->
        <div class="absolute top-0 left-0 w-full h-full bg-cover bg-center z-0 pointer-events-none">
          <div class="dust-container">
            <div 
              v-for="index in 100" 
              :key="index" 
              class="dust"
              :style="generateParticleStyle(index)"
            ></div>
          </div>
        </div>

        <!-- Content Layer (Welcome section) -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sm:gap-0 z-10">
          <!-- Text Content -->
          <div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl mb-2 font-bold">
              Welcome, <span class="text-green-400">{{ username }}</span>
            </h1>
            <h3 class="mb-2 text-base sm:text-lg text-gray-300">
              Ready to enhance your gaming experience today?
            </h3>
          </div>

          
        </div>

        <!-- Search Bar and SVG Section -->
        <div class="relative w-full max-w-7xl z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          
          <!-- Search Bar -->
          <div class="relative w-full sm:w-[500px]">
            <div class="absolute left-2 top-1/2 -translate-y-1/2 p-2">
              <svg class="h-[1.5em] opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor">
                  <circle cx="11" cy="11" r="8"></circle>
                  <path d="m21 21-4.3-4.3"></path>
                </g>
              </svg>
            </div>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search game.."
              class="bg-[#1e293b] text-gray-300 border border-gray-700 rounded-full pl-16 pr-4 py-4 h-13 shadow-md 
                    w-full focus:outline-none focus:border-4 focus:border-green-600 focus:text-gray-300"
            />
          </div>

          <!-- SVG (desktop only) -->
          <div class="hidden sm:flex items-center mr-40">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="90" height="90" fill="none" stroke="#047857" stroke-width="2">
              <path d="M6 12L9 10L12 3L15 10L18 12L15 14L12 21L9 14L6 12Z" transform="scale(0.8) translate(1.5, 3)" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M6 12L9 10L12 3L15 10L18 12L15 14L12 21L9 14L6 12Z" transform="translate(16, 14) scale(0.4)" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>

        </div>


        <!-- Button shown under text on small screens -->
        <div class="block mb-10">
          <router-link to="/pilot-matching">
            <button class="btn bg-green-500 hover:bg-green-600 text-white border-none shadow-none rounded-full w-fit 
                          text-sm sm:text-lg px-4 py-2 sm:px-8 sm:py-8">
              Find a Pilot
            </button>
          </router-link>
        </div>
      </div>

      <!-- Trending Categories Section -->
      <div class="py-10">
        <section class="max-w-7xl mx-auto">
          <h2 class="text-2xl font-bold mb-10">Trending Categories</h2>
          <div v-if="!categories.length" class="text-center mt-10 text-gray-400">
              Loading categories...
          </div>
          <!-- Category Cards -->
          <div v-else class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-10 justify-items-center">
            <router-link 
              v-for="category in filteredCategories" 
              :key="category.id"
              :to="{ name: 'ServiceView', params: { title: category.game } }"
              class="bg-blue-900 bg-opacity-20 p-6 rounded-xl border border-gray-700 group hover:border-green-400 hover:shadow-green-500/50 hover:-translate-y-2 transition-all duration-300 cursor-pointer flex flex-col items-center justify-center w-[290px] min-h-[120px]">
              <!-- Category Title -->
              <h3 class="font-bold text-gray-300 text-center text-lg group-hover:font-bold group-hover:text-green-400 group-hover:text-xl">
                {{ category.title }}
              </h3>
            </router-link>
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
import '@/assets/css/style.css'; 

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

// Function to generate random styles for dust particles
const generateParticleStyle = (index) => {
  const left = Math.random() * 100;
  const top = Math.random() * 100;
  const size = Math.random() * 3 + 2;
  const duration = Math.random() * 10 + 5;
  const delay = Math.random() * 5;
  
  return {
    left: `${left}%`,
    top: `${top}%`,
    width: `${size}px`,
    height: `${size}px`,
    animationDuration: `${duration}s`,
    animationDelay: `${delay}s`,
  };
};
</script>