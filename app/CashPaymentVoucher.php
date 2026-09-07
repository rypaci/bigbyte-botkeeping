<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashPaymentVoucher extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cash_payment_vouchers';

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
    // protected $fillable = ['business_name', 'business_address', 'vat_reg_tin', 'business_cv_number', 'date', 'pay_to', 'tin', 'address', 'payment_method', 'check', 'bank_code', 'cv_number', 'invoice_amount'];
    protected $fillable = ['vendor_id', 'supplier_invoice_id', 'cv_number', 'date', 'amount', 'payment_method', 'on_hand', 'bank', 'bank_code', 'check_number', 'balance', 'invoice_number', 'invoice_amount', 'description'];
    
    public function vendor()
    {
        return $this->belongsTo('App\Vendor', 'vendor_id');
    }
    
    public function supplier_invoice()
    {
        return $this->belongsTo('App\SupplierInvoice', 'supplier_invoice_id');
    }
    
    public static function moduleAlias() {
        return 'cpv';
    }
}
