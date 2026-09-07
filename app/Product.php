<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    
    public $fillable = ['name', 'description', 'price', 'inventory_status', 'vendor_id', 'sr_priority', 'cost_price', 'stock_threshold', 'barcode'];
}