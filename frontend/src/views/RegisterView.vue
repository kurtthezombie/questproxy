<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import loginservice from '@/services/login-service';
import { useLoader } from '@/services/loader-service';

// Form state
const form = reactive({
  username: '',
  email: '',
  f_name: '',
  l_name: '',
  password: '',
  confirmPassword: '',
  contact_number: '',
  role: '',
});

// Message state
const message = reactive({
  success: null,
  error: null,
});

const { loadShow, loadHide } = useLoader();
const router = useRouter();

// Form submission handler
const submitForm = async () => {
  message.error = ''; // Reset error message

  // Basic field validation
  if (!form.username || !form.email || !form.f_name || !form.l_name || !form.password || !form.confirmPassword || !form.contact_number || !form.role) {
    message.error = "Please fill all required fields.";
    return;
  }

  // Password match validation
  if (form.password !== form.confirmPassword) {
    message.error = "Passwords do not match!";
    return;
  }

  // Loader visibility
  const loader = loadShow();

  // Prepare form data
  const formData = {
    username: form.username,
    email: form.email,
    f_name: form.f_name,
    l_name: form.l_name,
    password: form.password,
    contact_number: form.contact_number,
    role: form.role,
  };

  try {
    const response = await loginservice.register(formData);
    if (response.status) {
      message.success = 'Registration successful!';
      
      // Perform login after successful registration
      await loginservice.login({
        username: formData.username,
        password: formData.password,
      });

      // Navigate to OTP verification
      router.push({ path: '/otp-verification', query: { email: form.email } });
    } else {
      message.error = response.message || 'Registration failed. Please try again.';
    }
  } catch (error) {
    message.error = 'An error occurred during registration. Please try again later.';
  } finally {
    loadHide(loader);
  }
};

// Cancel button handler
const cancelHandler = () => {
  router.push('/'); // Navigate to the home page
};

onMounted(() => {
  // Optional: Remove captcha
});
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-900">
    <div class="absolute inset-0 flex justify-center items-center">
    </div> 
    <div class="bg-white backdrop-blur-md p-8 rounded-lg shadow-2xl max-w-lg w-full border-lime-300">
      <div class="text-center mb-6">
        <router-link to="/" class="block">
          <img src="@/assets/img/qplogo3.png" alt="logo" class="w-20 h-20 mx-auto">
        </router-link>
        <h1 class="text-2xl font-bold mt-4">Sign up</h1>
        <p class="text-gray-500 text-sm mt-4">Join QuestProxy to connect with skilled gaming pilots</p>

      </div>

      <!-- Success Message -->
      <div v-if="message.success" class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50">
        <svg class="w-5 h-5 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
        </svg>
        <span><strong>Success!</strong> {{ message.success }}</span>
      </div>

      <!-- Error Message -->
      <div v-if="message.error" class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50">
        <svg class="w-5 h-5 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
        </svg>
        <span><strong>Error!</strong> {{ message.error }}</span>
      </div>

      <!-- Registration Form -->
      <form @submit.prevent="submitForm" class="flex flex-col items-center space-y-6">
        <div class="space-y-4 w-3/4">
          <input type="text" id="username" v-model="form.username" placeholder="Username" required class="w-full p-3 bg-transparent border border-gray-300 text-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
          <input type="text" id="first-name" v-model="form.f_name" placeholder="First Name" required class="w-full p-3 bg-transparent border border-gray-300 text-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
          <input type="text" id="last-name" v-model="form.l_name" placeholder="Last Name" required class="w-full p-3 bg-transparent border border-gray-300 text-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
          <input type="email" id="email" v-model="form.email" placeholder="Email" required class="w-full p-3 bg-transparent border border-gray-300 text-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
          <input type="password" id="password" v-model="form.password" placeholder="Password" required class="w-full p-3 bg-transparent border border-gray-300 text-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
          <input type="password" id="confirm-password" v-model="form.confirmPassword" placeholder="Confirm Password" required class="w-full p-3 bg-transparent border border-gray-300 text-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
          <input type="text" id="contact-number" v-model="form.contact_number" placeholder="Contact Number" required class="w-full p-3 bg-transparent border border-gray-300 text-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
          <select id="role" v-model="form.role" required class="w-full p-3 bg-transparent border border-gray-300 text-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="" disabled>Select Role</option>
            <option value="gamer">Online Games Enthusiast</option>
            <option value="game pilot">Game Pilot</option>
          </select>
        </div>

        <!-- Submit Button -->
        <div class="flex flex-col space-y-4 w-3/4">
          <div class="flex space-x-4">
            <button 
              type="button" 
              class="w-1/2 p-3 border border-gray-300 bg-transparent rounded-lg hover:bg-gray-300 transition duration-300"
              @click="cancelHandler">Cancel
            </button>
            <button 
              type="submit" 
              class="w-1/2 p-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-300">
              Register
            </button>
          </div>
        </div>
      </form>
      
      <!-- Divider -->
      <div class="border-t border-gray-200 dark:border-gray-300 my-5"></div>

      <!-- Login Link -->
      <router-link class="block text-sm text-center mt-4 hover:underline text-gray-600" to="/login">
        Already have an account? <span class="text-green-500">Log in</span>
      </router-link>
    </div>
  </div>
</template>
