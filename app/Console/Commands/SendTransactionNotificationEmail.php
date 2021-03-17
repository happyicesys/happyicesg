<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Transaction;
use App\TransSubscription;
use App\Person;
use App\GeneralSetting;
use Carbon\Carbon;
use DB;
use PDF;

class SendTransactionNotificationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send transactions email to subscribed users to notify week ahead';

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
        $date_start = Carbon::today()->addDays(2)->toDateString();
        $date_end = Carbon::today()->addDays(8)->toDateString();

        $transactions = DB::table('transactions')
                        ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->leftJoin('custcategories', 'people.custcategory_id', '=', 'custcategories.id')
                        ->select(
                                    'people.cust_id', 'people.company',
                                    'people.name', 'people.id as person_id', 'transactions.del_postcode', 'transactions.bill_postcode',
                                    'transactions.status', 'transactions.delivery_date', 'transactions.driver',
                                    'transactions.total_qty', 'transactions.pay_status',
                                    'transactions.updated_by', 'transactions.updated_at', 'transactions.delivery_fee', 'transactions.id',
                                    DB::raw('ROUND((
                                                CASE WHEN transactions.gst=1 THEN (
                                                    CASE WHEN transactions.is_gst_inclusive=0 THEN total*((100+transactions.gst_rate)/100)
                                                    ELSE transactions.total
                                                    END)
                                                ELSE transactions.total
                                                END) + (
                                                CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee
                                                ELSE 0
                                                END), 2) AS total'),
                                    'profiles.id as profile_id', 'transactions.gst', 'transactions.is_gst_inclusive', 'transactions.gst_rate',
                                     'custcategories.name as custcategory'
                                );

        $transactions = $transactions->whereDate('transactions.delivery_date', '>=', $date_start)
                                    ->whereDate('transactions.delivery_date', '<=', $date_end);

        $transactions = $transactions->orderBy('transactions.delivery_date', 'asc');

        $transactions = $transactions->get();

        $data = [
            'transactions' => $transactions,
            'date_start' => $date_start,
            'date_end' => $date_end
        ];

        $sender = env('MAIL_USERNAME', 'system@happyice.com.sg');
        $receiver = [];
        $subscribers = TransSubscription::all();

        if(count($subscribers) > 0) {
            foreach ($subscribers as $subscriber) {
                array_push($receiver, $subscriber->user->email);
            }

            $title = 'Invoices for Week Ahead ('.$date_start.' - '.$date_end.')';

            Mail::send('email.send_transaction_notification', $data, function ($message) use ($sender, $receiver, $title)
            {
                $message->from($sender);
                $message->subject($title);
                $message->setTo($receiver);
            });
        }


    }
}
