import axios from 'axios';

const api = axios.create({ baseURL: process.env.BACKEND_URL + 'api' });

// set auth token
const authToken = localStorage.getItem('authToken');
if (authToken) api.defaults.headers.common['Authorization'] = `Bearer ${access_token}`;

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
 