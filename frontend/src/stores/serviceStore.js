import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axios from 'axios';
import { useUserStore } from '@/stores/userStore'; 
import toast from "@/utils/toast";
export const useServiceStore = defineStore('service', () => {

    const services = ref([]);
    const categories = ref([]);
    const loading = ref(false); 
    const error = ref(null); 
    const message = ref('');
    const myBookings = ref([]); 
    const bookingsLoading = ref(false); 
    const bookingsError = ref(null); 

    const hasServices = computed(() => services.value.length > 0);
    const categoriesLoaded = computed(() => categories.value.length > 0);

    const clearServices = () => {
        services.value = [];
        categories.value = [];
        error.value = null;
        message.value = '';
        myBookings.value = []; 
        bookingsError.value = null; 
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

    // Ensure useUserStore is used within the action where it's needed
    const submitBooking = async (serviceId, additionalNotes, credentialsUsername, credentialsPassword) => {
        try {
          const userStore = useUserStore(); 
          const authToken = localStorage.getItem('authToken');

          if (!authToken) {
            console.error('Authentication token missing.');
            throw new Error('Authentication token missing.'); 
          }

          // Ensure userStore.userData and userStore.userData.id are available
          if (!userStore.userData || !userStore.userData.id) {
              console.error('User data or ID missing for booking submission.');
              throw new Error('User data missing. Please log in.');
          }

          const response = await axios.post(
            'http://127.0.0.1:8000/api/bookings/store',
            {
              client_id: userStore.userData.id, 
              service_id: serviceId,
              additional_notes: additionalNotes,
              credentials_username: credentialsUsername,
              credentials_password: credentialsPassword,
            },
            {
              headers: {
                Authorization: `Bearer ${authToken}`,
                'Content-Type': 'application/json',
              },
            }
          );

          return response.data;
        } catch (error) {
          console.error('Booking submission error:', error);
          throw error; 
        }
      };

    const createService = async (serviceData) => {
        loading.value = true;
        try {
            const tokenType = localStorage.getItem('tokenType');
            const authToken = localStorage.getItem('authToken');
            if (!authToken || !tokenType) throw new Error('Authentication token missing.');

            const response = await axios.post(
                'http://127.0.0.1:8000/api/services/create',
                serviceData,
                {
                    headers: {
                        Authorization: `${tokenType} ${authToken}`
                    }
                }
            );
            message.value = 'Your service has been successfully created!';
            error.value = null;

            services.value.push(response.data.service);

            setTimeout(() => {
                message.value = '';
            }, 2000);

            return response.data;
        } catch (err) {
            if (err.response && err.response.status === 422) {
                error.value = 'Inputs are invalid. Please check and try again.';
            } else {
                error.value = err.message || 'Error creating service';
            }
            message.value = ''; 
            
            throw new Error(error.value);
        } finally {
            loading.value = false;
        }
    };

    const fetchUserServices = async () => {
        loading.value = true;
        try {
            const tokenType = localStorage.getItem('tokenType');
            const authToken = localStorage.getItem('authToken');
            if (!authToken || !tokenType) throw new Error('Authentication token missing.');

            const response = await axios.get('http://127.0.0.1:8000/api/services', {
                headers: {
                    Authorization: `${tokenType} ${authToken}`
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
        if (!serviceId) {
            error.value = "Service ID is required.";
            return;
        }

        loading.value = true;
        try {
            const tokenType = localStorage.getItem('tokenType');
            const authToken = localStorage.getItem('authToken');
            if (!authToken || !tokenType) throw new Error('Authentication token missing.');

            const response = await axios.get(`http://127.0.0.1:8000/api/services/${serviceId}`, {
                headers: {
                    Authorization: `${tokenType} ${authToken}`
                }
            });

            console.log("Fetched service by ID:", response.data);

            if (response.data.service) {
                
                return response.data.service; 
            } else if (response.data.data && response.data.data.service) {
                 
                 return response.data.data.service; 
            } else {
                error.value = "Invalid service data received.";
                 throw new Error(error.value); 
            }
        } catch (err) {
            error.value = err.message || "Error fetching service details.";
            console.error("Error fetching service:", err);
            throw err; // Re-throw the error
        } finally {
            loading.value = false;
        }
    };

    const fetchServicesByPilot = async (pilot_id) => {
        loading.value = true; // Use the main loading for services
        error.value = null; // Clear service errors
        try {
          const tokenType = localStorage.getItem('tokenType');
          const authToken = localStorage.getItem('authToken');
          if (!authToken || !tokenType) throw new Error('Authentication token missing.');

          const response = await axios.get(
            `http://127.0.0.1:8000/api/pilots/${pilot_id}/services`,
            {
              headers: {
                Authorization: `${tokenType} ${authToken}`
              }
            }
          );
          services.value = response.data.services;
          error.value = null;
        } catch (err) {
          error.value = err.message || 'Failed to fetch services for pilot.';
          console.error('Error fetching services for pilot:', err);
           throw err; 
        } finally {
          loading.value = false;
        }
      };


    const updateService = async (serviceId, serviceData) => {
        loading.value = true;
        try {
            const tokenType = localStorage.getItem('tokenType');
            const authToken = localStorage.getItem('authToken');
            if (!authToken || !tokenType) throw new Error('Authentication token missing.');

            const response = await axios.patch(
                `http://127.0.0.1:8000/api/services/edit/${serviceId}`, serviceData,
                {
                    headers: {
                        Authorization: `${tokenType} ${authToken}`
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
            const tokenType = localStorage.getItem('tokenType');
            const authToken = localStorage.getItem('authToken');
            if (!authToken || !tokenType) throw new Error('Authentication token missing.');

            await axios.delete(
                `http://127.0.0.1:8000/api/services/destroy/${serviceId}`,
                {
                    headers: {
                        Authorization: `${tokenType} ${authToken}`
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

    // Action to fetch bookings for the authenticated pilot (used on My Services page)
    const fetchBookingsByPilot = async () => {
        bookingsLoading.value = true;
        bookingsError.value = null;
        try {
          const token = localStorage.getItem('authToken');
          if (!token) throw new Error('No authentication token found');

          const response = await axios.get('http://127.0.0.1:8000/api/pilot/bookings', {
            headers: {
              Authorization: `Bearer ${token}` 
            }
          });

          console.log('Bookings API Response (Initial):', response.data); 

          // Handle different response structures
          const initialBookings = response.data?.data ||
                                 response.data ||
                                 [];

          
          const bookingsWithClientData = await Promise.all(initialBookings.map(async (booking) => {
            
            if (!booking.client || !booking.client.username) {
              const clientId = booking.client_id;
              if (clientId) {
                try {
                  // Fetch the client's user details using their ID
                  const clientResponse = await axios.get(`http://127.0.0.1:8000/api/users/${clientId}`, {
                    headers: {
                      Authorization: `Bearer ${token}` 
                    }
                  });
                  
                  if (clientResponse.data && clientResponse.data.user) {
                    
                    booking.client = clientResponse.data.user;
                    console.log(`Fetched client username for booking ${booking.id}: ${booking.client.username}`);
                  } else {
                     console.warn(`User data not found in response for client ID ${clientId} (booking ${booking.id})`);
                     // Provide a placeholder if data is not found
                     booking.client = { username: 'Client data missing' };
                  }
                } catch (clientErr) {
                  console.error(`Error fetching client details for ID ${clientId} (booking ${booking.id}):`, clientErr);
                  // Provide an error placeholder if fetch fails
                  booking.client = { username: 'Error fetching client' };
                }
              } else {
                 console.warn(`Booking ${booking.id} is missing client_id.`);
                 // Provide a placeholder if client_id is missing
                 booking.client = { username: 'Client ID missing' };
              }
            } else {
                console.log(`Client data already available for booking ${booking.id}.`);
            }
            return booking; 
          }));
          // --- End of Frontend Workaround ---

          myBookings.value = bookingsWithClientData; 
          console.log('My Bookings ref updated with client data:', myBookings.value); // Final debug log

        } catch (err) {
          console.error('Booking fetch error:', err);
          bookingsError.value = err.response?.data?.message ||
                              err.message ||
                              'Failed to load bookings';
           throw err; 
        } finally {
          bookingsLoading.value = false;
        }
      };

    const fetchBookingsByClient = async (clientId) => {
        bookingsLoading.value = true; 
        bookingsError.value = null; 
        try {
            const token = localStorage.getItem('authToken');
            if (!token) throw new Error('No authentication token found');

            // Use the correct endpoint for fetching bookings by client ID
            const response = await axios.get(`http://127.0.0.1:8000/api/bookings/client/${clientId}`, {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            });

            console.log('Bookings by Client API Response:', response.data); // Debug log

            
            myBookings.value = response.data?.bookings || [];

        } catch (err) {
            console.error('Bookings by client fetch error:', err);
            bookingsError.value = err.response?.data?.message ||
                                  err.message ||
                                  'Failed to load bookings for this user.';
             throw err; 
        } finally {
            bookingsLoading.value = false;
        }
    };

      const markBookingAsCompleted = async (bookingId) => {
        try {
            const token = localStorage.getItem('authToken');
            if (!token) throw new Error('No authentication token found');
            
            const response = await axios.put(
                `http://127.0.0.1:8000/api/bookings/${bookingId}/status`,
                { status: 'completed' },
                {
                  headers: {
                    Authorization: `Bearer ${token}`
                  }
                }
            );
            console.log("MARK BOOKING AS COMPLETED: ", bookingId);
        } catch (error) {
            toast.error(error.response.data.message);
            throw error;
        }
      }



    return {
        services,
        categories,
        loading, // Main loading for services/categories
        error, // Main error for services/categories
        message,
        hasServices,
        categoriesLoaded,
        myBookings, // Bookings state
        bookingsLoading, // Loading state for bookings
        bookingsError, // Error state for bookings

        fetchBookingsByPilot, 
        fetchBookingsByClient, 
        fetchCategories,
        createService, 
        fetchUserServices, 
        fetchServiceById, 
        clearServices,
        updateService,
        deleteService,
        fetchServicesByPilot,
        submitBooking,
        markBookingAsCompleted
    };
});
