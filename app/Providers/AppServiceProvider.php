<?php

namespace App\Providers;

// use App\Services\CloudinaryService;
use App\Services\SupabaseStorageService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // $this->app->singleton(CloudinaryService::class, function ($app) {
        //     return new CloudinaryService();
        // });

        $this->app->singleton(SupabaseStorageService::class, function ($app) {
            return new SupabaseStorageService();
        });

        // Register Repository Service Provider
        $this->app->register(\App\Providers\RepositoryServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
