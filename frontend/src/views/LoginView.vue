<script setup>
import { ref, onMounted } from 'vue';
import loginservice from '@/services/login-service';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const username = ref('');
const password = ref('');
const message = ref('');

onMounted(() => {
    if (route.query.message) {
        message.value = route.query.message;
    }
});

const submitForm = async () => {
    const formData = {
        username: username.value,
        password: password.value,
    };
    try {
        const response = await loginservice.login(formData);
        username.value = '';
        password.value = '';
        message.value = response.message;
        router.push({ name: 'dashboard' });
    } catch (error) {
        console.log('Login error: ', error);
        message.value = 'Login failed. Please try again.';
    }
};
</script>


<template>
  <div class="login-form">
    <div class="login-header">
      <svg class="gaming-icon" viewBox="0 0 24 24" width="40" height="40">
        <path d="M21 6H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-10 7H8v3H6v-3H3v-2h3V8h2v3h3v2zm4.5 2c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm4-3c-.83 0-1.5-.67-1.5-1.5S18.67 9 19.5 9s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z" fill="#FF69B4"/>
      </svg>
      <h1> Login </h1>
    </div>
    <h4>{{ message }}</h4>
    <form @submit.prevent="submitForm">
      <div class="form-group">
        <input type="text" id="username" v-model="username" placeholder="User Name or email..." required>
      </div>

      <div class="form-group">
        <input type="password" id="password" v-model="password" placeholder="Password" required>
      </div>

      <button type="submit">Submit</button>
    </form>
    
    <router-link class="router-link-custom" to="/signup">
      Sign up
    </router-link>
  </div>
</template>

<style scoped>
.login-form {
  max-width: 400px;
  margin: 50px auto;
  padding: 50px;
  background-color: rgba(0, 20, 30, 0.8);
  border-radius: 10px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
  color: #fff;
}

.login-header {
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

h4 {
  color: #FF69B4;
  margin-bottom: 20px;
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

input {
  width: 100%;
  padding: 10px;
  border: none;
  background-color: rgba(0, 40, 60, 0.6);
  color: #fff;
  font-size: 16px;
}

input::placeholder {
  color: rgba(255, 255, 255, 0.7);
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
</style>