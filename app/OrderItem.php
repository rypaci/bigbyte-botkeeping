<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    
    protected $table = 'order_items';
    
    protected $primaryKey = 'id';
    
    protected $fillable = ['name', 'qty', 'price', 'amount', 'product_id', 'service_id', 'ref_id', 'module_alias'];
    
}
