<script setup>
import logo from '../../../../public/images/kike-logo.png';
import {defineProps, ref} from "vue";


    function logout(){
  localStorage.clear();
        window.location.replace('/account/login');
    }

const props = defineProps({

    data: {
        type: {},
        required: true
    },
    isSidebarVisible: {
        type: Boolean,
    },
    toggleSidebar: {
        type: Function,
    },



});

</script>

<template>
    <aside :class="['iaside', { 'is-visible': props.isSidebarVisible }]">
       <div style="display: flex;width: 100%;justify-content: space-between;align-items: center">
           <img src="https://uat.smefunds.com/public/images/kike-logo.png" width="100">
           <button class="btn" style="padding-right: 10px;color:white;background: unset;border: 0px " @click="toggleSidebar">Close</button>
       </div>

        <nav>
            <div class="nav-content">
                <router-link to="/dashboard">
                <span><span class="i1 fas fa-home"></span><span class="i">Dashboard</span></span>
                </router-link>
            </div>
            <div class="nav-content" v-if="props.data.role==='super_admin' || props.data.role === 'admin'">
                <router-link to="/dashboard/customers">
                    <span><span class="i1 fas fa-users"></span><span class="i">Customers</span></span>
                </router-link>
            </div>
            <div class="nav-content" v-if="props.data.role==='vendor'">
                <router-link to="/dashboard/my-customer">
                    <span><span class="i1 fas fa-users"></span><span class="i">My Customers</span></span>
                </router-link>
            </div>
            <div class="nav-content">
                <router-link to="/dashboard/chat-feature">
                    <span><span class="i1 fas fa-message"></span><span class="i">Fuel Available</span></span>
                </router-link>
            </div>

            <div v-if="props.data.role==='super_admin' || props.data.role === 'admin'" class="nav-content">
                <router-link to="/dashboard/create-product">
                    <span><span class="i1 fas fa-file"></span><span class="i">Create Cooking Fuel</span></span>
                </router-link>
            </div>
            <div class="nav-content" v-if="props.data.role==='super_admin'">
                <router-link to="/dashboard/add-admin">
                    <span><span class="i1 fas fa-gears"></span><span class="i">Add Admin</span></span>
                </router-link>
            </div>
            <div class="nav-content" v-if="props.data.role==='super_admin' || props.data.role === 'admin'">
                <router-link to="/dashboard/all-product">

                    <span><span class="i1 fas fa-users"></span><span class="i">All Cooking Fuel</span></span>
                </router-link>
            </div>
            <div class="nav-content" v-if="props.data.role==='super_admin'">
                <router-link to="/dashboard/users">

                    <span><span class="i1 fas fa-users"></span><span class="i">Users</span></span>
                </router-link>
            </div>
            <div class="nav-content">
                <router-link to="/dashboard/ai">

                    <span><span class="i1 fas fa-users"></span><span class="i">AI Model</span></span>
                </router-link>
            </div>
            <div class="nav-content">
                <router-link to="/dashboard/setting">
                    <span><span class="i1 fas fa-gear"></span><span class="i">Settings</span></span>
                </router-link>
            </div>
            <div class="nav-content logout">
                    <span v-on:click="logout"><span  class="fas fa-sign-out"></span>Sign out</span>
            </div>
        </nav>

    </aside>



</template>

<style >
.logout{
    margin-top: 100px;
}
.i{
    padding-left: 5px;
}
.i1{
    width: 15px;
}
.mainAside{
    display: block !important;
}
.btn{
    display: none;
}
@media (max-width: 800px) {
    .btn{
        display: block;
    }

}

</style>
