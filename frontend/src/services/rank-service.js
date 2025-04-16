import axios from 'axios';

export const leaderboards = async () => {
    try {
        const response = await axios.get('http://127.0.0.1:8000/api/leaderboards'); // Make the API call
        console.log("Rank service:", response.data);  // Log the response data
        return response.data;  // Return the data directly
    } catch (error) {
        console.error("Error fetching leaderboard data:", error);  // Log any errors
        return null;  // Return null to indicate an error occurred
    }
};
