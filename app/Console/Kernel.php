<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\GetVendCodeFromCustId::class,
        \App\Console\Commands\SendTransactionNotificationEmail::class,
        \App\Console\Commands\RemovePendingTransactions::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
/*        $schedule->call(function(){
            DB::command("ALTER TABLE transactions AUTO_INCREMENT = 100000");
        })->everyMinute();

        $schedule->call(function () {

        })->weekly()->mondays()->at('13:00');   */
    }
}
