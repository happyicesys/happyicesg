<?php

use App\Person;
use Illuminate\Database\Seeder;
use GuzzleHttp\Client;

class SyncSysVendCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = new Client();
        $clientUrl = "https://sys.happyice.com.sg/api/v1/customers/person";
        $items = json_decode($client->post($clientUrl)->getBody()->getContents());

        if($items) {
            foreach($items as $item) {
                $person = Person::where('id', $item->person_id)->first();
                if($person) {
                    if($item->vend_binding) {
                        $person->vend_code = $item->vend_binding->vend->code;
                        $person->save();
                    }
                }
            }
        }
    }
}
