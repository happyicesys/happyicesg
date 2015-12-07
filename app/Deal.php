<?php

namespace App;
// namespace MyApp\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
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
    protected $historyLimit = 500; 

    //storing new creation
    protected $revisionCreationsEnabled = true;  

    //revision appear format name
    protected $revisionFormattedFieldNames = array(
        'item_id' => 'Item',
        'qty' => 'Quantity',
        'amount' => 'Amount'
    );        

    protected $fillable = [
        'item_id', 'transaction_id', 'qty',
        'amount'
    ];

    public function item()
    {
        return $this->belongsTo('App\Item');
    }

    public function transaction()
    {
        return $this->belongsTo('App\Transaction');
    }

/*    public function revision()
    {
        return $this->hasOne('App\Revision');
    }*/    
}
