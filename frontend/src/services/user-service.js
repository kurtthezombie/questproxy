import axios from 'axios';

const userService = {
  // Fetch user profile by ID
  getUserProfile: async (userId) => {
    try {
      const token = localStorage.getItem('authToken');

      if (!token) {
        throw new Error('Unauthorized: Missing authentication token');
      }

      // Fetch the user profile for the logged-in user or another user by ID
      const response = await axios.get(`http://127.0.0.1:8000/users/${userId}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
        },
      });

      return response.data;
    } catch (error) {
      if (error.response && error.response.status === 401) {
        console.error('Unauthorized access - token may be expired or invalid');
      } else {
        console.error('Error fetching user profile:', error.message);
      }
      throw error;
    }
  },

  // Fetch user by username
  getUserByUsername: async (username) => {
    try {
      const token = localStorage.getItem('authToken');

      if (!token) {
        throw new Error('Unauthorized: Missing authentication token');
      }

      const response = await axios.get(`http://127.0.0.1:8000/users/username/${username}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
        },
      });

      return response.data;
    } catch (error) {
      if (error.response && error.response.status === 401) {
        console.error('Unauthorized access - token may be expired or invalid');
      } else {
        console.error('Error fetching user by username:', error.message);
      }
      throw error;
    }
  },

  // Update user profile
  updateUserProfile: async (userId, profileData) => {
    try {
      const token = localStorage.getItem('authToken');

      if (!token) {
        throw new Error('Unauthorized: Missing authentication token');
      }

      const response = await axios.patch(`http://127.0.0.1:8000/users/edit/${userId}`, profileData, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
        },
      });

      return response.data;
    } catch (error) {
      if (error.response && error.response.status === 401) {
        console.error('Unauthorized access - token may be expired or invalid');
      } else {
        console.error('Error updating user profile:', error.message);
      }
      throw error;
    }
  },
};

export default userService;
