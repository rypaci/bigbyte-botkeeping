<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CollectionReceipt extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'collection_receipts';

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
        'cr_number', 'date', 'amount', 'balance', 'sales_discount', 'payment_method', 'on_hand', 'bank', 'bank_code', 'check_number', 'invoice_number', 'invoice_amount', 'description'
    ];
    
    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }
    
    public static function moduleAlias() {
        return 'cr';
    }
}
