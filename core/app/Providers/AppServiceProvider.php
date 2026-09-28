<?php

namespace App\Providers;

use Illuminate\{
    Support\ServiceProvider,
    Support\Facades\DB
};
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (!app()->isLocal() && (request()->isSecure() || request()->header('X-Forwarded-Proto') === 'https')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        Paginator::useBootstrap();
        view()->composer('*', function ($view) {
            static $setting = null;
            static $extra_settings = null;
            static $menus = null;

            if ($setting === null) {
                $setting = DB::table('settings')->find(1);
                $extra_settings = DB::table('extra_settings')->find(1);
                $menus = DB::table('menus')->find(1);
            }

            $view->with('setting', $setting);
            $view->with('extra_settings', $extra_settings);
            $view->with('menus', $menus);

            if (!session()->has('popup')) {
                view()->share('visit', 1);
            }
            session()->put('popup', 1);
        });
    }

    public function register()
    {
    }
}
