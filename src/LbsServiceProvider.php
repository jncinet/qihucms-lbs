<?php

namespace Qihucms\Lbs;

use Illuminate\Support\ServiceProvider;

class LbsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('tencentLbs', function () {
            return new Tencent();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/qihu_lbs.php' => config_path('qihu_lbs.php'),
        ], 'lbs');
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
    }
}
