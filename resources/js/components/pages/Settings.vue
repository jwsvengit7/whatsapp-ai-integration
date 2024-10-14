<template>
    <div class="dashboard">
        <AppSidebar :data="user" />
        <main>
            <AppHeader text="Settings" :data="user" />
            <div class="box-container">

                <Preloader :loading="loadings" />
                <form class="add-container" @submit.prevent="createNewAdmin">
                    <div class="container-box">
                        <p>Update Information</p>
                        <center><div style="width: 100px;height:100px;border-radius: 100px">
                            <label for="file" style="position: absolute;margin-top: 50px;margin-left: 80px;color: orange;font-size: 18px;cursor: pointer" class="fas fa-upload"></label>

                            <input type="file" style="display: none" id="file" @change="onImageChange"  />
                            <img v-if="previewImage" :src="previewImage"  style="width: 100%;height:100%;border-radius: 100px"  alt=""/>
                            <img v-else :src="utils.getImage(user.image)" style="width: 100%;height:100%;border-radius: 100px"  alt=""/>
                        </div>
                        </center>
                        <p></p>

                        <div class="information" >
                            <label>Email&nbsp;<span>*</span></label>
                            <input
                                v-model="user.email"
                                readonly

                                type="email"
                                name="email"
                                required
                            />
                        </div>
                        <div class="information" >
                            <label>Name&nbsp;<span>*</span></label>
                            <input
                                readonly
                                v-model="user.name"
                                type="text"
                                name="name"
                                required
                            />
                        </div>
                        <div class="information" >
                            <label>Phone&nbsp;<span>*</span></label>
                            <input

                                v-model="user.phone"
                                type="number"
                                name="phone"
                                required
                            />
                        </div>



                        <div class="information">
                            <label>Location&nbsp;<span>*</span></label>
                            <input v-model="location" type="text" name="address"  required />
                        </div>

                        <div class="information">
                            <div class="information">
                                <label>Role&nbsp;<span>*</span></label>
                                <input :value="utils.getRole(user.role)" type="text" name="role" required readonly/>
                            </div>
                        </div>
                        <div class="information">
                            <label>Password&nbsp;<span>*</span></label>
                            <input v-model="user.role" type="password" name="password" required />
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
import Utils from "../../Utils.js";

export default {
    name: 'Settings',
    components: { Preloader, AppHeader, AppSidebar },

    setup() {
        const router = useRouter();
        const { loadUser, user } = useUser();

        const email = ref('');
        const password = ref('');
        const name = ref('');
        const location = ref('');
        const phone = ref('');
        const image = ref('');
        const role = ref('');
        const loadings = ref(false);
        const errors = ref('');
        const previewImage =ref('')

        const adminFields = ref([
            { text: 'First Name', model: name, type: 'text', name: 'name' },
            { text: 'Email', model: email, type: 'email', name: 'email' },
            { text: 'Phone', model: phone, type: 'text', name: 'phone' },
        ]);
        const onImageChange = (event) => {
            const file = event.target.files[0];
            if (file) {
                image.value = file; // Store the file
                previewImage.value = URL.createObjectURL(file); // Preview the image
            }
        };


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

        const utils =new Utils();


        onMounted(loadUser);
        console.log("*********")
        console.log(loadUser)



        return {
            utils,
            email,
            password,
            name,
            phone,
            role,
            location,
            loadings,
            errors,
            adminFields,
            onImageChange, // For handling image change
            previewImage,
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
    display: flex;
    flex-direction: column;
    align-items: center;
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
    width: 60%;
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
    color: #9f9e9e;
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
