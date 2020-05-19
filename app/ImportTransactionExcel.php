<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportTransactionExcel extends Model
{
    protected $fillable = [
        'upload_date', 'file_name', 'file_url', 'result_url', 'uploaded_by'
    ];

    // relationships
    public function uploader()
    {
        return $this->belongsTo('App\User', 'uploaded_by');
    }
}
