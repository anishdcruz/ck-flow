<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Config;
use Artisan;
use Schema;
use Cache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
           'contacts' => 'App\Contact\Contact',
           'invoices' => 'App\Invoice\Invoice'
       ]);

        $settings = Cache::remember('settings:check', 48000, function() {
            return Schema::hasTable('settings');
        });

        if($settings) {
            // set user timezone
            date_default_timezone_set(settings()->get('application_timezone'));
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
