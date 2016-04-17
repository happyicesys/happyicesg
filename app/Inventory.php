<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Inventory extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }

    public function identifiableName()
    {
        return $this->title;
    }

    protected $dontKeepRevisionOf = array(
        'creator_id'
    );

    protected $revisionEnabled = false;

    //Remove old revisions (works only when used with $historyLimit)
    protected $revisionCleanup = true;

    //Maintain a maximum of 500 changes at any point of time, while cleaning up old revisions.
    protected $historyLimit = 500;

    //storing new creation
    protected $revisionCreationsEnabled = true;

    //revision appear format name
    protected $revisionFormattedFieldNames = array(
        'batch_num' => 'Batch Num',
        'remark' => 'Remark',
        'type' => 'Action',
        'created_by' => 'Created By',
        'qtytotal_current' => 'Current Total',
        'qtytotal_incoming'  => 'Incoming Total',
        'qtytotal_after' => 'After Total',
        'rec_date' => 'Receiving Date',
        'updated_by' => 'Updated By',
    );

    protected $table = 'inventories';

    protected $fillable = [
        'batch_num', 'remark', 'type',
        'creator_id', 'created_by', 'qtytotal_current',
        'qtytotal_incoming', 'qtytotal_after',
        'updated_by', 'rec_date'
    ];

    protected $dates = [
        'rec_date'
    ];

    public function setRecDateAttribute($date)
    {
        $this->attributes['rec_date'] = Carbon::parse($date);
    }

    public function invrecords()
    {
        return $this->hasMany('App\InvRecord');
    }

    public function getRecDateAttribute($date)
    {
        return Carbon::parse($date)->format('Y-m-d');
    }
}
