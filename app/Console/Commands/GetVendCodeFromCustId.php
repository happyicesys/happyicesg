<?php

namespace App\Console\Commands;

use App\Person;
use Illuminate\Console\Command;

class GetVendCodeFromCustId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:vend-code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve vend code from person cust id';

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
        $people = Person::all();

        foreach($people as $person)
        {
            if(
                $person->cust_id[0] !== 'H'
                and $person->cust_id[0] !== 'D'
                and $person->active == 'Yes'
                and ($person->is_vending or $person->is_dvm or $person->is_combi)
            ) {
                $vendCode = abs((int) filter_var($person->cust_id, FILTER_SANITIZE_NUMBER_INT));
                $person->vend_code = $vendCode ? $vendCode : null;
                $person->save();
            }
        }
    }
}
