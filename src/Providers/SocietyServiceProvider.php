<?php


namespace Milebits\Society\Providers;

use Illuminate\Support\ServiceProvider;

class SocietyServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->mergeConfigFrom(__DIR__ . '/../../config/society.php', 'society');
    }
}