import axios from 'axios';

axios.defaults.baseURL = 'https://uat.smefunds.com/api/v1';

axios.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
});

export default axios;
