<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpenInvoice extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'open_invoices';

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
        'customer_id', 'credit_invoice_id', 
        'oi_number', 'date', 'amount', 'sales_discount', 'balance', 'payment_method', 'on_hand', 'bank', 'bank_code', 'check_number', 'invoice_number', 'description', 'invoice_amount'
    ];
    
    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }
    
    public function getInvoicesByCustomerId( $customer_id )
    {
        $invoices = $this->select('id', 'oi_number', 'amount')
            ->where('customer_id', '=', $customer_id)
            ->where('credit_invoice_id', '=', 0)
            ->orderBy('id', 'asc')->get();
        
        return (is_callable([$invoices, 'toArray']) ? $invoices->toArray() : []);
    }
    
    public static function moduleAlias() {
        return 'oi';
    }
}
