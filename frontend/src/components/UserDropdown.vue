<template>
  <div class="relative inline-block text-left">
    <div class="relative w-10 h-10 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
      <div 
        id="avatarButton" 
        @click="toggleDropdown" 
        class="w-10 h-10 rounded-full cursor-pointer bg-green-500 flex items-center justify-center text-white text-lg font-semibold">
        {{ initials }}
      </div>
    </div>
    <!-- Dropdown menu -->
    <div 
      v-if="isDropdownOpen" 
      id="userDropdown" 
      class="z-10 absolute right-0 mt-2 w-64 bg-white text-gray-900 divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-100 dark:text-gray-800 dark:divide-gray-200">
      
      <div class="px-4 py-3 space-y-1">
        <p class="text-sm font-medium text-gray-900 dark:text-gray-800 break-words">
          <span class="font-bold text-blue-600 dark:text-blue-500">Email:</span> {{ email }}
        </p>
        <p class="text-sm font-medium text-gray-900 dark:text-gray-800 break-words">
          <span class="font-bold text-green-600 dark:text-green-500">Username:</span>
          <router-link 
            v-if="username" 
            :to="{ name: 'userprofile', params: { id: userId } }"
            @click.native.prevent="navigateToProfile">
            {{ username }}
          </router-link>
        </p>
        <p class="text-sm font-medium text-gray-900 dark:text-gray-800 break-words">
          <span class="font-bold text-purple-600 dark:text-purple-500">Role:</span> {{ capitalizedRole }}
        </p>
      </div>

      <ul class="py-2 text-sm" aria-labelledby="avatarButton">
        <li>
          <router-link to="/account-settings" class="block px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-300">Settings</router-link>
        </li>
        <li>
          <router-link v-if="role === 'game pilot'" 
        to="/services-history" class="block px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-300">Services</router-link>        
        </li>
      
      <div class="py-0">
        <button 
          @click="callLogout" 
          class="block px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-300 w-full text-left">
          Sign out
        </button>
      </div>
      </ul>
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
const fName = ref('');
const userId = ref(''); 

const props = defineProps({
  username: String,
  fName: String,
});

const navigateToProfile = () => {
  const token = localStorage.getItem('token');
  if (token) {
    router.push({ 
      name: 'userprofile', 
      params: { id: userId.value } 
    });
  }
};


const generateUserAvatar = (fName) => {
  return fName.charAt(0).toUpperCase();
};

const fetchUserData = async () => {
  try {
    const userData = await loginService.fetchUserData();
    username.value = userData.username;
    email.value = userData.email;
    role.value = userData.role || 'User';
    userId.value = userData.id; 
    fName.value = userData.f_name || '';
  } catch (error) {
    console.error('Error fetching user data:', error);
    userAvatar.value = generateUserAvatar(userId.value);
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
  return role.value
    .split(',')
    .map(word => word.trim())
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(', ');
});

const initials = computed(() => {
  return fName.value ? fName.value.charAt(0).toUpperCase() : '?';
});

// Handle logout
const callLogout = () => {
  loginService.logout();
  router.push({ name: 'login' });
};
</script>
