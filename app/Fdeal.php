<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fdeal extends Model
{
    //qty status condition
    /*
        qty_status = 1 (Stock Order/ Confirmed)
        qty_status = 2 (Actual Stock Deducted/ Delivered)
        qty_status = 3 (Stock Removed/ Deleted || Cancelled)
    */
    protected $table = 'fdeals';

    protected $fillable = [
        'qty', 'amount', 'unit_price', 'qty_status', 'item_id', 'ftransaction_id',
        'dividend', 'divisor', 'unit_cost', 'qty_before', 'qty_after'
    ];

    public function item()
    {
        return $this->belongsTo('App\Item');
    }

    public function ftransaction()
    {
        return $this->belongsTo('App\Ftransaction');
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
