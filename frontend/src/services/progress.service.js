import api from "@/utils/api";

const fetchBookingData = async (bookingId) => {
  const req = await api.get(`bookings/${bookingId}/with-relations`);
  console.log(req.data);
  return req.data;
}

const fetchProgressLogs = async (bookingId) => {
  const req = await api.get(`progress/booking/${bookingId}`);

  return req.data;
}

const createProgressUpdate = async (data) => {
  
  data.forEach((value, key) => {
    console.log(`${key}:`, value);
  });
  try {
    const response = await api.post('/progress/', data, {
      headers: { "Content-Type": "multipart/form-data" }
    });
    return response;
  } catch (error) {
    console.error("Axios Error:", error.response ? error.response.data : error);
    throw error;
  }
};

const deleteProgress = async (id) => {
  const req = await api.delete(`/progress/${id}`);
  
  return req.data;
}


export { fetchBookingData, fetchProgressLogs, createProgressUpdate };