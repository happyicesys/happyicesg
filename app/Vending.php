<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Chrisbjr\ApiGuard\Models\Mixins\Apikeyable;

class Vending extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }

    protected $revisionEnabled = true;

    //Remove old revisions (works only when used with $historyLimit)
    protected $revisionCleanup = true;

    //Maintain a maximum of 500 changes at any point of time, while cleaning up old revisions.
    protected $historyLimit = 30;

    //storing new creation
    protected $revisionCreationsEnabled = true;

    //revision appear format name
    protected $revisionFormattedFieldNames = array(
        'vend_id' => 'Vend ID',
        'serial_no' => 'Serial No',
        'type' => 'Type',
        'router' => 'Router',
        'desc' => 'Desc',
        'person_id' => 'Current Customer',
        'simcard_id' => 'Simcard ID',
        'cashless_terminal_id' => 'Cashless Terminal ID',
    );

    protected $revisionFormattedFields = [
        'created_at'   => 'datetime:Y-m-d g:i A',
    ];

    protected $dontKeepRevisionOf = array(
        'updated_by',
    );

    protected $fillable = [
        'vend_id', 'serial_no', 'type', 'router', 'desc',
        'person_id', 'updated_by', 'simcard_id', 'cashless_terminal_id',
        'racking_config_id',
    ];

    // relationships
    public function person()
    {
    	return $this->belongsTo('App\Person');
    }

    public function vmhistories()
    {
        return $this->hasMany('App\Vmhistory');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }

    public function simcard()
    {
        return $this->belongsTo('App\Simcard');
    }

    public function cashlessTerminal()
    {
        return $this->belongsTo('App\CashlessTerminal');
    }

    public function rackingConfig()
    {
        return $this->belongsTo('App\RackingConfig');
    }
}
