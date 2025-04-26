import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axios from 'axios';
import { useUserStore } from '@/stores/userStore'; // Import useUserStore to access user data for submitBooking

export const useServiceStore = defineStore('service', () => {

    const services = ref([]);
    const categories = ref([]);
    const loading = ref(false); // Main loading for services/categories
    const error = ref(null); // Main error for services/categories
    const message = ref('');
    const myBookings = ref([]); // State to hold bookings for the current user (client or pilot)
    const bookingsLoading = ref(false); // Loading state for bookings
    const bookingsError = ref(null); // Error state for bookings

    const hasServices = computed(() => services.value.length > 0);
    const categoriesLoaded = computed(() => categories.value.length > 0);

    const clearServices = () => {
        services.value = [];
        categories.value = [];
        error.value = null;
        message.value = '';
        myBookings.value = []; // Clear bookings as well
        bookingsError.value = null; // Clear booking errors
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
          const userStore = useUserStore(); // Access userStore here
          const authToken = localStorage.getItem('authToken');

          if (!authToken) {
            console.error('Authentication token missing.');
            throw new Error('Authentication token missing.'); // Throw error to be caught by caller
          }

          // Ensure userStore.userData and userStore.userData.id are available
          if (!userStore.userData || !userStore.userData.id) {
              console.error('User data or ID missing for booking submission.');
              throw new Error('User data missing. Please log in.');
          }

          const response = await axios.post(
            'http://127.0.0.1:8000/api/bookings/store',
            {
              client_id: userStore.userData.id, // Use userStore data
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
          throw error; // Re-throw the error for handling in the component
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
            message.value = ''; // Clear success message on error
            // No need for a second timeout here as error is set
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
                // Assuming 'service' ref exists in the component using this store
                // service.value = response.data.service;
                return response.data.service; // Return the fetched service
            } else if (response.data.data && response.data.data.service) {
                 // Assuming 'service' ref exists in the component using this store
                // service.value = response.data.data.service;
                 return response.data.data.service; // Return the fetched service
            } else {
                error.value = "Invalid service data received.";
                 throw new Error(error.value); // Throw error for handling in component
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
           throw err; // Re-throw the error
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
            throw err; // Re-throw the error
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
            throw err; // Re-throw the error
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
              Authorization: `Bearer ${token}` // Assuming 'Bearer' token type for this endpoint
            }
          });

          console.log('Bookings API Response (Initial):', response.data); // Debug log

          // Handle different response structures
          const initialBookings = response.data?.data ||
                                 response.data ||
                                 [];

          // --- Frontend Workaround: Fetch Client Details if missing ---
          // This is an N+1 approach and less efficient than backend eager loading,
          // but necessary if backend changes are not possible.
          const bookingsWithClientData = await Promise.all(initialBookings.map(async (booking) => {
            // Check if client data is already available and has a username
            // Added a check for booking.client_id existence
            if (!booking.client || !booking.client.username) {
              const clientId = booking.client_id;
              if (clientId) {
                try {
                  // Fetch the client's user details using their ID
                  const clientResponse = await axios.get(`http://127.0.0.1:8000/api/users/${clientId}`, {
                    headers: {
                      Authorization: `Bearer ${token}` // Use the same token for this call
                    }
                  });
                  // Assuming the user data is in response.data.user
                  if (clientResponse.data && clientResponse.data.user) {
                    // Attach the fetched user data to the booking object
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
            return booking; // Return the potentially updated booking
          }));
          // --- End of Frontend Workaround ---

          myBookings.value = bookingsWithClientData; // Update the state with enriched bookings
          console.log('My Bookings ref updated with client data:', myBookings.value); // Final debug log

        } catch (err) {
          console.error('Booking fetch error:', err);
          bookingsError.value = err.response?.data?.message ||
                              err.message ||
                              'Failed to load bookings';
           throw err; // Re-throw the error
        } finally {
          bookingsLoading.value = false;
        }
      };

    // Action to fetch bookings for a specific client (used on User Profile page for Gamers)
    // NOTE: This action assumes the backend endpoint /api/bookings/client/{clientId}
    // returns the bookings data directly in a 'bookings' key, and that the backend
    // eager-loads the 'service' relationship.
    const fetchBookingsByClient = async (clientId) => {
        bookingsLoading.value = true; // Use specific loading for bookings
        bookingsError.value = null; // Clear booking errors
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

            // Assuming the backend now returns { bookings: [...] } and eager-loads 'service'
            myBookings.value = response.data?.bookings || [];

        } catch (err) {
            console.error('Bookings by client fetch error:', err);
            bookingsError.value = err.response?.data?.message ||
                                  err.message ||
                                  'Failed to load bookings for this user.';
             throw err; // Re-throw the error
        } finally {
            bookingsLoading.value = false;
        }
    };


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

        fetchBookingsByPilot, // For pilot's own bookings page (now includes client fetch)
        fetchBookingsByClient, // For displaying bookings on gamer profile
        fetchCategories,
        createService, // Assuming you have this
        fetchUserServices, // Assuming you have this
        fetchServiceById, // Assuming you have this
        clearServices,
        updateService, // Assuming you have this
        deleteService, // Assuming you have this
        fetchServicesByPilot, // For displaying services on pilot profile
        submitBooking // Assuming you have this

    };
});
