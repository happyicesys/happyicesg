<?php

namespace App;
// namespace MyApp\Models;

use Illuminate\Database\Eloquent\Model;

class DtdDeal extends Model
{
    //qty status condition
    /*
        qty_status = 1 (Stock Order/ Confirmed)
        qty_status = 2 (Actual Stock Deducted/ Delivered)
        qty_status = 3 (Stock Removed/ Deleted || Cancelled)
    */
    protected $table = 'dtddeals';

    protected $fillable = [
        'qty', 'amount', 'unit_price', 'qty_status', 'item_id', 'transaction_id',
        'dividend', 'divisor', 'unit_cost'
    ];

    public function item()
    {
        return $this->belongsTo('App\Item');
    }

    public function dtdtransaction()
    {
        return $this->belongsTo('App\DtdTransaction');
    }

    public function setQtyAttribute($value)
    {
        if(strstr($value, '/')){
            $this->attributes['qty'] = $this->fraction($value);
        }else if(!$value) {
            $this->attributes['qty'] = null;
        }else {
            $this->attributes['qty'] = $value;
        }
    }


    private function fraction($frac)
    {
        $fraction = explode("/",$frac);
        if($fraction[1] != 0) {
            return $fraction[0]/$fraction[1];
        }
        return "Division by zero error!";

    }
}
