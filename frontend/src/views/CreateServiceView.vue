<template>
  <div class="min-h-screen bg-gray-900 flex flex-col">
    <!-- Header -->
    <NavBar :username="username" :email="email" :role="role" :callLogout="callLogout" />

    <!-- Form Container -->
    <div class="flex items-center justify-center flex-grow">
      <form @submit.prevent="submitService" class="bg-gray-800 p-6 rounded-lg shadow-lg w-96">
        <!-- Message -->
        <div v-if="message" class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50" role="alert">
          <svg class="flex-shrink-0 inline w-4 h-4 me-3" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
          </svg>
          <div>
            <span class="font-medium">Success!</span> {{ message }}
          </div>
        </div>

        <!-- Form Fields -->
        <div>
          <label for="games" class="block mb-2 text-sm font-medium text-white">Game:</label>
          <select v-model="game" id="games" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5">
            <option value="" disabled>Select a game</option>
            <option v-for="category in categories" :key="category.id" :value="category.game">
              {{ category.title }}
            </option>
          </select>
        </div>

        <div>
          <label for="description" class="block mb-2 text-sm font-medium text-white">Description:</label>
          <textarea v-model="description" id="description" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5" required></textarea>
        </div>

        <div>
          <label for="price" class="block mb-2 text-sm font-medium text-white">Price (₱):</label>
          <input v-model="price" type="number" step="0.01" id="price" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5" required />
        </div>

        <div>
          <label for="duration" class="block mb-2 text-sm font-medium text-white">Duration:</label>
          <input v-model="duration" type="datetime-local" id="duration" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5" required />
        </div>

        <div>
          <label for="availability" class="block mb-2 text-sm font-medium text-white">Availability:</label>
          <select v-model="availability" id="availability" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5">
            <option :value="1">Available</option>
            <option :value="0">Not Available</option>
          </select>
        </div>

        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 mt-4">
          Submit
        </button>
      </form>
    </div>
  </div>
</template>



<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import UserDropdown from '@/components/UserDropdown.vue';
import NavBar from '@/components/NavBar.vue';


const username = ref(''); 
const email = ref('');    
const role = ref('');
const categories = ref([]);
const game = ref('');
const description = ref('');
const price = ref(null);
const duration = ref('');
const availability = ref(1);
const message = ref('');  
const router = useRouter();

const isLoggedIn = () => {
  return !!localStorage.getItem('authToken');
};

if (!isLoggedIn()) {
  router.push({ name: 'login' });
}

const fetchCategories = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/categories');
    categories.value = response.data.categories; 
    console.log("Fetched categories:", categories.value); 
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

onMounted(() => {
  fetchCategories();
});

const submitService = async () => {
  const serviceData = {
    game: game.value,
    description: description.value,
    price: price.value,
    duration: duration.value,
    availability: availability.value,
  };

  try {
    const response = await axios.post('http://127.0.0.1:8000/api/services/create', serviceData, {
      headers: {
        Authorization: `${localStorage.getItem('tokenType')} ${localStorage.getItem('authToken')}`
      }
    });
    console.log('Service Created:', response);
    message.value = 'Your service has been successfully created!'; 
    setTimeout(() => {
        message.value = '';
      }, 2000);
  } catch (error) {
    console.error('Error creating service:', error);
    message.value = 'Error creating service. Please try again.'; 
    setTimeout(() => {
        message.value = '';
      }, 2000);
  }
};

const callLogout = () => {
  localStorage.removeItem('authToken');
  localStorage.removeItem('tokenType');
  router.push({ name: 'login' });
};
</script>
