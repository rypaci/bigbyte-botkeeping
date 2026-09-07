<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class SupplierInvoice extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'supplier_invoices';

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
    protected $fillable = ['vendor_id', 'date', 'invoice_number', 'terms', 'period', 'vat_perc', 'vat_amount', 'amount', 'amount_subj_to_vat', 'amount_due', 'journal_entry', 'ref_number', 'description', 'status'];
    
    public function vendor()
    {
        return $this->belongsTo('App\Vendor', 'vendor_id');
    }

    public function getInvoicesByVendorId( $vendor_id )
    {
        $supplier_invoices = $this->select('si.id', 'si.invoice_number', 'si.description', 'si.amount', 'v.credit as coa_credit')
                            ->from('supplier_invoices as si')
                            ->leftJoin('vouchers as v', function($join) { 
                                $join->on('v.ref_id', '=', 'si.id')
                                    ->on('v.module_alias', '=', DB::raw("'si'"))
                                    ->on('v.key', '=', DB::raw("'coa_credit'"));
                            })
                            ->where('vendor_id', '=', $vendor_id)
                            ->where('si.status', '=', '')
                            ->orderBy('id', 'asc')->get();
        // dd($chart_accounts->toArray());
        
        return (is_callable([$supplier_invoices, 'toArray']) ? $supplier_invoices->toArray() : []);
    }
    
    public static function moduleAlias() {
        return 'si';
    }
}
