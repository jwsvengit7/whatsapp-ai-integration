<template>
    <div class="dashboard">
        <AppSidebar :data="user"></AppSidebar>
        <main>
            <AppHeader text="AI MODEL" :data="user"></AppHeader>
            <div class="box-container">
                <div class="box-div">
                    <div class="box">
                        <img src="https://uat.smefunds.com/public/images/kike-logo.png" alt="" width="20" height="20"  onclick="window.location.href='educate-ai?name=Fuel'"/>



                    </div>


                </div>

            </div>
        </main>
    </div>
</template>

<script>
import AppSidebar from "../dashboard-components/AppSidebar.vue";
import AppHeader from "../dashboard-components/AppHeader.vue";
import Feature from "../dashboard-components/Feature.vue";
import { onMounted} from "vue";
import {useUser} from "../composables/useUser.js";
import Utils from "../../Utils.js";
import pic from "../../../../public/images/kike-logo.png";

export default {
    name: 'AI_MODEL',
    components: {Feature, AppHeader, AppSidebar},
    setup() {
        const { loadUser, user,loading, error } = useUser();
        const utils = new Utils();

        onMounted(() => {
            loadUser();

        });

        return { user,utils, loading, error ,pic};
    }


};
</script>

<style scoped>
.box-div{
    width:100%;
    height: 600px;
    overflow: scroll;
    display: flex;
    flex-wrap: wrap;
}
.box {
    width: 170px;
    height: 130px;
    border-radius: 10px;
    padding: 10px;
    border: 1px solid #ccc;
    margin: 10px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    button{
        padding: 10px;
        color: white;
        font-weight: bold;
        border: 0px none;
        border-radius: 30px;
        background: #007bff;
    }

    img {
        width: 100%;
        height: 100px;
    }
}
.box .p{
    font-weight: bold;
    font-size: 20px;
}

</style>
