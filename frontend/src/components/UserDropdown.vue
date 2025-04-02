<template>
  <div class="relative inline-block text-left">
    <!-- Avatar Circle and Dropdown Menu -->
    <div 
      class="relative w-10 h-10 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600 flex items-center justify-center"
      @click="toggleDropdown"
      :style="{ backgroundColor: avatarColor }"
    >
      <span class="text-xl font-bold text-white">{{ avatarLetter }}</span>
    </div>
    
    <!-- Dropdown menu -->
    <div 
      v-if="isDropdownOpen" 
      id="userDropdown" 
      class="z-10 absolute right-0 mt-2 w-64 bg-white text-gray-900 divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-100 dark:text-gray-800 dark:divide-gray-200"
    >
      <!-- User Info (Avatar + Username + Role) -->
      <div class="px-4 py-3 flex items-center space-x-3">
        <div 
          class="relative w-10 h-10 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600 flex items-center justify-center"
          :style="{ backgroundColor: avatarColor }"
        >
          <span class="text-xl font-bold text-white">{{ avatarLetter }}</span>
        </div>
        <div class="flex flex-col">
          <p class="text-sm font-medium text-gray-900 dark:text-gray-800">
            <router-link 
              v-if="username" 
              :to="{ name: 'userprofile', params: { username } }"
              class="text-blue-600 dark:text-blue-500 no-underline"
            >
              {{ username }}
            </router-link>
          </p>
          <p class="text-xs font-medium text-gray-600 dark:text-gray-400">
            {{ capitalizedRole }}
          </p>
        </div>
      </div>

      <!-- Dropdown Menu Items -->
      <ul class="py-2 text-sm" aria-labelledby="avatarButton">
        <li>
          <router-link 
            to="/account-settings" 
            class="block px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-300 no-underline"
          >
            Settings
          </router-link>
        </li>
        <li v-if="role === 'game pilot'">
          <router-link 
            to="/serviceshistory" 
            class="block px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-300 no-underline"
          >
            Services
          </router-link>
        </li>
      </ul>

      <!-- Logout Button -->
      <div class="py-0">
        <button 
          @click="callLogout" 
          class="block px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-300 w-full text-left"
        >
          Sign out
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import loginService from '@/services/login-service';
import { useRouter } from 'vue-router';

const router = useRouter();
const isDropdownOpen = ref(false);
const username = ref('');
const email = ref('');
const role = ref('');

const fetchUserData = async () => {
  try {
    const userData = await loginService.fetchUserData();
    username.value = userData.username;
    email.value = userData.email;
    role.value = userData.role || 'User';
  } catch (error) {
    console.error('Error fetching user data:', error);
  }
};

onMounted(() => {
  fetchUserData();
});

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value;
};

const capitalizedRole = computed(() => {
  if (!role.value) return '';
  return role.value.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
});

// Avatar logic
const avatarLetter = computed(() => {
  if (username.value && username.value.length > 0) {
    return username.value.charAt(0).toUpperCase(); // First letter of the username
  }
  return ''; // Fallback if no username
});

const avatarColor = computed(() => {
  // Generate a simple color based on the first letter of the username for uniqueness
  const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  const index = letters.indexOf(avatarLetter.value) % 6; // Limit to 6 colors
  const colors = ['#FF5733', '#33FF57', '#3357FF', '#FF33A6', '#F1C40F', '#9B59B6'];
  return colors[index] || '#FF5733'; // Default fallback color
});

const props = defineProps({
  avatar: {
    type: String,
    default: '@/assets/img/qplogo3.png'
  },
  callLogout: Function
});

const callLogout = () => {
  if (props.callLogout) {
    props.callLogout();  
  } else {
    loginService.logout();
    router.push({ name: 'login' });
  }
};
</script>
