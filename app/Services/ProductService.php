<?php

namespace App\Services;

interface ProductService
{

    public function fetchProduct();

    public function addProduct(\Illuminate\Http\Request $request);
    public function updateProduct(\Illuminate\Http\Request $request);

    public function deleteProduct(\Illuminate\Http\Request $request);
}
