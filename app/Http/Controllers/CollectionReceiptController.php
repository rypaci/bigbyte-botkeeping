<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;

use App\ChartAccount;
use App\CollectionReceipt;
use App\CreditInvoice;
use App\OpenInvoice;
use App\Option;
use App\SupplierInvoice;
use App\Voucher;
use Carbon\Carbon;
use DB;
use Session;

class CollectionReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index()
    {
        // $collectionreceipt = CollectionReceipt::paginate(3);
        // dd($collectionreceipt);
        
        $page = Input::get('page', 1);
        $paginate = 10;
        
        $count_all = OpenInvoice::count();
        $collection = OpenInvoice::select('id', 'customer_id', 'date', 'oi_number as cr_number', 'invoice_number', 'payment_method', 'amount', 'sales_discount', DB::raw('"OpenInvoice" as model'));

        $count_all += CollectionReceipt::count();
        $collection = CollectionReceipt::select('id', 'customer_id', 'date', 'cr_number', 'invoice_number', 'payment_method', 'amount', 'sales_discount', DB::raw('"CollectionReceipt" as model'))
            ->unionAll($collection)
            ->skip(($page - 1) * $paginate)->take($paginate)
            ->get();

        /* Resolves method render not exists */
        $collection = new LengthAwarePaginator($collection, $count_all, $paginate, $page);
        $collection->setPath( url('collection-receipt') );
        
        $collectionreceipt = $collection;
        // dd($collectionreceipt);
        
        /* Add Custom fields */
        foreach($collectionreceipt as &$item) {
            $item->customer_name = '';
            
            if( !empty($item->customer) )
                $item->customer_name = ($item->customer->individual)
                    ? trim("{$item->customer->first_name} {$item->customer->middle_name} {$item->customer->last_name}")
                    : trim("{$item->customer->company_name}");
        }

        return view('collection-receipt.index', compact('collectionreceipt'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        $new_cr_number = $this->getCrNumber();
        
        $coas = [];
        $coas['debit']   = $this->getCoaDebits();
        $coas['credit']  = $this->getCoaCredits();
        
        $taxes = [];
        $taxes['debit']   = $this->getTaxDebits();
        
        $discounts = [];
        $discounts['debit']   = $this->getDiscountDebits();
        
        return view('collection-receipt.create', 
            compact(
                'coas', 'taxes', 'discounts', 'new_cr_number'
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
        $request->merge([ 
            'invoice_amount' => $request->cri['amount_payable'],
        ]);
        // dd( $request->all() );
        
        $cr = CollectionReceipt::create( $request->all() );
        $cr_alias = CollectionReceipt::moduleAlias();
        
        if($cr && count($request->vouchers)) {
            $order = 0;
            foreach($request->vouchers as $voucher) {
                /* Optional fields */
                if(in_array($voucher['key'], ['tax_debit']) && empty($voucher['tax_id'])) {
                    /* do not create tax voucher */
                    continue;
                }
                
                $voucher['ref_id']       = $cr->id;
                $voucher['module_alias'] = $cr_alias;
                $voucher['order']        = $order;
                $voucher['debit']        = floatval($voucher['debit']);
                $voucher['credit']       = floatval($voucher['credit']);
                $voucher['date']         = $cr->date;
                
                Voucher::create($voucher);
                $order++;
            }
        }

        /* update status to paid in credit invoice */
        $cri = CreditInvoice::findOrFail($request->credit_invoice_id);
        $cri->update(['status' => 'paid']);
        
        Session::flash('flash_message', 'Collection Receipt added!');

        return redirect('collection-receipt');
        // $this->validate($request, ['payment_method' => '16', 'bank_code' => '32', 'check_number' => '32', 'invoice_number' => '32', ]);
    }

    public function getCrNumber()
    {
        $cr = CollectionReceipt::select('cr_number')->orderBy('cr_number', 'DESC')->first();
        $last_cr_number = ($cr && !empty($cr->cr_number)) ? $cr->cr_number : 0;
        
        /* fix our number format is we retrieved a cr_number else set to 0 */
        $last_cr_number = ($last_cr_number) ? ltrim($last_cr_number, '0') : 0;
        
        /* Add 1 to generate a new cr_number */
        $new_cr_number = $last_cr_number + 1;
        
        /* format the number to preferred length by adding zeros at the left side */
        $length = 6;
        $new_cr_number = str_pad($new_cr_number, $length, '0', STR_PAD_LEFT);
        
        // dd($last_cr_number);
        return $new_cr_number;
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
    
    function getTaxDebits()
    {
        $tax_debit_ids = $this->getCoaOption('tax_debit');
        
        $chart_accounts = ChartAccount::select('id', 'name', 'code', 'tax_id', DB::raw('(SELECT rate FROM taxes WHERE id = chart_accounts.tax_id LIMIT 1) as rate'))
                        ->whereIn('id', $tax_debit_ids)
                        ->orderBy('name', 'asc')->get();
                        
        return (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
    }
    
    function getDiscountDebits()
    {
        $coa_credit_ids = $this->getCoaOption('discount_debit');
        
        $chart_accounts = ChartAccount::select('id', 'name', 'code')
                        ->whereIn('id', $coa_credit_ids)
                        ->orderBy('name', 'asc')->get();
                        
        return (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
    }
    
    function getCoaOption( $option_name )
    {
        $option = Option::where('name', "collectionreceipt_$option_name")->first();
        $coas = ($option && $option->value)? $option->value: [];
        
        if( gettype($coas) == 'string' ) 
            $coas = explode(',', $coas);
        
        return $coas;
    }
    
    function saveCoaOption( $option_name, $option_value )
    {
        $option = Option::firstOrNew(['name' => "collectionreceipt_$option_name"]);
        $option->value = implode(',', $option_value);
        $option->save();
    }
    
    function accountDetails()
    {
        $chart_accounts = ChartAccount::orderBy('name', 'asc')->lists('name', 'id');
        $chart_accounts_option = (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
        
        $chart_account_discounts_option = $chart_accounts_option;
        
        $chart_accounts = ChartAccount::orderBy('name', 'asc')
            ->where('tax_id', '!=', DB::raw(0))
            ->lists('name', 'id');
        $chart_account_taxes_option = (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
        
        $coa_options = new \stdClass;
        $coa_options->coa_debit = $this->getCoaOption('coa_debit');
        $coa_options->tax_debit = $this->getCoaOption('tax_debit');
        $coa_options->discount_debit = $this->getCoaOption('discount_debit');
        $coa_options->coa_credit = $this->getCoaOption('coa_credit');
        
        // dd( $coa_options );
        
        return view('collection-receipt.account-details', 
            compact(
                'coa_options', 'chart_accounts_option', 'chart_account_taxes_option', 'chart_account_discounts_option'
            )
        );
    }
    
    function accountDetailsUpdate(Request $request)
    {
        /* Set a blank value for the options */
        if( !isset($request->coa_debit) ) $request->merge([ 'coa_debit' => [] ]);
        if( !isset($request->discount_debit) ) $request->merge([ 'discount_debit' => [] ]);
        if( !isset($request->tax_debit) ) $request->merge([ 'tax_debit' => [] ]);
        if( !isset($request->coa_credit) ) $request->merge([ 'coa_credit' => [] ]);
        
        /* Start updating */
        foreach( $request->all() as $option_key => $option_value ) {
            /* Do not save this fields as option */
            if( in_array($option_key, ['_method', '_token']) ) 
                continue;
            
            /* Save, as Option, the values for this ff fields: */
            if( in_array($option_key, ['coa_debit', 'discount_debit', 'tax_debit', 'coa_credit']) ) {
                $this->saveCoaOption( $option_key, $option_value );
            }
        }
        
        Session::flash('flash_message', 'Options saved.');
        
        return redirect('collection-receipt/account-details');
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
        $cr = CollectionReceipt::findOrFail($id);
        $cr_alias = CollectionReceipt::moduleAlias();

        $cr->customer_name = '';
        if( !empty($cr->customer) ) {
            $cr->customer_name = ($cr->customer->individual)
                ? trim("{$cr->customer->first_name} {$cr->customer->middle_name} {$cr->customer->last_name}")
                : trim($cr->customer->company_name);
        }
        
        $vouchers = Voucher::where('module_alias', $cr_alias)
            ->where('ref_id', $id)
            ->orderBy('order', 'ASC') 
            ->get();
        foreach($vouchers as &$item){
            if($item->account){
                $item->account_number = $item->account->code;
                $item->account_title  = $item->account->name;
                $item->type = 'account';
            }
            elseif($item->tax){
                $item->account_number = '';
                $item->account_title  = $item->tax->name;
                $item->type = 'tax';
            }//dump($item->debit);dump($item->credit);
        }//dd( $vouchers );

        $collectionreceipt = $cr;

        return view('collection-receipt.show', compact('collectionreceipt', 'vouchers'));
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
        $cr = $collectionreceipt =  CollectionReceipt::findOrFail($id);
        $cr_alias = CollectionReceipt::moduleAlias();
        
        $invoice = CreditInvoice::find($cr->credit_invoice_id);
        
        // $collectionreceipt->balance = $invoice->amount_due - $cr->amount - $cr->sales_discount; 

        $vouchers = Voucher::where('module_alias', $cr_alias)
            ->where('ref_id', $id)
            ->orderBy('order', 'ASC') 
            ->get();
        
        $vouchers_by_key = Voucher::byKey( $vouchers );
        // dd($vouchers_by_key);
        
        $coas = [];
        $coas['debit']   = $this->getCoaDebits();
        $coas['credit']  = $this->getCoaCredits();
        
        $taxes = [];
        $taxes['debit']   = $this->getTaxDebits();
        
        $discounts = [];
        $discounts['debit']   = $this->getDiscountDebits();
        
        $debit_coa_id  = $vouchers_by_key["coa_debit"]->chart_account_id;
        $credit_coa_id = $vouchers_by_key["coa_credit"]->chart_account_id;
        
        $debit_tax_id = $debit_tax_coa_id = 0;
        if(isset($vouchers_by_key["tax_debit"])) {
            $debit_tax_id     = $vouchers_by_key["tax_debit"]->tax_id;
            $debit_tax_coa_id = $vouchers_by_key["tax_debit"]->chart_account_id;
        }
        
        $debit_disc_id     = $vouchers_by_key["discount_debit"]->chart_account_id; //for clarification in the future if should be linked to discounts table.
        $debit_disc_coa_id = $vouchers_by_key["discount_debit"]->chart_account_id;
        
        return view('collection-receipt.edit', 
            compact(
                'collectionreceipt', 'vouchers_by_key', 'invoice', 
                'coas', 'taxes', 'discounts',
                'debit_coa_id', 'credit_coa_id', 'debit_tax_id', 'debit_tax_coa_id', 'debit_disc_id', 'debit_disc_coa_id'
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
        $cr = CollectionReceipt::findOrFail($id);
        $cr->update($request->all());
        $cr_alias = CollectionReceipt::moduleAlias();

        if($cr && count($request->vouchers)) {
            $order = 0;
            foreach($request->vouchers as $voucher) {
                if($voucher['key'] == 'tax_debit') {
                    /* if voucher tax exists, and there is no tax_id, then we need to delete this voucher. */
                    if($voucher['id'] && empty($voucher['tax_id'])) {
                        Voucher::destroy($voucher['id']);
                        continue;
                    }
                }
                
                $voucher['ref_id']       = $cr->id;
                $voucher['module_alias'] = $cr_alias;
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
                        'date'             => $cr->date
                    ]
                );
                
                $order++;
            }
        }

        Session::flash('flash_message', 'Collection Receipt updated!');

        return redirect('collection-receipt');
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
        $cr_alias = CollectionReceipt::moduleAlias();
        Voucher::where('module_alias', $cr_alias)->where('ref_id', $id)->delete();
        
        CollectionReceipt::destroy($id);

        Session::flash('flash_message', 'Collection Receipt deleted!');

        return redirect('collection-receipt');
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
        if(empty($request->cr_number)) {
            $validation['errors'][] = ['field' => 'cr_number', 'message' => 'CR # is required'];
        }
        if(empty($request->payment_method)) {
            $validation['errors'][] = ['field' => 'payment_method', 'message' => 'Payment method is required'];
        }
        elseif($request->payment_method == 'check') {
            if(empty($request->bank_code)) {
                $validation['errors'][] = ['field' => 'bank_code', 'message' => 'Bank code is required'];
            }
            if(empty($request->check_number)) {
                $validation['errors'][] = ['field' => 'check_number', 'message' => 'Check # is required'];
            }
        }
        if(empty($request->amount)) {
            $validation['errors'][] = ['field' => 'amount', 'message' => 'Amount is required'];
        }
        
        if(empty($request->cri['credit_invoice_id']) && empty($request->id)) {
            $validation['errors'][] = ['field' => 'credit_invoice_id', 'message' => 'Please select an invoice.'];
        }
        if($request->balance == '' || floatval($request->balance) != 0) {
            $validation['errors'][] = ['field' => 'balance', 'message' => 'Balance is not zero.'];
        }
        
        $is_valid_voucher1 = true;
        if( empty($request->vouchers[0]['chart_account_id']) || empty($request->vouchers[0]['code']) 
            || empty($request->vouchers[0]['debit']) || empty($request->vouchers[0]['ref_number']) ) {
            $is_valid_voucher1 = false;
            $validation['errors'][] = ['field' => 'voucher1', 'message' => 'Account Debit Row is missing some data. Please review.'];
        }
        
        $is_valid_voucher2 = true;
        if( $request->vouchers[1]['tax_id'] ) {
            if( empty($request->vouchers[1]['tax_id']) || empty($request->vouchers[1]['rate']) 
                || empty($request->vouchers[1]['debit']) || empty($request->vouchers[1]['ref_number']) ) {
                $is_valid_voucher2 = false;
                $validation['errors'][] = ['field' => 'voucher2', 'message' => 'Tax Debit Row is missing some data. Please review.'];
            }
        }
        
        $is_valid_voucher3 = true;
        if( $request->vouchers[2]['chart_account_id'] ) {
            if( empty($request->vouchers[2]['chart_account_id']) || empty($request->vouchers[2]['code']) 
                || empty($request->vouchers[2]['ref_number']) ) {
                $is_valid_voucher3 = false;
                $validation['errors'][] = ['field' => 'voucher3', 'message' => 'Discount Debit Row is missing some data. Please review.'];
            }
        }
        
        $is_valid_voucher4 = true;
        if( empty($request->vouchers[3]['chart_account_id']) || empty($request->vouchers[3]['code']) 
            || empty($request->vouchers[3]['credit']) || empty($request->vouchers[3]['ref_number']) ) {
            $is_valid_voucher4 = false;
            $validation['errors'][] = ['field' => 'voucher4', 'message' => 'Account Credit Row is missing some data. Please review.'];
        }
        
        if(count($validation['errors']) == 0)
            $validation['valid'] = true;
        
        return $validation;
    }
}
