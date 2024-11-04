<template>
    <div class="dashboard">
        <AppSidebar  :isSidebarVisible="isSidebarVisible" :toggleSidebar="toggleSidebar" :data="user"></AppSidebar>
        <main>
            <AppHeader text="Dashboard" :data="user" :toggleSidebar="toggleSidebar" :isSidebarVisible="isSidebarVisible"></AppHeader>

            <div class="box-container">
                <Preloader :loading="loadings" />
                <form class="add-container" @submit.prevent="createNewAdmin">
                    <div class="container-box">
                        <p>Update Information</p>
                        <center>
                            <label for="file" >
                                <div style="width: 100px;height:100px;border-radius: 100px">
                                <div

                                    style="position: absolute;margin-top: 50px;margin-left: 80px;color: orange;font-size: 18px;cursor: pointer"
                                    class="fas fa-upload"
                                ></div>
                                <input
                                    type="file"
                                    style="display: none"
                                    id="file"
                                    @change="onImageChange"
                                />
                                <img
                                    v-if="previewImage"
                                    :src="previewImage"
                                    style="width: 100%;height:100%;border-radius: 100px"
                                    alt=""
                                />
                                <img
                                    v-else
                                    :src="utils.getImage(user.image)"
                                    style="width: 100%;height:100%;border-radius: 100px"
                                    alt=""
                                />
                                </div>
                            </label>
                        </center>
                        <p></p>
                        <div class="information">
                            <label>Email&nbsp;<span>*</span></label>
                            <input v-model="user.email" readonly type="email" name="email" required />
                        </div>
                        <div class="information">
                            <label>Name&nbsp;<span>*</span></label>
                            <input v-model="user.name"  type="text" name="name" required />
                        </div>
                        <div class="information">
                            <label>Phone&nbsp;<span>*</span></label>
                            <input v-model="user.phone" type="number" name="phone" required />
                        </div>
                        <div class="information">
                            <label>Location&nbsp;<span>*</span></label>
                            <input v-model="user.address" type="text" name="address" required />
                        </div>
                        <div class="information">
                            <label>Role&nbsp;<span>*</span></label>
                            <input
                                :value="utils.getRole(user.role)"
                                type="text"
                                readonly
                                required
                            />
                        </div>
                        <div class="information">
                            <label>Status&nbsp;<span>*</span></label>
                            <input v-model="user.status" type="text" name="status" required />
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
import { ref, onMounted } from "vue";
import { useUser } from "../composables/useUser.js";
import axios from "../../axios.js";
import Swal from "sweetalert2";
import { useRouter } from "vue-router";
import Utils from "../../Utils.js";
import pic from "../../../../public/images/default.png";
const isSidebarVisible = ref(false); // Ref to control sidebar visibility

const toggleSidebar = function () {
    isSidebarVisible.value = !isSidebarVisible.value; // Toggle visibility
};
export default {
    name: "Settings",
    components: { Preloader, AppHeader, AppSidebar },

    setup() {
        const router = useRouter();
        const { loadUser, user } = useUser();
        let loadings = ref(false);
        let previewImage = ref('');
        let image = ref('');

        let errors = ref('');

        const onImageChange = (event) => {
            const file = event.target.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    showError("Please upload a valid image file (JPEG/PNG).");
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    showError("Image must be under 2MB.");
                    return;
                }

                image.value = file;
                previewImage.value = URL.createObjectURL(file);
            }
        };


        const createNewAdmin = async () => {
            loadings.value = true;
            const formData = new FormData();
            formData.append('email', user.value.email);
            formData.append('name', user.value.name);
            formData.append('address', user.value.address);
            formData.append('status', user.value.status);
            formData.append('phone', user.value.phone);
            if (image.value) {
                formData.append('image', image.value);
            }

            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }

            try {
                const response = await axios.post("/update-user", formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                await handleResponse(response);
            } catch (error) {
                console.log(error)
                handleError(error);
            } finally {
                loadings.value = false;
            }
        };

        const handleResponse = async (response) => {
            if ([200, 201].includes(response.status)) {
                await Swal.fire({
                    title: "Success!",
                    text: "User information updated successfully",
                    icon: "success",
                    confirmButtonText: "Okay",
                });
            } else {
                await showError("An unexpected error occurred.");
            }
        };

        const handleError = (error) => {
            const errorMessage =
                error.response?.data?.message || "An error occurred. Please try again.";
            const validationErrors = error.response?.data?.errors || {};
            errors.value = Object.values(validationErrors).flat()[0] || errorMessage;
            showError(errors.value);
        };

        const showError = async (message) => {
            const Toast = Swal. mixin({
                toast: true,
                position: 'top-end',
                timer: 3000,
                timerProgressBar: true
            })

            await Toast.fire(message, 'Error!', 'error')
            // await Swal.fire({
            //     title: "Error!",
            //     text: message,
            //     icon: "error",
            //     confirmButtonText: "Okay",
            // });

        };

        const utils = new Utils();

        onMounted(loadUser);

        return {
            isSidebarVisible,
            toggleSidebar,
            utils,
            loadings,
            onImageChange,  // For handling image change
            previewImage,
            createNewAdmin,
            user,
            pic
        };
    },
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
.information input {
    width: 60%;
    padding: 10px;
    border-radius: 5px;
    outline: none;
    border: 1px solid #ccc;
    background: #f8f8f8;
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

@media (max-width: 600px) {
    .container-box {
        width: 100%;
    }
}
</style>
