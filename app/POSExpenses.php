<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class POSExpenses extends Model
{
    public $fillable = ['id','vendor_id', 'invoice_no', 'remarks', 'terms', 'period', 'amount', 'description','date'];

    /**
     * Get the products associated with this expense
     */
    public function items()
    {
        return $this->hasMany('App\POSExpenseItem', 'pos_expense_id');
    }
}
