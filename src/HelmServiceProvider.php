<?php 

namespace Kytoonlabs\LaravelHelm;

use Illuminate\Support\ServiceProvider;

class HelmServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/helm.php' => config_path('helm.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/helm.php', 'helm');
    }
}

