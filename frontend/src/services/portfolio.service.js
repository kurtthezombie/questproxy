import api from "@/utils/api";

const createPortfolio = async (data) => {
      try {
            const response = await api.post('/portfolios/create', data, {
                  headers: { "Content-Type" : "multipart/form-data" } 
            });
            return response;
      } catch (error) {
            console.error("Axios Error:", error.response ? error.response.data : error);
            throw error;
      }
};

const fetchPortolios = async (pilotId) => {
      try {
        const response = await api.get(`/portfolios/${pilotId}`);
        return response.data;
      } catch (error) {
        console.error("Error fetching portfolios: ", error);
        return [];
      }
}

export { createPortfolio, fetchPortolios };