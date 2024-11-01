<template>
    <div class="dashboard">
        <AppSidebar :data="user"></AppSidebar>
        <main>
            <AppHeader text="My Customer" :data="user"></AppHeader>
            <div class="box-container">
                <!-- User Table -->
                <table class="user-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Date Joined</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, index) in allCustomers" :key="index">
                        <td>{{ index+1 }}</td>
                        <td>    <div style="display: flex;align-items: center"><img src="https://uat.smefunds.com/public/images/default.png" alt=""  style="width:25px;height: 25px;border-radius: 30px"/>&nbsp;{{ item.name }}</div></td>

                        <td>{{ item.phone }}</td>
                        <td>{{ utils.getDateFormat(item.created_at) }}</td>
                        <td><button style="background: none;border: 0 none;outline: none;cursor: pointer" @click="openLink(item)"><b>Conversations</b></button></td>
                    </tr>
                    <tr v-if="loading">
                        <td colspan="5">Loading...</td>
                    </tr>
                    <tr v-if="error">
                        <td colspan="5">Error: {{ error }}</td>
                    </tr>
                    <tr v-if="!loading && !allCustomers.length && !error">
                        <td colspan="5">No users available</td>
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
    name: 'MyCustomer',
    computed: {
        pic() {
            return pic
        }
    },
    components: { AppHeader, AppSidebar},
    methods:{
        openLink(item) {
            this.$router.push("view-conversations?id="+item.id+"&name="+item.name+"&phone="+item.phone);
        }
    },
    setup() {
        const { loadUser,loadAllCustomers,allCustomers, user, loading, error } = useUser();
        const utils = new Utils();
        onMounted(() => {
            loadUser();
            loadAllCustomers();
        });

        return { allCustomers,user, loading, error ,utils};
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
</style>
