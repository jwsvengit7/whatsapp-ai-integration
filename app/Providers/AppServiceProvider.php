<?php

namespace App\Providers;

use App\Services\CustomerService;
use App\Services\CustomerServiceImpl;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
