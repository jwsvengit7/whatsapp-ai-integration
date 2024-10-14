<template>
    <div class="dashboard">
        <AppSidebar :data="user"></AppSidebar>
        <main>
            <AppHeader text="Product Feature" :data="user"></AppHeader>
            <div class="box-container">
                <Feature :data="allProduct"></Feature>
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
        const { loadUser, user, loadAllProduct,allProduct,loading, error } = useUser();

        onMounted(() => {
            loadUser();
            loadAllProduct()

        });

        return { user, loading, error,allProduct };
    }


};
</script>

<style scoped>
/* Add any styles specific to this component */
</style>
