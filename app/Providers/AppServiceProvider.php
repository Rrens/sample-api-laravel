<?php

namespace App\Providers;

use App\Models\PersonalAccessTokenMysql;
use Illuminate\Support\ServiceProvider;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FakerGenerator::class, function () {
            return FakerFactory::create(config('app.faker_locale', 'en_US'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessTokenMysql::class);
    }
}
