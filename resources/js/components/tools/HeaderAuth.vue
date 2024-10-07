<script setup>
import { defineProps } from 'vue';

// Define props
const props = defineProps({
    message: {
        type: String,
        required: true
    },
    text: {
        type: String,
        required: true
    },
    route: {
        type: String,
        required: true
    }
});
</script>

<template>
    <div>
        <header>
            <span>{{ message }}</span>
            <router-link :to="route"> <!-- Use :to for binding -->
                <button>{{ text }}</button>
            </router-link>
        </header>
    </div>
</template>

<style scoped>
header {
    align-items: center;
    justify-content: flex-end;
    font-size: 14px;
    font-weight: 400;
    color: #18181b;
    margin: 5px;
    padding-right: 32px;
    display: flex;
    flex: 1;
    background: #fff;
    height: 100%;
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
    line-height: 32px;
    text-align: center;
    font-weight: bold;
    border-radius: 30px;
    width: 80px;
    cursor: pointer;
}

button:hover {
    color: #fff;
    background: #007bff;
}
</style>
