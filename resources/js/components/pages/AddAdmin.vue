<template>
    <div class="dashboard">
        <AppSidebar :data="user" />
        <main>
            <AppHeader text="Admin Feature" :data="user" />
            <div class="box-container">
                <p v-if="user">Welcome, {{ user.name }}!</p>
                <p v-else>Please log in to access features.</p>
                <Preloader :loading="loadings" />
                <form class="add-container" @submit.prevent="createNewAdmin">
                    <div class="container-box">
                        <p>Admin Information</p>
                        <div class="information" v-for="(label, index) in adminFields" :key="index">
                            <label>{{ label.text }}&nbsp;<span>*</span></label>
                            <input
                                v-model="label.model"
                                :type="label.type"
                                :name="label.name"
                                required
                            />
                        </div>
                    </div>
                    <div class="container-box">
                        <p>Address</p>
                        <div class="information">
                            <label>Location&nbsp;<span>*</span></label>
                            <input v-model="location" type="text" name="address" required />
                        </div>
                    </div>
                    <div class="container-box">
                        <p>Admin Vital Information</p>
                        <div class="information">
                            <label>Role&nbsp;<span>*</span></label>
                            <select class="information-select" v-model="role" required>
                                <option value="super_admin">SUPER ADMIN</option>
                                <option value="admin">ADMIN</option>
                            </select>
                        </div>
                        <div class="information">
                            <label>Password&nbsp;<span>*</span></label>
                            <input v-model="password" type="password" name="password" required />
                        </div>
                    </div>
                    <div class="container-box">
                        <div class="information">
                            <button :disabled="loadings">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>

<script>
import AppSidebar from "../dashboard-components/AppSidebar.vue";
import AppHeader from "../dashboard-components/AppHeader.vue";
import Preloader from "../tools/preloader.vue";
import { onMounted, ref } from "vue";
import { useUser } from "../composables/useUser.js";
import axios from "../../axios.js";
import Swal from "sweetalert2";
import { useRouter } from "vue-router";

export default {
    name: 'AddAdmin',
    components: { Preloader, AppHeader, AppSidebar },

    setup() {
        const router = useRouter();
        const { loadUser, user } = useUser();

        const email = ref('');
        const password = ref('');
        const name = ref('');
        const location = ref('');
        const phone = ref('');
        const role = ref('');
        const loadings = ref(false);
        const errors = ref('');

        const adminFields = ref([
            { text: 'First Name', model: name, type: 'text', name: 'name' },
            { text: 'Email', model: email, type: 'email', name: 'email' },
            { text: 'Phone', model: phone, type: 'text', name: 'phone' },
        ]);

        const createNewAdmin = async () => {
            loadings.value = true;
            errors.value = '';

            try {
                const response = await axios.post('/create-admin', {
                    email: email.value,
                    password: password.value,
                    phone: phone.value,
                    name: name.value,
                    address: location.value,
                    role: role.value,
                });

                await handleResponse(response);
            } catch (error) {
                handleError(error);
            } finally {
                loadings.value = false;
            }
        };

        const handleResponse = async (response) => {
            if ([200, 201].includes(response.status)) {
                await Swal.fire({
                    title: 'Success!',
                    text: 'Admin created successfully',
                    icon: 'success',
                    confirmButtonText: 'Okay'
                });
                await router.push('/dashboard/users');
            } else {
                await showError('An unexpected error occurred.');
            }
        };

        const handleError = (error) => {
            const errorMessage = error.response?.data?.message || 'An error occurred. Please try again.';
            const validationErrors = error.response?.data?.errors || {};

            errors.value = Object.values(validationErrors).flat()[0] || errorMessage;
            showError(errors.value);
        };

        const showError = async (message) => {
            await Swal.fire({
                title: 'Error!',
                text: message,
                icon: 'error',
                confirmButtonText: 'Okay'
            });
        };

        onMounted(loadUser);

        return {
            email,
            password,
            name,
            phone,
            role,
            location,
            loadings,
            errors,
            adminFields,
            createNewAdmin,
            user,
        };
    }
};
</script>

<style scoped>
.add-container {
    width: 100%;
    height: auto;
}
.container-box {
    width: 45%;
    margin-top: 40px;
}
.information {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}
.container-box p {
    font-weight: bold;
}
.information input,
.information-select {
    width: 51%;
    padding: 10px;
    border-radius: 5px;
    outline: none;
    border: 1px solid #ccc;
    background: #f8f8f8;
}
.information-select{
    width:55%
}
.information label {
    color: #ccc;
    font-size: 13px;
}
.information label span {
    color: red;
}
.information button {
    background: #007bff;
    color: white;
    cursor: pointer;
    padding: 15px 25px;
    border: none;
    border-radius: 5px;
}
</style>
