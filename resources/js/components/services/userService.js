// services/userService.js
import axios from '../../axios.js';

export const fetchUser = async (token) => {
    return await axios.get('/fetch-user', {
        headers: {
            Authorization: `Bearer ${token}`
        }
    });
};

export const fetchContext = async (token) => {
    return await axios.get('/context', {
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
export const fetchAllCustomers =async (token)=> {

    return await axios.get('/all-customer', {
        headers: {
            Authorization: `Bearer ${token}`
        }
    });
}
export const fetchAllProduct =async (token)=> {

    return await axios.get('/fetch-product', {
        headers: {
            Authorization: `Bearer ${token}`
        }
    });
}
    export const fetchAllConversation =async (token,id)=>{

        return await axios.get('/fetch-chat?id='+id, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
}

