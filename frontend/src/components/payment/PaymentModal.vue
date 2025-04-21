<template>
    <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md mx-4">
            <h2 class="text-xl font-bold mb-4 text-center dark:text-white">Confirm Payment</h2>
            <p class="text-gray-600 dark:text-gray-300 text-center mb-6">
                You are about to be redirected to PayMongo to complete your payment.
            </p>

            <div class="mt-6 flex justify-between">
                <button @click="closeModal"
                    class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white">
                    Cancel
                </button>
                <button @click="initiatePayment" :disabled="paymentLoading"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 flex items-center justify-center gap-2">
                    <span v-if="!paymentLoading">Proceed to PayMongo</span>
                    <svg v-else class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span v-if="paymentLoading">Processing...</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { initiatePayment as initiatePaymentApi } from '@/services/payment-service.js';

const props = defineProps({
    isOpen: {
        type: Boolean,
        required: true
    },
    confirmedBooking: {
        type: Object,
        required: true,
    },
    service: {
        type: Object,
        required: true,
    },
    closeModal: {
        type: Function,
        required: true,
    }
});

const paymentLoading = ref(false);
const error = ref(null);

const initiatePayment = async () => {
    paymentLoading.value = true;
    try {
        const authToken = localStorage.getItem('authToken');
        if (!authToken) {
            throw new Error('You must be logged in to make a payment.');
        }

        const successUrl = window.location.origin + `/payment-success/${props.confirmedBooking.id}`;
        const cancelUrl = window.location.origin + `/payment-cancelled/${props.confirmedBooking.id}`;

        const checkoutUrl = await initiatePaymentApi(
            props.confirmedBooking.id,
            successUrl,
            cancelUrl,
            authToken
        );

        window.location.replace(checkoutUrl);
    } catch (err) {
        console.error('Payment initiation failed:', err);
        error.value = err.response?.data?.message || err.message || 'Failed to initiate payment.';
    } finally {
        paymentLoading.value = false;
    }
};
const closeModal = () => {
    emit('close-modal');
};
defineEmits(['close-modal'])
</script>
