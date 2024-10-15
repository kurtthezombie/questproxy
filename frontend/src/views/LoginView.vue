<script setup>
import { ref, onMounted } from 'vue';
import loginservice from '@/services/login-service';
import { useRouter, useRoute } from 'vue-router';
import { useLoading } from 'vue-loading-overlay';

const router = useRouter();
const route = useRoute();
const username = ref('');
const password = ref('');
const message = ref(''); // For success message
const errorMessage = ref(''); // For error message

onMounted(() => {
    if (route.query.message) {
        message.value = route.query.message;
        // Clear the success message after 2 seconds
        setTimeout(() => {
            message.value = '';
        }, 2000);
    }
});

const $loading = useLoading({
    isFullPage: true,
    color: "#58E246",
    height: 128,
    width: 128,
    loader: 'spinner',
    backgroundColor: "#454545",
    enforceFocus: true 
});

const fullPage = ref(false);

const submitForm = async () => {
    errorMessage.value = ''; 
    message.value = ''; 

    const formData = {
        username: username.value,
        password: password.value,
    };
    const loader = $loading.show();
    try {
        const response = await loginservice.login(formData);
        if (response.status) {
            // Login success, navigate based on user role
            username.value = '';
            password.value = '';

            const userRole = response.authenticated_user.role;
            if (userRole === 'gamer') {
                router.push({ name: 'gamer' });
            } else if (userRole === 'game pilot') {
                router.push({ name: 'game-pilot' });
            }
            message.value = response.message; 

            // Clear success message after 2 seconds
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

        // Clear error message after 3 seconds
        setTimeout(() => {
            errorMessage.value = '';
        }, 3000);
    } finally {
        loader.hide();
    }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-200">
    <div class="bg-gray-100 p-8 rounded-lg shadow-lg max-w-md w-full">
      <div class="text-center mb-6">
        <img src="@/assets/img/qplogo3.png" alt="logo" class="w-20 h-20 mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mt-4">Welcome</h1>
        
        <!-- Success message -->
        <div v-if="message" class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-300 dark:border-green-800" role="alert">
          <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
          </svg>
          <span class="sr-only">Info</span>
          <div>
            <span class="font-medium">Success!</span> {{ message }}
          </div>
        </div>

        <!-- Error message -->
        <div v-if="errorMessage" class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
          <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
          </svg>
          <span class="sr-only">Error</span>
          <div>
            <span class="font-medium">BOGO!</span> {{ errorMessage }}
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
        <button type="submit" class="w-full p-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-300">
          Login
        </button>
      </form>

      <router-link class="block text-center text-green-500 mt-4 hover:underline" to="/signup">
        Signup now
      </router-link>
    </div>
  </div>
</template>
