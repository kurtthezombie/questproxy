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

const initiatePayment = async (bookingId, successUrl, cancelUrl, authToken) => {
  try {
    console.log('Initiating payment for booking:', bookingId);
    
    const response = await axios.post(
      `http://127.0.0.1:8000/api/payments/${bookingId}`,
      {
        success_url: successUrl,
        cancel_url: cancelUrl
      },
      {
        headers: {
          Authorization: `Bearer ${authToken}`,
          'Content-Type': 'application/json'
        }
      }
    );

    console.log('Payment initiation response:', response.data);
    return response.data.checkout_url;

  } catch (err) {
    console.error('Payment initiation error details:', {
      status: err.response?.status,
      data: err.response?.data,
      message: err.message
    });
    throw err;
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
