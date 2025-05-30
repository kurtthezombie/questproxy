<script setup>
import { ref, onMounted } from 'vue';
import loginservice from '@/services/login-service';
import { useRouter, useRoute } from 'vue-router';
import { useLoader } from '@/services/loader-service';
import '@/assets/css/style.css';

const router = useRouter();
const route = useRoute();
const username = ref('');
const password = ref('');
const message = ref('');
const errorMessage = ref('');
const dustStyles = ref([]);

onMounted(() => {
  if (route.query.message) {
    message.value = route.query.message;
    setTimeout(() => {
      message.value = '';
    }, 2000);
  }

  generateDustParticles();
});

// Function to generate random styles for dust particles (only once on mounted)
const generateDustParticles = () => {
  const particles = [];
  for (let i = 0; i < 100; i++) {
    const left = Math.random() * 100;
    const top = Math.random() * 100;
    const size = Math.random() * 3 + 2;
    const duration = Math.random() * 10 + 5;
    const delay = Math.random() * 5;

    particles.push({
      left: `${left}%`,
      top: `${top}%`,
      width: `${size}px`,
      height: `${size}px`,
      animationDuration: `${duration}s`,
      animationDelay: `${delay}s`,
    });
  }

  dustStyles.value = particles;
};

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

const handleBack = async () => {
  console.log('handleBack clicked!');
  router.push({ name: 'home' });
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-900 relative">
    <div class="dust-container absolute inset-0 z-0">
      <div 
        v-for="(style, index) in dustStyles" 
        :key="index" 
        class="dust"
        :style="style"
      ></div>
    </div>
    <div class="bg-gray-900 border border-gray-700 p-8 rounded-lg max-w-md w-full bg-opacity-100 relative z-10">
      <div>
        <Button 
            class="btn btn-xs rounded-md text-white bg-transparent border-0 shadow-none hover:pb-2" 
            @click="handleBack">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </Button>

      </div>
      <div class="text-center mb-6">
        <router-link to="/" class="block">
          <img src="@/assets/img/qplogo3.png" alt="logo" class="w-16 h-16 mx-auto">
        </router-link>
        <h1 class="text-2xl font-bold text-green-400">QuestProxy</h1>

        <!-- Success message -->
        <div v-if="message"
          class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-300 dark:border-green-800"
          role="alert">
          <svg class="flex-shrink-0 inline w-4 h-4 me-3" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
            viewBox="0 0 20 20">
            <path
              d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
          </svg>
          <span class="sr-only">Info</span>
          <div>
            <span class="font-medium">Success!</span> {{ message }}
          </div>
        </div>

        <!-- Error message -->
        <div v-if="errorMessage"
          class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
          role="alert">
          <svg class="flex-shrink-0 inline w-4 h-4 me-3" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
            viewBox="0 0 20 20">
            <path
              d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
          </svg>
          <span class="sr-only">Error</span>
          <div>
            <span class="font-medium">Error!</span> {{ errorMessage }}
          </div>
        </div>
      </div>

      <form @submit.prevent="submitForm" class="space-y-4 pt-5">
        <div class="form-group">
          <input type="text" v-model="username" placeholder="Username" required
            class="w-full p-3 bg-[#1e293b] text-white border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
        <div class="form-group">
          <input type="password" v-model="password" placeholder="Password" required
            class="w-full p-3 bg-[#1e293b] text-white border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
        <button type="submit"
          class="font-bold w-full p-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-300">
          Log in
        </button>
      </form>

      <!-- Divider -->
      <div class="border-t border-gray-700 dark:border-gray-700 my-5"></div>

      <router-link class="block text-center mt-4 hover:underline text-white" to="/signup">
        Don't have an account? <span class="text-green-400 font-semibold">Sign Up</span>
      </router-link>
    </div>
  </div>
</template>
