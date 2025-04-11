<template>
  <div class="min-h-screen bg-gray-900 flex flex-col text-white">
    <!-- Header -->
    <NavBar :username="username" :email="email" :role="role" :callLogout="callLogout" />
    <!-- Title + Description -->
    <div class="mt-20 flex justify-center items-center">
      <h2 class="text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-green-500 to-blue-400 leading-normal">
        Gaming Services
      </h2>
    </div>
    <div class="mt-2 flex justify-center items-center">
      <h5 class="text-lg text-gray-300 text-center px-4">
        Connect with gamers and offer your expertise to help others level up their gaming experience
      </h5>
    </div>
    <!-- Content Section -->
    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto mt-10 px-4">
      <!-- Left Column: Form -->
      <form @submit.prevent="submitService" class="bg-gray-800 rounded-lg p-6 space-y-6 shadow-lg border border-gray-700 md:col-span-2 lg:col-span-2">
        <div class="space-y-1">
          <h2 class="text-2xl font-bold">Offer Your Gaming Services</h2>
          <p class="text-sm text-gray-400">Share your gaming expertise and help others improve their skills</p>
        </div>
        <!-- Game -->
        <div>
          <label class="block text-sm mb-1">Game</label>
          <select v-model="formData.game" class="w-full rounded-md bg-gray-700 text-white border border-gray-600 p-2">
            <option value="" disabled>Select a game</option>
            <option v-for="category in serviceStore.categories" :key="category.id" :value="category.game">
              {{ category.title }}
            </option>
          </select>
        </div>
        <!-- Description -->
        <div>
          <!-- Label with Icon -->
          <div class="flex items-center space-x-2 mb-1">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z" />
            </svg>
            <label class="text-sm">Description</label>
          </div>
          <!-- Textarea -->
          <textarea v-model="formData.description" class="w-full rounded-md bg-gray-700 text-white border border-gray-600 p-2" rows="3" placeholder="Describe what you need help with..."></textarea>
        </div>
        <!-- Price -->
        <div>
          <div class="flex items-center space-x-2 mb-1">
            <span class="text-green-400 font-semibold">₱</span>
            <label class="text-sm">Price</label>
          </div>
          <!-- Peso input field -->
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm pointer-events-none">₱</span>
            <input 
              v-model="formData.price" 
              type="text" 
              @input="formatPrice" 
              class="w-full pl-8 rounded-md bg-gray-700 text-white border border-gray-600 p-2" 
              placeholder="0.00" 
            />
          </div>
          <p class="text-xs text-gray-400">Set a fair price for the service (in platform currency)</p>
        </div>
        <!-- Duration & Availability -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <div class="flex items-center space-x-2 mb-1">
              <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <label class="text-sm">Duration</label>
            </div>
            <input v-model="formData.duration" type="datetime-local" class="w-full rounded-md bg-gray-700 text-white border border-gray-600 p-2" />
          </div>
          <div>
            <!-- Label with Availability Icon -->
            <div class="flex items-center space-x-2 mb-1">
              <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <label class="text-sm">Availability</label>
            </div>
            <!-- Availability Select -->
            <select v-model="formData.availability" class="w-full rounded-md bg-gray-700 text-white border border-gray-600 p-2">
              <option :value="1">Available</option>
              <option :value="0">Not Available</option>
            </select>
          </div>
        </div>
        <!-- Submit Buttons -->
        <div class="flex gap-4 mt-6">
          <button type="submit" class="w-full bg-emerald-500 text-white py-2 rounded-md">Submit Request</button>
          <button type="button" class="w-full bg-white text-gray-800 py-2 rounded-md">Cancel</button>
        </div>
      </form>

      <!-- Right Column: Sidebar -->
      <div class="space-y-6">
        <!-- How It Works -->
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg border border-gray-700">
          <h4 class="text-2xl font-bold mb-4">How It Works</h4>
          <ul class="text-sm space-y-3">
            <li class="flex items-start space-x-2">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-900 text-lg text-green-400 font-semibold mr-2">1</span>
              <div>
                <span class="text-lg font-semibold">Fill the form</span>
                <div class="text-sm text-gray-500">Share your gaming expertise</div>
              </div>
            </li>
            <li class="text-lg flex items-start space-x-2">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-900 text-lg text-green-400 font-semibold mr-2">2</span>
              <div>
                <span class="font-semibold">Get discovered</span>
                <div class="text-sm text-gray-500">Players looking for your skills will find you</div>
              </div>
            </li>
            <li class="text-lg flex items-start space-x-2">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-900 text-lg text-green-400 font-semibold mr-2">3</span>
              <div>
                <span class="font-semibold">Start gaming</span>
                <div class="text-sm text-gray-500">Help others improve their gaming experience</div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>


<script setup>
import axios from 'axios';

const router = useRouter();
import { ref, onMounted, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useServiceStore } from '@/stores/serviceStore';
import { useUserStore } from '@/stores/userStore';
import NavBar from '@/components/NavBar.vue';
const serviceStore = useServiceStore();
const userStore = useUserStore();

const username = ref(userStore.userData?.username || '');
const email = ref(userStore.userData?.email || '');
const role = ref(userStore.userData?.role || '');

const formatPrice = () => {
  let formattedPrice = formData.price.replace(/[^0-9]/g, '');
  formattedPrice = formattedPrice.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  formData.price = formattedPrice;
};

const formData = reactive({
  game: '',
  description: '',
  price: null,
  duration: '',
  availability: 1
});

const isLoggedIn = () => {
  return !!localStorage.getItem('authToken');
};

if (!isLoggedIn()) {
  router.push({ name: 'login' });
}

onMounted(async () => {
  await serviceStore.fetchCategories();
});

const submitService = async () => {
  try {
    await serviceStore.createService({
      game: formData.game,
      description: formData.description,
      price: formData.price,
      duration: formData.duration,
      availability: formData.availability,
    });

    Object.assign(formData, {
      game: '',
      description: '',
      price: null,
      duration: '',
      availability: 1
    });
  } catch (error) {
    console.error('Error in submitService:', error);
  }
};

const callLogout = () => {
  userStore.clearUser();
  serviceStore.clearServices();
  localStorage.removeItem('authToken');
  localStorage.removeItem('tokenType');
  router.push({ name: 'login' });
};
</script>