<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
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
        'person_id'
    );

    protected $revisionEnabled = true;

    //Remove old revisions (works only when used with $historyLimit)
    protected $revisionCleanup = true;

    //Maintain a maximum of 500 changes at any point of time, while cleaning up old revisions.
    protected $historyLimit = 200;

    //storing new creation
    protected $revisionCreationsEnabled = true;

    //revision appear format name
    protected $revisionFormattedFieldNames = array(
        'retail_price' => 'Retail Price',
        'quote_price' => 'Quote Price',
        'remark' => 'Remark',
        'item_id' => 'Item',
    );

    protected $fillable = [
        'retail_price', 'quote_price', 'remark',
        'person_id', 'item_id'
    ];

    public function person()
    {
        return $this->belongsTo('App\Person');
    }

    public function item()
    {
        return $this->belongsTo('App\Item');
    }
}
