<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;

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

        //Profile
        view()->share('PROFILE_TITLE', 'Profile');
        view()->share('PROFILE_PREFIX', 'Pro');

        //Person
        view()->share('PERSON_TITLE', 'Customer');
        view()->share('PERSON_PREFIX', 'C');

        //User
        view()->share('USER_TITLE', 'User');
        view()->share('USER_PREFIX', 'U');

        //Transaction
        view()->share('ITEM_TITLE', 'Inventory');
        view()->share('ITEM_PREFIX', 'I');

        //Transaction
        view()->share('TRANS_TITLE', 'Transaction');
        view()->share('TRANS_PREFIX', 'T');

        //Market
        view()->share('REPORT_TITLE', 'Report');
        view()->share('REPORT_PREFIX', 'R');

        //Market
        view()->share('MARKETING_TITLE', 'Marketing');
        view()->share('MARKETING_PREFIX', 'M');

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
