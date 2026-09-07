<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\CashInvoice;
use App\ChartAccount;
use App\Customer;
use App\Discount;
use App\OpenInvoice;
use App\Option;
use App\OrderItem;
use App\Product;
use App\Tax;
use App\Voucher;
use Carbon\Carbon;
use DateTime;
use DB;
use Session;

class CashInvoiceController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        
        $this->mdOption                = new Option;
        $this->mdOption->prefix        = 'cashinvoice_';
        $this->mdOption->accountFields = ['coa_debit', 'tax_debit', 'discount_debit', 'coa_credit', 'coa_credit2', 'coa_credit3', 'coa_credit4', 'tax_credit'];
        
        // dd( $this->mdOption->getCoas('coa_debit') );
        // dd( ChartAccount::getByIds( [1,2] ) );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cashinvoices = CashInvoice::select('ci.*', DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) as customer_name"))
            ->from('cashinvoices as ci')
            ->leftJoin('customers as cus', 'cus.id', '=', 'ci.customer_id')
            // ->toSql();
            ->paginate(15);

        // dd( $cashinvoices );
        
        return view('cash-invoice.index', compact('cashinvoices'));
    }

    public function prepare_data($cashinvoice = null, $vouchers = null)
    {
        $discount = Discount::getScPwd();
        
        $vat = Tax::getVat();
        
        /* Withholding Tax */
        $withholding = [
            'value'       => ($cashinvoice && $cashinvoice->whtax_id) ? $cashinvoice->whtax_id : 0,
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
        extract( $this->prepare_data() );

        return view('cash-invoice.create', 
            compact(
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
        $ci = CashInvoice::create( $request->all() );
        $ci_alias = CashInvoice::moduleAlias();
        
        if($ci && count($request->products)) {
            foreach($request->products as $product) {
                $product['ref_id']       = $ci->id; 
                $product['module_alias'] = $ci_alias; 
                
                OrderItem::create($product);
            }
        }
        
        if($ci && count($request->vouchers)) {
            $order = 0;
            foreach($request->vouchers as $voucher) {
                // optional voucher. Do not create when conditions are not met.
                if($voucher['key'] == 'tax_debit' && !floatval($request->whtax_amount)) continue;
                if($voucher['key'] == 'discount_debit' && (!$request->discounted || !floatval($request->discount_amount))) continue;
                if($voucher['key'] == 'coa_credit2' && (!$request->discounted || !floatval($request->vat_exempt_sales))) continue;
                
                $voucher['ref_id']       = $ci->id;
                $voucher['module_alias'] = $ci_alias;
                $voucher['order']        = $order;
                $voucher['debit']        = floatval($voucher['debit']);
                $voucher['credit']       = floatval($voucher['credit']);
                $voucher['date']         = $ci->date;
                // 'chart_account_id', 'tax_id', 'discount_id', 'rate', 'key', 'ref_number'
                // dump($voucher);
                
                Voucher::create($voucher);
                $order++;
            }
        }
        // dd('done');

        Session::flash('flash_message', 'Cash Invoice created!');

        return redirect('cash-invoice');
    }

    public function show($id)
    {
        $cashinvoice = CashInvoice::select('ci.*', DB::raw("TRIM(IF(cus.individual, CONCAT(cus.first_name, ' ', cus.middle_name, ' ', cus.last_name), cus.company_name)) as customer_name"))
            ->from('cashinvoices as ci')
            ->leftJoin('customers as cus', 'cus.id', '=', 'ci.customer_id')
            ->where('ci.id', $id)->first();
        $ci_alias = CashInvoice::moduleAlias();

        $orderitems = OrderItem::where('module_alias', $ci_alias)->where('ref_id', $id)
            ->orderBy('id', 'ASC') 
            ->get();
            
        $discounts_option = ['0' => 'No', '1' => 'Yes'];
        
        $whtaxes_option = $this->findWithHoldingTax( new Request(['whtaxtype' => 'Withholding']) );
        
        $vouchers = Voucher::where('module_alias', $ci_alias)
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
        // dd( $cashinvoice );
        return view('cash-invoice.show', compact(
            'cashinvoice', 'orderitems', 'discounts_option', 'whtaxes_option', 'vouchers_by_key', 'vouchers', 
            'debit_coa_id', 'credit_coa_id', 'credit_tax_coa_id', 
            'credit_tax_id', 
            'vat_perc', 'discount_perc', 
            'coas', 'taxes'
        ));
    }

    public function edit(Request $request, $id)
    {
        $cashinvoice = CashInvoice::find($id);
        $ci_alias = CashInvoice::moduleAlias();

        $orderitems = OrderItem::where('module_alias', $ci_alias)->where('ref_id', $id)
            ->orderBy('id', 'ASC') 
            ->get();
        
        $vouchers = Voucher::where('module_alias', $ci_alias)
            ->where('ref_id', $id)
            ->orderBy('order', 'ASC') 
            ->get();
        
        extract( $this->prepare_data( $cashinvoice, $vouchers ) );
        
        return view('cash-invoice.edit', 
            compact(
                'cashinvoice', 'orderitems',
                'vat', 'discount', 'withholding', 'account_rows'
            )
        );
    }

    public function searchName(Request $request)
    {
        $cashinv = DB::table('cashinvoices')
        ->select('cashinvoices.*')
        ->orderBy('invoice_no', 'DESC')
        ->limit(1)
        ->get();

        $customer_name = $request->customer_name;

        if(!empty($customer_name)){
        $customers = DB::table('customers')
            ->where('first_name', 'LIKE', '%'.$request->customer_name.'%')
            ->orwhere('middle_name', 'LIKE', '%'.$request->customer_name.'%')
            ->orwhere('last_name', 'LIKE', '%'.$request->customer_name.'%')
            ->orwhere('company_name', 'LIKE', '%'.$request->customer_name.'%')
            ->get();
        }else{
            $request->customer_name = "Search";
            $customers = DB::table('customers')
            ->where('first_name', 'LIKE', '%'.$request->customer_name.'%')
            ->orwhere('middle_name', 'LIKE', '%'.$request->customer_name.'%')
            ->orwhere('last_name', 'LIKE', '%'.$request->customer_name.'%')
            ->orwhere('company_name', 'LIKE', '%'.$request->customer_name.'%')
            ->get();
        }
        return view('cash-invoice.create-cash-invoice', compact('customers','cashinv'));
    }

    /* function searchNameAjax(Request $request)
    {
        $cust_name = $request->cust_name;

        if(!empty($request->customer_id)) {
            $searchcustomers = Customer::find($request->customer_id);
            if($searchcustomers)
            $searchcustomers->name = trim( "{$searchcustomers->first_name} {$searchcustomers->middle_name} {$searchcustomers->last_name} {$searchcustomers->company_name}");
            return $searchcustomers? $searchcustomers: [];
        }
        if(!$cust_name) return [];
            $searchcustomers = Customer::select('*', DB::raw('CONCAT(`company_name`,"",`first_name`," ",`middle_name`," ",`last_name`) as full_name'))
            ->havingRaw("full_name LIKE '%$cust_name%'")
            ->get();
            return $searchcustomers? $searchcustomers: [];
    } */
    
    public function getCustomers(Request $request)
    {
        $s = $request->term;
        
        $customer = Customer::select('*', DB::raw('TRIM(CONCAT(first_name, " ", middle_name, " ", last_name)) as `name`'))
            ->where('individual', '=', 1)
            ->where(function($query) use ($s){
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
        foreach($customers as $c){
            $response[] = [ 'id' => $c->id, 'label' => $c->name, 'value' => $c->name ];
        }
        
        return $response;
    }

    function findCustomerOnly(Request $request)
    {
        if(!empty($request->customer_id)) {
            $customer = Customer::select('*', DB::raw("TRIM(IF(individual, CONCAT(first_name, ' ', middle_name, ' ', last_name), company_name)) as full_name"))
                ->find($request->customer_id);
            
            if(!empty($request->inc_open) && $customer) {
                $customer->open_invoices = (new OpenInvoice)->getInvoicesByCustomerId($customer->id);
            }
            
            return $customer? $customer: [];
        }
        
        $s = $request->cust_name;
        
        if(!$s) return [];
        
        $customer = Customer::select('*', DB::raw('CONCAT(`first_name`," ",`middle_name`," ",`last_name`) as full_name'))
            ->where('individual', '=', 1)
            ->where(DB::raw('TRIM(CONCAT(first_name, " ", middle_name, " ", last_name))'), 'LIKE', "%$s%")
            ->first();
        
        if(!$customer) {
            $customer = Customer::select('*', 'company_name as full_name')
                ->where('individual', '=', 0)
                ->where('company_name', 'LIKE', "%$s%")
                ->first();
        }
        
        if(!empty($request->inc_open) && $customer) {
            $customer->open_invoices = (new OpenInvoice)->getInvoicesByCustomerId($customer->id);
        }
        
        return $customer? $customer: [];
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
        // dd( $request->all() );
        $ci = CashInvoice::findOrFail($id);
        $ci->update($request->all());
        $ci_alias = CashInvoice::moduleAlias();
        
        /* Get all previous order items. Only IDs. 
           We do this because we need to delete the remaining orderitems soon */
        $orderitems = OrderItem::select('id')->where('module_alias', $ci_alias)->where('ref_id', $id)->get();
        $orderitem_ids = [];
        foreach($orderitems as $item) {
            $orderitem_ids[] = $item->id;
        }
        
        if($ci && count($request->products)) {
            foreach($request->products as $product) {
                // price, qty, amount, product_id, name, ref_id
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
                        'ref_id'       => $ci->id, 
                        'module_alias' => $ci_alias, 
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
        
        
        if($ci && count($request->vouchers)) {
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
                
                $voucher['ref_id']       = $ci->id;
                $voucher['module_alias'] = $ci_alias;
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
                        'date'             => $ci->date
                    ]
                );
                
                $order++;
            }
        }
        
        Session::flash('flash_message', 'Cash Invoice updated!');

        return redirect('cash-invoice');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ci_alias = CashInvoice::moduleAlias();
        OrderItem::where('module_alias', $ci_alias)->where('ref_id', $id)->delete();
        Voucher::where('module_alias', $ci_alias)->where('ref_id', $id)->delete();
        
        $affectedRows = CashInvoice::find($id)->delete();
        
        Session::flash('flash_message', 'Cash Invoice deleted!');

        return redirect('cash-invoice');
    }

    function findProduct(Request $request)
    {
        $s = $request->s;

        if(!empty($request->prod_id)) {
            $searchproduct = Product::find($request->prod_id);
            
            if($searchproduct) 
                $searchproduct->name = trim( "{$searchproduct->name}");
        
            return $searchproduct? $searchproduct: [];
        }
        
        if(!$s) return [];
        
        $searchproduct = Product::where('name', 'LIKE', "$s%")
            ->first();
                
        return $searchproduct? $searchproduct: [];
    }

    /* function findProductAutoSuggest(Request $request)
    {
        $ss = $request->ss;

        if(!empty($request->prod_id)) {
            $searchproducts = Item::find($request->prod_id);
            if($searchproducts) 
            $searchproducts->name = $searchproducts->product_name;
            return $searchproducts? $searchproducts: [];
        }
        if(!$ss) return [];
            $searchproducts = Item::select('product_name')
            ->where('product_name', 'LIKE', '%'.$ss.'%')
            ->orderBy('product_name')
            ->get();
            return $searchproducts? $searchproducts: [];
    } */
    
    function findProductAutoSuggest(Request $request)
    {
        $s = $request->term;
        
        $product = Product::select('id', 'name')
            ->where('name', 'LIKE', "%$s%")
            ->orderBy('name');
            
        $products = $product->get();
        
        $response = [];
        foreach($products as $c){
            $response[] = [ 'id' => $c->id, 'label' => $c->name, 'value' => $c->name ];
        }
        
        return $response;
    }

    function findVat(Request $request)
    {
        $vat_id = $request->vat_id;
        if(!$vat_id) return [];
            $findvat_id = Tax::select('rate')
            ->where('id', '=', $vat_id)
            ->first();
            return $findvat_id? $findvat_id: [];
    }

    function findSCPWDDiscount(Request $request)
    {
        $discount_id = $request->discount_id;
        if(!$discount_id) return [];
        
        $finddiscount_id = Discount::select('rate')
            ->where('id', '=', $discount_id)
            ->first();
        
        return $finddiscount_id? $finddiscount_id: [];
    }

    function findChartAccountLevel(Request $request)
    {
        $levels_id = $request->levels_id;
        if(!$levels_id) return [];

        $findlevel = DB::table('chart_accounts')
        ->select('name','code')
        ->where('name', 'LIKE', '%'.$levels_id.'%')
        ->orderBy('name', 'ASC')
        ->get();
        return $findlevel? $findlevel: [];
    }

    function findWithHoldingTax(Request $request)
    {
        $withholdingtax = $request->whtaxtype;
        if(!$withholdingtax) return [];
        
        $taxes = DB::table('taxes')
            ->select('id', 'name', 'rate')
            ->where('type', 'LIKE', '%'.$withholdingtax.'%')
            ->orderBy('name', 'ASC')
            ->get();
        
        if(!$taxes) $taxes = [];
        
        $default = (object) [ 'id' => '0', 'name' => 'Select Withholding tax', 'rate' => '0.00' ];
        array_unshift($taxes, $default);
        
        return $taxes;
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
        
        return view('cash-invoice.account-details', 
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
        
        return redirect('cash-invoice/account-details');
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
        $option = Option::where('name', "cashinvoice_$option_name")->first();
        $coas = ($option && $option->value)? $option->value: [];
        
        if( gettype($coas) == 'string' ) 
            $coas = explode(',', $coas);
        
        return $coas;
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