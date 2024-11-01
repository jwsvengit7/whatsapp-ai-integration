<script setup>
import { defineProps } from 'vue';
import Utils from "../../Utils.js";
import pic from "../../../../public/images/default.png";

const utils = new Utils()
const props = defineProps({

    text: {
        type: String,
        required: true
    },
    data: {
        type: {},
        required: true
    },

});
</script>

<template>
        <header>
            <div class="text-content">
                <span class="fas fa-chevron-left"></span>
                <span><b>{{ text }}</b></span>
            </div>
            <div class="content">
                <button>Invite a member</button>
                <div style="cursor: pointer;display: flex;align-items: center;justify-content: space-around;margin-left: 10px">
                    <img src="https://uat.smefunds.com/public/images/default.png" alt=""  style="width:25px;height: 25px;border-radius: 30px"/>
                    &nbsp;
                    <span>{{props.data.name}}</span>
                    <span class="fas fa-chevron-down"></span>
                </div>
            </div>


        </header>

</template>

<style scoped>
.text-content{
    width:20%;
    padding: 10px;

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
.content{
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
</style>
