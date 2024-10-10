<script setup>
import logo from '../../../../public/images/kike-logo.png';
</script>

<template>
    <aside>
        <img :src="logo" width="100">
        <nav>
            <div class="nav-content">
                <router-link to="/dashboard">
                <span><span class="fas fa-home"></span>Dashboard</span>
                </router-link>
            </div>
            <div class="nav-content">
                <router-link to="/dashboard/customers">
                <span><span class="fas fa-users"></span>Customers</span>
                </router-link>
            </div>
            <div class="nav-content">
                <router-link to="/dashboard/analytics">
                <span><span class="fas fa-home"></span>Analytics</span>
                </router-link>
            </div>
            <div class="nav-content">
                <router-link to="/dashboard/statistics">
                <span><span class="fas fa-home"></span>Usage Statistics</span>
                </router-link>
            </div>
            <div class="nav-content">
                <router-link to="/dashboard/account">
                <span><span class="fas fa-home"></span>Account</span>
                </router-link>
            </div>
            <div class="nav-content">
                <router-link to="/dashboard/chat-feature">
                <span><span class="fas fa-home"></span>Settings</span>
                </router-link>
            </div>
            <div class="nav-content">
                <router-link to="/dashboard/chat-feature">

                <span><span class="fas fa-home"></span>Chatbot Features</span>
                </router-link>
            </div>


        </nav>

    </aside>

</template>

<style scoped>

</style>
