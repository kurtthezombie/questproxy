userdropdown

<template>
  <div class="relative inline-block text-left">
    <!-- Avatar Button -->
    <div 
      id="avatarButton" 
      @click="toggleDropdown" 
      class="w-10 h-10 rounded-full cursor-pointer bg-green-500 flex items-center justify-center text-white text-lg font-semibold">
      {{ initial }}
    </div>

    <!-- Dropdown menu -->
    <div 
      v-if="isDropdownOpen" 
      id="userDropdown" 
      class="z-10 absolute right-0 mt-2 w-72 bg-white text-gray-900 divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-100 dark:text-gray-800 dark:divide-gray-200">
      
      <!-- User Info Section -->
      <div class="px-4 py-3 flex items-center space-x-4">
        <!-- Avatar Inside Dropdown -->
        <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white text-lg font-semibold">
          {{ initial }}
        </div>
        <!-- Username and Role -->
        <div>
          <router-link 
            v-if="username" 
            :to="{ name: 'userprofile', params: { id: userId } }"
            @click.native.prevent="navigateToProfile"
            class="text-sm font-semibold text-gray-900 dark:text-gray-800 hover:underline -ml-4">
            {{ username }}
          </router-link>
          <p class="text-xs text-gray-300 dark:text-gray-500">
            {{ capitalizedRole }}
          </p>
        </div>
      </div>

      <!-- Menu Items -->
      <ul class="py-2 text-sm" aria-labelledby="avatarButton">
        <li>
          <router-link to="/account-settings" class="block px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-300">
            Settings
          </router-link>
        </li>
        <li v-if="role === 'game pilot'">
          <router-link to="/services-history" class="block px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-300">
            Services
          </router-link>        
        </li>
        <li>
          <router-link to="/payment-history" class="block px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-300">
            Payment History
          </router-link>
        </li>

        <!-- Divider -->
        <div class="border-t border-gray-200 dark:border-gray-300 my-1"></div>

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

const fetchUserData = async () => {
  try {
    const userData = await loginService.fetchUserData();
    username.value = userData.username;
    role.value = userData.role || 'User';
    userId.value = userData.id; 
    fName.value = userData.f_name || '';
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
  return role.value
    .split(',')
    .map(word => word.trim())
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(', ');
});

const initial = computed(() => {
  return username.value ? username.value.trim().charAt(0).toUpperCase() : '';
});


const callLogout = () => {
  loginService.logout();
  router.push({ name: 'login' });
};
</script>
