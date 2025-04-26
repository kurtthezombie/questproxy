<template>
  <div class="min-h-screen bg-gray-900 text-white flex flex-col">
    <!-- Header -->
    <NavBar/>
     
    <!-- Main Content -->
    <div class="flex-1 py-10">
      <div class="container mx-auto max-w-7xl px-4 md:px-8">
        <div class="flex flex-col md:flex-row gap-6 w-full">

          <!-- Left Sidebar with Profile Info and Navigation -->
          <div class="w-full md:w-[260px] space-y-6">
            <!-- User Profile Card -->
            <div class="bg-blue-800 bg-opacity-5 rounded-lg p-6 text-center shadow border border-gray-700">
              <div class="flex justify-center">
                <div class="w-24 h-24 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold text-5xl leading-[1]">
                  {{ initial }}
                </div>
              </div>
              <h3 class="text-lg font-semibold">{{ firstName }} {{ lastName }}</h3>
              <p class="text-sm text-gray-400">@{{ username }}</p>
              <div class="mt-2">
                <span class="bg-green-950 text-green-400 px-2 py-1 text-xs rounded-full border border-green-800">
                  {{ formattedRole }}
                </span>
              </div>

              <div class="flex justify-center">
                <div class="mt-6 text-sm text-gray-400 space-y-2">
                  <div class="w-full text-left space-y-2">

                    <!-- Email -->
                    <div class="flex items-center gap-2">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 0a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
                      </svg>
                      <span>{{ email }}</span>
                    </div>

                    <!-- Phone -->
                    <div class="flex items-center gap-2">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 5a2 2 0 012-2h1.11c.445 0 .845.292.96.723l.73 2.69a1 1 0 01-.272.98L6.21 9.21a16 16 0 007.59 7.59l1.816-1.316a1 1 0 01.98-.272l2.69.73c.431.115.723.515.723.96V19a2 2 0 01-2 2h-.25C9.455 21 3 14.545 3 6.25V6a2 2 0 012-1z" />
                      </svg>
                      <span>{{ contactNumber }}</span>
                    </div>

                    <!-- Member Since
                    <div class="flex items-center gap-2">
                      <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="M5.121 17.804A10.97 10.97 0 0112 15c2.21 0 4.25.672 5.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      <span>Member since January 2023</span>
                    </div> -->
                    
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-blue-800 bg-opacity-5 rounded-lg p-4 border border-gray-700">
              <h2 class="text-green-400 font-medium text-lg mb-4 flex items-center gap-2">
                <UserInfo/>
            </h2>

            <!-- Embedded User Info Component -->
            
            
              <!-- Divider -->
              <div class="border-t border-gray-700 dark:border-gray-700 my-1"></div>
              
              <!-- Sign Out -->
              <div class="mt-4">
                <nav class="space-y-4 text-base">
                  <a 
                    href="#" 
                    @click="callLogout" 
                    class="flex items-center gap-2 text-red-500 hover:text-red-600 text-left w-full"
                  >
                    <!-- Sign Out SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                    </svg>
                    Sign Out
                  </a>
                </nav>
              </div> 
            </div>
          </div>

          <!-- Right Content Area -->
          <div class="flex-grow md:w-[calc(100%-260px)]">
            <div class="bg-blue-800 bg-opacity-5 w-full max-w-full p-8 rounded-lg shadow-lg border border-gray-700">
              <!--Header -->
              <div class="flex items-center mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0" />
                </svg>
                <h2 class="text-2xl font-semibold text-white">Personal Information</h2>
              </div>
              <h2 class="text-sm text-gray-400 mb-6">Update your personal details and contact information</h2>
              <!-- Success Message -->
              <div
                v-if="message"
                class="flex items-center p-4 mb-6 text-sm text-green-300 border border-green-600 rounded-lg bg-green-950"
                role="alert"
              >
                <svg class="w-4 h-4 mr-2 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                  <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"
                  />
                </svg>
                <span class="font-medium">Success:</span> {{ message }}
              </div>
              <!-- Form -->
              <form @submit.prevent="UpdateForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-sm mb-2 text-gray-300">First Name</label>
                    <input v-model="firstName"
                      type="text"
                      class="w-full p-2.5 text-sm text-white bg-[#1e293b] border border-gray-700 rounded-md focus:ring-2 focus:ring-red-500 focus:outline-none"
                    />
                  </div>
                  <div>
                    <label class="block text-sm mb-2 text-gray-300">Last Name</label>
                    <input v-model="lastName"
                      type="text"
                      class="w-full p-2.5 text-sm text-white bg-[#1e293b] border border-gray-700 rounded-md focus:ring-2 focus:ring-red-500 focus:outline-none"
                    />  
                  </div>
                  <div>
                    <label class="block text-sm mb-2 text-gray-300">Email</label>
                    <input v-model="email"
                      type="email"
                      class="w-full p-2.5 text-sm text-white bg-[#1e293b] border border-gray-700 rounded-md focus:ring-2 focus:ring-red-500 focus:outline-none"
                    />  
                  </div>
                  <div>
                    <label class="block text-sm mb-2 text-gray-300">Contact Number</label>
                    <input v-model="contactNumber"
                      type="tel"
                      class="w-full p-2.5 text-sm text-white bg-[#1e293b] border border-gray-700 rounded-md focus:ring-2 focus:ring-red-500 focus:outline-none"
                    />  
                  </div>
                </div>
                <button
                  type="submit"
                  class="mt-6 bg-green-600 hover:bg-green-700 transition rounded-md px-5 py-2 text-sm font-medium"
                >
                  Save Changes
                </button>
              </form>

              <!-- Delete Account -->
              <div class="mt-10 border-t border-gray-700 pt-6">
                <h3 class="text-lg font-semibold text-red-400 mb-3">Delete Account</h3>
                <p class="text-sm text-gray-400 mb-4">
                  Deleting your QuestProxy account will permanently remove all your personal information, settings, saved data, and activity history from our system.
                </p>
                <p class="text-sm text-gray-400 mb-2 font-semibold">Before proceeding, please note:</p>
                <ul class="list-disc list-inside text-sm text-gray-400 mb-4">
                  <li>You will lose access to all services associated with your account.</li>
                  <li>Any content or data linked to your account will be permanently deleted.</li>
                  <li>This action cannot be undone.</li>
                </ul>
                <p class="text-sm text-gray-400 mb-4">
                  If you are sure you want to delete your account, please confirm below.
                </p>
                <button
                  @click="showConfirmationModal"
                  class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md"
                >
                  Delete My Account
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Confirmation Modal -->
      <div v-if="isConfirmationModalOpen" class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center z-50">
        <div class="bg-[#1E293B] rounded-lg p-6 w-full max-w-md text-white">
          <h3 class="text-lg font-semibold mb-4">Confirm Account Deletion</h3>
          <p class="text-sm mb-6">Are you sure you want to delete your account? This action cannot be undone.</p>
          <div class="flex justify-end gap-4">
            <button @click="hideConfirmationModal" class="px-4 py-2 bg-gray-600 rounded hover:bg-gray-700">Cancel</button>
            <button @click="handleDeleteAccount" class="px-4 py-2 bg-red-600 rounded hover:bg-red-700">Yes, Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Sidebar from '@/components/Sidebar.vue';
