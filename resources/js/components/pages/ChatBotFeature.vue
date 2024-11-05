<template>
    <div class="dashboard">
        <AppSidebar :toggleSidebar="toggleSidebar" :isSidebarVisible="isSidebarVisible" :data="user"></AppSidebar>
        <main>
            <AppHeader text="Dashboard" :data="user" :toggleSidebar="toggleSidebar" :isSidebarVisible="isSidebarVisible"></AppHeader>

            <div class="box-container">
                <div v-if="allProduct==null">HELLO</div>

                <Feature v-if="allProduct" :data="allProduct" :func="addProduct"></Feature>
            </div>
        </main>
    </div>
</template>

<script>
import AppSidebar from "../dashboard-components/AppSidebar.vue";
import AppHeader from "../dashboard-components/AppHeader.vue";
import Feature from "../dashboard-components/Feature.vue";
import { useUserStore } from "../../store.js";
import {computed, onMounted, ref} from "vue";
import router from "../../router.js";
import {useUser} from "../composables/useUser.js";
import Preloader from "../tools/preloader.vue";
const isSidebarVisible = ref(false);

const toggleSidebar = function () {
    isSidebarVisible.value = !isSidebarVisible.value; // Toggle visibility
};
export default {
    name: 'ChatBotFeature',
    components: {Preloader, Feature, AppHeader, AppSidebar},
    methods:{
        addProduct(){
            alert(1)
        }

    },
    setup() {
        const { loadUser, user, loadAllProduct,allProduct,loading, error } = useUser();

        onMounted(() => {
            loadUser();
            loadAllProduct()

        });

        return { user, loading, error,allProduct,isSidebarVisible,toggleSidebar };
    }


};
</script>

<style scoped>
/* Add any styles specific to this component */
</style>
