import { createRouter, createWebHistory } from 'vue-router';
import Home from './components/pages/Home.vue';
import Dashboard from './components/pages/Dashboard.vue';
import Login from "./components/pages/Login.vue";
import Signup from "./components/pages/Signup.vue";
import ForgetPassword from "./components/pages/ForgetPassword.vue";
import VerifyOTP from "./components/pages/VerifyOTP.vue";
import ChatBotFeature from "./components/pages/ChatBotFeature.vue";
import Customer from "./components/pages/Customer.vue";

const routes = [
    { path: '/', component: Home, name: 'Home' },
    { path: '/account/login', component: Login, name: 'Login' },
    { path: '/account/signup', component: Signup, name: 'Signup' },
    { path: '/account/forget-password', component: ForgetPassword, name: 'ForgetPassword' },
    { path: '/account/verify-otp', component: VerifyOTP, name: 'VerifyOTP' },
    // { path: '/dashboard', component: Dashboard, name: 'Dashboard' },
    { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true } },
    { path: '/dashboard/chat-feature', component: ChatBotFeature, name: 'ChatBotFeature' },
    { path: '/dashboard/customers', component: Customer, name: 'Customer' },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
