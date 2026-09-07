<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Creditinvoice extends Model
{
    public $fillable = [
        'invoice_number', 'customer_id', 'date', 'terms', 'customer_name', 
        'amount', 'amount_due', 'vat_amount', 'vatable_sales', 'vat_exempt_sales', 'net_of_vat', 'vat_exempt', 
        'no_of_person', 'no_of_scpwd', 'discounted', 'discount_id', 'discount_amount', 'discount_perc', 'net_sales', 'vat_id', 'vat_perc', 'whtax_id', 'whtax_amount',
        'debit_total', 'credit_total',
        'status', 'for_open_invoice', 'open_invoice_id', 'advance_payment'
    ];
    
    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }
    
    public function getInvoicesByCustomerId( $customer_id )
    {
        $invoices = $this->select('id', 'invoice_number', 'amount_due')
            ->where('customer_id', '=', $customer_id)
            ->where('status', '!=', 'paid')
            ->orderBy('id', 'asc')->get();
        
        return (is_callable([$invoices, 'toArray']) ? $invoices->toArray() : []);
    }
    
    public static function moduleAlias() {
        return 'cri';
    }
}