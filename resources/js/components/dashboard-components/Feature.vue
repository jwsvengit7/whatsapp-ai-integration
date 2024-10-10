<script setup>
import fuel from '../../../../public/images/fuel.jpeg';
</script>
<template>
<div class="box-div">
    <div class="box">
        <p>Bio Fuel</p>
        <img :src="fuel"  alt="" />
        <p class="p"><span style="text-decoration: line-through;">N</span>10,000</p>
        <button>Sell</button>


    </div>
    <div class="box">
        <p>Kerosene</p>
        <img :src="fuel"  alt="" />
        <p class="p"><span style="text-decoration: line-through;">N</span>10,000</p>
        <button>Sell</button>


    </div>

    <div class="box">
        <p>Gel</p>
        <img :src="fuel"  alt="" />
        <p class="p"><span style="text-decoration: line-through;">N</span>10,000</p>
        <button>Sell</button>

    </div>
    <div class="box">
        <p>Fuel</p>
        <img :src="fuel"  alt="" />
        <p class="p"><span style="text-decoration: line-through;">N</span>10,000</p>
        <button>Sell</button>

    </div>


</div>
</template>


<style scoped>
.box-div{
    width:100%;
    height: auto;
    display: flex;
    flex-wrap: wrap;
}
.box {
    width: 200px;
    height: 300px;
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
        border-radius: 205px;
        width: 130px;
        height: 130px;

    }
}
.box .p{
        font-weight: bold;
    font-size: 20px;
    }


</style>
