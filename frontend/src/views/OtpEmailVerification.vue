<script setup>
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import router from '@/router';

const email = ref('');
const otp = ref('');
const message = ref('');

const route = useRoute();
email.value = route.query.email;

const submitOtp = async () => {
    /*
        const token = localStorage.getItem('authToken');
      try {
        const response = await axios.get(`http://127.0.0.1:8000/api/user`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
    */
    const formData = {
        email: email.value,
        otp: otp.value,
    }
    const token = localStorage.getItem('authToken');
    try{ 
        const response = await axios.post('http://127.0.0.1:8000/api/check-otp', formData, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        
        console.log(response.data);
        router.push({ name: 'homepage' });  
    } catch (error) {
        console.log("ni error man", error.response.data);
    } //to do: add loader if pwede
};
</script>


<template>
    <div class="flex h-screen items-center justify-center">
        <div class="w-full max-w-sm rounded-lg border border-green-200 bg-white p-4 shadow-lg sm:p-6 md:p-8">
            <form class="space-y-6" @submit.prevent="submitOtp">
                <h5 class="text-xl font-medium text-gray-900">OTP Verification</h5>
                <h3>{{  message }}</h3>
                <div>
                    <label for="email"
                        class="mb-2 block text-sm  text-center text-lime-700 font-medium bg-lime-100 p-3">A six-digit
                        OTP was sent to your email: {{ email.value }}</label>
                    <input type="hidden" v-model="email">
                    <input v-model="otp" type="text"
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="ex. 123456" required />
                </div>
                <button type="submit"
                    class="w-full rounded-lg bg-lime-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-lime-800 focus:outline-none focus:ring-4 focus:ring-lime-300">Submit</button>
            </form>
        </div>
    </div>

</template>