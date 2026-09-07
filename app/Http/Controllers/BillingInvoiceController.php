<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\BillingInvoice;
use App\ChartAccount;
use App\Customer;
use App\Discount;
use App\Option;
use App\OrderItem;
use App\Service;
use App\Tax;
use App\Voucher;
use Carbon\Carbon;
use DateTime;
use DB;
use Session;

class BillingInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
        $this->mdOption                = new Option;
        $this->mdOption->prefix        = 'billinginvoice_';
        $this->mdOption->accountFields = ['coa_debit', 'tax_debit', 'discount_debit', 'coa_credit', 'coa_credit2', 'tax_credit'];
    }

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index()
    {
        // $billinginvoice = BillingInvoice::paginate(15);
        
        $customer_id = '';

        $billinginvoice = BillingInvoice::from('billing_invoices as b_i')
        ->leftjoin('official_receipts as o_r', 'b_i.id', '=', 'o_r.billing_invoice_id')
        ->leftjoin('customers as cust', 'b_i.customer_id', '=', 'cust.id')
        ->select(DB::raw("b_i.*, o_r.*, cust.*, b_i.invoice_number as bi_inv_no, b_i.amount as bi_amount, o_r.customer_id as or_customer_id, b_i.id as id"))
        ->paginate(15);
        // dd($billinginvoice);

        /* Add Custom fields */
        // foreach($billinginvoice as &$item) {
        //     $item->customer_name = '';
            
        //     if( !empty($item->customer) )
        //         $item->customer_name = ($item->customer->individual) ? 
        //             trim("{$item->customer->first_name} {$item->customer->middle_name} {$item->customer->last_name}") :
        //             trim("{$item->customer->company_name}");
        // }
        return view('billing-invoice.index', compact('billinginvoice'));
    }

    public function prepare_data($billinginvoice = null, $vouchers = null)
    {
        $discount = Discount::getScPwd();
        
        $vat = Tax::getVat();
        
        /* Withholding Tax */
        $withholding = [
            'value'       => ($billinginvoice && $billinginvoice->whtax_id) ? $billinginvoice->whtax_id : 0,
            'options'     => Tax::getAllWithholdingTaxes(),
            'data_fields' => ['chart_account_id', 'chart_account_name', 'id', 'rate', 'code'],
        ];
        // dd( $withholding );
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
    
    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        extract( $this->prepare_data() );
        
        return view('billing-invoice.create',
            compact(
                'vat', 'discount', 'withholding', 'account_rows'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $bi = BillingInvoice::create($request->all());
        $bi_alias = BillingInvoice::moduleAlias();

        if($bi && count($request->services)) {
            foreach($request->services as $service) {
                $service['ref_id']       = $bi->id; 
                $service['module_alias'] = $bi_alias; 
                
                OrderItem::create($service);
            }
        }
        
        if($bi && count($request->vouchers)) {
            $order = 0;
            foreach($request->vouchers as $voucher) {
                // optional voucher. Do not create when conditions are not met.
                if($voucher['key'] == 'tax_debit' && !floatval($request->whtax_amount)) continue;
                if($voucher['key'] == 'discount_debit' && (!$request->discounted || !floatval($request->discount_amount))) continue;
                if($voucher['key'] == 'coa_credit2' && (!$request->discounted || !floatval($request->vat_exempt_sales))) continue;
                
                $voucher['ref_id']       = $bi->id;
                $voucher['module_alias'] = $bi_alias;
                $voucher['order']        = $order;
                $voucher['debit']        = floatval($voucher['debit']);
                $voucher['credit']       = floatval($voucher['credit']);
                $voucher['date']         = $bi->date;
                // 'chart_account_id', 'tax_id', 'key', 'ref_number'
                // dd($voucher);
                
                Voucher::create($voucher);
                $order++;
            }
        }

        Session::flash('flash_message', 'Billing Invoice added!');

        return redirect('billing-invoice');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function show($id)
    {
        $billinginvoice = BillingInvoice::select('bi.*', DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) as customer_name"))
            ->from('billing_invoices as bi')
            ->leftJoin('customers as cus', 'cus.id', '=', 'bi.customer_id')
            ->where('bi.id', $id)->first();
        $bi_alias = BillingInvoice::moduleAlias();

        $orderitems = OrderItem::where('module_alias', $bi_alias)->where('ref_id', $id)
            ->orderBy('id', 'ASC') 
            ->get();
            
        $discounts_option = ['0' => 'No', '1' => 'Yes'];
        
        $whtaxes_option = ( new CashInvoiceController )->findWithHoldingTax( new Request(['whtaxtype' => 'Withholding']) );
        
        $vouchers = Voucher::where('module_alias', $bi_alias)
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
        
        return view('billing-invoice.show', compact(
            'billinginvoice', 'orderitems', 'discounts_option', 'whtaxes_option', 'vouchers_by_key', 'vouchers', 
            'debit_coa_id', 'credit_coa_id', 'credit_tax_coa_id', 
            'credit_tax_id', 
            'vat_perc', 'discount_perc', 
            'coas', 'taxes'
        ));
        return view('billing-invoice.show', compact('billinginvoice'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function edit($id)
    {
        $billinginvoice = BillingInvoice::findOrFail($id);
        $bi_alias = BillingInvoice::moduleAlias();
        
        $orderitems = OrderItem::where('module_alias', $bi_alias)->where('ref_id', $id)
            ->orderBy('id', 'ASC') 
            ->get();
            
        $vouchers = Voucher::where('module_alias', $bi_alias)
            ->where('ref_id', $id)
            ->orderBy('order', 'ASC') 
            ->get();
        
        extract( $this->prepare_data( $billinginvoice, $vouchers ) );
        
        return view('billing-invoice.edit', 
            compact(
                'billinginvoice', 'orderitems',
                'vat', 'discount', 'withholding', 'account_rows'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function update($id, Request $request)
    {
        // dd($request->all());
        $bi = BillingInvoice::findOrFail($id);
        $bi->update($request->all());
        $bi_alias = BillingInvoice::moduleAlias();
        
        /* Get all previous order items. Only IDs. 
           We do this because we need to delete the remaining orderitems soon */
        $orderitems = OrderItem::select('id')->where('module_alias', $bi_alias)->where('ref_id', $id)->get();
        $orderitem_ids = [];
        foreach($orderitems as $item) {
            $orderitem_ids[] = $item->id;
        }
        
        if($bi && count($request->services)) {
            foreach($request->services as $service) {
                // price, qty, amount, service_id, name, ref_id
                $service['id'] = (!empty($service['id'])) ? $service['id'] : 0;
                
                OrderItem::updateOrCreate(
                    // fields to match in DB
                    ['id' => $service['id']], 
                    
                    // fields to set the values
                    [
                        'name'         => $service['name'], 
                        'amount'       => $service['amount'], 
                        'service_id'   => $service['service_id'], 
                        'ref_id'       => $bi->id, 
                        'module_alias' => $bi_alias, 
                    ]
                );
                
                /* Remove the orderitem ID so we can exclude from deleting orderitem */
                if (($k = array_search($service['id'], $orderitem_ids)) !== false) {
                    unset($orderitem_ids[$k]);
                }
            }
        }
        
        /* Delete old orderitems */
        if(count($orderitem_ids)) {
            OrderItem::whereIn('id', $orderitem_ids)->delete();
        }
        
        
        if($bi && count($request->vouchers)) {
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
                
                $voucher['ref_id']       = $bi->id;
                $voucher['module_alias'] = $bi_alias;
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
                        'discount_id'      => $voucher['discount_id'], 
                        'rate'             => $voucher['rate'], 
                        'order'            => $voucher['order'], 
                        'debit'            => $voucher['debit'], 
                        'credit'           => $voucher['credit'], 
                        'key'              => $voucher['key'], 
                        'date'             => $bi->date
                    ]
                );
                
                $order++;
            }
        }
        
        Session::flash('flash_message', 'Billing Invoice updated!');

        return redirect('billing-invoice');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function destroy($id)
    {
        $bi_alias = BillingInvoice::moduleAlias();
        OrderItem::where('module_alias', $bi_alias)->where('ref_id', $id)->delete();
        Voucher::where('module_alias', $bi_alias)->where('ref_id', $id)->delete();
        
        BillingInvoice::destroy($id);

        Session::flash('flash_message', 'BillingInvoice deleted!');

        return redirect('billing-invoice');
    }

    function getService(Request $request)
    {
        $s = $request->s;

        if(!empty($request->serv_id)) {
            $service = Service::find($request->serv_id);
            return $service? $service: [];
        }
        
        if(!$s) return [];
        
        $service = Service::where('name', 'LIKE', "$s%")->first();
        return $service? $service: [];
    }
    
    function getServices(Request $request)
    {
        $s = $request->term;
        
        $service = Service::select('id', 'name')
            ->where('name', 'LIKE', "%$s%")
            ->orderBy('name');
            
        $services = $service->get();
        
        $response = [];
        foreach($services as $c){
            $response[] = [ 'id' => $c->id, 'label' => $c->name, 'value' => $c->name ];
        }
        
        return $response;
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
        
        return view('billing-invoice.account-details', 
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
        
        return redirect('billing-invoice/account-details');
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
        $option = Option::where('name', "billinginvoice_$option_name")->first();
        $coas = ($option && $option->value)? $option->value: [];
        
        if( gettype($coas) == 'string' ) 
            $coas = explode(',', $coas);
        
        return $coas;
    }
    
    function saveCoaOption( $option_name, $option_value )
    {
        $option = Option::firstOrNew(['name' => "billinginvoice_$option_name"]);
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
}
