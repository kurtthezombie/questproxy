import api from "@/utils/api";

export const submitOtp = async (formData) => {
      try {
            console.log("formData", formData);
            const response = await api.post("/otp/check", formData);
            console.log("Response in submit otp: ", response);
            return response;
      } catch (error) {
            throw new Error(error.response?.data?.message || 'Failed to verify OTP');
      }
}

export const resendOtp = async (email) => {
      try {
            const response = await api.post("/otp/resend", { email });
            return response.data;
      } catch (error) {
            throw new Error(error.response?.data?.message || 'Failed to resend OTP');
      }
}

export const cancelVerification = async (email) => {
      try {
            const response = await api.post("/otp/cancel", { email });
            return response.data;
      } catch (error) {
            throw new Error(error.response?.data?.message || 'Failed to cancel verification');
      }
}


