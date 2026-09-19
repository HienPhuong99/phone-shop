<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Services\CartService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.shop', function ($view) {
            $view->with('cartItemCount', app(CartService::class)->itemCount(request()));
            $view->with('announcement', Announcement::current()->latest('id')->first());
        });
    }
}
