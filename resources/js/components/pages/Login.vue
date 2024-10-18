<template>
    <div class="login-div">
        <Sidebar />
        <div class="right">
            <HeaderAuth message="Don't have an account?" text="Sign up" route="/account/signup" />
            <div class="form-div">
                <preloader :loading="loading" />
                <form class="form" @submit.prevent="login">
                    <h1>Welcome Back!</h1>
                    <div class="el-form-item">
                        <label for="email" class="el-form-item__label">Email</label>
                        <input v-model="email" type="text" autocomplete="off" placeholder="name@email.com" maxlength="100" class="el-input__inner" />
                    </div>
                    <div class="el-form-item">
                        <label for="password" class="el-form-item__label">Password</label>
                        <input v-model="password" type="password" autocomplete="off" placeholder="********" maxlength="100" class="el-input__inner" />
                    </div>
                    <div class="el-form-item">
                        <router-link to="/account/forget-password">
                            <p>Forget Password</p>
                        </router-link>
                    </div>
                    <p style="color:red;font-size: 17px;font-weight: bold" v-if="error">{{ error }}</p>
                    <button class="login-button" :disabled="loading">Login</button>
                    <br />
                    <button class="login-code">Login with verification code</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from 'vue';
import Sidebar from "../tools/Sidebar.vue";
import HeaderAuth from "../tools/HeaderAuth.vue";
import axios from "../../axios.js";
import Preloader from "../tools/preloader.vue";
import { useRouter } from "vue-router";

export default {
    name: 'Login',
    components: { Preloader, HeaderAuth, Sidebar },
    setup() {
        const router = useRouter();

        const email = ref('');
        const password = ref('');
        const loading = ref(false);
        const error = ref('');

        const login = async () => {
            loading.value = true;
            error.value = '';
            try {
                const response = await axios.post('/login', {
                    email: email.value,
                    password: password.value
                });
                if(response.status===200 || response.status===201){
                localStorage.setItem('token', response.data.message.data.token);
                localStorage.setItem('role', response.data.message.data.role);
                console.log(response.data.message.data)
                 window.location.replace('/dashboard');
                }
            } catch (err) {
                error.value = err.response?.data.message || 'Login failed. Please try again.';
                console.error(err);
            } finally {
                loading.value = false;
            }
        };

        return {
            email,
            password,
            loading,
            error,
            login
        };
    }
};
</script>



<style scoped>

</style>
