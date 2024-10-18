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

});
</script>
<template>
<div class="box-div">
    <div class="box" v-for="(item, index) in props.data" :key="index">
        <p>{{item.name}}</p>
        <img :src="utils.getImage(item.image)"  alt="" />
        <p class="p"><span style="text-decoration: line-through;">N</span>{{item.price}}</p>
            <button onclick="window.location.href='account/add-product'">Add Product</button>
    </div>


</div>
</template>


<style scoped>
.box-div{
    width:100%;
    height: 600px;
    overflow: scroll;
    display: flex;
    flex-wrap: wrap;
}
.box {
    width: 200px;
    height: 250px;
    border-radius: 10px;
    border: 1px solid #ccc;
    margin: 10px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    button{
        width: 70px;
        height: 35px;
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
