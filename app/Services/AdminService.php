<?php

namespace App\Services;

interface AdminService
{

    public function createProduct(\Illuminate\Http\Request $request);


    public function createAdmin(\Illuminate\Http\Request $request);

    public function fetchAllUsers();
}
