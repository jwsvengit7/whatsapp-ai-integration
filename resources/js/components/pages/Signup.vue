<template>
    <div class="login-div">
        <Sidebar></Sidebar>
        <div class="right">
            <HeaderAuth  message="Already have an account?" text="Login" route="/account/login"></HeaderAuth>
            <div class="form-div">
                <preloader :loading="loading"></preloader>
                <form class="form"  @submit.prevent="signup">
                    <h1>Create your account !</h1>

                    <div data-v-1adbda30="" class="el-form-item ">
                        <label for="name" class="el-form-item__label">Username</label>

                        <input v-model="name" type="text" autocomplete="off" placeholder="john doe" maxlength="100" class="el-input__inner"><!----><!----><!----><!---->


                    </div>
                    <div data-v-1adbda30="" class="el-form-item ">
                        <label for="email" class="el-form-item__label">Email</label>

                        <input v-model="email" type="text" autocomplete="off" placeholder="name@email.com" maxlength="100" class="el-input__inner"><!----><!----><!----><!---->


                    </div>
                    <div data-v-1adbda30="" class="el-form-item ">
                        <label for="email" class="el-form-item__label">Phone</label>

                        <input v-model="phone" type="text" autocomplete="off" placeholder="23490987854" maxlength="100" class="el-input__inner"><!----><!----><!----><!---->
                    </div>

                    <div data-v-1adbda30="" class="el-form-item ">
                        <label for="email" class="el-form-item__label">Password</label>

                        <input v-model="password" type="password" autocomplete="off" placeholder="********" maxlength="100" class="el-input__inner"><!----><!----><!----><!---->
                    </div>

                    <div data-v-1adbda30="" class="el-form-item ">
                        <label for="password" class="el-form-item__label">Confirm Password</label>

                        <input v-model="c_password" type="password" autocomplete="off" placeholder="********" maxlength="100" class="el-input__inner"><!----><!----><!----><!---->
                    </div>

                    <div data-v-1adbda30="" class="el-form-item v2">

<p>I want to receive product updates, marketing news, and other relevant content by email from
    </p>
                    </div>
                    <p style="color:red;font-size: 17px;font-weight: bold" v-if="error">{{ error }}</p>

                    <button class="login-button" :disabled="loading">Start free trail</button>



                </form>

            </div>
        </div>
    </div>
</template>

<script>
import HeaderAuth from "../tools/HeaderAuth.vue";
import Sidebar from "../tools/Sidebar.vue";
import axios from "../../axios.js";
import Preloader from "../tools/preloader.vue";
import { useRouter } from 'vue-router';
const router = useRouter();
export default {
    name: 'Signup',
    components: {Preloader, HeaderAuth,Sidebar},

    data() {
        return {
            email: '',
            name:'',
            c_password:'',
            phone:'',
            password: '',
            loading: false,
            error: ''
        };
    },
    methods: {

        async signup() {
            this.loading = true;
            this.error = '';
            if(this.password!==this.c_password){
                this.error = 'Password does not match';
            }else {
                try {
                    const response = await axios.post('/create-user', {
                        email: this.email,
                        password: this.password,
                        phone: this.phone,
                        name: this.name,
                        role:"vendor"
                    });
                    console.log(response.data)
                   if(response.status===200 || response.status===201){
                       localStorage.setItem('email', this.email);
                       localStorage.setItem('otp_screen', "1");
                       await router.push('/account/verify-otp');
                   }
                } catch (error) {
                    if (error.response && error.response.data) {
                        const  err = error.response.data.errors;
                        if(err.phone){
                            this.error = err.phone[0]
                        }
                        if(err.name){
                            this.error = err.name[0]
                        }
                        if(err.email){
                            this.error = err.email[0]
                        }
                        if(err.role){
                            this.error = err.role[0]
                        }
                        if(err.password){
                            this.error = err.password[0]
                        }

                    } else {
                        this.error = 'An unexpected error occurred.';
                    }
                    console.log(error)
                } finally {
                    this.loading = false;
                }
            }
        }
    },

};
</script>

<style scoped>

</style>
