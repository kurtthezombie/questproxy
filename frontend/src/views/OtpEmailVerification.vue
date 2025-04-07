<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import router from '@/router';
import { useLoader } from '@/services/loader-service';
import { cancelVerification, resendOtp, submitOtp } from '@/services/otp.service';

const email = ref('');
const otp = ref('');
const otpError = ref('');
const isOtpValid = ref(false);
const showCancelModal = ref(false);
const countdown = ref(0);
let countdownTimer = null;
const resendSuccess = ref(false);  // To track the OTP resend success

const { loadShow, loadHide } = useLoader();
const maskedEmail = computed(() => maskEmail(email.value));

const route = useRoute();
email.value = route.query.email;

// Validate OTP
const validateOtp = () => {
  // Remove any non-digit characters
  otp.value = otp.value.replace(/\D/g, '');
  
  if (otp.value.length === 0) {
    otpError.value = '';
    isOtpValid.value = false;
  } else if (otp.value.length < 6) {
    otpError.value = 'Please enter all 6 digits';
    isOtpValid.value = false;
  } else {
    otpError.value = '';
    isOtpValid.value = true;
  } 
};

const handleSubmitOtp = async () => {
    resendSuccess.value = false;
    const formData = {
        email: email.value,
        otp: otp.value,
    }
    const loader = loadShow();
    
    try {
        const response = await submitOtp(formData);

        console.log(response);
        router.push({ name: 'homepage' });
    } catch (error) {
        otpError.value = error.message || 'An unexpected error occurred';
    } finally {
        loadHide(loader);
    }
};

const handleResendOtp = async () => {
    const token = localStorage.getItem('authToken')?.trim();
    const loader = loadShow();
    try {
        const response = await resendOtp(email.value) 
        resendSuccess.value = true;
        console.log('OTP resent successfully.');
        otpError.value = '';

    } catch (error) {
        console.log('Resend error: ', error.response.data);
        otpError.value = error.message || 'An unexpected error occurred';
    } finally {
        loadHide(loader);
    }
}

// Cancel verification
const handleCancelVerification = async () => {
    // Here you would typically make an API call to cancel the verification and delete the record
    try {
        await cancelVerification(email.value);
        showCancelModal.value = false;
        router.push({ name: 'signup' });
    } catch (error) {
        otpError.value = 'An error occurred while canceling the verification.';
        console.error('Error canceling verification:', error);
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

// Start countdown timer
const startCountdown = () => {
    countdownTimer = setInterval(() => {
        if (countdown.value > 0) {
            countdown.value--;
        } else {
            clearInterval(countdownTimer);
        }
    }, 1000);
};

const handleShowCancelModal = () => {
    showCancelModal.value = true;
    resendSuccess.value = false; 
};

// Lifecycle hooks
onMounted(() => {
    startCountdown();
});

onUnmounted(() => {
    if (countdownTimer) {
        clearInterval(countdownTimer);
    }
});

</script>


<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-800 ">
        <div class="w-full max-w-md bg-white rounded-lg shadow-md p-6 space-y-6">
            <form class="space-y-6" @submit.prevent="handleSubmitOtp">
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-gray-900">Verify Your Email</h1>
                    <p class="mt-2 text-gray-600">
                        We've sent a 6-digit verification code to your email address at <span class="font-bold">{{
                            maskedEmail }}</span>.
                        Please enter it below to continue.
                    </p>
                </div>

                <!-- OTP INPUT -->
                <div class="space-y-4">
                    <div>
                        <label for="otp" class="block text-sm font-medium text-gray-700 mb-1">
                            Verification Code
                        </label>
                        <input type="hidden" v-model="email">
                        <input id="otp" v-model="otp" type="text" maxlength="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-md text-center text-xl tracking-widest focus:ring-2 focus:ring-primary focus:border-primary"
                            placeholder="Enter 6-digit code" @input="validateOtp" />
                        <p v-if="otpError" class="mt-1 text-sm text-red-600">
                            {{ otpError }}
                        </p>
                    </div>

                    <!-- Timer and Resend -->
                    <div class="flex justify-between items-center">
                        <p v-if="countdown > 0" class="text-sm text-gray-500">
                            Resend in {{ countdown }}s
                        </p>
                        <button v-else @click="handleResendOtp"
                            class="text-sm font-medium text-green-900 hover:text-green-400 focus:outline-none transition duration-100 ease-in-out"
                            type="button">
                            Resend Code
                        </button>
                    </div>
                </div>
                <!-- Show success message if OTP is resent -->
                <div v-if="resendSuccess" class="bg-green-100 text-green-800 p-2 text-center rounded">
                    OTP has been resent successfully!
                </div>
                <!-- ACTION BUTTONS -->
                <div class="space-y-3">
                    <button @click="handleSubmitOtp" :disabled="!isOtpValid"
                        class="w-full py-3 px-4 bg-green-700 text-white font-medium rounded-md shadow-sm hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 disabled:cursor-not-allowed"
                        type="button">
                        Verify
                    </button>
                    <button @click="handleShowCancelModal"
                        class="w-full py-3 px-4 bg-white text-gray-700 font-medium rounded-md border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                        type="button">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
        <!-- Cancel Confirmation Modal -->
        <div v-if="showCancelModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Are you sure you want to cancel?
                </h3>
                <p class="text-gray-600 mb-6">
                    If you cancel now, your verification process will be terminated and your record will be deleted.
                </p>
                <div class="flex justify-end space-x-3">
                    <button @click="showCancelModal = false"
                        class="px-4 py-2 bg-white text-gray-700 font-medium rounded-md border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                        type="button">
                        Stay Here
                    </button>
                    <button @click="handleCancelVerification"
                        class="px-4 py-2 bg-red-600 text-white font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        type="button">
                        Yes, Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>