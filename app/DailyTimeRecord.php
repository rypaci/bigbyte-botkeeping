<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DailyTimeRecord extends Model
{
    public $fillable = [
        'e_id', 
        'time_in', 
        'time_out', 
        'notes', 
        'date',
        'status',
        'user_account',
        'start_time',
        'over_time',
        'min_hours',
        'break_start',
        'break_end',
        'ref_id',
        'keys',
        'break_time'
    ];
}