// services/userService.js
import axios from '../../axios.js';

export const fetchUser = async (token) => {
    return await axios.get('/fetch-user', {
        headers: {
            Authorization: `Bearer ${token}`
        }
    });
};
export const fetchAllUser =async (token)=>{
    return await axios.get('/all-users', {
        headers: {
            Authorization: `Bearer ${token}`
        }
    });
}
export const fetchAllCustomers =async (token)=>{
    console.log(token)
    console.log("********")
    return await axios.get('/all-customer', {
        headers: {
            Authorization: `Bearer ${token}`
        }
    });
}

