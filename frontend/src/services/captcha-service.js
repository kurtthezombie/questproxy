import axios from 'axios'

export const fetchImage = async() => {
    try {
        const response = await axios.get('http://127.0.0.1:8000/captcha/api/math');
        console.log('Data: ', response);
        return response.data;
    } catch (error) {
        console.log("Error fetching captcha image: ", error);
    }
}
