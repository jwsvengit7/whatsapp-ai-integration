<script setup>

import image from '../../../../public/images/download.png';
import logo from '../../../../public/images/kike-logo.png';
import imageBg from '../../../../public/images/img_login_banner_right.929cc6f5.png';


</script>

<template>
    <div class="left">
        <p></p>
       <img :src="logo" alt="" width="100">
        <div class="side-div">
            <h2 class="build">Build Powerful Interaction Apps with Voice & Video Chat API</h2>
            <div class="quick-integration">
                <div class="quick-row">
                    <img :src="image" alt="Quick Integration" width="25" />
                    <div class="quick">
                        <p>Quick Response From Kiki AI</p>
                        <p>Nearby Customer are interested in a verified Vendor</p>
                    </div>
                </div>
                <div class="quick-row">
                    <img :src="image" alt="Quick Integration" width="25" />
                    <div class="quick">
                        <p>BIO-FUEL</p>
                        <p>Set an affordable price BIO-FUEL</p>
                    </div>
                </div>

            </div>
        </div>
        <img :src="imageBg" class="bg-color">
    </div>
</template>

<style scoped>

</style>
