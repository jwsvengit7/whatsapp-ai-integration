<template>
    <div class="dashboard">
        <AppSidebar></AppSidebar>
        <main>
            <AppHeader text="Dashboard" :data="user"></AppHeader>
            <div class="box-container">
                <p v-if="loading">Loading user data...</p>
                <p v-if="error">{{ error }}</p>
                <p v-if="user">Welcome, {{ user.name }}!</p>
                <p v-if="!user && !loading">Please log in to see your information.</p>
            </div>
        </main>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import AppSidebar from "../dashboard-components/AppSidebar.vue";
import AppHeader from "../dashboard-components/AppHeader.vue";
import { useUser } from "../composables/useUser.js";

export default {
    name: 'Dashboard',
    components: { AppSidebar, AppHeader },

    setup() {
        const { loadUser, user, loading, error } = useUser();

        onMounted(() => {
            loadUser();

        });

        return { user, loading, error };
    }
};
</script>
