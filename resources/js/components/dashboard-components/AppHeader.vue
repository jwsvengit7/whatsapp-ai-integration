<script setup>
import { ref } from 'vue';
import { defineProps } from 'vue';
import Utils from "../../Utils.js";

const utils = new Utils();


    const props = defineProps({
    text: {
    type: String,
    required: true
},
    data: {
    type: Object,
    required: true
},
    toggleSidebar: {
    type: Function,
    required: true
},
    isSidebarVisible: {
    type: Boolean,
    required: true
}
});
</script>

<template>
    <header>
        <div class="text-content">
            <span class="none fas fa-bars"></span>
            <span @click="props.toggleSidebar" class="hide fas fa-bars"></span>
            <span><b>{{ text }}</b></span>
        </div>
        <div class="content">
            <button class="none"> Invite a member</button>
            <div style="cursor: pointer; display: flex; align-items: center; justify-content: space-around; margin-left: 10px">
                <img src="https://uat.smefunds.com/public/images/default.png" alt="" style="width:25px; height: 25px; border-radius: 30px"/>
                &nbsp;
                <span>{{ data.name }}</span>
                <span class="fas fa-chevron-down"></span>
            </div>
        </div>
    </header>


</template>



<style scoped>
.text-content {
    padding: 10px;
    display: flex;
    span {
        cursor: pointer;
        font-size: 17px;
    }
}

.hide {
    display: none;
}

header {
    width: 100%;
    align-items: center;
    justify-content: space-between;
    font-size: 14px;
    font-weight: 400;
    color: #18181b;
    margin: 5px;
    display: flex;
    flex: 1;
    background: #fff;
    height: 10%;
}

.content {
    display: flex;
    align-items: center;
    justify-content: space-around;
}

span {
    line-height: 32px;
    padding-right: 20px;
}

button {
    border: 0;
    outline: none;
    background: white;
    font-size: 14px;
    color: #0071ff;
    line-height: 22px;
    text-align: center;
    font-weight: bold;
    border-radius: 30px;
    width: 150px;
    cursor: pointer;
}

button:hover {
    color: #fff;
    background: #007bff;
}

.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100vh;
    background: #f4f4f4;
    padding: 20px;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
    z-index: 1000;
}

@media (max-width: 600px) {
    .hide {
        display: block;
    }
    .none {
        display: none;
    }
}
</style>
