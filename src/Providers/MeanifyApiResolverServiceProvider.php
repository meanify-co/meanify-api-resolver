<?php

namespace Meanify\ApiResolver\Providers;

use Illuminate\Support\ServiceProvider;

class MeanifyApiResolverServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        //Global helper
        if (file_exists(__DIR__ . '/../Helpers/boot.php')) {
            require_once __DIR__ . '/../Helpers/boot.php';
        }

        //Config
        $this->publishes([
            __DIR__ . '/../Config/meanify-api-resolver.php' => config_path('meanify-api-resolver.php'),
        ], 'meanify-configs');
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/meanify-api-resolver.php',
            'meanify-api-resolver'
        );

    }
}
