/* import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || null,
    user: JSON.parse(localStorage.getItem('user')) || null
  }),
  
  getters: {
    isAuthenticated: (state) => !!state.token,
    getUser: (state) => state.user
  },
  
  actions: {
    async login(credentials) {
      try {
        const response = await axios.post('http://127.0.0.1:8000/api/auth/login', credentials);
        const { token, user } = response.data;
        
        this.setToken(token);
        this.setUser(user);
        
        return response.data;
      } catch (error) {
        console.error('Login error:', error);
        throw error;
      }
    },
    
    async register(userData) {
      try {
        const response = await axios.post('http://127.0.0.1:8000/api/auth/register', userData);
        return response.data;
      } catch (error) {
        console.error('Register error:', error);
        throw error;
      }
    },
    
    setToken(token) {
      this.token = token;
      localStorage.setItem('token', token);
      
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    },
    
    setUser(user) {
      this.user = user;
      localStorage.setItem('user', JSON.stringify(user));
    },
    
    logout() {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      
      this.token = null;
      this.user = null;
      
      delete axios.defaults.headers.common['Authorization'];
    },
    
    checkAuth() {
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
      }
      
      return this.isAuthenticated;
    }
  }
}); */
