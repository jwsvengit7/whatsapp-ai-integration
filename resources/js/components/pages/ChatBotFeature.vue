<template>
    <div class="dashboard">
        <AppSidebar :data="user"></AppSidebar>
        <main>
            <AppHeader text="Product Feature" :data="user"></AppHeader>
            <div class="box-container">
                <Feature></Feature>
                <p v-if="user">Welcome, {{ user.name }}!</p>
                <p v-else>Please log in to access features.</p>
            </div>
        </main>
    </div>
</template>

<script>
import AppSidebar from "../dashboard-components/AppSidebar.vue";
import AppHeader from "../dashboard-components/AppHeader.vue";
import Feature from "../dashboard-components/Feature.vue";
import { useUserStore } from "../../store.js";
import {computed, onMounted} from "vue";
import router from "../../router.js";
import {useUser} from "../composables/useUser.js";

export default {
    name: 'ChatBotFeature',
    components: {Feature, AppHeader, AppSidebar},
    setup() {
        const { loadUser, user, loading, error } = useUser();

        onMounted(() => {
            loadUser();

        });

        return { user, loading, error };
    }


};
</script>

<style scoped>
/* Add any styles specific to this component */
</style>
