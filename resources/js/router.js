import { createRouter, createWebHistory } from 'vue-router';
import Home from './components/pages/Home.vue';
import Dashboard from './components/pages/Dashboard.vue';
import Login from "./components/pages/Login.vue";
import Signup from "./components/pages/Signup.vue";
import ForgetPassword from "./components/pages/ForgetPassword.vue";
import VerifyOTP from "./components/pages/VerifyOTP.vue";
import ChatBotFeature from "./components/pages/ChatBotFeature.vue";
import Customer from "./components/pages/Customer.vue";
import CreateProduct from "./components/pages/CreateProduct.vue";
import AddAdmin from "./components/pages/AddAdmin.vue";
import Users from "./components/pages/Users.vue";
import Settings from "./components/pages/Settings.vue";
import ChangePassword from "./components/pages/ChangePassword.vue";
import AllProduct from "./components/pages/AllProduct.vue";
import AI_Model from "./components/pages/AI_Model.vue";
import EducateAI from "./components/pages/EducateAI.vue";
import Conversations from "./components/pages/Conversations.vue";
import MyCustomer from "./components/pages/MyCustomer.vue";

const routes = [
    { path: '/', component: Home, name: 'Home' },
    { path: '/account/login', component: Login, name: 'Login' },
    { path: '/account/signup', component: Signup, name: 'Signup' },
    { path: '/account/forget-password', component: ForgetPassword, name: 'ForgetPassword' },
    { path: '/account/verify-otp', component: VerifyOTP, name: 'VerifyOTP' },
    { path: '/account/change-password', component: ChangePassword, name: 'ChangePassword' },
    // { path: '/dashboard', component: Dashboard, name: 'Dashboard' },
    { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true } },
    { path: '/dashboard/chat-feature', component: ChatBotFeature, name: 'ChatBotFeature' },
    { path: '/dashboard/customers', component: Customer, name: 'Customer' },
    { path: '/dashboard/setting', component: Settings, name: 'Settings' },
    { path: '/dashboard/users', component: Users, name: 'Users' },
    { path: '/dashboard/add-admin', component: AddAdmin, name: 'AddAdmin' },
    { path: '/dashboard/create-product', component: CreateProduct, name: 'CreateProduct' },
    { path: '/dashboard/all-product', component: AllProduct, name: 'AllProduct' },
    { path: '/dashboard/ai', component: AI_Model, name: 'AI_MODEL' },
    { path: '/dashboard/educate-ai', component: EducateAI, name: 'EducateAI' },
    { path: '/dashboard/view-conversations', component: Conversations, name: 'Conversations' },
    { path: '/dashboard/my-customer', component: MyCustomer, name: 'MyCustomer' },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
