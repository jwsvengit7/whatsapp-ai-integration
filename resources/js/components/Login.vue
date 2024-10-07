<template>
    <div class="login-div">
        <Sidebar></Sidebar>
        <div class="right">
            <HeaderAuth  message="Don't have an account?" text="Sign up" route="/account/signup"></HeaderAuth>
            <div class="form-div">
                <div class="form">
                    <h1>Welcome Back !</h1>

                    <div data-v-1adbda30="" class="el-form-item ">
                        <label for="email" class="el-form-item__label">Email</label>
                        <p></p>


                        <input type="text" autocomplete="off" placeholder="name@email.com" maxlength="100" class="el-input__inner"><!----><!----><!----><!---->


                    </div>
                    <div data-v-1adbda30="" class="el-form-item ">
                        <label for="email" class="el-form-item__label">Password</label>
                        <p></p>


                            <input type="password" autocomplete="off" placeholder="********" maxlength="100" class="el-input__inner"><!----><!----><!----><!---->




                    </div>

                    <div data-v-1adbda30="" class="el-form-item v">
                        <p class="" >Forget Password</p>

                    </div>

                    <button class="login-button">Login</button>
                    <br>
                    <button class="login-code">Login with verification code</button>


                </div>

            </div>
        </div>
    </div>
</template>

<script>

import Sidebar from "./tools/Sidebar.vue";
import HeaderAuth from "./tools/HeaderAuth.vue";

export default {
    name: 'Login',
    components: {HeaderAuth, Sidebar}

};
</script>

<style scoped>

</style>
