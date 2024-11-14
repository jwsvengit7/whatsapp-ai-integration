<?php
namespace App\Helpers;

class Response {
    public static function loginResponse($user,$token): array
    {
        return [
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'role' => $user->role->name,
                'status' =>$user->status->name,
                'token' => $token,
            ]
        ];
    }
}
