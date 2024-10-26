<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import loginservice from '@/services/login-service';
import { fetchImage } from '@/services/captcha-service';
import { useLoader } from '@/services/loader-service';

const username = ref('');
const email = ref('');
const f_name = ref('');
const l_name = ref('');
const password = ref('');
const confirmPassword = ref(''); // New confirm password field
const contact_number = ref('');
const role = ref('');
const message = ref('');
const errorMessage = ref('');
const captcha_input = ref('');
const captchaImg = ref('');
const captchaKey = ref('');
const { loadShow, loadHide } = useLoader();
const router = useRouter();

const submitForm = async () => {
  errorMessage.value = '';

  // Confirm password validation
  if (password.value !== confirmPassword.value) {
    errorMessage.value = "Passwords do not match!";
    return;
  }

  const loader = loadShow();
  const formData = {
    username: username.value,
    email: email.value,
    f_name: f_name.value,
    l_name: l_name.value,
    password: password.value,
    contact_number: contact_number.value,
    role: role.value,
    captcha: captcha_input.value,
    key: captchaKey.value,
  };

  try {
    const response = await loginservice.register(formData);
    if (response.status) {
      //message
      message.value = 'Registration successful!';
      //login
      loginservice.login({
        username: formData.username,
        password: formData.password
      });
      //push to otp
      router.push({ path: '/otp-verification', query: { email: email.value } });
      //router.push({ path: '/login', query: { message: 'Registration successful!' } });
    } else {
      errorMessage.value = response.message;
      handleReload();
    }
    captcha_input.value = '';
  } catch (error) {
    errorMessage.value = 'An error occurred during registration. Please try again.';
  } finally {
    loadHide(loader);
  }
};

const handleReload = async () => {
  fetchImage()
    .then(response => {
      captchaImg.value = response.img;
      captchaKey.value = response.key;
    })
    .catch(error => {
      console.log('Error reloading CAPTCHA', error);
    })
};

onMounted(() => {
  handleReload();
});
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-200">
    <div class="bg-gray-100 p-8 rounded-lg shadow-lg max-w-3xl w-full shadow-lime-100 border-lime-300 border">
      <div class="text-center mb-6">
        <img src="@/assets/img/qplogo3.png" alt="logo" class="w-20 h-20 mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mt-4">Signup</h1>
      </div>

      <!-- Success message -->
      <div v-if="message"
        class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-300 dark:border-green-800"
        role="alert">
        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
          fill="currentColor" viewBox="0 0 20 20">
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
        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
          fill="currentColor" viewBox="0 0 20 20">
          <path
            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
        </svg>
        <span class="sr-only">Error</span>
        <div>
          <span class="font-medium">Error!</span> {{ errorMessage }}
        </div>
      </div>

      <form @submit.prevent="submitForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Form inputs remain unchanged -->
        <div class="space-y-3">
          <div class="form-group">
            <input type="text" id="username" v-model="username" placeholder="Username" required
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          </div>
          <div class="form-group">
            <input type="text" id="first-name" v-model="f_name" placeholder="First Name" required
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          </div>
          <div class="form-group">
            <input type="text" id="last-name" v-model="l_name" placeholder="Last Name" required
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          </div>
          <div class="form-group">
            <input type="email" id="email" v-model="email" placeholder="Email" required
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          </div>
        </div>
        <div class="space-y-3">
          <div class="form-group">
            <input type="password" id="password" v-model="password" placeholder="Password" required
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          </div>
          <div class="form-group">
            <input type="password" id="confirm-password" v-model="confirmPassword" placeholder="Confirm Password"
              required
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          </div>
          <div class="form-group">
            <input type="text" id="contact-number" v-model="contact_number" placeholder="Contact Number" required
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          </div>
          <div class="form-group">
            <select id="role" v-model="role" required
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
              <option value="" disabled>Select Role</option>
              <option value="gamer">Online Games Enthusiast</option>
              <option value="game pilot">Game Pilot</option>
            </select>
          </div>
        </div>
        <div class="md:col-span-2 flex flex-col space-y-4">
          <div class="form-group flex items-center space-x-4">
            <img :src="captchaImg" alt="captcha" class="w-24 h-10">
            <button type="button" @click="handleReload" class="text-gray-500 hover:text-gray-800">&#x21bb;</button>
            <input type="text" placeholder="Enter CAPTCHA" v-model="captcha_input"
              class="p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 w-48">
          </div>
          <button type="submit"
            class="w-full p-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-300">
            Register
          </button>
        </div>
      </form>

      <router-link class="block text-center text-green-500 mt-4 hover:underline" to="/login">
        Login now
      </router-link>
    </div>
  </div>
</template>
