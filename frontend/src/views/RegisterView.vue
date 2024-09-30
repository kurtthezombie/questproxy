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
  <div class="registration-form">
    <div class="registration-header">
      <svg class="gaming-icon" viewBox="0 0 24 24" width="40" height="40">
        <path d="M21 6H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-10 7H8v3H6v-3H3v-2h3V8h2v3h3v2zm4.5 2c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm4-3c-.83 0-1.5-.67-1.5-1.5S18.67 9 19.5 9s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z" fill="#FF69B4"/>
      </svg>
      <h1>Registration</h1>
    </div>
    <form @submit.prevent="submitForm">
      <div class="form-group">
        <input type="text" id="username" v-model="username" placeholder="Username" required>
      </div>

      <div class="form-group">
        <input type="text" id="first-name" v-model="f_name" placeholder="First Name" required>
      </div>

      <div class="form-group">
        <input type="text" id="last-name" v-model="l_name" placeholder="Last Name" required>
      </div>

      <div class="form-group">
        <input type="email" id="email" v-model="email" placeholder="Email" required>
      </div>

      <div class="form-group">
        <input type="password" id="password" v-model="password" placeholder="Password" required>
      </div>

      <div class="form-group">
        <input type="text" id="contact-number" v-model="contact_number" placeholder="Contact Number" required>
      </div>

      <div class="form-group">
        <select id="role" v-model="role" required>
          <option value="" disabled selected>Select Role</option>
          <option value="gamer">Online Games Enthusiast</option>
          <option value="game pilot">Game Pilot</option>
        </select>
      </div>

      <button type="submit">Register</button>
    </form>

    <router-link class="router-link-custom" to="/login">
      Login now
    </router-link>

    <h5>{{ message }}</h5>
  </div>
</template>

 
<style scoped>
.registration-form {
  max-width: 400px;
  margin: 50px auto;
  padding: 50px;
  background-color: rgba(0, 20, 30, 0.8);
  border-radius: 10px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
  color: #fff;
}

.registration-header {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.gaming-icon {
  margin-right: 10px;
}

h1 {
  font-size: 24px;
  margin: 0;
  font-family: 'Arial', sans-serif;
  text-transform: uppercase;
  letter-spacing: 1px;
}

form {
  display: flex;
  flex-direction: column;
}

.form-group {
  margin-bottom: 15px;
  display: flex;
  justify-content: center;
}

input, select {
  width: 100%;
  padding: 10px;
  border: none;
  background-color: rgba(0, 40, 60, 0.6);
  color: #fff;
  font-size: 16px;
}

input::placeholder, select {
  color: rgba(255, 255, 255, 0.7);
}

select {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url("data:image/svg+xml;utf8,<svg fill='white' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/><path d='M0 0h24v24H0z' fill='none'/></svg>");
  background-repeat: no-repeat;
  background-position-x: 100%;
  background-position-y: 5px;
}

button {
  padding: 10px;
  background-color: #FF69B4;
  border: none;
  border-radius: 5px;
  color: #fff;
  font-size: 18px;
  cursor: pointer;
  transition: background-color 0.3s;
}

button:hover {
  background-color: #FF1493;
}

.router-link-custom {
  display: inline-block;
  margin-top: 15px;
  color: #FF69B4;
  text-decoration: none;
}

.router-link-custom:hover {
  text-decoration: underline;
}

h5 {
  color: #FF69B4;
  margin-top: 15px;
}
</style>