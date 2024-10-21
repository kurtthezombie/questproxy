<template>
    <div class="max-h-screen bg-gray-900 text-white">
      <!-- Header -->
      <header class="bg-gray-800 sticky top-0 z-50 p-4 flex justify-between items-center shadow-lg border-b-4 border-green-500">
        <div class="flex items-center">
          <img src="@/assets/img/qplogo3.png" alt="Logo" class="w-12 h-12">
          <span class="text-2xl font-bold text-white-500">QuestProxy</span>
        </div>
        <nav class="flex space-x-6">
          <!-- Home Link -->
          <router-link to="/home" class="text-white hover:text-green-500 transition-colors duration-300">
            Home
          </router-link>
          <router-link 
            v-if="role === 'game pilot'" 
            to="/create-service" 
            class="text-white hover:text-green-500 transition-colors duration-300">
            Service
          </router-link>
  
          <!-- Avatar Dropdown Component -->
          <UserDropdown 
            :username="username" 
            :email="email" 
            :role="role" 
            :callLogout="callLogout"
          />
        </nav>
      </header>
      
      <!-- Sidebar and Account Form -->
      <div class="flex">
        <Sidebar />
        <!-- Integrated Account Form with New Fields -->
        <div class="flex-1 bg-white flex justify-center items-start py-8">
          <div class="w-full p-8 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Personal</h2>
  
            <form @submit.prevent="saveData">
              <div class="grid gap-6 mb-6 md:grid-cols-2">
                <!-- First Name -->
                <div>
                  <label for="first_name" class="block mb-2 text-sm font-medium text-gray-800">First Name</label>
                  <input
                    type="text"
                    id="first_name"
                    class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5"
                    v-model="firstName"
                    required
                  />
                </div>
  
                <!-- Last Name -->
                <div>
                  <label for="last_name" class="block mb-2 text-sm font-medium text-gray-800">Last Name</label>
                  <input
                    type="text"
                    id="last_name"
                    class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5"
                    v-model="lastName"
                    required
                  />
                </div>
  
                <!-- Contact Number -->
                <div class="md:col-span-2">
                  <label for="contact_number" class="block mb-2 text-sm font-medium text-gray-800">Contact Number</label>
                  <input
                    type="tel"
                    id="contact_number"
                    class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-half p-2.5"
                    v-model="contactNumber"
                    placeholder="123-456-7890"
                    required
                  />
                </div>
              </div>
  
              <!-- Save Button -->
              <button
                type="submit"
                class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
              >
                Save
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import Sidebar from '@/components/Sidebar.vue';
  import loginService from '@/services/login-service'; 
  import { useRouter } from 'vue-router';
  import UserDropdown from '@/components/UserDropdown.vue';
  
  const router = useRouter();
  const isDropdownOpen = ref(false);
  const role = ref(null);
  const username = ref('');
  const email = ref('');
  const firstName = ref('');   
  const lastName = ref('');    
  const contactNumber = ref('');
  
  function toggleDropdown() {
    isDropdownOpen.value = !isDropdownOpen.value;
  }
  
  // Logout function
  const callLogout = async () => {
    console.log('LOGOUT function CALLED from AccountSettingsView');
    await loginService.logout(); // Await logout to ensure cleanup
    router.push({ name: 'login' });
  };
  
  onMounted(async () => {
    try {
      const user = await loginService.fetchUserData(); 
      if (user) { 
        role.value = user.role; 
        username.value = user.name;
        email.value = user.email;
        firstName.value = user.first_name || '';  
        lastName.value = user.last_name || '';
        contactNumber.value = user.contact_number || ''; 
      } else {
        console.error('No user data received');
      }
    } catch (err) {
      console.error('Error fetching user details:', err); 
      
      router.push({ name: 'login' });
    }
  });
  
  // Method to save data

  
  </script>
  