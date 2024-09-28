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
  <h4>{{ message }}</h4>
    <div class="login-form">
      <div class="flex flex-col items-center">
        <!--<img id="qplogo" class="w-auto h-auto" src="../assets/img/qplogo2.png" alt="">-->
        <p class="text-center">Login to your account</p>
      </div>
      <form @submit.prevent="submitForm">
        <label for="">Username</label> <br>
        <input type="username" v-model="username"> <br>
        <label for="">Password</label><br>
        <input type="password" v-model="password"> <br>
        <button type="submit">Login</button>
      </form>
      <router-link class="mt-5 bg-slate-200 text-slate-900 px-5 py-2 border border-slate-900 rounded text-center hover:shadow-xl" to="/signup">
                  Signup now
        </router-link>
    </div>
  </template>

<style scoped>
.qplogo {
  max-width: 100px;
  max-height: 100px;
}
</style>