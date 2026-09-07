<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cashinvoice extends Model
{
    public $fillable = [
        'invoice_number', 'customer_id', 'date', 'customer_name', 
        'amount', 'amount_due', 'vat_amount', 'vatable_sales', 'vat_exempt_sales', 'net_of_vat', 'vat_exempt', 
        'no_of_person', 'no_of_scpwd', 'discounted', 'discount_id', 'discount_amount', 'discount_perc', 'net_sales', 'vat_id', 'vat_perc', 'whtax_id', 'whtax_amount', 
        'debit_total', 'credit_total',
    ];
    
    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }
    
    public static function moduleAlias() {
        return 'ci';
    }
}