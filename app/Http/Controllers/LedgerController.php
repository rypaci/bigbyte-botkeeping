<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ChartAccount;
use App\Vendor;
use App\Customer;
use Carbon\Carbon;
use Session;
use DB;

class LedgerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if( count($request->all()) && !empty($request->chart_account_id) ) {
            return $this->generate_ledger($request);
        }

        $chartaccounts = ChartAccount::select('id', DB::raw('CONCAT(code, " -- ", name) as option_name'))->orderBy('option_name', 'asc')->lists('option_name', 'id');
        $chartaccounts_option = ['' => 'Select'] + (is_callable([$chartaccounts, 'toArray']) ? $chartaccounts->toArray() : []);

        return view('ledger.index', compact('chartaccounts_option'));
    }

    public function merge_more_info($vouchers)
    {
        if(!$vouchers || count($vouchers) == 0)
            return [];

        // Get the modules and reference IDs in order to combine all same modules as a single DB query
        $module_aliases = [];
        foreach( $vouchers as $v ) {
            if(!isset($module_aliases[ $v->module_alias ]))
                $module_aliases[ $v->module_alias ] = [];
            
            $module_aliases[ $v->module_alias ][] = $v->ref_id;
        }

        // Get info from modules: supplier_invoices, cash_payment_vouchers, 
        $modules_info = [];
        foreach( $module_aliases as $alias => $ref_ids ) {
            if( $alias == 'si' ) {
                $rows = DB::table('supplier_invoices as si')
                    ->leftJoin('vendors as ven', 'si.vendor_id', '=', 'ven.id')
                    ->select(
                        'si.id', 
                        'si.date', 
                        'si.vendor_id', 
                        'si.invoice_number', 
                        'si.description', 
                        DB::raw('IF(ven.individual=1,TRIM(CONCAT(first_name, " ", middle_name, " ", last_name)),company_name) as `name`')
                    )
                    ->whereIn('si.id', $ref_ids)
                    ->get();

                foreach( $rows as $r ) {
                    if(!isset($modules_info[ $alias ]))
                        $modules_info[ $alias ] = [];
                    
                    $rid = $r->id; unset($r->id);
                    $modules_info[ $alias ][ $rid ] = $r;
                }
            }
            elseif($alias == 'cpv' ) {
                $rows = DB::table('cash_payment_vouchers as cpv')
                    ->leftJoin('vendors as ven', 'cpv.vendor_id', '=', 'ven.id')
                    ->leftJoin('supplier_invoices as si', 'cpv.supplier_invoice_id', '=', 'si.id')
                    ->select(
                        'cpv.id', 
                        'cpv.date', 
                        'cpv.vendor_id', 
                        'si.invoice_number', 
                        'si.description', 
                        DB::raw('IF(ven.individual=1,TRIM(CONCAT(first_name, " ", middle_name, " ", last_name)),company_name) as `name`')
                    )
                    ->whereIn('cpv.id', $ref_ids)
                    ->get();

                foreach( $rows as $r ) {
                    if(!isset($modules_info[ $alias ]))
                        $modules_info[ $alias ] = [];

                    $rid = $r->id; unset($r->id);
                    $modules_info[ $alias ][ $rid ] = $r;
                }
            }
        }
        // dump( $modules_info );

        // Combine info to vouchers
        $empty_info_cols = ['date' => '', 'vendor_id' => '', 'invoice_number' => '', 'description' => '', 'name' => '', ];
        $vouchers_with_info = [];
        foreach( $vouchers as $v ) {
            if( isset($modules_info[ $v->module_alias ][ $v->ref_id ]) ) {
                $vouchers_with_info[] = array_merge( (array) $v, (array) $modules_info[ $v->module_alias ][ $v->ref_id ] );
            }
            else {
                $vouchers_with_info[] = array_merge( (array) $v, $empty_info_cols );
            }
        }
        // dump( $vouchers_with_info );
        // dd( $vouchers );

        return $vouchers_with_info;
    }

    public function generate_ledger($request)
    {
        $mod_vouchers = [];

        /* linked tables --> vouchers, chart_accounts, supplier_invoices, vendors */
        {
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

            if($request->chart_account_id) {
                $mv->where('v.chart_account_id', $request->chart_account_id);
            }

            $mod_vouchers[] = $mv;
        }
        /* linked tables --> vouchers, chart_accounts, supplier_invoices, vendors */



        /* linked tables --> vouchers, chart_accounts, cash_payment_vouchers, vendors */
        {
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

            if($request->chart_account_id) {
                $mv->where('v.chart_account_id', $request->chart_account_id);
            }

            $mod_vouchers[] = $mv;
        }
        /* linked tables --> vouchers, chart_accounts, cash_payment_vouchers, vendors */



        /* linked tables --> vouchers, chart_accounts, cashinvoices, customers */
        {
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

            if($request->chart_account_id) {
                $mv->where('v.chart_account_id', $request->chart_account_id);
            }

            $mod_vouchers[] = $mv;
        }
        /* linked tables --> vouchers, chart_accounts, cashinvoices, customers */



        /* linked tables --> vouchers, chart_accounts, creditinvoices, customers */
        {
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

            if($request->chart_account_id) {
                $mv->where('v.chart_account_id', $request->chart_account_id);
            }

            $mod_vouchers[] = $mv;
        }
        /* linked tables --> vouchers, chart_accounts, creditinvoices, customers */



        /* linked tables --> vouchers, chart_accounts, collection_receipts, customers */
        {
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

            if($request->chart_account_id) {
                $mv->where('v.chart_account_id', $request->chart_account_id);
            }

            $mod_vouchers[] = $mv;
        }
        /* linked tables --> vouchers, chart_accounts, collection_receipts, customers */



        /* linked tables --> vouchers, chart_accounts, open_invoices, customers */
        {
            $mv = DB::table('vouchers as v')
                ->select(
                    'v.id', 'v.ref_number', 'v.chart_account_id', 'v.tax_id', 'v.discount_id', 'v.rate', 'v.debit', 'v.credit', 
                    'coa.name as coa_name', 'coa.code as coa_code', 'coa.level as coa_level', 'oi.id as mod_id', 'oi.date as date', DB::raw("'' as description"),
                    DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) as entity_name") 
                )
                ->leftJoin('chart_accounts as coa', 'v.chart_account_id', '=', 'coa.id')
                ->leftJoin('open_invoices as oi', function($join) { 
                    $join->on('v.ref_id', '=', 'oi.id')->on('v.module_alias', '=', DB::raw("'oi'"));
                })
                ->leftJoin('customers as cus', 'oi.customer_id', '=', 'cus.id')
                ->where('v.module_alias', '=', DB::raw("'oi'"))
                ->where('oi.date', '>=', $request->from)
                ->where('oi.date', '<=', $request->to);

            if($request->chart_account_id) {
                $mv->where('v.chart_account_id', $request->chart_account_id);
            }

            $mod_vouchers[] = $mv;
        }
        /* linked tables --> vouchers, chart_accounts, open_invoices, customers */



        /* linked tables --> vouchers, chart_accounts, billing_invoices, customers */
        {
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

            if($request->chart_account_id) {
                $mv->where('v.chart_account_id', $request->chart_account_id);
            }

            $mod_vouchers[] = $mv;
        }
        /* linked tables --> vouchers, chart_accounts, billing_invoices, customers */



        /* linked tables --> vouchers, chart_accounts, official_receipts, customers */
        {
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

            if($request->chart_account_id) {
                $mv->where('v.chart_account_id', $request->chart_account_id);
            }

            $mod_vouchers[] = $mv;
        }
        /* linked tables --> vouchers, chart_accounts, official_receipts, customers */



        /* linked tables --> vouchers, chart_accounts, adjustments, customers */
        {
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
                ->where('adj.entity_type', '=', DB::raw("'customers'"))
                ->where('v.module_alias', '=', DB::raw("'ad'"))
                ->where('adj.date', '>=', $request->from)
                ->where('adj.date', '<=', $request->to);

            if($request->chart_account_id) {
                $mv->where('v.chart_account_id', $request->chart_account_id);
            }

            $mod_vouchers[] = $mv;
        }
        /* linked tables --> vouchers, chart_accounts, adjustments, customers */



        /* linked tables --> vouchers, chart_accounts, adjustments, vendors */
        {
            $mv = DB::table('vouchers as v')
                ->select(
                    'v.id', 'v.ref_number', 'v.chart_account_id', 'v.tax_id', 'v.discount_id', 'v.rate', 'v.debit', 'v.credit', 
                    'coa.name as coa_name', 'coa.code as coa_code', 'coa.level as coa_level', 'adj.id as mod_id', 'adj.date as date', DB::raw("'' as description"),
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

            if($request->chart_account_id) {
                $mv->where('v.chart_account_id', $request->chart_account_id);
            }

            $mod_vouchers[] = $mv;
        }
        /* linked tables --> vouchers, chart_accounts, adjustments, vendors */



        /* Union All */
        for($i = 1; $i < count($mod_vouchers); $i++) {
            $mod_vouchers[0]->unionAll( $mod_vouchers[$i] );
        }



        // dd( $mod_vouchers[0]->toSql() );
        $mod_voucher = $mod_vouchers[0];
        $vouchers = $mod_voucher->orderBy('date')->get();
        // $vouchers = $mod_voucher->orderBy('date')->toSql(); dd( $vouchers );

        $balance = 0;
        foreach( $vouchers as &$v ) {
            $balance = $balance + floatval($v->debit) - floatval($v->credit);

            if(round($balance, 2) == 0) $balance = 0;

            $v->balance = $balance;
            $v->debit_formatted = (floatval($v->debit) > 0) ? number_format( floatval($v->debit), 2, '.', '' ) : '';
            $v->credit_formatted = (floatval($v->credit) > 0) ? number_format( floatval($v->credit), 2, '.', '' ) : '';
            $v->balance_formatted = number_format( floatval($v->balance), 2, '.', '' );
        }

        // dd( $vouchers );
        return view('ledger.ledger', compact('vouchers'));
    }

    public function generate_ledger2($request)
    {
        dd( $request->all() );
        $mod_voucher = DB::table('vouchers as v')
            ->leftJoin('chart_accounts as coa', 'v.chart_account_id', '=', 'coa.id')
            ->select('v.id', 'v.ref_number', 'v.chart_account_id', 'v.tax_id', 'v.discount_id', 'v.rate', 'v.debit', 'v.credit', 'coa.name as coa_name', 'coa.code as coa_code', 'coa.level as coa_level');
            
        $mod_voucher->where('v.tax_id', 0);
        
        if( $request->module ) {
            $mod_voucher->where('v.module_alias', $request->module);
        }
        if( $request->chart_account_id ) {
            $mod_voucher->where('v.chart_account_id', $request->chart_account_id);
        }
        if( $request->ref_number ) {
            $mod_voucher->where('v.ref_number', 'like', "{$request->ref_number}%");
        }
        
        $vouchers = $mod_voucher->get();
        // dump( $mod_voucher->toSql() );
        // dump($vouchers); die;
        
        $vouchers = $this->merge_more_info($vouchers);
        // dump($vouchers); die;
        
        $balance = 0;
        foreach( $vouchers as &$v ) {
            $balance = $balance + floatval($v['debit']) - floatval($v['credit']);
            
            if(round($balance, 2) == 0) $balance = 0;
            
            $v[ 'balance' ] = $balance;
            $v[ 'debit_formatted' ] = number_format( floatval($v['debit']), 2, '.', '' );
            $v[ 'credit_formatted' ] = number_format( floatval($v['credit']), 2, '.', '' );
            $v[ 'balance_formatted' ] = number_format( floatval($v['balance']), 2, '.', '' );
        }
        
        // dump(end($vouchers)); die;
        
        return view('ledger.ledger', compact('vouchers'));
    }
    
    public function getCustomers(Request $request)
    {
        $s = $request->term;
        
        $customer = Customer::select('*', DB::raw('TRIM(CONCAT(first_name, " ", middle_name, " ", last_name)) as `name`'))
            ->where('individual', '=', 1)
            ->where(function($query) use ($s) {
                $query->where('first_name', 'LIKE', "$s%")
                    ->orWhere('middle_name', 'LIKE', "$s%")
                    ->orWhere('last_name', 'LIKE', "$s%");
            });
            
        $customer = Customer::select('*', 'company_name as name')
            ->where('individual', '=', 0)
            ->where('company_name', 'LIKE', "$s%")
            ->unionAll($customer);
            
        $customers = $customer->get();
        
        $response = [];
        foreach($customers as $c) {
            $response[] = [ 'id' => $c->id, 'label' => $c->name, 'value' => $c->name ];
        }
        
        return $response;
    }
    
    public function getVendors(Request $request)
    {
        $s = $request->term;
        
        $vendor = Vendor::select('*', DB::raw('TRIM(CONCAT(first_name, " ", middle_name, " ", last_name)) as `name`'))
            ->where('individual', '=', 1)
            ->where(function($query) use ($s) {
                $query->where('first_name', 'LIKE', "$s%")
                    ->orWhere('middle_name', 'LIKE', "$s%")
                    ->orWhere('last_name', 'LIKE', "$s%");
            });
            
        $vendor = Vendor::select('*', 'company_name as name')
            ->where('individual', '=', 0)
            ->where('company_name', 'LIKE', "$s%")
            ->unionAll($vendor);
            
        $vendors = $vendor->get();
        
        $response = [];
        foreach($vendors as $v) {
            $response[] = [ 'id' => $v->id, 'label' => $v->name, 'value' => $v->name ];
        }
        
        return $response;
    }
    
}
