import { createRouter, createWebHistory } from 'vue-router';
import Home from './components/Home.vue';
import Dashboard from './components/Dashboard.vue';
import Login from "./components/Login.vue";
import Signup from "./components/Signup.vue";

const routes = [
    { path: '/', component: Home, name: 'Home' },
    { path: '/account/login', component: Login, name: 'Login' },
    { path: '/account/signup', component: Signup, name: 'Signup' },
    { path: '/dashboard', component: Dashboard, name: 'Dashboard' },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
