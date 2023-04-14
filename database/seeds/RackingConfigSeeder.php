<?php

use App\Custcategory;
use App\Person;
use App\RackingConfig;
use App\Vending;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Storage;

class RackingConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $custcategoryNames = ['V-E01 (UEH)','V-E03 (XE)','V-E06 (UEH)','V-E05 (UEJ)','V-E02 (BE)','V-E09 (UEI)','V-E11 (BEH)','V-E12 (UEC)','V-E12-F (UFC)','V-E05-F (UFJ)','V-E09-F (UFI)','V-E14-F (BFC)','V-E14 (BEC)','V-E15-F (UFI)','V-E15 (UEI)','V-E16-F (BFC)','V-E17 (Hibachi)','V-E18-F (BFC)','V-E20-F (BFC)','V-E21-F (Arun)','V-E301 (Umami)','V-E302a (QuickChef)','V-E25c (CFK)','V-E15-F (XE)','V-E41 (CFL)'];

        $custcategories = Custcategory::whereIn('name', $custcategoryNames)->get();

        foreach($custcategories as $custcategory) {
            $rackingConfig = RackingConfig::updateOrCreate([
                'name' => $custcategory->name,
            ],[
                'desc' => $custcategory->desc,
            ]);

            if($custcategory->attachments()->exists()) {
                foreach($custcategory->attachments as $attachment) {
                    $now = Carbon::now()->format('dmYHi');
                    $isFileExists = Storage::exists('racking_config_attachments/'.$attachment->url.'_'.$now);

                    if(!$isFileExists) {
                        Storage::copy($attachment->url, 'racking_config_attachments/'.$attachment->url.'_'.$now);
                    }
                    $url = (Storage::url('racking_config_attachments/'.$attachment->url.'_'.$now));
                    $rackingConfig->attachments()->create([
                        'url' => 'racking_config_attachments/'.$attachment->url.'_'.$now,
                        'full_url' => $url,
                    ]);
                }
            }

            if($custcategory->people()->exists()) {
                foreach($custcategory->people as $person) {
                    if($person->vending()->exists()) {
                        $person->vending()->update(['racking_config_id' => $rackingConfig->id]);
                    }
                }
            }
        }
    }
}
