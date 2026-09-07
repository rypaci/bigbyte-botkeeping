<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WRMChangesapfiltercounter extends Model
{
    public $fillable = ['id', 'order_qty', 'refill_bottle', 'others_qty','container_qty','dealer_qty','type_filter'];
}
