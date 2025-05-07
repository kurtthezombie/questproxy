import axios from 'axios';
import api from '@/utils/api';

const getPayments = async () => {
  try {
    const response = await axios.get('/api/payments');
    return response.data.data.payment;
  } catch (error) {
    console.error('Failed to fetch payments:', error);
    throw error;
  }
};

export async function initiatePayment(bookingId) {
  return api.post(`/payments/${bookingId}`);
}

const verifyPayment = async (transactionId) => {
  try {
    const response = await axios.get(`/api/payments/success/${transactionId}`);
    return response.data;
  } catch (error) {
    console.error('Payment verification failed:', error);
    throw error;
  }
};

const getUserPaidPayments = async (userId) => {
  try {
    const response = await axios.get(`/api/users/${userId}/payments/paid`);
    return response.data.data.payments;
  } catch (error) {
    console.error('Failed to fetch user payments:', error);
    throw error;
  }
};

export async function createCheckoutSession(bookingId) {
  return api.post(`/payments/${bookingId}`);
}

export {
  getPayments,
  verifyPayment,
  getUserPaidPayments
};
