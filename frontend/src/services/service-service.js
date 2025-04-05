import axios from 'axios';
import { useAuthStore } from '@/stores/authStore';


const getAuthHeaders = () => {
  const authStore = useAuthStore();
  return {
    headers: {
      'Authorization': `Bearer ${authStore.token}`,
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }
  };
};

export default {
  async fetchUserDataById(userId) {
    try {
     
      const response = await axios.get(`http://127.0.0.1:8000/api/pilots/${userId}/services`, getAuthHeaders());
      return response.data.data || []; 
    } catch (error) {
      console.error('Error fetching user services:', error);
      throw error;
    }
  },
  
  async fetchAllServices() {
    try {
      const response = await axios.get(`http://127.0.0.1:8000/api/services`, getAuthHeaders());
      return response.data.data || []; 
    } catch (error) {
      console.error('Error fetching all services:', error);
      throw error;
    }
  },
  
  async fetchPilotServices(pilotId) {
    try {
      const response = await axios.get(`http://127.0.0.1:8000/api/pilots/${pilotId}/services`, getAuthHeaders());
      return response.data.data || [];
    } catch (error) {
      console.error('Error fetching pilot services:', error);
      throw error;
    }
  },
  
  async createService(serviceData) {
    try {
      const response = await axios.post(`http://127.0.0.1:8000/api/services/create`, serviceData, getAuthHeaders());
      return response.data;
    } catch (error) {
      console.error('Error creating service:', error);
      throw error;
    }
  },
  
  async updateService(serviceId, serviceData) {
    try {
      const response = await axios.patch(`'http://127.0.0.1:8000/api'/services/edit/${serviceId}`, serviceData, getAuthHeaders());
      return response.data;
    } catch (error) {
      console.error('Error updating service:', error);
      throw error;
    }
  },
  
  async deleteService(serviceId) {
    try {
      const response = await axios.delete(`http://127.0.0.1:8000/api/services/destroy/${serviceId}`, getAuthHeaders());
      return response.data;
    } catch (error) {
      console.error('Error deleting service:', error);
      throw error;
    }
  },
  
  async getServiceById(serviceId) {
    try {
      const response = await axios.get(`http://127.0.0.1:8000/api/services/${serviceId}`, getAuthHeaders());
      return response.data.data || null;
    } catch (error) {
      console.error('Error fetching service details:', error);
      throw error;
    }
  }
};