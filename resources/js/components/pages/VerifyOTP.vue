<template>
    <div class="otp-div">
        <Sidebar />
        <div class="right">
            <HeaderAuth message="Already have an account?" text="Login" route="/account/login" />
            <div class="form-div">
                <Preloader :loading="loading" />
                <form class="form" @submit.prevent="submitOTP">
                    <h1>Enter OTP</h1>
                    <p class="description">We've sent an OTP to your email. Please enter it below.</p>
                    <div class="otp-inputs">
                        <input v-model="otp[0]" @input="focusNext(0)" maxlength="1" class="otp-input" type="text" />
                        <input v-model="otp[1]" @input="focusNext(1)" maxlength="1" class="otp-input" type="text" />
                        <input v-model="otp[2]" @input="focusNext(2)" maxlength="1" class="otp-input" type="text" />
                        <input v-model="otp[3]" @input="focusNext(3)" maxlength="1" class="otp-input" type="text" />
                    </div>
                    <p class="error-message" v-if="error">{{ error }}</p>
                    <button type="submit" class="login-button">Verify OTP</button>
                    <p class="resend">Didn't receive the OTP? <span style="cursor: pointer" @click="sendOTP" class="resend-link">Resend</span></p>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router'; // Import useRouter for redirection
import Sidebar from "../tools/Sidebar.vue";
import HeaderAuth from "../tools/HeaderAuth.vue";
import Preloader from "../tools/preloader.vue";
import axios from "../../axios.js";
import Swal from "sweetalert2";

const router = useRouter(); // Initialize router
const otp = ref(['', '', '', '']);
const loading = ref(false);
const error = ref('');

const focusNext = (index) => {
    if (index < otp.value.length - 1 && otp.value[index]) {
        document.querySelectorAll('.otp-input')[index + 1].focus();
    }
};

const submitOTP = async () => {
    const otpCode = otp.value.join('');
    loading.value = true; // Set loading state
    error.value = '';

    try {
        const response = await axios.post('/verify-otp', {
            email: localStorage.getItem("email")?.toString(),
            otp: otpCode
        });
        console.log(response.data);
        if(response.status===200 || response.status===201){
            await Swal.fire({
                title: 'Success!',
                text: 'User Verified successfully',
                icon: 'success',
                confirmButtonText: 'Okay'
            });
           await router.push('/account/login');
        }



    } catch (err) {
        error.value = err.response?.data.message || 'Login failed. Please try again.';
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const sendOTP = async ()=> {
    loading.value = true; // Set loading state
    error.value = '';
    try {
        const response = await axios.post('/resend', {
            email: localStorage.getItem("email")?.toString(),

        });
        if (response.status === 200 || response.status === 201) {

            await Swal.fire({
                title: 'Success!',
                text: 'OTP Sent successfully',
                icon: 'success',
                confirmButtonText: 'Okay'
            });
        }
        console.log(response.data);

    } catch (err) {
        error.value = err.response?.data.message || 'Login failed. Please try again.';
        console.error(err);
    } finally {
        loading.value = false;
    }




};
</script>

<style scoped>
.otp-div {
    display: flex;
}

.right {
    flex: 1;
    padding: 20px;
}

.form-div {
    max-width: 400px;
    margin: 0 auto;
}

h1 {
    text-align: center;
    margin-bottom: 20px;
}

.description {
    text-align: center;
    margin-bottom: 20px;
    color: #666;
}

.otp-inputs {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.otp-input {
    width: 60px;
    height: 60px;
    font-size: 24px;
    text-align: center;
    border: 1px solid #ccc;
    border-radius: 8px;
    outline: none;
    transition: border-color 0.2s;
}

.otp-input:focus {
    border-color: #007bff;
}

.login-button {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}

.login-button:hover {
    background-color: #0071ff;
}

.error-message {
    color: red;
    font-size: 17px;
    font-weight: bold;
    text-align: center;
}

.resend {
    text-align: center;
    margin-top: 10px;
}

.resend-link {
    color: #0071ff;
    cursor: pointer;
}
</style>
