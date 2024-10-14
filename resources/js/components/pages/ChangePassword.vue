

<template>
    <div class="login-div">
        <Sidebar />
        <div class="right">
            <HeaderAuth message="Don't have an account?" text="Sign up" route="/account/signup" />
            <div class="form-div">
                <Preloader :loading="loading" />
                <p v-if="token">{{ error }}</p>
                <form v-if="!error" class="form" @submit.prevent="change">
                    <h1>Change Password!</h1>

                    <div class="el-form-item">
                        <label for="email" class="el-form-item__label">Password</label>
                        <input
                            type="password"
                            id="email"
                            v-model="password"
                            autocomplete="off"
                            placeholder=""
                            maxlength="100"
                            class="el-input__inner"
                            required
                        />
                    </div>


                    <div class="el-form-item">
                        <label for="email" class="el-form-item__label">Confirm Password</label>
                        <input
                            type="password"
                            id="email"
                            v-model="cpassword"
                            autocomplete="off"
                            maxlength="100"
                            class="el-input__inner"
                            required
                        />
                    </div>

                    <p class="error-message" v-if="error">{{ error }}</p>

                    <button class="login-button" type="submit">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</template>


<script>
import HeaderAuth from "../tools/HeaderAuth.vue";
import Sidebar from "../tools/Sidebar.vue";
import Preloader from "../tools/preloader.vue";
import axios from "../../axios.js";


export default {
    name: 'ChangePassword',
    components: {Preloader, Sidebar, HeaderAuth},
    data() {
        return {
            token: '',
            error: '',
            loading: false,
            password:'',
            cpassword:''
        };
    },
    mounted() {
        this.token = this.$route.query.link;

        if (this.token) {
            this.verifyToken(this.token);
        } else {
            this.error = "Token is missing in the URL.";
        }
    },
    methods: {
        async verifyToken(token) {
            this.loading = true;
            try {

                const response = await axios.get('/verify-link/'+token);

                if (response.status === 200) {
                    console.log('Token verified successfully.');

                } else {
                    this.error = 'Invalid or expired token.';
                }
            } catch (error) {
                console.error('Error verifying token:', error);
                this.error = 'Failed to verify token. Please try again.';
            } finally {
                this.loading = false;
            }
        },

        async change(){

        }
    }
};
</script>

<style scoped>

</style>
