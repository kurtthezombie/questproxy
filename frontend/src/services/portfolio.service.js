import api from "@/utils/api";

const createPortfolio = async (data) => {
  try {
    const response = await api.post('/portfolios/create', data, {
      headers: { "Content-Type": "multipart/form-data" }
    });
    return response;
  } catch (error) {
    console.error("Axios Error:", error.response ? error.response.data : error);
    throw error;
  }
};

const fetchPortfolios = async (pilot_id) => {
  try {
    const response = await api.get(`/portfolios/${pilot_id}`);
    return response.portfolios || [];
  } catch (error) {
    console.error("Error fetching portfolios: ", error);
    return [];
  }
}

const updatePortfolio = async (id, data) => {
  try {
    const response = await api.post(`/portfolios/edit/${id}`, data, {
      headers: { "Content-Type": "multipart/form-data" }
    });
    return response;
  } catch (error) {
    console.error("Error updating portfolio: ", error);
    throw error;
  }
}

const deletePortfolio = async (id) => {
  try {
    const response = await api.delete(`/portfolios/delete/${id}`);
    return response;
  } catch (error) {
    console.error("Error deleting portfolio: ", error);
    throw error;
  }
}

const fetchUserByUsername = async (username) => {
  try {
    const response = await api.get(`/users/username/${username}`);
    return response.data || {};
  } catch (error) {
    throw error;
  }
}

const fetchPortfoliosByUser = async (id) => {
  try {
    const response = await api.get(`/portfolios/user/${id}`);
    return response || {};
  } catch (error) {
    throw error;
  }
}

const fetchPointsByUsername = async (username) => {
  try {
    const response = await api.get(`portfolios/user/points/${username}`);
    console.log("Points by user response: ", response);
    return response.points;
  } catch (error) {
    throw error;
  }
}

export { createPortfolio, fetchPortfolios, updatePortfolio, deletePortfolio, fetchUserByUsername, fetchPortfoliosByUser, fetchPointsByUsername };