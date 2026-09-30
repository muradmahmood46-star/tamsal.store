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
            static $default_language = null;
            static $all_currencies = null;
            static $header_categories = null;
            static $footer_pages_pos0_2 = null;
            static $footer_pages_pos1_2 = null;
            static $free_shipping = null;
            static $global_popup = null;

            if ($setting === null) {
                try {
                    $setting = \App\Models\Setting::first();
                } catch (\Throwable $e) {}
                if (!$setting) {
                    $setting = DB::table('settings')->find(1);
                }
                $extra_settings = DB::table('extra_settings')->find(1);
                $menus = DB::table('menus')->find(1);
                $default_language = DB::table('languages')->where('is_default', 1)->first();
                $all_currencies = DB::table('currencies')->get();
                $header_categories = DB::table('categories')->whereStatus(1)->take($setting->buyer_category_limit ?? 50)->get();
                $footer_pages_pos0_2 = DB::table('pages')->wherePos(0)->orWhere('pos', 2)->get();
                $footer_pages_pos1_2 = DB::table('pages')->wherePos(2)->orWhere('pos', 1)->get();
                $free_shipping = DB::table('shipping_services')->whereStatus(1)->whereIsCondition(1)->first();
                $global_popup = DB::table('global_popup_settings')->first();
            }

            $view->with('setting', $setting);
            $view->with('extra_settings', $extra_settings);
            $view->with('menus', $menus);
            $view->with('default_language', $default_language);
            $view->with('all_currencies', $all_currencies);
            $view->with('header_categories', $header_categories);
            $view->with('footer_pages_pos0_2', $footer_pages_pos0_2);
            $view->with('footer_pages_pos1_2', $footer_pages_pos1_2);
            $view->with('free_shipping_global', $free_shipping);
            $view->with('global_popup_shared', $global_popup);

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
