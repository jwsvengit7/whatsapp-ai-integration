<template>
    <div class="login-div">
        <Sidebar></Sidebar>
        <div class="right">
            <HeaderAuth  message="Already have an account?" text="Login" route="/account/login"></HeaderAuth>
            <div class="form-div">
                <preloader :loading="loading"></preloader>
                <form class="form"  @submit.prevent="signup">
                    <h1>Create your account !</h1>
                    <h4 v-if="refer_name">{{refer_name}} Has Referred you</h4>

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
            error: '',
            link:'',
            users:'',
            refer_name:'',
            refer_mail:'',
            refer_id:''
        };
    },

    mounted() {
        this.link = this.$route.query.link;

        if (this.link) {
            this.verifyRefererLink(this.link);
        }
    },
    methods: {
            async verifyRefererLink(link) {
                this.loading = true;
                try {

                    const response = await axios.get('/referer-link/'+link);

                    if (response.status === 200) {

                        console.log(response.data.message);
                        this.users=response.data.message;
                        this.refer_name=response.data.message.name;
                        this.refer_mail=response.data.message.email;
                        this.refer_id=response.data.message.id;
                        return response.data.message;

                    } else {
                        this.error = 'Invalid Link .';
                    }
                } catch (error) {
                    console.error('Error verifying token:', error);
                    this.error = 'Failed to verify token. Please try again.';
                } finally {
                    this.loading = false;
                }
            },

            async change(){

            },

        async signup() {
            const router = useRouter();

            this.loading = true;
            this.error = '';

            if (this.password !== this.c_password) {
                this.error = 'Password does not match';
            } else {
                const data = {
                    email: this.email,
                    password: this.password,
                    phone: this.phone,
                    name: this.name,
                    role: "vendor",
                    referer: this.refer_id
                };

                try {
                    console.log(JSON.stringify(data));
                    const response = await axios.post('/create-user', data);
                    console.log(response.data);
                    if (response.status === 201) {
                        localStorage.setItem('email', this.email);
                        localStorage.setItem('otp_screen', "1");
                        window.location.replace('/account/verify-otp');
                    }
                } catch (error) {
                    console.log(error);

                    // Ensure error.response exists before trying to access error.response.data
                    if (error.response) {
                        if (error.response.data.message !== "Validation failed") {
                            this.error = error.response.data.message;
                        } else {
                            const err = error.response.data.errors;
                            if (err.phone) {
                                this.error = err.phone[0];
                            } if (err.name) {
                                this.error = err.name[0];
                            } if (err.email) {
                                this.error = err.email[0];
                            } if (err.role) {
                                this.error = err.role[0];
                            } if (err.password) {
                                this.error = err.password[0];
                            }
                        }
                    } else {
                        // If error.response is undefined, handle network or unexpected errors
                        this.error = 'Something went wrong. Please try again later.';
                    }
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
