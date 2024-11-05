<template>
    <div class="dashboard">
        <AppSidebar  :isSidebarVisible="isSidebarVisible" :toggleSidebar="toggleSidebar" :data="user"></AppSidebar>
        <main>
            <AppHeader text="Dashboard" :data="user" :toggleSidebar="toggleSidebar" :isSidebarVisible="isSidebarVisible"></AppHeader>
            <div class="box-container">
                <p v-if="loading">Loading user data...</p>
                <p v-if="error">{{ error }}</p>
                <p v-if="user">Welcome, {{ user.name }}!</p>
                <p v-if="!user && !loading">Please log in to see your information.</p>
                <div class="wrap">


                <div class="box-items">
                    <span class="fa fa-users size"></span>

                    <span class="nu">11</span>
                    <span class="text">Total Users</span>

                </div>
                <div class="box-items">
                    <span class="fa fa-users size"></span>

                    <span class="nu">ACTIVE</span>
                    <span class="text">Status</span>

                </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import {onMounted, ref} from 'vue';
import AppSidebar from "../dashboard-components/AppSidebar.vue";
import AppHeader from "../dashboard-components/AppHeader.vue";
import { useUser } from "../composables/useUser.js";
const isSidebarVisible = ref(false); // Ref to control sidebar visibility

const toggleSidebar = function () {
    isSidebarVisible.value = !isSidebarVisible.value; // Toggle visibility
};
export default {
    name: 'Dashboard',
    components: { AppSidebar, AppHeader },

    setup() {
        const { loadUser, user, loading, error } = useUser();

        onMounted(() => {
            loadUser();

        });

        return { user, loading, error,toggleSidebar,isSidebarVisible };
    }
};
</script>

<style>
.box-items{
    width:170px;
    height: 170px;
    box-shadow: 0 0 3px 1px #ddd;
    border-radius: 10px;
    padding: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 10px;
    margin-right: 30px;
}
.wrap{
    display: flex;
    flex-wrap: wrap;
 }
.size{
    font-size: 30px;
    color: #0d1a3a;
    padding-top: 20px;
}
.nu{
    color: #0d1a3a;
    font-size: 20px;
    padding-top: 20px;
}
.text{
    font-size: 15px;
    padding-top: 20px;
    color: #0d1a3a;
}


</style>
