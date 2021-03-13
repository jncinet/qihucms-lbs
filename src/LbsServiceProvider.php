<?php

namespace Qihucms\Lbs;

use Illuminate\Support\ServiceProvider;
use Qihucms\Lbs\Gateways\BaiDuGateway;
use Qihucms\Lbs\Gateways\GaoDeGateway;
use Qihucms\Lbs\Gateways\TenXunGateway;

class LbsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('lbs.baidu', function () {
            return Lbs::BaiDu();
        });
        $this->app->singleton('lbs.tencent', function () {
            return Lbs::TenXun();
        });
        $this->app->singleton('lbs.gaode', function () {
            return Lbs::GaoDe();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/qihu_lbs.php', 'qihu_lbs');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'qihu_lbs');
        $this->publishes([
            __DIR__.'/../config/qihu_lbs.php' => config_path('qihu_lbs.php'),
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/qihu_lbs'),
        ], 'lbs');
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
    }
}
