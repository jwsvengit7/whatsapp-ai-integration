
<template>
    <div class="dashboard">
        <AppSidebar></AppSidebar>
        <main>
            <AppHeader text="Customers" :data="user"></AppHeader>
            <div class="box-container">

            </div>
        </main>
    </div>
</template>

<script>
import AppSidebar from "../dashboard-components/AppSidebar.vue";
import AppHeader from "../dashboard-components/AppHeader.vue";
import {useUser} from "../composables/useUser.js";
import {onMounted} from "vue";

export default {
    name: 'Customer',
    components: { AppHeader, AppSidebar},
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

</style>
