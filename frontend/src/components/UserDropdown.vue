<template>
  <div class="relative inline-block text-left">
    <div
      id="avatarButton"
      @click="toggleDropdown"
      class="w-10 h-10 rounded-full cursor-pointer bg-green-500 flex items-center justify-center text-white text-lg font-semibold">
      {{ initial }}
    </div>

    <div
      v-if="isDropdownOpen"
      @click.away="closeDropdown" 
      id="userDropdown"
      class="z-50 absolute right-0 mt-2 w-60 bg-[#1e293b] text-gray-200 divide-y divide-gray-800 rounded-lg shadow-lg border border-gray-700"> 

      <div class="px-4 py-3 flex items-center space-x-4">
        <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white text-lg font-semibold flex-shrink-0"> 
          {{ initial }}
        </div>
        <div class="overflow-hidden"> 
          <router-link
            v-if="username"
            :to="{ name: 'userprofile', params: { id: userId } }"
            @click="closeDropdown" 
            class="font-semibold text-gray-200 dark:text-gray-300 hover:underline truncate block"> 
            {{ username }}
          </router-link>
          <p v-else class="font-semibold text-gray-400">Loading...</p> 
          <p class="text-sm font-semibold text-green-400 capitalize"> 
            {{ capitalizedRole }}
          </p>
        </div>
      </div>

      <ul class="py-2 text-sm" aria-labelledby="avatarButton">
         <li>
          <router-link to="/leaderboards" @click="closeDropdown" class="block px-4 py-2 text-white hover:bg-gray-600 dark:hover:bg-gray-700">Leaderboards</router-link>
        </li>
         <li>
          <router-link to="/myreports" @click="closeDropdown" class="block px-4 py-2 text-white hover:bg-gray-600 dark:hover:bg-gray-700">
            My Reports
          </router-link>
        </li>
         <li>
          <router-link to="/account-settings" @click="closeDropdown" class="block px-4 py-2 text-white hover:bg-gray-600 dark:hover:bg-gray-700">
            Settings
          </router-link>
        </li>
        <li>
          <router-link to="/payment-history" @click="closeDropdown" class="block px-4 py-2 text-white hover:bg-gray-600 dark:hover:bg-gray-700">
            Payment History
          </router-link>
        </li>
        <div class="border-t border-gray-700 dark:border-gray-600 my-1"></div> 

        <div class="py-0">
          <button
            @click="callLogout"
            class="flex items-center gap-2 px-4 py-2 text-red-400 hover:bg-red-900 hover:text-red-300 w-full text-left transition-colors duration-150"> 
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
            </svg>
            Sign Out
          </button>
        </div>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'; 
import loginService from '@/services/login-service';
import { useRouter } from 'vue-router';

const router = useRouter();
const isDropdownOpen = ref(false);
const username = ref('');
const role = ref('');
const fName = ref(''); 
const userId = ref('');

const navigateToProfile = () => {
   closeDropdown(); 
   
};

// Fetch user data from service
const fetchUserData = async () => {
  try {
    const userData = await loginService.fetchUserData();
    if (userData) {
        username.value = userData.username || 'User'; 
        role.value = userData.role || 'N/A';
        userId.value = userData.id || '';
        fName.value = userData.f_name || ''; 
    } else {
         console.error('User data not found or fetch failed.');
         username.value = 'Error'; 
         role.value = 'N/A';
    }

  } catch (error) {
    console.error('Error fetching user data:', error);
     username.value = 'Error'; 
     role.value = 'N/A';
  }
};

// Fetch data when component is mounted
onMounted(() => {
  fetchUserData();
  document.addEventListener('click', handleClickOutside);
});

// Clean up event listener when component is unmounted
onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});


// Toggle dropdown visibility
const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value;
};

// Close dropdown
const closeDropdown = () => {
    isDropdownOpen.value = false;
}

// Close dropdown if clicked outside
const handleClickOutside = (event) => {
    const dropdownElement = document.getElementById('userDropdown');
    const avatarButton = document.getElementById('avatarButton');
    if (isDropdownOpen.value && dropdownElement && avatarButton &&
        !dropdownElement.contains(event.target) &&
        !avatarButton.contains(event.target)) {
        closeDropdown();
    }
};


// Compute capitalized role string
const capitalizedRole = computed(() => {
  if (!role.value) return '';
  if (role.value.toLowerCase() === 'game pilot') return 'Game Pilot';
  return role.value
    .split(',') 
    .map(word => word.trim())
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(', ');
});

// Compute initial from username
const initial = computed(() => {
  return username.value && username.value !== 'Error' ? username.value.trim().charAt(0).toUpperCase() : '?'; 
});

// Logout user and redirect
const callLogout = async () => {
  closeDropdown(); 
  try {
      await loginService.logout();
      router.push({ name: 'login' });
  } catch (error) {
      console.error("Logout failed:", error);
  }
};
</script>

<style scoped>

#userDropdown {
  z-index: 50; 
}
</style>
