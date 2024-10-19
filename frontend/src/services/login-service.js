import axios from 'axios';

const register = async(user) => {
    console.log("Inside register function");
    console.log(user);
    
    try {
      const response = await axios.post('http://127.0.0.1:8000/api/signup',user);
      return response.data;
    } catch (error) {
      return {
        status: error.response.data.status,
        message: error.response.data.message
      }
    } 
    
}

const login = async(credentials) => {
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/login',credentials);
    console.log(response);
    
    localStorage.setItem('authToken', response.data.token);
    localStorage.setItem('tokenType', response.data.token_type);
    
    console.log('Login successful!', response.data);

    return response.data;
  } catch (error) {
    console.log("Loginservice error: ",error.response.data);
    return {
      status: error.response.data.status,
      message: error.response.data.message,
    }
  }
    
}

const editGamer = async (id) => {
    try {
      const response = await axios.get(`http://127.0.0.1:8000/api/gamers/edit/${id}`);
      console.log('Gamer edit response:', response);
      return response.data.gamer; // Return the gamer data
    } catch (error) {
      console.error('Error fetching gamer:', error);
      return null;
    }
  };
  

  const updateGamer = async (id, updatedData) => {
    try {
      const response = await axios.patch(`http://127.0.0.1:8000/api/gamers/edit/${id}`, updatedData);
      console.log('Gamer update response:', response);
      return response.data.message; // Return success message
    } catch (error) {
      console.error('Error updating gamer:', error);
      return null;
    }
  };
  

  const editPilot = async (id) => {
    try {
      const response = await axios.get(`http://127.0.0.1:8000/api/edit/pilot/${id}`);
      console.log('Pilot edit response:', response);
      return response.data.pilot; // Return the pilot data
    } catch (error) {
      console.error('Error fetching pilot:', error);
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

  const createPortfolio = async (portfolioData) => {
    try {
      const response = await axios.post('http://127.0.0.1:8000/api/portfolio/create', portfolioData);
      console.log('Portfolio created:', response);
      return response.data.message; // Return success message
    } catch (error) {
      console.error('Error creating portfolio:', error);
      return null;
    }
  };

  const editPortfolio = async (id, updatedData) => {
    try {
      const response = await axios.patch(`http://127.0.0.1:8000/api/portfolio/edit/${id}`, updatedData);
      console.log('Portfolio update response:', response);
      return response.data.message; // Return success message
    } catch (error) {
      console.error('Error updating portfolio:', error);
      return null;
    }
  };

  const deletePortfolio = async (id) => {
    try {
      const response = await axios.delete(`http://127.0.0.1:8000/api/portfolio/destroy/${id}`);
      console.log('Portfolio deleted:', response);
      return response.data.message; // Return success message
    } catch (error) {
      console.error('Error deleting portfolio:', error);
      return null;
    }
  };
  
  
  const fetchUserData = async () => {
    const token = localStorage.getItem('authToken');
    try {
      const response = await axios.get('http://127.0.0.1:8000/api/user', {
        headers: {
          Authorization: `Bearer ${token}`, // Include the token for authentication
        },
      });
      return response.data; // This will return the user data from /api/user
    } catch (error) {
      console.error('Error fetching user data:', error);
      throw error;
    }
  };
  

const logout = async () => {
    const token = localStorage.getItem('authToken');

    if (token) {
        // Send a request to the logout API
        await axios.post('http://127.0.0.1:8000/api/logout', {}, {
            headers: {
                'Authorization': `Bearer ${token}` // Pass the token in the Authorization header
            }
        });
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
  fetchUserData,
  updateGamer, 
  editGamer, 
  editPilot, 
  updatePilot, 
  createPortfolio, 
  editPortfolio, 
  deletePortfolio 
}; 