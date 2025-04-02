import api from "@/utils/api";

const submitReport = async (report) => {
  try {
    const response = await api.post("/reports", report);
    console.log(response.data);
    return response.data || {};
  } catch (error) {
    console.error("Error submitting report: ", error);
    throw error;
  }
};

export { submitReport };