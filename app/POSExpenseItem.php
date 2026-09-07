<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class POSExpenseItem extends Model
{
    protected $table = 'pos_expense_items';
    
    public $fillable = ['pos_expense_id', 'product_id', 'quantity', 'cost_price', 'total_price', 'remarks'];

    /**
     * Get the product associated with this item
     */
    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    /**
     * Get the expense associated with this item
     */
    public function expense()
    {
        return $this->belongsTo('App\POSExpenses', 'pos_expense_id');
    }
}
