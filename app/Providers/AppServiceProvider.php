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

        // Franchise selection
        view()->share('FRANCHISE_TRANS', 'F Vend Cash');
        view()->share('FRANCHISE_RPT', 'F-Report');

        //Market
        view()->share('REPORT_TITLE', 'Report');
        view()->share('REPORT_PREFIX', 'R');

        view()->share('DETAILRPT_TITLE', 'Detailed Report');
        view()->share('DETAILRPT_PREFIX', 'DR');

        //Market
        view()->share('MARKETING_TITLE', 'Marketing');
        view()->share('MARKETING_PREFIX', 'M');

        // Ecommerce
        view()->share('SHOP_TITLE', 'Shop');
        view()->share('ECOMMERCE_TITLE', 'Ecommerce');
        view()->share('BOM_TITLE', 'BoM');

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