import loginService from '@/services/login-service'; 
import { useRouter } from 'vue-router';
import { useLoader } from '@/services/loader-service';
import NavBar from '@/components/NavBar.vue';
import UserInfo from '@/components/UserInfo.vue'



const { loadShow, loadHide } = useLoader();
const router = useRouter();

import dayjs from 'dayjs';

const role = ref(null);
const username = ref('');
const email = ref('');
const firstName = ref('');
const lastName = ref('');
const contactNumber = ref('');
const userId = ref('');
const message = ref(''); 
const isConfirmationModalOpen = ref(false); 


const getMemberSince = (createdAt) => {
  if (!createdAt) return '';
  const date = dayjs(createdAt);
  return date.format('MMMM YYYY');
};

// Fetch user data and populate form fields
const fetchUserData = async () => {
  const loader = loadShow();
  try {
    const user = await loginService.fetchUserData();
    if (user) {
      role.value = user.role;
      username.value = user.username;
      email.value = user.email;
      firstName.value = user.f_name; 
      lastName.value = user.l_name;    
      contactNumber.value = user.contact_number;  
      userId.value = user.id; 
    } else {
      console.error('No user data received');
    }
  } catch (error) {
    console.error('Error fetching user details:', error);
    router.push({ name: 'login' });
  } finally {
    loadHide(loader);
  }
};

onMounted(fetchUserData);

const UpdateForm = async () => {
  const loader = loadShow();
  try {
    await loginService.updateUser({ 
      id: userId.value,            
      firstName: firstName.value, 
      lastName: lastName.value, 
      contactNumber: contactNumber.value,
      email: email.value 
    });
    
    // Success message
    message.value = "Your profile has been successfully updated!";
    
    setTimeout(() => {
      message.value = '';
    }, 3000);
  } catch (error) {
    console.error('Failed to update user data:', error);
  } finally {
    loadHide(loader);
  }
};

// Show/hide confirmation modal
const showConfirmationModal = () => {
  isConfirmationModalOpen.value = true;
};

const hideConfirmationModal = () => {
  isConfirmationModalOpen.value = false;
};

// Handle account deletion
const handleDeleteAccount = async () => {
  hideConfirmationModal(); 
  const loader = loadShow();
  try {
    // Call the requestAccountDeletion endpoint
    const response = await axios.post(
      'http://localhost:8000/api/users/delete-email',
      {}, 
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('authToken')}`,
        },
      }
    );

    // Show success message
    if (response.data && response.data.message) {
      alert(response.data.message); 
    } else {
      alert('A confirmation email has been sent to your email address. Please check your inbox.');
    }
  } catch (error) {
    console.error('Error sending confirmation email:', error);
    if (error.response && error.response.data && error.response.data.message) {
      alert(`Error: ${error.response.data.message}`); 
    } else {
      alert('Failed to send confirmation email. Please try again.');
    }
  } finally {
    loadHide(loader);
  }
};

const callLogout = () => {
  localStorage.removeItem('authToken');
  localStorage.removeItem('tokenType');
  router.push({ name: 'login' });
};

const initial = computed(() => {
  return username.value ? username.value.trim().charAt(0).toUpperCase() : '';
});

const formattedRole = computed(() => {
  if (role.value) {
    return role.value
      .split(' ')
      .map(word => word.charAt(0).toUpperCase() + word.slice(1))  // Capitalize first letter of each word
      .join(' ');
  }
  return '';
});

</script>