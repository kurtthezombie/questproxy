import api from "@/utils/api";

const fetchCategories = async () => {
  const response = await api.get(`/categories`);

  return response;
};

const findMatchingPilot = async (data) => {
  const response = await api.post(`/match-pilot/`, data);

  return response;
};

const findMatchingServices = async (data) => {
  try {
    const response = await api.post(`/match-services/`, data);
    return response;
  } catch (error) {
    console.error('Error finding matching services:', error);
    throw error;
  }
};

export { fetchCategories, findMatchingPilot, findMatchingServices };