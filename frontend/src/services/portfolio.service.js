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

export { createPortfolio };