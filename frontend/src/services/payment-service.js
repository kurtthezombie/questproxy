import axios from 'axios';

const API_BASE_URL = 'http://127.0.0.1:8000/api';

const getAuthHeader = () => {
  const authToken = localStorage.getItem('authToken');
  return {
    headers: {
      Authorization: `Bearer ${authToken}`,
      'Content-Type': 'application/json'
    }
  };
};

export const getPayments = async () => {
  try {
    const response = await axios.get(`${API_BASE_URL}/payments`, getAuthHeader());
    return response.data.data?.payment || [];
  } catch (error) {
    console.error('Failed to fetch payments:', {
      error: error.message,
      response: error.response?.data
    });
    throw new Error(error.response?.data?.message || 'Failed to fetch payments');
  }
};

export const initiatePayment = async (bookingId, successUrl, cancelUrl) => {
  try {
    const response = await axios.post(
      `${API_BASE_URL}/payments/${bookingId}`,
      { success_url: successUrl, cancel_url: cancelUrl },
      getAuthHeader()
    );

    if (!response.data.data) {
      throw new Error('Invalid payment response structure');
    }

    return {
      checkout_url: response.data.data.checkout_url,
      transaction_id: response.data.data.transaction_id
    };

  } catch (error) {
    console.error('Payment initiation failed:', {
      error: error.message,
      bookingId,
      response: error.response?.data
    });
    throw new Error(error.response?.data?.message || 'Payment initiation failed');
  }
};

export const verifyPayment = async (transactionId) => {
  try {
    const response = await axios.get(
      `${API_BASE_URL}/payments/success/${transactionId}`,
      getAuthHeader()
    );
    
    if (response.data.status !== true) {
      throw new Error(response.data.message || 'Payment verification failed');
    }

    return {
      status: 'paid',
      transactionId,
      bookingId: response.data.data?.booking_id
    };

  } catch (error) {
    console.error('Payment verification failed:', {
      error: error.message,
      transactionId,
      response: error.response?.data
    });
    throw new Error(error.response?.data?.message || 'Payment verification failed');
  }
};