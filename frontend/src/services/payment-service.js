import axios from 'axios';

const getPayments = async () => {
  try {
    const response = await axios.get('/api/payments');
    return response.data.data.payment;
  } catch (error) {
    console.error('Failed to fetch payments:', error);
    throw error;
  }
};

const initiatePayment = async (bookingId, successUrl, cancelUrl, authToken = null) => {
  try {
    const config = {
      headers: {
        'Content-Type': 'application/json',
      },
    };

    if (authToken) {
      config.headers['Authorization'] = `Bearer ${authToken}`;
    }

    const response = await axios.post(
      `/api/payments/${bookingId}`,
      {
        success_url: successUrl,
        cancel_url: cancelUrl,
      },
      config
    );

    return response.data.data.checkout_url;
  } catch (error) {
    console.error('Payment initiation failed:', error);
    throw error;
  }
};

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


export {
  getPayments,
  initiatePayment,
  verifyPayment,
  getUserPaidPayments
};
