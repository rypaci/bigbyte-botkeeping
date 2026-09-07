<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WRMExpenses extends Model
{
    public $fillable = ['id','vendor_id', 'invoice_no', 'remarks', 'terms', 'period', 'amount', 'description','date'];
}
