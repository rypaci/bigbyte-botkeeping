<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ChartAccount;
use App\CreditInvoice;
use App\Customer;
use App\Discount;
use App\Product;
use App\OpenInvoice;
use App\Option;
use App\OrderItem;
use App\Tax;
use App\Voucher;
use Carbon\Carbon;
use DateTime;
use DB;
use Session;

class CreditInvoiceController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        
        $this->mdOption                = new Option;
        $this->mdOption->prefix        = 'creditinvoice_';
        $this->mdOption->accountFields = ['coa_debit', 'tax_debit', 'discount_debit', 'coa_credit', 'coa_credit2', 'tax_credit'];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $creditinvoices = CreditInvoice::select('cri.*', DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) as customer_name"))
            ->from('creditinvoices as cri')
            ->leftJoin('customers as cus', 'cus.id', '=', 'cri.customer_id')
            ->where('status', '!=', 'paid')
            // ->toSql();
            ->paginate(15);

        // dd( $creditinvoices );
        
        return view('credit-invoice.index', compact('creditinvoices'));
    }

    public function prepare_data($creditinvoice = null, $vouchers = null)
    {
        $discount = Discount::getScPwd();
        
        $vat = Tax::getVat();
        
        /* Withholding Tax */
        $withholding = [
            'value'       => ($creditinvoice && $creditinvoice->whtax_id) ? $creditinvoice->whtax_id : 0,
            'options'     => Tax::getAllWithholdingTaxes(),
            'data_fields' => ['chart_account_id', 'chart_account_name', 'id', 'rate', 'code'],
        ];
        
        $vouchers_by_key = false;
        if($vouchers) 
            $vouchers_by_key = Voucher::byKey( $vouchers );
        
        // dd( $vouchers_by_key, $vouchers_by_key['coa_debit']->toArray() );
        
        $account_rows = [];
        $account_rows[] = [
            'parent_name' => 'vouchers[0]', 'entry_type' => 'debit', 'account_row_class' => 'main', 'key' => 'coa_debit',
            'select' => [
                'value' => ($vouchers_by_key && isset($vouchers_by_key['coa_debit'])) ? $vouchers_by_key['coa_debit']->chart_account_id : 0,
                'options' => [(object)[ 'id' => '0', 'name' => 'Select Chart of Account' ]] + ChartAccount::getByIds( $this->mdOption->getCoas('coa_debit'), ['return_as' => 'object'] ),
                'data_fields' => ['code', 'id']
            ],
            'voucher' => ($vouchers_by_key && isset($vouchers_by_key['coa_debit'])) ? $vouchers_by_key['coa_debit'] : false
        ];
        $account_rows[] = [ 
            'parent_name' => 'vouchers[1]', 'entry_type' => 'debit', 'account_row_class' => 'tax withholding optional', 'key' => 'tax_debit',
            'select' => [
                'value' => ($vouchers_by_key && isset($vouchers_by_key['tax_debit'])) ? $vouchers_by_key['tax_debit']->chart_account_id : 0,
                'options' => ChartAccount::getByIds( $this->mdOption->getCoas('tax_debit'), ['return_as' => 'object'] ), 
                'data_fields' => ['code', 'id', 'rate', 'tax_id']
            ],
            'voucher' => ($vouchers_by_key && isset($vouchers_by_key['tax_debit'])) ? $vouchers_by_key['tax_debit'] : false
        ];
        $account_rows[] = [
            'parent_name' => 'vouchers[2]', 'entry_type' => 'debit', 'account_row_class' => 'discount optional', 'key' => 'discount_debit',
            'select' => [
                'value' => ($vouchers_by_key && isset($vouchers_by_key['discount_debit'])) ? $vouchers_by_key['discount_debit']->chart_account_id : 0,
                'options' => ChartAccount::getByIds( $this->mdOption->getCoas('discount_debit'), ['return_as' => 'object'] ), 
                'data_fields' => ['code', 'id', 'rate', 'discount_id']
            ],
            'voucher' => ($vouchers_by_key && isset($vouchers_by_key['discount_debit'])) ? $vouchers_by_key['discount_debit'] : false
        ];
        $account_rows[] = [
            'parent_name' => 'vouchers[3]', 'entry_type' => 'credit', 'account_row_class' => 'main', 'key' => 'coa_credit',
            'select' => [
                'value' => ($vouchers_by_key && isset($vouchers_by_key['coa_credit'])) ? $vouchers_by_key['coa_credit']->chart_account_id : 0,
                'options' => ChartAccount::getByIds( $this->mdOption->getCoas('coa_credit'), ['return_as' => 'object'] ), 
                'data_fields' => ['code', 'id']
            ],
            'voucher' => ($vouchers_by_key && isset($vouchers_by_key['coa_credit'])) ? $vouchers_by_key['coa_credit'] : false
        ];
        $account_rows[] = [
            'parent_name' => 'vouchers[4]', 'entry_type' => 'credit', 'account_row_class' => 'credit2 optional', 'key' => 'coa_credit2',
            'select' => [
                'value' => ($vouchers_by_key && isset($vouchers_by_key['coa_credit2'])) ? $vouchers_by_key['coa_credit2']->chart_account_id : 0,
                'options' => ChartAccount::getByIds( $this->mdOption->getCoas('coa_credit2'), ['return_as' => 'object'] ), 
                'data_fields' => ['code', 'id']
            ],
            'voucher' => ($vouchers_by_key && isset($vouchers_by_key['coa_credit2']))  ? $vouchers_by_key['coa_credit2'] : false
        ];
        $account_rows[] = [
            'parent_name' => 'vouchers[5]', 'entry_type' => 'credit', 'account_row_class' => 'tax vat', 'key' => 'tax_credit',
            'select' => [
                'value' => ($vouchers_by_key && isset($vouchers_by_key['tax_credit']))  ? $vouchers_by_key['tax_credit']->chart_account_id : 0,
                'options' => ChartAccount::getByIds( $this->mdOption->getCoas('tax_credit'), ['return_as' => 'object'] ), 
                'data_fields' => ['code', 'id', 'rate', 'tax_id']
            ],
            'voucher' => ($vouchers_by_key && isset($vouchers_by_key['tax_credit']))  ? $vouchers_by_key['tax_credit'] : false
        ];
        
        return compact('discount', 'vat', 'withholding', 'account_rows');
    }
    
    public function create(Request $request)
    {
        $term_options = $this->getTermOptions();
        
        extract( $this->prepare_data() );
        
        return view('credit-invoice.create', 
            compact(
                'term_options',
                'vat', 'discount', 'withholding', 'account_rows'
            )
        );
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd( $request->all() );
        $status = '';
        
        $has_open_invoice = !empty($request->for_open_invoice) && $request->open_invoice_id;
        if($has_open_invoice) {
            $status = 'partially-paid'; // default value
            
            if(floatval($request->amount_due) == 0) $status = 'paid';
        }
        
        $request->merge([ 'status' => $status ]);
        
        $cri = CreditInvoice::create( $request->all() );
        $cri_alias = CreditInvoice::moduleAlias();
        
        if($cri && $has_open_invoice) {
            OpenInvoice::find( $request->open_invoice_id )->update( ['credit_invoice_id' => $cri->id, 'invoice_number' => $cri->invoice_number, ] );
        }
        
        if($cri && count($request->products)) {
            foreach($request->products as $product) {
                $product['ref_id']       = $cri->id; 
                $product['module_alias'] = $cri_alias; 
                
                OrderItem::create($product);
            }
        }
        
        if($cri && count($request->vouchers)) {
            $order = 0;
            foreach($request->vouchers as $voucher) {
                // optional voucher. Do not create when conditions are not met.
                if($voucher['key'] == 'tax_debit' && !floatval($request->whtax_amount)) continue;
                if($voucher['key'] == 'discount_debit' && (!$request->discounted || !floatval($request->discount_amount))) continue;
                if($voucher['key'] == 'coa_credit2' && (!$request->discounted || !floatval($request->vat_exempt_sales))) continue;
                
                $voucher['ref_id']       = $cri->id;
                $voucher['module_alias'] = $cri_alias;
                $voucher['order']        = $order;
                $voucher['debit']        = floatval($voucher['debit']);
                $voucher['credit']       = floatval($voucher['credit']);
                $voucher['date']         = $cri->date;
                // 'chart_account_id', 'tax_id', 'key', 'ref_number'
                // dd($voucher);
                
                Voucher::create($voucher);
                $order++;
            }
        }

        Session::flash('flash_message', 'Credit Invoice created!');

        return redirect('credit-invoice');
    }

    public function show($id)
    {
        $creditinvoice = CreditInvoice::select('cri.*', DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) as customer_name"))
            ->from('creditinvoices as cri')
            ->leftJoin('customers as cus', 'cus.id', '=', 'cri.customer_id')
            ->where('cri.id', $id)->first();
        $cri_alias = CreditInvoice::moduleAlias();

        $orderitems = OrderItem::where('module_alias', $cri_alias)->where('ref_id', $id)
            ->orderBy('id', 'ASC') 
            ->get();
            
        $discounts_option = ['0' => 'No', '1' => 'Yes'];
        $term_options = $this->getTermOptions();
        
        $ci = new CashInvoiceController;
        $whtaxes_option = $ci->findWithHoldingTax( new Request(['whtaxtype' => 'Withholding']) );
        
        $vouchers = Voucher::where('module_alias', $cri_alias)
            ->where('ref_id', $id)
            ->orderBy('order', 'ASC') 
            ->get();
        
        $vouchers_by_key = Voucher::byKey( $vouchers );
        
        $coas = [];
        $coas['debit']   = $this->getCoaDebits();
        $coas['credit']  = $this->getCoaCredits();
        
        $taxes = [];
        $taxes['credit'] = $this->getTaxCredits();
        
        $debit_coa_id = $vouchers_by_key["coa_debit"]->chart_account_id;
        $credit_coa_id = $vouchers_by_key["coa_credit"]->chart_account_id;
        $credit_tax_id = $vouchers_by_key["tax_credit"]->tax_id;
        $credit_tax_coa_id = $vouchers_by_key["tax_credit"]->chart_account_id;
        
        $vat = Tax::select('rate')->find(19);
        $vat_perc = ($vat && !empty($vat->rate)) ? $vat->rate : 0;
        
        $discount = Discount::select('rate')->find(1);
        $discount_perc = ($discount && !empty($discount->rate)) ? $discount->rate : 0;
        // dd( $creditinvoice );
        return view('credit-invoice.show', compact(
            'creditinvoice', 'orderitems', 'discounts_option', 'term_options', 'whtaxes_option', 'vouchers_by_key', 'vouchers', 
            'debit_coa_id', 'credit_coa_id', 'credit_tax_coa_id', 
            'credit_tax_id', 
            'vat_perc', 'discount_perc', 
            'coas', 'taxes'
        ));
    }

    public function edit(Request $request, $id)
    {
        $creditinvoice = CreditInvoice::find($id);
        $cri_alias = CreditInvoice::moduleAlias();
        
        $term_options = $this->getTermOptions();

        $orderitems = OrderItem::where('module_alias', $cri_alias)->where('ref_id', $id)
            ->orderBy('id', 'ASC') 
            ->get();
            
        $vouchers = Voucher::where('module_alias', $cri_alias)
            ->where('ref_id', $id)
            ->orderBy('order', 'ASC') 
            ->get();
        
        extract( $this->prepare_data( $creditinvoice, $vouchers ) );
        
        $open_invoices = OpenInvoice::where('credit_invoice_id', '0')->orWhere('id', $creditinvoice->open_invoice_id)->get();
        $open_invoices = (is_callable([$open_invoices, 'toArray']) ? $open_invoices->toArray() : []);
        
        return view('credit-invoice.edit', 
            compact(
                'creditinvoice', 'orderitems', 'term_options', 'open_invoices', 
                'vat', 'discount', 'withholding', 'account_rows'
            )
        );
        /* return view('credit-invoice.edit', compact(
            'creditinvoice', 'orderitems', 'discounts_option', 'term_options', 'whtaxes_option', 'vouchers_by_key', 
            'debit_coa_id', 'credit_coa_id', 'credit_tax_coa_id', 
            'credit_tax_id', 
            'vat_perc', 'discount_perc', 
            'coas', 'taxes', 
            'open_invoices'
        )); */
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $status = '';
        
        $has_open_invoice = !empty($request->for_open_invoice) && $request->open_invoice_id;
        if($has_open_invoice) {
            $status = 'partially-paid'; // default value
            
            if(floatval($request->amount_due) == 0) $status = 'paid';
        }
        else {
            $request->merge([ 
                'for_open_invoice' => 0, 'open_invoice_id'  => 0 
            ]);
        }
        
        $request->merge([ 'status' => $status ]);
        
        // dd( $request->all() );
        $cri = CreditInvoice::findOrFail($id);
        
        /* if already linked to an old open_invoice AND id is not equal to open_invoice_id, then unlink that old open_invoice */
        $old_open_invoice_id = $cri->open_invoice_id;
        if($old_open_invoice_id && $old_open_invoice_id != $request->open_invoice_id) {
            OpenInvoice::find( $old_open_invoice_id )->update( ['credit_invoice_id' => 0, 'invoice_number' => ''] );
        }
        
        $cri->update($request->all());
        $cri_alias = CreditInvoice::moduleAlias();
        
        /* Update OpenInvoice column credit_invoice_id */
        if($cri && $has_open_invoice) {
            OpenInvoice::find( $request->open_invoice_id )->update( ['credit_invoice_id' => $cri->id, 'invoice_number' => $cri->invoice_number] );
        }
        
        /* Get all previous order items. Only IDs. 
           We do this because we need to delete the remaining orderitems soon */
        $orderitems = OrderItem::select('id')->where('module_alias', $cri_alias)->where('ref_id', $id)->get();
        $orderitem_ids = [];
        foreach($orderitems as $item) {
            $orderitem_ids[] = $item->id;
        }
        
        if($cri && count($request->products)) {
            foreach($request->products as $product) {
                // price, qty, amount, product_id, name, creditinvoice_id
                $product['id'] = (!empty($product['id'])) ? $product['id'] : 0;
                
                OrderItem::updateOrCreate(
                    // fields to match in DB
                    ['id' => $product['id']], 
                    
                    // fields to set the values
                    [
                        'name'         => $product['name'], 
                        'price'        => $product['price'], 
                        'qty'          => $product['qty'], 
                        'amount'       => $product['amount'], 
                        'product_id'   => $product['product_id'], 
                        'ref_id'       => $cri->id, 
                        'module_alias' => $cri_alias, 
                    ]
                );
                
                /* Remove the orderitem ID so we can exclude from deleting orderitem */
                if (($k = array_search($product['id'], $orderitem_ids)) !== false) {
                    unset($orderitem_ids[$k]);
                }
            }
        }
        
        /* Delete old orderitems */
        if(count($orderitem_ids)) {
            OrderItem::whereIn('id', $orderitem_ids)->delete();
        }
        
        
        if($cri && count($request->vouchers)) {
            $order = 0;
            foreach($request->vouchers as $voucher) {
                if($voucher['key'] == 'tax_debit') { // withholding tax
                    /* if whtax_amount is zero, then do not create a voucher */
                    if(!floatval($request->whtax_amount)) {
                        /* if voucher tax_debit exists, then delete this voucher */
                        if($voucher['id']) Voucher::destroy($voucher['id']);
                        
                        continue;
                    }
                }
                if($voucher['key'] == 'discount_debit') { // SC/PWD discount
                    /* if discount_amount is zero, then do not create a voucher */
                    if(!floatval($request->discount_amount)) {
                        /* if voucher discount_debit exists, then delete this voucher */
                        if($voucher['id']) Voucher::destroy($voucher['id']);
                        
                        continue;
                    }
                }
                if($voucher['key'] == 'coa_credit2') { // Vat-exempt Sales
                    /* if vat_exempt_sales is zero, then do not create a voucher */
                    if(!floatval($request->vat_exempt_sales)) {
                        /* if voucher coa_credit2 exists, then delete this voucher */
                        if($voucher['id']) Voucher::destroy($voucher['id']);
                        
                        continue;
                    }
                }
                
                $voucher['ref_id']       = $cri->id;
                $voucher['module_alias'] = $cri_alias;
                $voucher['order']        = $order;
                $voucher['debit']        = floatval($voucher['debit']);
                $voucher['credit']       = floatval($voucher['credit']);
                
                Voucher::updateOrCreate(
                    // fields to match in DB
                    ['key' => $voucher['key'], 'ref_id' => $voucher['ref_id'], 'module_alias' => $voucher['module_alias']], 
                    
                    // fields to set the values
                    [
                        'ref_id'           => $voucher['ref_id'], 
                        'ref_number'       => $voucher['ref_number'], 
                        'module_alias'     => $voucher['module_alias'], 
                        'chart_account_id' => $voucher['chart_account_id'], 
                        'tax_id'           => $voucher['tax_id'], 
                        'order'            => $voucher['order'], 
                        'debit'            => $voucher['debit'], 
                        'credit'           => $voucher['credit'], 
                        'key'              => $voucher['key'], 
                        'date'             => $cri->date
                    ]
                );
                
                $order++;
            }
        }
        
        Session::flash('flash_message', 'Credit Invoice updated!');
        
        return redirect('credit-invoice');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cri_alias = CreditInvoice::moduleAlias();
        Voucher::where('module_alias', $cri_alias)->where('ref_id', $id)->delete();
        OrderItem::where('module_alias', $cri_alias)->where('ref_id', $id)->delete();
        
        /* Reset column credit_invoice_id from all open_invoice having credit_invoice_id = $id */
        OpenInvoice::where('credit_invoice_id', $id)->update( ['credit_invoice_id' => 0, 'invoice_number' => ''] );
        
        CreditInvoice::find($id)->delete();
        
        Session::flash('flash_message', 'Credit Invoice deleted!');

        return redirect('credit-invoice');
    }

    function accountDetails()
    {
        $chart_accounts = ChartAccount::orderBy('name', 'asc')->lists('name', 'id');
        $chart_accounts_option = (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
        
        $chart_accounts = ChartAccount::orderBy('name', 'asc')
            ->whereRaw('id IN (select chart_account_id from taxes)')
            ->lists('name', 'id');
        $chart_account_taxes_option = (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
        
        $accounts = $this->mdOption->getAllCoas();
        
        // dd( $accounts );
        
        return view('credit-invoice.account-details', 
            compact(
                'accounts', 'chart_accounts_option', 'chart_account_taxes_option'
            )
        );
    }
    
    function accountDetailsUpdate(Request $request)
    {
        /* Save, as Option, the values for this ff fields: */
        foreach($this->mdOption->accountFields as $option_key) {
            $option_value = $request->input($option_key, []);
            if(!is_array($option_value)) $option_value = [];
            
            $this->mdOption->setCoa( $option_key, $option_value );
        }
        
        Session::flash('flash_message', 'Options saved.');
        
        return redirect('credit-invoice/account-details');
    }
    
    
    function getCoaDebits()
    {
        $coa_debit_ids = $this->getCoaOption('coa_debit');
        
        $chart_accounts = ChartAccount::select('id', 'name', 'code')
                        ->whereIn('id', $coa_debit_ids)
                        ->orderBy('name', 'asc')->get();
                        
        return (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
    }
    
    function getCoaCredits()
    {
        $coa_credit_ids = $this->getCoaOption('coa_credit');
        
        $chart_accounts = ChartAccount::select('id', 'name', 'code')
                        ->whereIn('id', $coa_credit_ids)
                        ->orderBy('name', 'asc')->get();
                        
        return (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
    }
    
    function getTaxCredits()
    {
        $tax_credit_ids = $this->getCoaOption('tax_credit');
        
        $chart_accounts = ChartAccount::select('id', 'name', 'code', 'tax_id', DB::raw('(SELECT rate FROM taxes WHERE id = chart_accounts.tax_id LIMIT 1) as rate'))
                        ->whereIn('id', $tax_credit_ids)
                        ->orderBy('name', 'asc')->get();
                        
        return (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
    }
    
    function getCoaOption( $option_name )
    {
        $option = Option::where('name', "creditinvoice_$option_name")->first();
        $coas = ($option && $option->value)? $option->value: [];
        
        if( gettype($coas) == 'string' ) 
            $coas = explode(',', $coas);
        
        return $coas;
    }
    
    function saveCoaOption( $option_name, $option_value )
    {
        $option = Option::firstOrNew(['name' => "creditinvoice_$option_name"]);
        $option->value = implode(',', $option_value);
        $option->save();
    }
    
    function customtFormValidation(Request $request)
    {
        // return $request->all(); 
        $validation = ['valid' => false, 'errors' => []];
        
        if(empty($request->customer_id)) {
            $validation['errors'][] = ['field' => 'customer_id', 'message' => 'Customer is required'];
        }
        if(empty($request->date)) {
            $validation['errors'][] = ['field' => 'date', 'message' => 'Date is required'];
        }
        if(empty($request->invoice_number)) {
            $validation['errors'][] = ['field' => 'invoice_number', 'message' => 'Inv. # is required'];
        }
        if(isset($request->amount) && floatval($request->amount) == 0) {
            $validation['errors'][] = ['field' => 'amount', 'message' => 'Amount should not be zero'];
        }
        if(!empty($request->for_open_invoice) && !$request->open_invoice_id) {
            $validation['errors'][] = ['field' => 'open_invoice_id', 'message' => 'Open Invoice is required'];
        }
        
        /* Main Account row coa_debit */
        $v = $request->vouchers[0];
        if( empty($v['chart_account_id']) || empty($v['ref_number']) || empty($v['debit']) ) {
            $validation['errors'][] = ['field' => 'voucher1', 'message' => 'Account Debit Row is missing some data. Please review.'];
        }

        /* Withholding Account row tax_debit (optional) */
        $v = $request->vouchers[1];
        if(floatval($request->whtax_amount)) {
            if( empty($v['chart_account_id']) || empty($v['ref_number']) || empty($v['debit']) || empty($v['tax_id']) || empty($v['rate']) ) {
                $validation['errors'][] = ['field' => 'voucher2', 'message' => 'Withholding Tax Row is missing some data. Please review.'];
            }
        }

        /* Discount Account row discount_debit (optional) */
        $v = $request->vouchers[2];
        if(floatval($request->discount_amount) && $request->discounted) {
            if( empty($v['chart_account_id']) || empty($v['ref_number']) || empty($v['debit']) || empty($v['discount_id']) || empty($v['rate']) ) {
                $validation['errors'][] = ['field' => 'voucher3', 'message' => 'Discount Row is missing some data. Please review.'];
            }
        }

        /* Main Account row coa_credit */
        $v = $request->vouchers[3];
        if( empty($v['chart_account_id']) || empty($v['ref_number']) || empty($v['credit']) ) {
            $validation['errors'][] = ['field' => 'voucher4', 'message' => 'Account Credit Row is missing some data. Please review.'];
        }

        /* Credit2 Account row coa_credit2 (optional) */
        $v = $request->vouchers[4];
        if(floatval($request->vat_exempt_sales)) {
            if( empty($v['chart_account_id']) || empty($v['ref_number']) || empty($v['credit']) ) {
                $validation['errors'][] = ['field' => 'voucher5', 'message' => 'Vat-exempt Sales Row is missing some data. Please review.'];
            }
        }

        /* VAT Account row tax_credit */
        $v = $request->vouchers[5];
        if( empty($v['chart_account_id']) || empty($v['ref_number']) || empty($v['credit']) || empty($v['tax_id']) || empty($v['rate']) ) {
            $validation['errors'][] = ['field' => 'voucher6', 'message' => 'Tax Credit Row is missing some data. Please review.'];
        }
        
        if(floatval($request->debit_total) != floatval($request->credit_total)) {
            $validation['errors'][] = ['field' => 'vouchertotal', 'message' => 'Account Debit vs Credit is not balanced.'];
        }

        if(count($validation['errors']) == 0)
            $validation['valid'] = true;
        
        return $validation;
    }
    
    function getTermOptions()
    {
        return ['' => 'Select', '1m' => '1 month', '3m' => '3 months', '6m' => '6 months', '1y' => '1 year'];
    }
}