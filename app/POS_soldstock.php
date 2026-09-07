<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class POS_soldstock extends Model
{
    public $fillable = ['sales_id','product_id','qty','price','amount','date'];
}
