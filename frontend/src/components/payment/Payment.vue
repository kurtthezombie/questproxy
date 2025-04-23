<template>
    <div>
        <button class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-3 rounded-md text-lg transition-all shadow-lg w-full mb-2"
            @click="initiatePayment" :disabled="paymentLoading">
            <span v-if="paymentLoading">Processing Payment...</span>
            <span v-else>Proceed to Payment ({{ formatPrice(service?.price) }})</span>
        </button>

        <!-- Add this new button for checking payment status -->
        <button v-if="confirmedBooking.id" 
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-md text-lg transition-all shadow-lg w-full mb-2"
            @click="checkPaymentStatus"
            :disabled="statusLoading">
            <span v-if="statusLoading">Checking Status...</span>
            <span v-else>Check Payment Status</span>
        </button>

        <button @click="cancelBooking"
            class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white w-full">
            Cancel Booking
        </button>
        <div v-if="error" class="mt-4 bg-red-800/30 text-red-300 p-3 rounded-md text-sm">
            {{ error }}
        </div>
    </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue';
import { initiatePayment as initiatePaymentApi } from '@/services/payment-service.js';
import axios from 'axios';

const props = defineProps({
  confirmedBooking: {
    type: Object,
    required: true,
    validator: (value) => {
      console.log('Payment component received booking:', value); // Debugging
      return value && value.id; // Ensure booking has an ID
    }
  },
  service: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['cancel-booking']);

const paymentLoading = ref(false);
const statusLoading = ref(false);
const error = ref(null);

console.log("Payment component props:", props.confirmedBooking); // Debugging
const initiatePayment = async () => {
    if (!props.confirmedBooking || !props.confirmedBooking.id) {
        error.value = 'No booking found to proceed with payment.';
        return;
    }

    paymentLoading.value = true;
    error.value = null;

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

const checkPaymentStatus = async () => {
    statusLoading.value = true;
    error.value = null;

    try {
        const authToken = localStorage.getItem('authToken');
        if (!authToken) {
            throw new Error('You must be logged in to check payment status.');
        }

        // Fetch booking details which should include payment status
        const response = await axios.get(
            `http://127.0.0.1:8000/api/bookings/${props.confirmedBooking.id}`,
            {
                headers: {
                    Authorization: `Bearer ${authToken}`,
                },
            }
        );

        const booking = response.data.data || response.data;

        if (booking.status === 'completed' || booking.status === 'paid') {
            alert('Payment is already completed!');
            
        } 
        else if (booking.payment_url) {
            window.location.href = booking.payment_url;
        } 
        else {
            // Show current status
            alert(`Current payment status: ${booking.status || 'pending'}`);
        }
    } catch (err) {
        console.error('Error checking payment status:', err);
        error.value = err.response?.data?.message || err.message || 'Failed to check payment status.';
    } finally {
        statusLoading.value = false;
    }
};

const cancelBooking = () => {
    emit('cancel-booking');
};

const formatPrice = (price) => {
    return price ? `₱${Number(price).toLocaleString('en-US', { minimumFractionDigits: 2 })}` : '₱0.00';
};


</script>