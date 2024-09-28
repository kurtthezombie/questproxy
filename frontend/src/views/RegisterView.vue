<script setup>

import { ref } from 'vue'
import { useRouter } from 'vue-router'
import loginservice from '@/services/login-service';

const username = ref('');
const email = ref('');
const f_name = ref('');
const l_name = ref('');
const password = ref('');
const contact_number = ref('');
const role = ref('');
const message = ref('');

const router = useRouter();

const submitForm = async () => {
    const formData = {
        username: username.value,
        email: email.value,
        f_name: f_name.value,
        l_name: l_name.value,
        password: password.value,
        contact_number: contact_number.value,
        role: role.value
    };
    try {
        console.log('calling register')
        const response = await loginservice.register(formData);
        console.log('after register')
        if(response.status) {
            console.log('inside status true')
            username.value = '';
            email.value = '';
            f_name.value ='';
            l_name.value = '';
            password.value = '';
            contact_number.value = '';
            role.value = '';

            router.push({ path: '/login', query: { message: 'Registration successful!' } });
        }
        message.value = response.message;
    } catch (error) {
        console.log('Error message: ', error);
    }

}

</script>

<template>
    <div class="registration">
      <form @submit.prevent="submitForm">
        <h1>Registration</h1>
        <label for="username">Username:</label>
        <input type="text" id="username" v-model="username" required>
        <br>
  
        <label for="first-name">First Name:</label>
        <input type="text" id="first-name" v-model="f_name" required>
        <br>
  
        <label for="last-name">Last Name:</label>
        <input type="text" id="last-name" v-model="l_name" required>
        <br>
  
        <label for="email">Email:</label>
        <input type="email" id="email" v-model="email" required>
        <br>
  
        <label for="password">Password:</label>
        <input type="password" id="password" v-model="password" required>
        <br>
  
        <label for="contact-number">Contact Number:</label>
        <input type="text" id="contact-number" v-model="contact_number" required>
        <br>
  
        <label for="role">Role:</label>
        <select id="role" v-model="role" required>
          <option value="gamer">Online Games Enthusiast</option>
          <option value="game pilot">Game Pilot</option>
        </select>
        <br>
  
        <button type="submit">Register</button>
      </form>
  
      <router-link class="mt-5 bg-slate-200 text-slate-900 px-5 py-2 border border-slate-900 rounded text-center hover:shadow-xl" to="/login">
        Login now
      </router-link>
  
      <h5>{{ message }}</h5>
    </div>
  </template>
