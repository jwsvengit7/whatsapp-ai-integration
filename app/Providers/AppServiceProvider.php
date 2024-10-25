<?php

namespace App\Providers;

use App\Services\AdminService;
use App\Services\CustomerService;
use App\Services\CustomerServiceImpl;
use App\Services\ProductServiceImpl;
use App\Services\AdminServiceImpl;
use App\Services\ProductService;
use App\Services\UserService;
use App\Services\UserServiceImpl;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserService::class, UserServiceImpl::class);
        $this->app->bind(CustomerService::class, CustomerServiceImpl::class);
        $this->app->bind(ProductService::class, ProductServiceImpl::class);
        $this->app->bind(AdminService::class, AdminServiceImpl::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        //
    }
}
