<template>
    <div class="dashboard">
        <AppSidebar :toggleSidebar="toggleSidebar"  :isSidebarVisible="isSidebarVisible" :data="user"></AppSidebar>
        <main>
            <AppHeader text="Dashboard" :data="user" :toggleSidebar="toggleSidebar" :isSidebarVisible="isSidebarVisible"></AppHeader>

            <div class="box-container">
                <p v-if="user">Welcome, {{ user.name }}!</p>
                <p v-else>Please log in to access features.</p>
                <Preloader :loading="loadings" />
                <form class="add-container" @submit.prevent="createNewAdmin">
                    <div class="container-box">
                        <p>Product Information</p>
                        <div class="information" v-for="(label, index) in adminFields" :key="index">
                            <label>{{ label.text }}&nbsp;<span>*</span></label>
                            <input
                                v-model="label.model"
                                :type="label.type"
                                :name="label.name"
                                required
                            />
                        </div>
                        <div class="information">
                            <label>Image&nbsp;<span>*</span></label>
                            <img v-if="previewImage" :src="previewImage" alt="Preview Image" style="max-width: 200px; margin-top: 10px;"/>
                            <label for="hide" class="hide">Upload File</label>
                            <input type="file" @change="onImageChange" style="display: none" id="hide"/>
                        </div>

                        <div class="information">
                            <label>Description&nbsp;<span>*</span></label>
                            <textarea class="information-textarea" name="description" v-model="description"></textarea>
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
const isSidebarVisible = ref(false); // Ref to control sidebar visibility

const toggleSidebar = function () {
    isSidebarVisible.value = !isSidebarVisible.value; // Toggle visibility
};
export default {
    name: 'CreateProduct',
    components: { Preloader, AppHeader, AppSidebar },

    setup() {
        const router = useRouter();
        const { loadUser, user } = useUser();

        const price = ref('');
        const rate = ref('');
        const name = ref('');
        const image = ref(null); // For storing the file
        const description = ref('');
        const loadings = ref(false);
        const errors = ref('');
        const previewImage = ref(''); // For previewing image before uploading

        const adminFields = ref([
            { text: 'Product Name', model: name, type: 'text', name: 'name' },
        ]);


        const onImageChange = (event) => {
            const file = event.target.files[0];
            if (file) {
                image.value = file;
                previewImage.value = URL.createObjectURL(file);
            }
        };

        const createNewAdmin = async () => {
            loadings.value = true;
            errors.value = '';

            const formData = new FormData();
            formData.append('description', description.value);
            formData.append('name', name.value);
            if (image.value) {
                formData.append('image', image.value);
            }

            try {
                const response = await axios.post('/create-product', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
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
                    text: 'Product created successfully',
                    icon: 'success',
                    confirmButtonText: 'Okay'
                });
                await router.push('/dashboard/all-product');
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
            price,
            rate,
            name,
            image,
            description,
            loadings,
            errors,
            adminFields,
            createNewAdmin,
            onImageChange,
            previewImage,
            user,
            isSidebarVisible,
            toggleSidebar
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
.information-textarea {
    width: 51%;
    padding: 10px;
    border-radius: 5px;
    outline: none;
    border: 1px solid #ccc;
    background: #f8f8f8;
}
.information-textarea{
    width:52%
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
.hide{

    padding: 15px;
    background: #007bff;
    color: #fff !important;
    cursor: pointer;
    border-radius: 5px;
}

@media (max-width: 600px) {
    .container-box {
        width: 100%;
    }
}
</style>
