<script setup>

    import image from '../../../../public/images/download.png';
    import imageBg from '../../../../public/images/img_login_banner_right.929cc6f5.png';


</script>

<template>
    <div class="left">
        <h2>Whatsapp AI</h2>
        <div class="side-div">
            <h2 class="build">Build Powerful Interaction Apps with Voice & Video Chat API</h2>
            <div class="quick-integration">
                <div class="quick-row">
                    <img :src="image" alt="Quick Integration" width="25" />
                    <div class="quick">
                        <p>Quick Integration</p>
                        <p>Few lines of code, within 30 mins</p>
                    </div>
                </div>
                <div class="quick-row">
                    <img :src="image" alt="Quick Integration" width="25" />
                    <div class="quick">
                        <p>Quick Integration</p>
                        <p>Few lines of code, within 30 mins</p>
                    </div>
                </div>
                <div class="quick-row">
                    <img :src="image" alt="Quick Integration" width="25" />
                    <div class="quick">
                        <p>Quick Integration</p>
                        <p>Few lines of code, within 30 mins</p>
                    </div>
                </div>
            </div>
        </div>
        <img :src="imageBg" class="bg-color">
    </div>
</template>

<style scoped>

</style>
