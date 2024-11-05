<script setup>
import fuel from '../../../../public/images/fuel.jpeg';
import {defineProps} from "vue";
import Utils from "../../Utils.js";

const utils=new Utils();
const props = defineProps({

    data: {
        type: {},
        required: true
    },
    func:{
        type:function(){},
    }
});
</script>
<template>
<div class="box-div">
    <div class="box" v-for="(item, index) in props.data" :key="index">
        <p>{{item.name}}</p>
<!--        <img :src="utils.getImage(item.image)"  alt="" /> -->
        <img src="https://uat.smefunds.com/public/images/kike-logo.png"  alt="" />
        <p class="p"></p>
            <button @click="props.func">Add Product</button>
    </div>
</div>
</template>


<style >
.box-div{
    width:100%;
    height: 600px;
    overflow: scroll;
    display: flex;
    flex-wrap: wrap;
}
.box {
    width: 30%;
    height: 250px;
    border-radius: 10px;
    border: 1px solid #ccc;
    margin: 10px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    button{
        color: white;
        padding: 10px;
        font-weight: bold;
        border: 0 none;
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

@media (max-width: 800px) {
    .box{
        width: 38% !important;
        height: 180px;
    }

}

</style>
