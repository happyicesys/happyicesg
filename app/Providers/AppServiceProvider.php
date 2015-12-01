<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //App name for all view
        view()->share('APP_NAME', 'HappyIce');

        //Person
        view()->share('PERSON_TITLE', 'Customer');
        view()->share('PERSON_PREFIX', 'C');

        //User
        view()->share('USER_TITLE', 'User');
        view()->share('USER_PREFIX', 'U');

        //Transaction
        view()->share('TRANS_TITLE', 'Transaction');
        view()->share('TRANS_PREFIX', 'T');  

        //trans status                  
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
