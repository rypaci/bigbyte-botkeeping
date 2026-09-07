<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ChartAccount;
use App\Vendor;
use App\Customer;
use Carbon\Carbon;
use Session;
use DB;

class JournalController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if( count($request->all()) ) {
            return $this->generate_journal($request);
        }
        
        $trans_journals_option = [
            ''                  => 'Select',
            'cash_disbursement' => 'Cash Disbursement Journal',
            'cash_receipt'      => 'Cash Receipt Journal',
            'general'           => 'General Journal',
            'purchase'          => 'Purchase Journal',
            'sale'              => 'Sale Journal',
        ];
        
        return view('journal.index', compact('trans_journals_option'));
    }

    public function generate_journal($request)
    {
        $mod_vouchers = [];
        $inc_tables = [];
        
        $names = '';
        foreach(['vendor', 'customer'] as $n) {
            $names .= !empty( $request->{ $n } ) ? $request->{ $n } : '';
        }
        $are_all_names_blank = empty( $names );



        /* Cash Disbursement Journal
           linked tables --> vouchers, chart_accounts, cash_payment_vouchers, vendors */
        if($request->vendor || $are_all_names_blank) {
            if(in_array($request->transaction_journal, ['', 'cash_disbursement'])) {
                $mv = DB::table('vouchers as v')
                    ->select(
                        'v.id', 'v.ref_number', 'v.chart_account_id', 'v.tax_id', 'v.discount_id', 'v.rate', 'v.debit', 'v.credit', 
                        'coa.name as coa_name', 'coa.code as coa_code', 'coa.level as coa_level', 'cpv.id as mod_id', 'cpv.date as date', DB::raw("'' as description"),
                        DB::raw("TRIM(IF(ven.individual, CONCAT(ven.first_name, ' ', ven.middle_name, ' ', ven.last_name), ven.company_name)) as entity_name") 
                    )
                    ->leftJoin('chart_accounts as coa', 'v.chart_account_id', '=', 'coa.id')
                    ->leftJoin('cash_payment_vouchers as cpv', function($join) { 
                        $join->on('v.ref_id', '=', 'cpv.id')->on('v.module_alias', '=', DB::raw("'cpv'"));
                    })
                    ->leftJoin('vendors as ven', 'cpv.vendor_id', '=', 'ven.id')
                    ->where('v.module_alias', '=', DB::raw("'cpv'"))
                    ->where('cpv.date', '>=', $request->from)
                    ->where('cpv.date', '<=', $request->to);
                    
                // filter by vendor
                if($request->vendor) {
                    $s = $request->vendor;
                    $mv->where(function($query) use ($s) {
                        $query->where(DB::raw("TRIM(IF(ven.individual, CONCAT(ven.first_name, ' ', ven.middle_name, ' ', ven.last_name), ven.company_name))"), 'LIKE', "$s%")
                            ->orWhere('ven.company_name', 'LIKE', "$s%");
                    });
                }
                if($request->ref_number) {
                    $mv->where('v.ref_number', 'like', "{$request->ref_number}%");
                }
                
                $mod_vouchers[] = $mv;
                $inc_tables[] = 'cash_payment_vouchers';
            }
        }
        /* Cash Disbursement Journal */



        /* Cash Receipt Journal
           linked tables --> vouchers, chart_accounts, cashinvoices, customers
           linked tables --> vouchers, chart_accounts, official_receipts, customers */
        if($request->customer || $are_all_names_blank) {
            if(in_array($request->transaction_journal, ['', 'cash_receipt'])) {
                /* cashinvoices */
                $mv = DB::table('vouchers as v')
                    ->select(
                        'v.id', 'v.ref_number', 'v.chart_account_id', 'v.tax_id', 'v.discount_id', 'v.rate', 'v.debit', 'v.credit', 
                        'coa.name as coa_name', 'coa.code as coa_code', 'coa.level as coa_level', 'ci.id as mod_id', 'ci.date as date', DB::raw("'' as description"),
                        DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) as entity_name") 
                    )
                    ->leftJoin('chart_accounts as coa', 'v.chart_account_id', '=', 'coa.id')
                    ->leftJoin('cashinvoices as ci', function($join) { 
                        $join->on('v.ref_id', '=', 'ci.id')->on('v.module_alias', '=', DB::raw("'ci'"));
                    })
                    ->leftJoin('customers as cus', 'ci.customer_id', '=', 'cus.id')
                    ->where('v.module_alias', '=', DB::raw("'ci'"))
                    ->where('ci.date', '>=', $request->from)
                    ->where('ci.date', '<=', $request->to);
                    
                // filter by customer
                if($request->customer) {
                    $s = $request->customer;
                    $mv->where(function($query) use ($s) {
                        $query->where(DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name))"), 'LIKE', "$s%")
                            ->orWhere('cus.company_name', 'LIKE', "$s%");
                    });
                }
                if($request->ref_number) {
                    $mv->where('v.ref_number', 'like', "{$request->ref_number}%");
                }
                
                $mod_vouchers[] = $mv;
                $inc_tables[] = 'cashinvoices';
                
                
                /* official_receipts */
                $mv = DB::table('vouchers as v')
                    ->select(
                        'v.id', 'v.ref_number', 'v.chart_account_id', 'v.tax_id', 'v.discount_id', 'v.rate', 'v.debit', 'v.credit', 
                        'coa.name as coa_name', 'coa.code as coa_code', 'coa.level as coa_level', 'or.id as mod_id', 'or.date as date', DB::raw("'' as description"),
                        DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) as entity_name") 
                    )
                    ->leftJoin('chart_accounts as coa', 'v.chart_account_id', '=', 'coa.id')
                    ->leftJoin('official_receipts as or', function($join) { 
                        $join->on('v.ref_id', '=', 'or.id')->on('v.module_alias', '=', DB::raw("'or'"));
                    })
                    ->leftJoin('customers as cus', 'or.customer_id', '=', 'cus.id')
                    ->where('v.module_alias', '=', DB::raw("'or'"))
                    ->where('or.date', '>=', $request->from)
                    ->where('or.date', '<=', $request->to);
                    
                // filter by customer
                if($request->customer) {
                    $s = $request->customer;
                    $mv->where(function($query) use ($s) {
                        $query->where(DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name))"), 'LIKE', "$s%")
                            ->orWhere('cus.company_name', 'LIKE', "$s%");
                    });
                }
                if($request->ref_number) {
                    $mv->where('v.ref_number', 'like', "{$request->ref_number}%");
                }
                
                $mod_vouchers[] = $mv;
                $inc_tables[] = 'official_receipts';
            }
        }
        /* Cash Receipt Journal */



        /* General Journal - Adjustment-Customer
           linked tables --> vouchers, chart_accounts, adjustment, customers */
        if($request->customer || $are_all_names_blank) {
            if(in_array($request->transaction_journal, ['', 'general'])) {
                /* adjustments */
                $mv = DB::table('vouchers as v')
                    ->select(
                        'v.id', 'v.ref_number', 'v.chart_account_id', 'v.tax_id', 'v.discount_id', 'v.rate', 'v.debit', 'v.credit', 
                        'coa.name as coa_name', 'coa.code as coa_code', 'coa.level as coa_level', 'adj.id as mod_id', 'adj.date as date', DB::raw("'' as description"),
                        DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) as entity_name") 
                    )
                    ->leftJoin('chart_accounts as coa', 'v.chart_account_id', '=', 'coa.id')
                    ->leftJoin('adjustments as adj', function($join) { 
                        $join->on('v.ref_id', '=', 'adj.id')->on('v.module_alias', '=', DB::raw("'ad'"));
                    })
                    ->leftJoin('customers as cus', 'adj.entity_id', '=', 'cus.id')
                    ->where('adj.entity_type', '=', DB::raw("'customer'"))
                    ->where('v.module_alias', '=', DB::raw("'ad'"))
                    ->where('adj.date', '>=', $request->from)
                    ->where('adj.date', '<=', $request->to);

                // filter by customer
                if($request->customer) {
                    $s = $request->customer;
                    $mv->where(function($query) use ($s) {
                        $query->where(DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name))"), 'LIKE', "$s%")
                            ->orWhere('cus.company_name', 'LIKE', "$s%");
                    });
                }
                if($request->ref_number) {
                    $mv->where('v.ref_number', 'like', "{$request->ref_number}%");
                }

                $mod_vouchers[] = $mv;
                $inc_tables[] = 'adjustments';
            }
        }
        /* General Journal */



        /* General Journal - Adjustment-Vendor
           linked tables --> vouchers, chart_accounts, adjustment, vendors */
        if($request->vendor || $are_all_names_blank) {
            if(in_array($request->transaction_journal, ['', 'general'])) {
                /* adjustments */
                $mv = DB::table('vouchers as v')
                    ->select(
                        'v.id', 'v.ref_number', 'v.chart_account_id', 'v.tax_id', 'v.discount_id', 'v.rate', 'v.debit', 'v.credit', 
                        'coa.name as coa_name', 'coa.code as coa_code', 'coa.level as coa_level', 'adj.id as mod_id', 'adj.date as date', 'adj.description',
                        DB::raw("TRIM(IF(ven.individual, CONCAT(ven.first_name, ' ', ven.middle_name, ' ', ven.last_name), ven.company_name)) as entity_name") 
                    )
                    ->leftJoin('chart_accounts as coa', 'v.chart_account_id', '=', 'coa.id')
                    ->leftJoin('adjustments as adj', function($join) { 
                        $join->on('v.ref_id', '=', 'adj.id')->on('v.module_alias', '=', DB::raw("'ad'"));
                    })
                    ->leftJoin('vendors as ven', 'adj.entity_id', '=', 'ven.id')
                    ->where('adj.entity_type', '=', DB::raw("'vendor'"))
                    ->where('v.module_alias', '=', DB::raw("'ad'"))
                    ->where('adj.date', '>=', $request->from)
                    ->where('adj.date', '<=', $request->to);

                // filter by vendor
                if($request->vendor) {
                    $s = $request->vendor;
                    $mv->where(function($query) use ($s) {
                        $query->where(DB::raw("TRIM(IF(ven.individual, CONCAT(ven.first_name, ' ', ven.middle_name, ' ', ven.last_name), ven.company_name))"), 'LIKE', "$s%")
                            ->orWhere('ven.company_name', 'LIKE', "$s%");
                    });
                }
                if($request->ref_number) {
                    $mv->where('v.ref_number', 'like', "{$request->ref_number}%");
                }

                $mod_vouchers[] = $mv;
                $inc_tables[] = 'adjustments';
            }
        }
        /* General Journal */



        /* Purchase Journal
           linked tables --> vouchers, chart_accounts, supplier_invoices, vendors */
        if($request->vendor || $are_all_names_blank) {
            if(in_array($request->transaction_journal, ['', 'purchase'])) {
                $mv = DB::table('vouchers as v')
                    ->select(
                        'v.id', 'v.ref_number', 'v.chart_account_id', 'v.tax_id', 'v.discount_id', 'v.rate', 'v.debit', 'v.credit', 
                        'coa.name as coa_name', 'coa.code as coa_code', 'coa.level as coa_level', 'si.id as mod_id', 'si.date as date', 'si.description',
                        DB::raw("TRIM(IF(ven.individual, CONCAT(ven.first_name, ' ', ven.middle_name, ' ', ven.last_name), ven.company_name)) as entity_name") 
                    )
                    ->leftJoin('chart_accounts as coa', 'v.chart_account_id', '=', 'coa.id')
                    ->leftJoin('supplier_invoices as si', function($join) { 
                        $join->on('v.ref_id', '=', 'si.id')->on('v.module_alias', '=', DB::raw("'si'"));
                    })
                    ->leftJoin('vendors as ven', 'si.vendor_id', '=', 'ven.id')
                    ->where('v.module_alias', '=', DB::raw("'si'"))
                    ->where('si.date', '>=', $request->from)
                    ->where('si.date', '<=', $request->to);
                    
                // filter by vendor
                if($request->vendor) {
                    $s = $request->vendor;
                    $mv->where(function($query) use ($s) {
                        $query->where(DB::raw("TRIM(IF(ven.individual, CONCAT(ven.first_name, ' ', ven.middle_name, ' ', ven.last_name), ven.company_name))"), 'LIKE', "$s%")
                            ->orWhere('ven.company_name', 'LIKE', "$s%");
                    });
                }
                if($request->ref_number) {
                    $mv->where('v.ref_number', 'like', "{$request->ref_number}%");
                }
                
                $mod_vouchers[] = $mv;
                $inc_tables[] = 'supplier_invoices';
            }
        }
        /* Purchase Journal */



        /* Sale Journal
           linked tables --> vouchers, chart_accounts, creditinvoices, customers
           linked tables --> vouchers, chart_accounts, collection_receipts, customers
           linked tables --> vouchers, chart_accounts, billing_invoices, customers
           linked tables --> vouchers, chart_accounts, official_receipts, customers */
        if($request->customer || $are_all_names_blank) {
            if(in_array($request->transaction_journal, ['', 'sale'])) {
                /* Trading */
                /* creditinvoices */
                $mv = DB::table('vouchers as v')
                    ->select(
                        'v.id', 'v.ref_number', 'v.chart_account_id', 'v.tax_id', 'v.discount_id', 'v.rate', 'v.debit', 'v.credit', 
                        'coa.name as coa_name', 'coa.code as coa_code', 'coa.level as coa_level', 'cri.id as mod_id', 'cri.date as date', DB::raw("'' as description"),
                        DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) as entity_name") 
                    )
                    ->leftJoin('chart_accounts as coa', 'v.chart_account_id', '=', 'coa.id')
                    ->leftJoin('creditinvoices as cri', function($join) { 
                        $join->on('v.ref_id', '=', 'cri.id')->on('v.module_alias', '=', DB::raw("'cri'"));
                    })
                    ->leftJoin('customers as cus', 'cri.customer_id', '=', 'cus.id')
                    ->where('v.module_alias', '=', DB::raw("'cri'"))
                    ->where('cri.date', '>=', $request->from)
                    ->where('cri.date', '<=', $request->to);
                    
                // filter by customer
                if($request->customer) {
                    $s = $request->customer;
                    $mv->where(function($query) use ($s) {
                        $query->where(DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name))"), 'LIKE', "$s%")
                            ->orWhere('cus.company_name', 'LIKE', "$s%");
                    });
                }
                if($request->ref_number) {
                    $mv->where('v.ref_number', 'like', "{$request->ref_number}%");
                }
                
                $mod_vouchers[] = $mv;
                $inc_tables[] = 'creditinvoices';
                
                
                /* Trading */
                /* collection_receipts */
                $mv = DB::table('vouchers as v')
                    ->select(
                        'v.id', 'v.ref_number', 'v.chart_account_id', 'v.tax_id', 'v.discount_id', 'v.rate', 'v.debit', 'v.credit', 
                        'coa.name as coa_name', 'coa.code as coa_code', 'coa.level as coa_level', 'cr.id as mod_id', 'cr.date as date', DB::raw("'' as description"),
                        DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) as entity_name") 
                    )
                    ->leftJoin('chart_accounts as coa', 'v.chart_account_id', '=', 'coa.id')
                    ->leftJoin('collection_receipts as cr', function($join) { 
                        $join->on('v.ref_id', '=', 'cr.id')->on('v.module_alias', '=', DB::raw("'cr'"));
                    })
                    ->leftJoin('customers as cus', 'cr.customer_id', '=', 'cus.id')
                    ->where('v.module_alias', '=', DB::raw("'cr'"))
                    ->where('cr.date', '>=', $request->from)
                    ->where('cr.date', '<=', $request->to);
                    
                // filter by customer
                if($request->customer) {
                    $s = $request->customer;
                    $mv->where(function($query) use ($s) {
                        $query->where(DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name))"), 'LIKE', "$s%")
                            ->orWhere('cus.company_name', 'LIKE', "$s%");
                    });
                }
                if($request->ref_number) {
                    $mv->where('v.ref_number', 'like', "{$request->ref_number}%");
                }
                
                $mod_vouchers[] = $mv;
                $inc_tables[] = 'collection_receipts';
                
                
                /* Services */
                /* billing_invoices */
                $mv = DB::table('vouchers as v')
                    ->select(
                        'v.id', 'v.ref_number', 'v.chart_account_id', 'v.tax_id', 'v.discount_id', 'v.rate', 'v.debit', 'v.credit', 
                        'coa.name as coa_name', 'coa.code as coa_code', 'coa.level as coa_level', 'bi.id as mod_id', 'bi.date as date', DB::raw("'' as description"),
                        DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) as entity_name") 
                    )
                    ->leftJoin('chart_accounts as coa', 'v.chart_account_id', '=', 'coa.id')
                    ->leftJoin('billing_invoices as bi', function($join) { 
                        $join->on('v.ref_id', '=', 'bi.id')->on('v.module_alias', '=', DB::raw("'bi'"));
                    })
                    ->leftJoin('customers as cus', 'bi.customer_id', '=', 'cus.id')
                    ->where('v.module_alias', '=', DB::raw("'bi'"))
                    ->where('bi.date', '>=', $request->from)
                    ->where('bi.date', '<=', $request->to);
                    
                // filter by customer
                if($request->customer) {
                    $s = $request->customer;
                    $mv->where(function($query) use ($s) {
                        $query->where(DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name))"), 'LIKE', "$s%")
                            ->orWhere('cus.company_name', 'LIKE', "$s%");
                    });
                }
                if($request->ref_number) {
                    $mv->where('v.ref_number', 'like', "{$request->ref_number}%");
                }
                
                $mod_vouchers[] = $mv;
                $inc_tables[] = 'billing_invoices';
                
                
                /* Services */
                /* official_receipts */
                if(!in_array('official_receipts', $inc_tables)) {
                    $mv = DB::table('vouchers as v')
                        ->select(
                            'v.id', 'v.ref_number', 'v.chart_account_id', 'v.tax_id', 'v.discount_id', 'v.rate', 'v.debit', 'v.credit', 
                            'coa.name as coa_name', 'coa.code as coa_code', 'coa.level as coa_level', 'or.id as mod_id', 'or.date as date', DB::raw("'' as description"),
                            DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) as entity_name") 
                        )
                        ->leftJoin('chart_accounts as coa', 'v.chart_account_id', '=', 'coa.id')
                        ->leftJoin('official_receipts as or', function($join) { 
                            $join->on('v.ref_id', '=', 'or.id')->on('v.module_alias', '=', DB::raw("'or'"));
                        })
                        ->leftJoin('customers as cus', 'or.customer_id', '=', 'cus.id')
                        ->where('v.module_alias', '=', DB::raw("'or'"))
                        ->where('or.date', '>=', $request->from)
                        ->where('or.date', '<=', $request->to);
                        
                    // filter by customer
                    if($request->customer) {
                        $s = $request->customer;
                        $mv->where(function($query) use ($s) {
                            $query->where(DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name))"), 'LIKE', "$s%")
                                ->orWhere('cus.company_name', 'LIKE', "$s%");
                        });
                    }
                    if($request->ref_number) {
                        $mv->where('v.ref_number', 'like', "{$request->ref_number}%");
                    }
                    
                    $mod_vouchers[] = $mv;
                    $inc_tables[] = 'official_receipts';
                }
            }
        }
        /* Sale Journal */



        /* Union All */
        for($i = 1; $i < count($mod_vouchers); $i++) {
            $mod_vouchers[0]->unionAll( $mod_vouchers[$i] );
        }



        $vouchers = [];
        if(count($mod_vouchers)) {
            // dd( $mod_vouchers[0]->toSql() );
            $mod_voucher = $mod_vouchers[0];
            $vouchers = $mod_voucher->orderBy('date')->get();
        }
        
        $total = (object) ['debit' => 0.0, 'credit' => 0.0];
        
        foreach( $vouchers as &$v ) {
            $v->debit_formatted = (floatval($v->debit) > 0) ? number_format( floatval($v->debit), 2, '.', '' ) : '';
            $v->credit_formatted = (floatval($v->credit) > 0) ? number_format( floatval($v->credit), 2, '.', '' ) : '';
            $total->debit += $v->debit;
            $total->credit += $v->credit;
        }
        // dd( $vouchers[0] );
        
        $total->debit_formatted = number_format( floatval($total->debit), 2, '.', '' );
        $total->credit_formatted = number_format( floatval($total->credit), 2, '.', '' );
        
        // dd( $vouchers );
        return view('journal.journal', compact('vouchers', 'total'));
    }
    
}