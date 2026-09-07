<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'vouchers';

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
    protected $fillable = ['ref_id', 'ref_number', 'date', 'module_alias', 'chart_account_id', 'tax_id', 'discount_id', 'rate', 'order', 'debit', 'credit', 'key'];
    
    /**
     * (module_alias):
     * Module Aliases:
     *   si    -> Supplier Invoice
     *   cpv   -> Cash Payment Voucher
     *   ci    -> Cash Invoice
     *   cri   -> Credit Invoice
     *   cr    -> Collection Receipts
     *   oi    -> Open Invoices
     *   bi    -> Billing Invoices
     *   or    -> Official Receipts
     */
     
    public function account()
    {
        return $this->belongsTo('App\ChartAccount', 'chart_account_id');
    }
    
    public function tax()
    {
        return $this->belongsTo('App\Tax', 'tax_id');
    }
    
    public static function byKey( $vouchers )
    {
        $vouchers_by_key = [];
        foreach($vouchers as $v) {
            $vouchers_by_key[ $v['key'] ] = $v;
        }
        
        $voucher_data = [
            'id'               => '',
            'code'             => '',
            'tax_id'           => '',
            'chart_account_id' => '',
            'ref_number'       => '',
            'debit'            => '',
            'credit'           => '',
            'key'              => '',
        ];
        
        if(empty( $vouchers_by_key['coa_debit'] ))
            $vouchers_by_key['coa_debit'] = (object) $voucher_data;
        
        if(empty( $vouchers_by_key['coa_credit'] ))
            $vouchers_by_key['coa_credit'] = (object) $voucher_data;
        
        return $vouchers_by_key;
    }
}
