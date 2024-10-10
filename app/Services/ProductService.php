<?php

namespace App\Services;

interface ProductService
{

    public function fetchProduct();

    public function addProduct(\Illuminate\Http\Request $request);
}
