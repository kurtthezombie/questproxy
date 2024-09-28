import axios from 'axios';

const register = async(user) => {
    const response = await axios.post('http://127.0.0.1:8000/api/signup',user);
    console.log(response);
    return response.data;
}

const login = async(credentials) => {
    const response = await axios.post('http://127.0.0.1:8000/api/login',credentials);
    console.log(response);
    
    localStorage.setItem('authToken', response.data.token);
    localStorage.setItem('tokenType', response.data.token_type);

    console.log('Login successful!', response.data);

    return response.data;
}

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

export default { register, login, logout }; 