<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable=[
        'job_title', 'duty', 'work_start',
        'work_end', 'work_day', 'salary_datea',
        'basic', 'ot_period', 'ot_rate',
        'probation_length', 'remark',
        'salary_dateb' 
    ];
}
