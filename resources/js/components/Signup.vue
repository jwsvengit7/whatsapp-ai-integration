<template>
    <div class="login-div">
        <Sidebar></Sidebar>
        <div class="right">
            <HeaderAuth  message="Already have an account?" text="Login" route="/account/login"></HeaderAuth>
            <div class="form-div">
                <div class="form">
                    <h1>Create your account !</h1>

                    <div data-v-1adbda30="" class="el-form-item ">
                        <label for="name" class="el-form-item__label">Username</label>
                        <p></p>


                        <input type="text" autocomplete="off" placeholder="john doe" maxlength="100" class="el-input__inner"><!----><!----><!----><!---->


                    </div>
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
                    <div data-v-1adbda30="" class="el-form-item ">
                        <label for="email" class="el-form-item__label">Select Product</label>
                        <p></p>
                        <select  class="el-input__inner" style="width:100%;height: 45px;padding: 0;padding-left: 10px">
                            <option>Gas</option>
                            <option>Fuel</option>
                        </select>

                    </div>
                    <div data-v-1adbda30="" class="el-form-item ">
                        <label for="password" class="el-form-item__label">Confirm Password</label>
                        <p></p>
                        <input type="password" autocomplete="off" placeholder="********" maxlength="100" class="el-input__inner"><!----><!----><!----><!---->
                    </div>

                    <div data-v-1adbda30="" class="el-form-item v2">

<p>I want to receive product updates, marketing news, and other relevant content by email from
    </p>
                    </div>

                    <button class="login-button">Start free trail</button>



                </div>

            </div>
        </div>
    </div>
</template>

<script>
import HeaderAuth from "./tools/HeaderAuth.vue";
import Sidebar from "./tools/Sidebar.vue";

export default {
    name: 'Signup',
    components: {HeaderAuth,Sidebar}

};
</script>

<style scoped>

</style>
