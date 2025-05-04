import api from "@/utils/api";

const fetchCategories = async () => {
  const response = await api.get(`/categories`);

  return response;
};

const findMatchingPilot = async (data) => {
  const response = await api.post(`/match-pilot/`, data);

  return response;
};

export { fetchCategories, findMatchingPilot };