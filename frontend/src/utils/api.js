import axios from 'axios';

const api = axios.create({ baseURL: import.meta.env.VITE_BACKEND_URL + 'api' });

// set auth token
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('authToken'); // Get token from storage
  if (token) {
    config.headers.Authorization = `Bearer ${token}`; // Attach token to headers
  }
  return config; // Return updated request config
});


const handleResponse = (response) => response.data;

const handleError = (error) => {
  const code = error.response?.data;

  // handle failed attempt on refreshing the token
  if (code === 401) {
    // remove from store
    localStorage.clear();
    window.location = '/login';
  }

  return Promise.reject(error);
};

// Add a response error interceptor
api.interceptors.response.use(handleResponse, handleError);

export default api;
 