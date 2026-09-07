<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    public $fillable = ['pro_id','added_qty','curr_qty','date'];
}
