<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportTransactionExcel extends Model
{
    const TYPE = [
        1 => 'Normal',
        2 => 'Different Unit Prices'
    ];


    protected $fillable = [
        'upload_date', 'file_name', 'file_url', 'result_url', 'uploaded_by', 'type',
    ];

    // relationships
    public function uploader()
    {
        return $this->belongsTo('App\User', 'uploaded_by');
    }
}
