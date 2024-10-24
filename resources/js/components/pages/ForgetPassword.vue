<template>
    <div class="login-div">
        <Sidebar />
        <div class="right">
            <HeaderAuth message="Don't have an account?" text="Sign up" route="/account/signup" />
            <div class="form-div">
                <Preloader :loading="loading" />
                <form class="form" @submit.prevent="sendOTP">
                    <h1>Forget Password!</h1>

                    <div class="el-form-item">
                        <label for="email" class="el-form-item__label">Email</label>
                        <input
                            type="email"
                            id="email"
                            v-model="email"
                            autocomplete="off"
                            placeholder="name@email.com"
                            maxlength="100"
                            class="el-input__inner"
                            required
                        />
                    </div>

                    <p class="error-message" v-if="error">{{ error }}</p>

                    <button class="login-button" type="submit">Forget Password</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from "vue";
import Sidebar from "../tools/Sidebar.vue";
import HeaderAuth from "../tools/HeaderAuth.vue";
import Preloader from "../tools/preloader.vue";
import axios from "../../axios.js";
import Swal from "sweetalert2";

export default {
    name: 'ForgetPassword',
    components: { Preloader, HeaderAuth, Sidebar },
    setup() {
        const loading = ref(false);
        const error = ref('');
        const email = ref('');

        const sendOTP = async () => {
            loading.value = true;
            error.value = '';

            try {
                const response = await axios.post('/forget-password', {
                    email: email.value,
                });

                if (response.status === 200 || response.status === 201) {
                    await Swal.fire({
                        title: 'Success!',
                        text: response.data.message,
                        icon: 'success',
                        confirmButtonText: 'Okay'
                    });
                }

                console.log(response.data);

            } catch (err) {
                error.value = err.response?.data.message || 'An error occurred. Please try again.';
                console.error(err);
            } finally {
                loading.value = false; // Reset loading state
            }
        };

        return {
            loading,
            error,
            email,
            sendOTP
        };
    }
};
</script>

<style scoped>
</style>
