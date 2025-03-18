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

const fetchPortfolios = async (pilot_id) => {
      try {
        const response = await api.get(`/portfolios/${pilot_id}`);
        return response.portfolios || [];
      } catch (error) {
        console.error("Error fetching portfolios: ", error);
        return [];
      }
}

export { createPortfolio, fetchPortfolios };