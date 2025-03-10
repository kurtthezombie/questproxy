import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axios from 'axios';

export const useServiceStore = defineStore('service', () => {

    const services = ref([]);
    const categories = ref([]);
    const loading = ref(false);
    const error = ref(null);
    const message = ref('');
    const service = ref(null);
    const pilots = ref([]);

    const hasServices = computed(() => services.value.length > 0);
    const categoriesLoaded = computed(() => categories.value.length > 0);

    const clearServices = () => {
        services.value = [];
        categories.value = [];
        error.value = null;
        message.value = '';
    };
    

    const fetchCategories = async () => {
        loading.value = true;
        try {
            const response = await axios.get('http://127.0.0.1:8000/api/categories');
            categories.value = response.data.categories;
            console.log("Fetched categories:", categories.value); 
            error.value = null;
        } catch (err) {
            error.value = err.message || 'Error fetching categories';
            console.error('Error fetching categories:', err);
        } finally {
            loading.value = false;
        }
    };

    const createService = async (serviceData) => {
        loading.value = true;
        try {
            const response = await axios.post(
                'http://127.0.0.1:8000/api/services/create',
                serviceData,
                {
                    headers: {
                        Authorization: `${localStorage.getItem('tokenType')} ${localStorage.getItem('authToken')}`
                    }
                }
            );
            message.value = 'Your service has been successfully created!';
            services.value.push(response.data.service);
            error.value = null;
            
            setTimeout(() => {
                message.value = '';
            }, 2000);
            
            return response.data;
        } catch (err) {
            error.value = err.message || 'Error creating service';
            message.value = 'Error creating service. Please try again.';
            setTimeout(() => {
                message.value = '';
            }, 2000);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const fetchUserServices = async () => {
        loading.value = true;
        try {
            const response = await axios.get('http://127.0.0.1:8000/api/services', {
                headers: {
                    Authorization: `${localStorage.getItem('tokenType')} ${localStorage.getItem('authToken')}`
                }
            });
            services.value = response.data.services;
            console.log("Fetched services:", services.value); 
            error.value = null;
        } catch (err) {
            error.value = err.message || 'Error fetching services';
            console.error('Error fetching services:', err);
        } finally {
            loading.value = false;
        }
    };

    const fetchServiceById = async (serviceId) => {
        loading.value = true;
        try {
            const response = await axios.get(`http://127.0.0.1:8000/api/services/${serviceId}`, {
                headers: {
                    Authorization: `${localStorage.getItem('tokenType')} ${localStorage.getItem('authToken')}`,
                },
            });
            service.value = response.data.service; 
            error.value = null;
        } catch (err) {
            error.value = err.message || 'Error fetching service';
            console.error('Error fetching service:', err);
        } finally {
            loading.value = false;
        }
    };
    
    const fetchServicesByPilot = async (pilot_id) => {
        loading.value = true;
        try {
            const response = await axios.get(
                `http://127.0.0.1:8000/api/pilots/${pilot_id}/services`,
                {
                    headers: {
                        Authorization: `${localStorage.getItem('tokenType')} ${localStorage.getItem('authToken')}`
                    }
                }
            );
            services.value = response.data.services;
            console.log("Fetched services:", services.value); 
            error.value = null; 
        } catch (err) {
            error.value = err.message || 'Failed to fetch services for pilot.';
            console.error('Error fetching services for pilot:', err);
        } finally {
            loading.value = false;
        }
    };

     const fetchPilots = async () => { 
        loading.value = true; 
        try { 
            const response = await axios.get('http://127.0.0.1:8000/api/pilots', 
                { 
                    headers: { 
                        Authorization: `Bearer ${localStorage.getItem('authToken')}` 
                    } 
                }); 
            pilots.value = response.data.pilots; error.value = null; 
        } catch (err) { error.value = err.message || 'Error fetching pilots'; 
        } finally { 
            loading.value = false;
        }
    };
      
    

    const updateService = async (serviceId, serviceData) => {
        loading.value = true;
        try {
            const response = await axios.patch(
                `http://127.0.0.1:8000/api/services/edit/${serviceId}`, serviceData,
                {
                    headers: {
                        Authorization: `${localStorage.getItem('tokenType')} ${localStorage.getItem('authToken')}`
                    }
                }
            );
            
            const index = services.value.findIndex(s => s.id === serviceId);
            if (index !== -1) {
                services.value[index] = response.data.service;
            }
            
            message.value = 'Service updated successfully!';
            setTimeout(() => {
                message.value = '';
            }, 1000);
            
            return response.data;
        } catch (err) {
            error.value = err.message || 'Error updating service';
            throw err;
        } finally {
            loading.value = false;
        }
    };
    
    const deleteService = async (serviceId) => {
        loading.value = true;
        try {
            await axios.delete(
                `http://127.0.0.1:8000/api/services/destroy/${serviceId}`,
                {
                    headers: {
                        Authorization: `${localStorage.getItem('tokenType')} ${localStorage.getItem('authToken')}`
                    }
                }
            );
            
            services.value = services.value.filter(s => s.id !== serviceId);
            message.value = 'Service deleted successfully!';
            setTimeout(() => {
                message.value = '';
            }, 2000);
        } catch (err) {
            error.value = err.message || 'Error deleting service';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    return {
        services,
        service,
        pilots,
        categories,
        loading,
        error,
        message,
        hasServices,
        categoriesLoaded, 

        fetchCategories,
        createService,
        fetchUserServices,
        fetchServiceById,
        clearServices,
        updateService,
        deleteService,
        fetchServicesByPilot,
        fetchPilots,
    };
});
