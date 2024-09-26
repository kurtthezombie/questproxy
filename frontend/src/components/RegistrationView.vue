<template>
    <div id="registration-view" class="h-screen w-screen bg-slate-400 text-slate-900 flex justify-center items-center px-10 md:px-44 xl:px-96">
      <div id="form-regis" class="bg-slate-200 rounded-lg shadow-2xl w-screen px-5 md:px-10 xl:px-16 py-8 md:py-10">
        <div class="flex flex-col items-center">
          <img class="w-20" src="../assets/img/qplogo2.png" alt="">
          <h1 class="text-center text-black pb-5 md:mb-10">
            Registration Page
          </h1>
          <div v-if="registrationStatus === 'success'" class="text-green-500 text-center mb-4">
                Registration successful!
            </div>
            <div v-else-if="registrationStatus === 'error'" class="text-red-500 text-center mb-4">
                Registration failed.
            </div>
        </div>
        <form @submit.prevent="register(user)" id="form" class="flex flex-col">
          <label class="mb-1" for="username">User Name</label>
          <input class="mb-5 px-2 py-2" v-model="user.username" id="username" type="text" placeholder="Enter your User Name" required>
  
          <label class="mb-1" for="email">Email</label>
          <input class="mb-5 px-2 py-2" v-model="user.email" type="email" name="email" id="email" placeholder="Enter your email" required>
  
          <label class="mb-1" for="email">Password</label>
          <input class="px-2 py-2" v-model="user.password" type="password" name="password" id="password" placeholder="Enter your password" required>
  
          <label class="mb-1" for="f_name">First Name</label>
          <input class="mb-5 px-2 py-2" v-model="user.f_name" id="f_name" type="text" placeholder="Enter your First Name" required>
  
          <label class="mb-1" for="l_name">Last Name</label>
          <input class="mb-5 px-2 py-2" v-model="user.l_name" id="l_name" type="text" placeholder="Enter your Last Name" required>
  
          <label class="mb-1" for="contact_number">Contact Number</label>
          <input class="mb-5 px-2 py-2" v-model="user.contact_number" id="contact_number" type="text" placeholder="Enter your Contact Number" required>
  
          <label class="mb-1" for="status">Status</label>
          <input class="mb-5 px-2 py-2" v-model="user.status" id="status" type="text" placeholder="Statussy" required>
  
          <label class="mb-1" for="role">Role</label>
          <select class="mb-5 px-2 py-2" v-model="user.role" id="role" required placeholder="Are you a gamer or a game pilot">
            <option value="gamer">Gamer</option>
            <option value="game_pilot">Game Pilot</option>
          </select>
  
          <div class="flex justify-start items-center mt-5">
            <button class="bg-slate-900 text-white px-5 py-2 rounded hover:shadow-xl text-center">
              <template v-if="!isLoading">
                Sign up
              </template>
              <template v-else>
                <img class="h-6" alt="loading" src="data:image/gif;base64,..." />
              </template>
            </button>
            <router-link class="ml-3 justify-end hover:underline" to="/login">Cancel</router-link>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  import { ref, reactive } from 'vue';
  import axios from 'axios';
  
  export default {
    setup() {
      const user = reactive({
        username: '',
        email: '',
        password: '',
        f_name: '',
        l_name: '',
        contact_number: '',
        status: '',
        role: '',
      });
      
      const isLoading = ref(false);
      const registrationStatus = ref(null);
  
      const register = async () => {
            isLoading.value = true;
            try {
                const { data } = await axios.post('http://127.0.0.1:8000/api/signup', user);
                console.log('Registration successful:', data.message);
                registrationStatus.value = 'success';
                
                user.username = '';
                user.email = '';
                user.password = '';
                user.f_name = '';
                user.l_name = '';
                user.contact_number = '';
                user.status = '';
                user.role = '';

            } catch (error) {
                console.error('Error registering user:', error.response?.data?.message);
                registrationStatus.value = 'error';
            } finally {
                isLoading.value = false;
            }
        };
    
      return {
        user,
        isLoading,
        registrationStatus,
        register,
      };
    },
  };
  </script>