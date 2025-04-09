<script setup>
import { ref, onMounted } from 'vue';
import loginservice from '@/services/login-service';
import { useRouter, useRoute } from 'vue-router';
import { useLoader } from '@/services/loader-service';

const router = useRouter();
const route = useRoute();
const username = ref('');
const password = ref('');
const message = ref('');
const errorMessage = ref('');

onMounted(() => {
  if (route.query.message) {
    message.value = route.query.message;
    setTimeout(() => {
      message.value = '';
    }, 2000);
  }
});

const { loadShow, loadHide } = useLoader();

const submitForm = async () => {
  errorMessage.value = '';
  message.value = '';

  const formData = {
    username: username.value,
    password: password.value,
  };

  const loader = loadShow();
  try {
    const response = await loginservice.login(formData);
    if (response.status) {
      username.value = '';
      password.value = '';

      const userRole = response.authenticated_user.role;
      console.log("User role:", userRole);

      if (userRole === 'gamer' || userRole === 'game pilot') {
        console.log("Navigating to homepage..")
        await router.push({ name: 'homepage' });  
      }

      message.value = response.message;

      setTimeout(() => {
        message.value = '';
      }, 2000);
    } else {
      errorMessage.value = response.message;
      setTimeout(() => {
        errorMessage.value = '';
      }, 3000);
    }
  } catch (error) {
    console.log('LoginView error: ', error);
    errorMessage.value = 'Login failed. Please try again.';
    setTimeout(() => {
      errorMessage.value = '';
    }, 3000);
  } finally {
    loadHide(loader);
  }
};
</script>


<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-900">
    <div class="absolute inset-0 flex justify-center items-center " >
    </div> 
    <div class="bg-white backdrop-blur-md p-8 rounded-lg shadow-lg max-w-md w-full">
      <div class="text-center mb-6">
        <router-link to="/" class="block">
          <img src="@/assets/img/qplogo3.png" alt="logo" class="w-20 h-20 mx-auto">
        </router-link>
        <h1 class="text-2xl font-bold text-green-500 mt-4">QuestProxy</h1>
        
        <!-- Success message -->
        <div v-if="message" class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-300 dark:border-green-800" role="alert">
          <svg class="flex-shrink-0 inline w-4 h-4 me-3" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
          </svg>
          <span class="sr-only">Info</span>
          <div>
            <span class="font-medium">Success!</span> {{ message }}
          </div>
        </div>

        <!-- Error message -->
        <div v-if="errorMessage" class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
          <svg class="flex-shrink-0 inline w-4 h-4 me-3" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
          </svg>
          <span class="sr-only">Error</span>
          <div>
            <span class="font-medium">Error!</span> {{ errorMessage }}
          </div>
        </div>
      </div>

      <form @submit.prevent="submitForm" class="space-y-4">
        <div class="form-group">
          <input type="text" v-model="username" placeholder="Username" required
            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
        <div class="form-group">
          <input type="password" v-model="password" placeholder="Password" required
            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
        <button type="submit" class="w-full p-3 bg-green-500 font-semibold text-white rounded-lg hover:bg-green-600 transition duration-300">
          Log in
        </button>
      </form>

        <!-- Divider -->
        <div class="border-t border-gray-200 dark:border-gray-300 my-5"></div>

      <router-link class="block text-center text-sm mt-4 hover:underline text-gray-600" to="/signup">
        Don't have an account? <span class="text-green-500">Sign Up</span>
      </router-link>
    </div>
  </div>
</template>
