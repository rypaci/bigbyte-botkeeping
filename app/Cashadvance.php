<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cashadvance extends Model
{
    //
    public $fillable = ['e_id', 'ca_amount', 'ca_description', 'date', 'ca_deduction', 'keys'];
}
