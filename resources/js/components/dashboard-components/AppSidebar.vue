<script setup>
import logo from '../../../../public/images/kike-logo.png';
import {defineProps} from "vue";


    function logout(){
alert(1)
    }

const props = defineProps({

    data: {
        type: {},
        required: true
    },



});

</script>

<template>
    <aside>
        <img :src="logo" width="100">
        <nav>
            <div class="nav-content">
                <router-link to="/dashboard">
                <span><span class="i1 fas fa-home"></span><span class="i">Dashboard</span></span>
                </router-link>
            </div>
            <div class="nav-content">
                <router-link to="/dashboard/customers">
                    <span><span class="i1 fas fa-users"></span><span class="i">Customers</span></span>
                </router-link>
            </div>

            <div class="nav-content">
                <router-link to="/dashboard/account">
                    <span><span class="i1 fas fa-usd"></span><span class="i">Account</span></span>
                </router-link>
            </div>

            <div class="nav-content">
                <router-link to="/dashboard/chat-feature">

                    <span><span class="i1 fas fa-message"></span><span class="i">Chatbot Features</span></span>
                </router-link>
            </div>
            <div class="nav-content">
                <router-link to="/dashboard/setting">
                    <span><span class="i1 fas fa-gear"></span><span class="i">Settings</span></span>
                </router-link>
            </div>
            <div v-if="props.data.role==='super_admin'" class="nav-content">
                <router-link to="/dashboard/create-product">
                    <span><span class="i1 fas fa-file"></span><span class="i">Create Product</span></span>
                </router-link>
            </div>
            <div class="nav-content" v-if="props.data.role==='super_admin'">
                <router-link to="/dashboard/add-admin">
                    <span><span class="i1 fas fa-gears"></span><span class="i">Add Admin</span></span>
                </router-link>
            </div>
            <div class="nav-content" v-if="props.data.role==='super_admin'">
                <router-link to="/dashboard/users">

                    <span><span class="i1 fas fa-users"></span><span class="i">Users</span></span>
                </router-link>
            </div>
            <div class="nav-content logout">


                    <span v-on:click="logout"><span  class="fas fa-sign-out"></span>Sign out</span>

            </div>


        </nav>

    </aside>

</template>

<style scoped>
.logout{
    margin-top: 150px;
}
.i{
    padding-left: 5px;
}
.i1{
    width: 15px;
}

</style>
