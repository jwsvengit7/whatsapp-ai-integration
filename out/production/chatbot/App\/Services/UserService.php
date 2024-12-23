<?php
namespace App\Services;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface UserService
{
    public function userSignup(Request $request): JsonResponse;
    public function userLogin(Request $request): JsonResponse;
    public function getUserById() :JsonResponse;
    public function confirmAccount(Request $request) :JsonResponse;
    public function changePassword(Request $request):JsonResponse;
    public function resendVerification(Request $request);
    public function updateAccount(Request $req):JsonResponse;
    public function forgetPassword(Request $request):JsonResponse;
    public function verifyLink($token) : JsonResponse;
    public function refererLink($link) : JsonResponse;

}
