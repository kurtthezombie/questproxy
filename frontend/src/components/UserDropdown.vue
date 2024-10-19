<template>
  <div class="relative inline-block text-left">
    <div class="relative w-10 h-10 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
      <img id="avatarButton" @click="toggleDropdown" 
           class="w-10 h-10 rounded-full cursor-pointer" 
           :src="avatar" alt="User dropdown">
    </div>
    <!-- Dropdown menu -->
    <div v-if="isDropdownOpen" id="userDropdown" class="z-10 absolute right-0 mt-2 w-64 bg-white text-gray-900 divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-100 dark:text-gray-800 dark:divide-gray-200">
      <div class="px-4 py-3 space-y-1">
        <p class="text-sm font-medium text-gray-900 dark:text-gray-800 break-words">
          <span class="leftfont-bold text-blue-600 dark:text-blue-500">Email:</span> {{ email }}
        </p>
        <p class="text-sm font-medium text-gray-900 dark:text-gray-800 break-words">
          <span class="font-bold text-green-600 dark:text-green-500">Username:</span> {{ username }}
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
          <a href="#" class="block px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-300">Racist</a>
        </li>
      </ul>
      <div class="py-0 ">
        <button @click="callLogout" class="block px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-300 w-full px-4 py-2 text-left hover:bg-gray-200 dark:hover:bg-gray-300">Sign out</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  avatar: {
    type: String,
    default: '@/assets/img/qplogo3.png'
  },
  username: String,
  email: String,
  role: String,
  callLogout: Function
});

const isDropdownOpen = ref(false);

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value;
};

const capitalizedRole = computed(() => {
  if (!props.role) return '';
  return props.role.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
});
</script>