<?php

namespace App;
use DB;

use Illuminate\Database\Eloquent\Model;

class BillingInvoice extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'billing_invoices';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_number', 'customer_id', 'date', 
        'amount', 'amount_due', 'vat_amount', 'vatable_sales', 'vat_exempt_sales', 'net_of_vat', 'vat_exempt', 
        'no_of_person', 'no_of_scpwd', 'discounted', 'discount_id', 'discount_amount', 'discount_perc', 'net_sales', 'vat_id', 'vat_perc', 'whtax_id', 'whtax_amount', 
        'debit_total', 'credit_total',
    ];
    
    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }

    public function getInvoicesByCustomerId( $customer_id )
    {
        $invoices = $this->from('billing_invoices as b_i')
            ->select('b_i.id', 'b_i.invoice_number', 'amount_due')
            ->leftjoin('official_receipts as o_r', 'b_i.id', '=', 'o_r.billing_invoice_id')
            ->where(DB::raw("(amount_due - o_r.amount) > 0"))
            ->where('b_i.customer_id', '=', $customer_id)
            ->orderBy('b_i.id', 'asc')->get();

        return (is_callable([$invoices, 'toArray']) ? $invoices->toArray() : []);
    }
    
    public static function moduleAlias() {
        return 'bi';
    }
}
