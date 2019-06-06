<?php
/**
 * Created by PhpStorm.
 * User: wyh
 * Date: 2019/5/27
 * Time: 17:23
 */

namespace Wuliaowyh\AliOss;


use Illuminate\Support\ServiceProvider;

class OssServiceProvider extends ServiceProvider
{
    public function register (){

        $this->mergeConfigFrom(__DIR__.'/../config/alioss.php', 'alioss');
        $this->app->singleton(Oss::class, function () {
            return new Oss();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/alioss.php' => config_path('alioss.php'),
        ]);
    }
}