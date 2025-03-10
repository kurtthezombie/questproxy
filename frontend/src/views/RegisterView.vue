<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import loginservice from '@/services/login-service';
import { useLoader } from '@/services/loader-service';

// Generate random avatar function based on user ID
const generateUserAvatar = (userId) => {
  const avatarNumber = (userId % 6) + 1; 
  return `/assets/avatarimg/avatar${avatarNumber}.png`;
};

//form
const form = reactive({
  username: '',
  email: '',
  f_name: '',
  l_name: '',
  password: '',
  confirmPassword: '',
  contact_number: '',
  role: '', 
  captcha_input: '',
  captchaKey: '',
  avatar: ''
});

// Watch for changes in user ID and update avatar
watch(() => form.username, (newUsername) => {
  if (newUsername) {
    const userId = newUsername.split('').reduce((sum, char) => sum + char.charCodeAt(0), 0); // Generate a pseudo user ID based on username
    form.avatar = generateUserAvatar(userId);
  }
});

//captchaImg
const captchaImg = ref('');
//message variables
const message = reactive({
  success: null,
  error: null
})

const { loadShow, loadHide } = useLoader();
const router = useRouter();

const submitForm = async () => {
  message.error = '';

  // Confirm password validation
  if (form.password !== form.confirmPassword) {
    message.error = "Passwords do not match!";
    return;
  }

  const loader = loadShow();
  const userId = form.username.split('').reduce((sum, char) => sum + char.charCodeAt(0), 0); // Generate a pseudo user ID based on username
  form.avatar = generateUserAvatar(userId);

  const formData = {
    username: form.username,
    email: form.email,
    f_name: form.f_name,
    l_name: form.l_name,
    password: form.password,
    contact_number: form.contact_number,
    role: form.role,
    captcha: form.captcha_input,
    key: form.captchaKey,
    avatar: form.avatar 
  };

  try {
    const response = await loginservice.register(formData);
    if (response.status) {
      //message
      message.success = 'Registration successful!';
      //login
      loginservice.login({
        username: formData.username,
        password: formData.password
      });
      //push to otp
      router.push({ path: '/otp-verification', query: { email: form.email } });
    } else {
      message.error = response.message;
      handleReload();
    }
  } catch (error) {
    message.error = 'An error occurred during registration. Please try again.';
  } finally {
    loadHide(loader);
  }
};

onMounted(() => {
  handleReload();  
  if (form.username) {
    const userId = form.username.split('').reduce((sum, char) => sum + char.charCodeAt(0), 0); // Generate a pseudo user ID based on username
    form.avatar = generateUserAvatar(userId); 
  }
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
      <div v-if="message.success"
        class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-300 dark:border-green-800"
        role="alert">
        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
          fill="currentColor" viewBox="0 0 20 20">
          <path
            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
        </svg>
        <span class="sr-only">Info</span>
        <div>
          <span class="font-medium">Success!</span> {{ message.success }}
        </div>
      </div>

      <!-- Error message -->
      <div v-if="message.error"
        class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
        role="alert">
        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
          fill="currentColor" viewBox="0 0 20 20">
          <path
            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
        </svg>
        <span class="sr-only">Error</span>
        <div>
          <span class="font-medium">Error!</span> {{ message.error }}
        </div>
      </div>

      <form @submit.prevent="submitForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Form inputs remain unchanged -->
        <div class="space-y-3">
          <div class="form-group">
            <input type="text" id="username" v-model="form.username" placeholder="Username" required
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          </div>
          <div class="form-group">
            <input type="text" id="first-name" v-model="form.f_name" placeholder="First Name" required
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          </div>
          <div class="form-group">
            <input type="text" id="last-name" v-model="form.l_name" placeholder="Last Name" required
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          </div>
          <div class="form-group">
            <input type="email" id="email" v-model="form.email" placeholder="Email" required
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          </div>
        </div>
        <div class="space-y-3">
          <div class="form-group">
            <input type="password" id="password" v-model="form.password" placeholder="Password" required
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          </div>
          <div class="form-group">
            <input type="password" id="confirm-password" v-model="form.confirmPassword" placeholder="Confirm Password"
              required
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          </div>
          <div class="form-group">
            <input type="text" id="contact-number" v-model="form.contact_number" placeholder="Contact Number" required
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          </div>
          <div class="form-group">
            <select id="role" v-model="form.role" required
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
              <option value="" disabled>Select Role</option>
              <option value="gamer">Online Games Enthusiast</option>
              <option value="game pilot">Game Pilot</option>
            </select>
          </div>
        </div>
        <div class="md:col-span-2 flex flex-col space-y-4">
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
