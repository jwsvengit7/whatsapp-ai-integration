<template>
    <div class="dashboard">
        <AppSidebar :data="user"></AppSidebar>
        <main>
            <AppHeader text="Users" :data="user"></AppHeader>
            <div class="box-container">
                <table class="user-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>

                        <th>Date Joined</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, index) in allUsers" :key="index">
                        <td>{{ index+1 }}</td>
                        <td>    <div style="display: flex;align-items: center"><img :src="utils.getImage(item.image)" alt=""  style="width:25px;height: 25px;border-radius: 30px"/>&nbsp;{{ item.name }}</div></td>
                        <td>{{ item.email }}</td>
                        <td>{{ item.phone }}</td>
                        <td>{{ utils.getRole(item.role) }}</td>

                        <td>{{ utils.getDateFormat(item.created_at) }}</td>
                        <td>   <button
                            :class="{
                                   'active-status': item.status === 'active',
                                    'inactive-status': item.status === 'inactive'
                                    }"  class="but">
                            {{ item.status.toUpperCase() }}
                        </button></td>
                        <td>
                            <button class="but">Delete</button>

                            <button class="but">Edit</button>
                        </td>
                    </tr>
                    <tr v-if="loading">
                        <td colspan="8">Loading...</td>
                    </tr>
                    <tr v-if="error">
                        <td colspan="8">Error: {{ error }}</td>
                    </tr>
                    <tr v-if="!loading && !allUsers.length && !error">
                        <td colspan="8">No users available</td>
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

export default {
    name: 'Users',
    computed: {
        pic() {
            return pic
        }
    },
    components: { AppHeader, AppSidebar},
    setup() {
        const { loadUser,loadAllUsers,allUsers, user, loading, error } = useUser();
        const userList = ref([]);
        const utils = new Utils();


        onMounted(() => {
            loadUser();
            loadAllUsers();
        });

        return { allUsers,user, loading, error ,utils};
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
    background: orange;
}

.active-status {
    background-color: #007bff;
    font-size: 10px;
}

.inactive-status {
    background-color: orange;
    font-size: 10px;
}
</style>
