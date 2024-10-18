<script >
import Feature from "../dashboard-components/Feature.vue";
import AppHeader from "../dashboard-components/AppHeader.vue";
import AppSidebar from "../dashboard-components/AppSidebar.vue";
import {useUser} from "../composables/useUser.js";
import Utils from "../../Utils.js";
import {onMounted} from "vue";
import pic from "../../../../public/images/kike-logo.png";
export default {
    name: 'AI_MODEL',
    components: {Feature, AppHeader, AppSidebar},
    setup(){
        const { loadUser, user,loading, error } = useUser();
        const utils = new Utils();

        onMounted(() => {
            loadUser();

        });

        return { user,utils, loading, error ,pic};
    }
}

</script>
<template>
    <div class="dashboard">
        <AppSidebar :data="user"></AppSidebar>
        <main>
            <AppHeader text="Conversations" :data="user"></AppHeader>
            <div class="box-container">


            </div>
        </main>
    </div>
</template>

<style scoped>

</style>
