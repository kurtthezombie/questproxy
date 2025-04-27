import api from "@/utils/api";

const fetchData = async (id) => {
  const response = await api.get(`/services/${id}/details`);

  return response.service;
};

const createBooking = async (data) => {
  const response = await api.post(`/bookings/store`, data);
  console.log('response: ', response);
  return response.booking;
}

const fetchPaymentUrl = async (cancelUrl, bookingId) => {
  const formData = {
    cancel_url: cancelUrl,
  }
  const response = await api.post(`/payments/${bookingId}`, formData);
  console.log('response: ', response);
  return response.checkout_url;
}

export { fetchData, createBooking, fetchPaymentUrl };