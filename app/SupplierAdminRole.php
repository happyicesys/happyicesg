<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Role;

class SupplierAdminRole extends Model
{

    public function run()
    {
        Role::create([
            'name' => 'hd_user',
            'label' => 'Haagen Daz User'
        ]);

        Role::create([
            'name' => 'watcher',
            'label' => 'Watcher'
        ]);
    }
}
