<template>
  <form @submit.prevent="submitService">
    <div class="min-h-screen bg-gray-900 text-white">
      <!-- Header -->
      <header class="bg-gray-800 sticky top-0 z-50 p-4 flex justify-between items-center shadow-lg border-b-4 border-green-500">
        <div class="flex items-center">
          <img src="@/assets/img/qplogo3.png" alt="Logo" class="w-12 h-12">
          <span class="text-2xl font-bold text-green-500">QuestProxy</span>
        </div>
        <nav class="flex space-x-6">
          <router-link to="/home" class="text-white hover:text-green-500 transition-colors duration-300">Home</router-link>
          <router-link to="/leaderboards" class="text-white hover:text-green-500 transition-colors duration-300">Leaderboard</router-link>
          <UserDropdown :username="username" :email="email" :role="role" :callLogout="callLogout" />
        </nav>
      </header>

      <!-- Form Fields -->
      <div>
        <label for="games" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Game:</label>
        <select v-model="serviceData.game" id="games" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
          <option v-for="game in categories" :key="game.id" :value="game.title">
            {{ game.title }}
          </option>
        </select>
      </div>

      <div>
        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description:</label>
        <textarea v-model="serviceData.description" id="description" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required></textarea>
      </div>

      <div>
        <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Price (₱):</label>
        <input v-model="serviceData.price" type="number" step="0.01" id="price" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
      </div>

      <div>
        <label for="duration" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Duration:</label>
        <input v-model="serviceData.duration" type="datetime-local" id="duration" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
      </div>

      <div>
        <label for="availability" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Availability:</label>
        <select v-model="serviceData.availability" id="availability" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
          <option :value="1">Available</option>
          <option :value="0">Not Available</option>
        </select>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        Submit
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import UserDropdown from '@/components/UserDropdown.vue';

const serviceData = ref({
  game: '',
  description: '',
  price: null,
  duration: '',
  availability: 1,
});

const errorMessage = ref('');
const message = ref('');
const categories = ref([]); 
const router = useRouter();

const fetchCategories = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/categories'); 
    categories.value = response.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

fetchCategories();

const submitService = async () => {
  const loader = $loading.show();
  errorMessage.value = '';
  message.value = '';

  try {
    const response = await axios.post('http://127.0.0.1:8000/api/services/create', serviceData.value);
    
    if (response.status === 201) { 
      serviceData.value = {
        game: '',
        description: '',
        price: null,
        duration: '',
        availability: 1,
      };
      message.value = 'Service created successfully!';
      router.push({ path: '/services', query: { message: 'Service created successfully!' } });
    } else {
      errorMessage.value = response.data.message || 'Error creating service.';
    }
  } catch (error) {
    errorMessage.value = 'An error occurred during service creation. Please try again.';
    console.error('Error creating service:', error);
  } finally {
    loader.hide();
  }
};
</script>