<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;

class RemovePendingTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Yesterday Pending Transactions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::table('transactions')
            ->whereDate('delivery_date', '=', Carbon::yesterday()->toDateString())
            ->where('status', 'Pending')
            ->delete();
    }
}
