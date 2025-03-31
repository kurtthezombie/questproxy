import { useUserStore } from '@/stores/userStore';
import axios from 'axios';

const register = async (user) => {
  console.log("Inside register function");
  console.log(user);

  try {
    const response = await axios.post('http://127.0.0.1:8000/api/signup', user);
    return response.data;
  } catch (error) {
    return {
      status: error.response.data.status,
      message: error.response.data.message
    }
  }
}

  const login = async (credentials) => {
    const userStore = useUserStore();
  
    return axios.get('http://127.0.0.1:8000/sanctum/csrf-cookie', { withCredentials: true })
      .then(() => {
        console.log("CSRF cookie set.");
        return axios.post(
          'http://127.0.0.1:8000/api/login',
          credentials,
          { withCredentials: true } // Ensures cookies are included
        );
      })
      .then((response) => {
        const { authenticated_user, token, token_type } = response.data;

        userStore.setUser(authenticated_user);
        localStorage.setItem('authToken', token);
        localStorage.setItem('tokenType', token_type);
  
        console.log("User data after login:", userStore.userData);
        return response.data;
      })
      .catch((error) => {
        console.log("Login error:", error.response?.data || error.message);
        return {
          status: error.response?.data?.status || 500,
          message: error.response?.data?.message || "An error occurred.",
        };
      });
  };

const updateUser = async ({ id, firstName, lastName, contactNumber, email }) => {
  try {
    const updatedData = {
      f_name: firstName,
      l_name: lastName,
      contact_number: contactNumber,
      email: email
    };

    const response = await axios.patch(`http://localhost:8000/api/users/edit/${id}`, updatedData, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
      },
    });

    return response.data;
  } catch (error) {
    console.error('Error updating account:', error);
    alert('Failed to update account.');
    throw error;
  }
};

const editPilot = async (id) => {
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/edit/pilot/${id}`);
    console.log('User update response:', response);
    return response.data.pilot;
  } catch (error) {
    console.error('Error Update User:', error);
    return null;
  }
};

const updatePilot = async (id, updatedData) => {
  try {
    const response = await axios.patch(`http://127.0.0.1:8000/api/edit/pilot/${id}`, updatedData);
    console.log('Pilot update response:', response);
    return response.data.message; // Return success message
  } catch (error) {
    console.error('Error updating pilot:', error);
    return null;
  }
};

const fetchPortfolio = async (userId) => {
  const token = localStorage.getItem('authToken');
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/portfolios/${userId}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    return response.data;
  } catch (error) {
    console.error('Error fetching portfolio:', error);
    throw error;
  }
};

const fetchUserDataById = async (userId) => {
  const token = localStorage.getItem('authToken');
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/users/${userId}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    return response.data;
  } catch (error) {
    console.error('Error fetching user data:', error);
    throw error;
  }
};



const createPortfolio = async (portfolioData) => {
  const token = localStorage.getItem('authToken');
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/portfolios/create', portfolioData, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    if (response.data.success) {
      alert('Portfolio created successfully');
    } else {}
    return response.data;
  } catch (error) {
    console.error('Error creating portfolio:', error);
    alert('Error creating portfolio');
    throw error;
  }
};



const updatePortfolio = async (id, portfolioData) => {
  const token = localStorage.getItem('authToken');
  try {
    const response = await axios.patch(`http://127.0.0.1:8000/api/portfolios/edit/${id}`, portfolioData, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    return response.data;
  } catch (error) {
    console.error('Error updating portfolio:', error);
    throw error;
  }
};

const deletePortfolio = async (id) => {
  try {
    const response = await axios.delete(`http://127.0.0.1:8000/api/portfolios/destroy/${id}`);
    console.log('Portfolio deleted:', response);
    return response.data.message; // Return success message
  } catch (error) {
    console.error('Error deleting portfolio:', error);
    return null;
  }
};

const createService = async (serviceData) => {
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/services/create', serviceData);
    console.log('Service Created:', response);
    return response.data.message;
  } catch (error) {
    console.error('Error creating service:', error);
    return null;
  }
}

const reportUser = async ({ reportedUserId, reason }) => {
  try {
    const reportData = {
      reason: reason,
      reported_user_id: reportedUserId, // The ID of the user being reported
    };

    const response = await axios.post('http://localhost:8000/api/reports', reportData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('authToken')}`,
      },
    });

    return response.data; // Return the success response from the server
  } catch (error) {
    console.error('Error reporting user:', error);
    alert('Failed to report user.');
    throw error; // Re-throw the error for additional handling
  }
};

const fetchUserData = async () => {
  const token = localStorage.getItem('authToken');
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/user`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    return response.data;
  } catch (error) {
    console.error('Error fetching user data:', error);
    throw error;
  }
};

const fetchCategories = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/categories');
    return response.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
    throw error;
  }
};

const logout = async () => {
  const token = localStorage.getItem('authToken');
  const userStore = useUserStore();

  if (token) {
    await axios.post('http://127.0.0.1:8000/api/logout', {}, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    userStore.clearUser();
    localStorage.removeItem('authToken');
    console.log('Logout successful!');
  } else {
    console.log('No token found, cannot log out.');
  }
}

export default {
  register,
  login,
  logout,
  updateUser,
  fetchUserData,
  fetchCategories,
  reportUser,
  editPilot,
  updatePilot,
  fetchPortfolio,
  fetchUserDataById,
  createPortfolio,
  updatePortfolio,
  deletePortfolio,
  createService,
};
