<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import router from '@/router';
import { useLoader } from '@/services/loader-service';

const email = ref('');
const otp = ref('');
const message = ref('');
const { loadShow, loadHide } = useLoader();
const maskedEmail = computed(() => maskEmail(email.value));

const route = useRoute();
email.value = route.query.email;

const submitOtp = async () => {
    const formData = {
        email: email.value,
        otp: otp.value,
    }
    const token = localStorage.getItem('authToken')?.trim();
    console.log(`Bearer ${token}`);
    const loader = loadShow();
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
        message.value = error.response.data.message;
    } finally {
        loadHide(loader);
    }
};

const maskEmail = (email) => {
    if (!email.includes('@')) return email;

    const [username, domain] = email.split('@');
    const domainParts = domain.split('.');

    if (username.length < 2 || domainParts.length < 2) return email;
    const maskedUsername = username[0] + '***';
    const maskedDomain = domainParts[0][0] + '***';

    return `${maskedUsername}@${maskedDomain}.${domainParts.slice(1).join('.')}`;
}

</script>


<template>
    <div class="flex h-screen items-center justify-center">
        <div class="w-full max-w-sm rounded-lg border border-green-200 bg-white p-4 shadow-lg sm:p-6 md:p-8">
            <form class="space-y-6" @submit.prevent="submitOtp">
                <h5 class="text-xl font-medium text-gray-900">OTP Verification</h5>
                <h3 class="text-red-800 bg-red-100 text-center p-1" v-if="message">{{ message }}</h3>
                <div>
                    <label for="email"
                        class="mb-2 block text-sm  text-center text-lime-700 font-medium bg-lime-100 p-3">A six-digit
                        OTP was sent to your email: {{ maskedEmail }}</label>
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