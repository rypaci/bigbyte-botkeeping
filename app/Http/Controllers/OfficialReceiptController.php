<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\BillingInvoice;
use App\ChartAccount;
use App\OfficialReceipt;
use App\Option;
use App\Voucher;
use Carbon\Carbon;
use DateTime;
use DB;
use Session;

class OfficialReceiptController extends Controller
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
        $officialreceipt = OfficialReceipt::paginate(15);

        /* Add Custom fields */
        foreach($officialreceipt as &$item) {
            $item->customer_name = '';
            
            if( !empty($item->customer) )
                $item->customer_name = ($item->customer->individual)
                    ? trim("{$item->customer->first_name} {$item->customer->middle_name} {$item->customer->last_name}")
                    : trim("{$item->customer->company_name}");
        }

        return view('official-receipt.index', compact('officialreceipt'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        $new_or_number = $this->getOrNumber();
        
        $coas = [];
        $coas['debit']   = $this->getCoaDebits();
        $coas['credit']  = $this->getCoaCredits();
        
        $taxes = [];
        $taxes['debit']   = $this->getTaxDebits();
        
        $discounts = [];
        $discounts['debit']   = $this->getDiscountDebits();
        
        return view('official-receipt.create', 
            compact(
                'coas', 'taxes', 'discounts', 'new_or_number'
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
            'invoice_amount' => $request->bi['amount_payable'],
        ]);
        // dd( $request->all() );
        
        $or = OfficialReceipt::create( $request->all() );
        $or_alias = OfficialReceipt::moduleAlias();
        
        if($or && count($request->vouchers)) {
            $order = 0;
            foreach($request->vouchers as $voucher) {
                /* Optional fields */
                if(in_array($voucher['key'], ['tax_debit']) && empty($voucher['tax_id'])) {
                    /* do not create tax voucher */
                    continue;
                }
                
                $voucher['ref_id']       = $or->id;
                $voucher['module_alias'] = $or_alias;
                $voucher['order']        = $order;
                $voucher['debit']        = floatval($voucher['debit']);
                $voucher['credit']       = floatval($voucher['credit']);
                $voucher['date']         = $or->date;
                
                Voucher::create($voucher);
                $order++;
            }
        }

        Session::flash('flash_message', 'Official Receipt added!');

        return redirect('official-receipt');
    }

    public function getOrNumber()
    {
        $or = OfficialReceipt::select('or_number')->orderBy('or_number', 'DESC')->first();
        $last_or_number = ($or && !empty($or->or_number)) ? $or->or_number : 0;
        
        /* fix our number format is we retrieved a or_number else set to 0 */
        $last_or_number = ($last_or_number) ? ltrim($last_or_number, '0') : 0;
        
        /* Add 1 to generate a new or_number */
        $new_or_number = $last_or_number + 1;
        
        /* format the number to preferred length by adding zeros at the left side */
        $length = 6;
        $new_or_number = str_pad($new_or_number, $length, '0', STR_PAD_LEFT);
        
        // dd($last_or_number);
        return $new_or_number;
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
        $option = Option::where('name', "officialreceipt_$option_name")->first();
        $coas = ($option && $option->value)? $option->value: [];
        
        if( gettype($coas) == 'string' ) 
            $coas = explode(',', $coas);
        
        return $coas;
    }
    
    function saveCoaOption( $option_name, $option_value )
    {
        $option = Option::firstOrNew(['name' => "officialreceipt_$option_name"]);
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
        
        return view('official-receipt.account-details', 
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
        
        return redirect('official-receipt/account-details');
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
        $or = OfficialReceipt::findOrFail($id);
        $or_alias = OfficialReceipt::moduleAlias();

        $or->customer_name = '';
        if( !empty($or->customer) ) {
            $or->customer_name = ($or->customer->individual)
                ? trim("{$or->customer->first_name} {$or->customer->middle_name} {$or->customer->last_name}")
                : trim($or->customer->company_name);
        }
        
        $vouchers = Voucher::where('module_alias', $or_alias)
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

        $officialreceipt = $or;

        return view('official-receipt.show', compact('officialreceipt', 'vouchers'));
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
        $or = $officialreceipt = OfficialReceipt::findOrFail($id);
        $or_alias = OfficialReceipt::moduleAlias();
        
        $invoice = BillingInvoice::find($or->billing_invoice_id);
        
        // $officialreceipt->balance = $invoice->amount_due - $or->amount - $or->sales_discount; 

        $vouchers = Voucher::where('module_alias', $or_alias)
            ->where('ref_id', $id)
            ->orderBy('order', 'ASC') 
            ->get();
        
        $vouchers_by_key = Voucher::byKey( $vouchers );
        
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
        
        return view('official-receipt.edit', 
            compact(
                'officialreceipt', 'vouchers_by_key', 'invoice', 
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
        $or = OfficialReceipt::findOrFail($id);
        $or->update($request->all());
        $or_alias = OfficialReceipt::moduleAlias();

        if($or && count($request->vouchers)) {
            $order = 0;
            foreach($request->vouchers as $voucher) {
                if($voucher['key'] == 'tax_debit') {
                    /* if voucher tax exists, and there is no tax_id, then we need to delete this voucher. */
                    if($voucher['id'] && empty($voucher['tax_id'])) {
                        Voucher::destroy($voucher['id']);
                        continue;
                    }
                }
                
                $voucher['ref_id']       = $or->id;
                $voucher['module_alias'] = $or_alias;
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
                        'date'             => $or->date
                    ]
                );
                
                $order++;
            }
        }

        Session::flash('flash_message', 'Official Receipt updated!');

        return redirect('official-receipt');
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
        $or_alias = OfficialReceipt::moduleAlias();
        Voucher::where('module_alias', $or_alias)->where('ref_id', $id)->delete();
        
        OfficialReceipt::destroy($id);

        Session::flash('flash_message', 'Official Receipt deleted!');

        return redirect('official-receipt');
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
        if(empty($request->or_number)) {
            $validation['errors'][] = ['field' => 'or_number', 'message' => 'OR # is required'];
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
        
        if(empty($request->bi['billing_invoice_id']) && empty($request->id)) {
            $validation['errors'][] = ['field' => 'billing_invoice_id', 'message' => 'Please select an invoice.'];
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
