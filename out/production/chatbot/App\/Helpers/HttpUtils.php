<?php
namespace App\Helpers;
class HttpUtils
{
    public static function URLs():array
    {
        return
            ['createAccount', 'loginAuth','confirmAccount', 'resendVerification','forgetPassword','verifyLink','refererLink','image'];

    }

}
