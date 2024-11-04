<template>
    <div class="dashboard">
        <AppSidebar :toggleSidebar="toggleSidebar"  :isSidebarVisible="isSidebarVisible" :data="user"></AppSidebar>
        <main>
            <AppHeader text="Dashboard" :data="user" :toggleSidebar="toggleSidebar" :isSidebarVisible="isSidebarVisible"></AppHeader>

            <div class="box-container">
                <!-- User Table -->
                <table class="user-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Questions</th>
                        <th>Date Joined</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, index) in allProduct" :key="index">
                        <td>{{ index+1 }}</td>
                        <td>    <div style="display: flex;align-items: center"><img src="https://uat.smefunds.com/public/images/kike-logo.png" alt=""  style="width:35px;height: 35px;border-radius: 30px"/>&nbsp;{{ item.name }}</div></td>

                        <td>Total Questions: {{ item.questions.length }}</td>
                        <td>{{ utils.getDateFormat(item.created_at) }}</td>
                        <td><button  class="but" @click="deleteProduct(item.id)">Delete</button>
                            <button class="but" @click="editProduct(item.name)">Edit</button></td>
                    </tr>
                    <tr v-if="loading">
                        <td colspan="6">Loading...</td>
                    </tr>
                    <tr v-if="error">
                        <td colspan="6">Error: {{ error }}</td>
                    </tr>
                    <tr v-if="!loading && !allProduct.length && !error">
                        <td colspan="6">No users available</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</template>
<script>
import AppSidebar from "../dashboard-components/AppSidebar.vue";
import AppHeader from "../dashboard-components/AppHeader.vue";
import {useUser} from "../composables/useUser.js";
import {onMounted, ref} from "vue";
import pic from "../../../../public/images/1723524642068.jpeg";
import Utils from "../../Utils.js";
import axios from "../../axios.js";
import Swal from "sweetalert2";
import {useRouter} from "vue-router";

const isSidebarVisible = ref(false); // Ref to control sidebar visibility

const toggleSidebar = function () {
    isSidebarVisible.value = !isSidebarVisible.value; // Toggle visibility
};
export default {
    name: 'AllProduct',
    computed: {
        pic() {
            return `${pic}`
        }
    },
    components: { AppHeader, AppSidebar},
    methods: {
        editProduct(name) {
            this.$router.push("educate-ai?name="+name);
        }
    },
    setup() {
        const router = useRouter();
        const loadings = ref(false);
        const errors = ref('');

        const { loadUser,loadAllProduct,allProduct, user, loading, error } = useUser();

        const utils = new Utils();
        onMounted(() => {
            loadUser();
            loadAllProduct();
        });
        const deleteProduct = async (id) =>{

                loadings.value = true;
                errors.value = '';

                try {
                    const response = await axios.delete('/delete-product?id='+id
                    );

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
                        text: 'Product Delete successfully',
                        icon: 'success',
                        confirmButtonText: 'Okay'
                    });
                     window.location.replace('all-product');
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


        return { allProduct,user, loading, error,deleteProduct ,utils,isSidebarVisible,toggleSidebar};
    }
};
</script>

<style scoped>
.user-table {
    width: 100%;
    border-collapse: collapse;
}

.user-table th, .user-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
    font-size: 13px;
    color: #2c2b2b;
}

.user-table th {
    background-color: #f2f2f2;
    font-weight: bold;
    font-size: 13px;
}

.user-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.user-table tr:hover {
    background-color: #ddd;
}

.box-container {
    margin: 20px;
}
.but{
    margin-left: 10px;
    background: red;border: 0 none;outline: none;cursor: pointer;color:white;padding:10px;
    border-radius: 5px;
}
.but:nth-child(2){
    background: #0d1a3a;
}
</style>
