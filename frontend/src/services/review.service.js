import api from "@/utils/api";

const fetchServiceData = async (id) => {
  const response = await api.get(`reviews/${id}/info`);

  return response.data || {};
}

export { fetchServiceData };