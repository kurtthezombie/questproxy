import api from "@/utils/api";

const submitReport = async (report) => {
  try {
    const response = await api.post("/reports/", report);
    return response.data || {};
  } catch (error) {
    console.error("Error submitting report: ", error);
    throw error;
  }
};

const fetchMyReports = async (page = 1) => {
  try {
    console.log("reached in service");
    const response = await api.get(`/reports?page=${page}`);
    console.log("The data: ",response);
    return response.reports || [];
  } catch (error) {
    console.error("Error fetching reports: ", error);
    throw error;
  }
}

export { submitReport, fetchMyReports };