<template>
    <div id="app">
        <router-view></router-view> <!-- This is where routed components will be rendered -->
    </div>

</template>

<script>
import {useUser} from "./composables/useUser.js";
import {onMounted, ref} from "vue";
import Utils from "../Utils.js";


export default {
    name: 'App',
        // setup() {
        //     const {loadUser, user, loading, error} = useUser();
        //     const utils = new Utils();
        //     if(loadUser().catch(()=>{
        //         window.location.replace("http://localhost:8000")
        //     }))
        //
        //
        //     onMounted(() => {
        //         loadUser();
        //     });
        //
        //     return {user, loading, error, utils}
        // }

};


</script>
