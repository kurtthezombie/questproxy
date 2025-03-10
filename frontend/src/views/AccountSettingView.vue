<template>
  <div class="max-h-screen bg-gray-900 text-white">
    <!-- Header -->
    <NavBar :username="username" :email="email" :role="role" :callLogout="callLogout" />
    
    <!-- Sidebar and Account Form -->
    <div class="flex">
      <Sidebar />
      <div class="flex-1 bg-white flex justify-center items-start py-8">
        <div class="w-full p-8 bg-white shadow-lg rounded-lg">
          <h2 class="text-2xl font-semibold text-gray-800 mb-6">Personal</h2>

          <!-- Success message -->
          <div v-if="message" class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-300 dark:border-green-800" role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 me-3" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div>
              <span class="font-medium">Success!</span> {{ message }}
            </div>
          </div>

          <!-- Update Account Form -->
          <form @submit.prevent="UpdateForm">
            <div class="grid gap-6 mb-6 md:grid-cols-2">
              <!-- Form Fields for Name, Email, and Contact Number -->
              <div>
                <label for="firstName" class="block mb-2 text-sm font-medium text-gray-800">First Name</label>
                <input type="text" id="firstName" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" v-model="firstName" required />
              </div>
              <div>
                <label for="lastName" class="block mb-2 text-sm font-medium text-gray-800">Last Name</label>
                <input type="text" id="last_name" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" v-model="lastName" required />
              </div>
              <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-800">Email</label>
                <input type="email" id="email" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" v-model="email" required />
              </div>
              <div>
                <label for="contact_number" class="block mb-2 text-sm font-medium text-gray-800">Contact Number</label>
                <input type="tel" id="contact_number" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" v-model="contactNumber" placeholder="123-456-7890" required />
              </div>
            </div>
            <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Save</button>
          </form>

          <!-- Account Deletion Section -->
          <div class="mt-10">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Delete Account</h2>
            <p class="text-gray-700 mb-4">This action cannot be undone. Confirm if you want to delete your account.</p>
            <form @submit.prevent="confirmDeletion">
              <button type="submit" class="bg-red-500 text-white font-semibold py-2 px-4 rounded hover:bg-red-600 transition duration-200">Delete My Account</button>
            </form>
          </div>
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
import { useLoader } from '@/services/loader-service';
import NavBar from '@/components/NavBar.vue';

const { loadShow, loadHide } = useLoader();
const router = useRouter();

const role = ref(null);
const username = ref('');
const email = ref('');
const firstName = ref('');
const lastName = ref('');
const contactNumber = ref('');
const userId = ref('');
const message = ref(''); 

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

// Delete Account Function
const confirmDeletion = async () => {
  const loader = loadShow();
  try {
    const response = await axios.delete(`http://localhost:8000/api/users/delete/${userId.value}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('authToken')}`,
      },
    });
    alert('Account has been deleted.');
    router.push({ name: 'home' }); 
  } catch (error) {
    console.error('Error deleting account:', error);
  } finally {
    loadHide(loader);
  }
};

const callLogout = () => {
  localStorage.removeItem('authToken');
  localStorage.removeItem('tokenType');
  router.push({ name: 'login' });
};
</script>

<style scoped>
/* Add any additional styles here if necessary */
</style>
