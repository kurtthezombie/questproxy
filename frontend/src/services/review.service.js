import api from "@/utils/api";

const fetchServiceData = async (id) => {
  const response = await api.get(`reviews/${id}/info`);

  return response.data || {};
}

const submitReview = async (data) => {
  try {
    const response = await api.post(`/reviews/`, data);

    return response;
  } catch (error) {
    throw error;
  }
}

export { fetchServiceData, submitReview };
