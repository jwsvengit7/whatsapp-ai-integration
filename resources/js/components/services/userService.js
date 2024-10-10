// services/userService.js
import axios from '../../axios.js';

export const fetchUser = async (token) => {
    return await axios.get('/fetch-user', {
        headers: {
            Authorization: `Bearer ${token}`
        }
    });
};
