<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\CashPaymentVoucher;
use App\Option;
use App\ChartAccount;
use App\SupplierInvoice;
use App\Voucher;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use DB;

class CashPaymentVoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // header("Access-Control-Allow-Origin: *");
        // $this->middleware('auth', ['only' => 'getPopupPreview']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index()
    {
        $cashpaymentvoucher = CashPaymentVoucher::paginate(15);
        // dd( $cashpaymentvoucher );
        
        /* Add Custom fields */
        foreach($cashpaymentvoucher as &$item) {
            $item->vendor_name = '';
            
            if( !empty($item->vendor) )
                $item->vendor_name = ($item->vendor->individual)
                    ? trim("{$item->vendor->first_name} {$item->vendor->middle_name} {$item->vendor->last_name}")
                    : trim("{$item->vendor->company_name}");
        }

        return view('cash-payment-voucher.index', compact('cashpaymentvoucher'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        $new_cv_number = $this->getCvNumber();
        
        $coas = [];
        $coas['debit']   = $this->getCoaDebits();
        $coas['credit']  = $this->getCoaCredits();
        
        return view('cash-payment-voucher.create', 
            compact(
                'coas', 'new_cv_number'
            )
        );
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
    
    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function store(Request $request)
    {
        // dd( $request->all() );
        // die('cash-payment-voucher -- Saving is currently disabled.');
        
        $request->merge([ 
            'invoice_amount' => $request->sii[0]['amount_payable'],
            'description' => $request->sii[0]['description']
        ]);
        
        $cpv = CashPaymentVoucher::create($request->all());
        $cpv_alias = CashPaymentVoucher::moduleAlias();
        
        if($cpv && count($request->vouchers)) {
            $order = 0;
            foreach($request->vouchers as $voucher) {
                $voucher['ref_id']       = $cpv->id;
                $voucher['module_alias'] = $cpv_alias;
                $voucher['order']        = $order;
                $voucher['debit']        = floatval($voucher['debit']);
                $voucher['credit']       = floatval($voucher['credit']);
                $voucher['date']         = $cpv->date;
                // 'chart_account_id', 'tax_id', 'key', 'ref_number'
                // dd($voucher);
                
                Voucher::create($voucher);
                $order++;
            }
        }


        /* update status to paid in supplier invoice */
        $si = SupplierInvoice::findOrFail($request->supplier_invoice_id);
        $si->update(['status' => 'paid']);


        Session::flash('flash_message', 'Cash payment Voucher created!');

        return redirect('cash-payment-voucher');
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
        $cpv = CashPaymentVoucher::findOrFail($id);
        $cpv_alias = CashPaymentVoucher::moduleAlias();
        
        $cpv->vendor_name = '';
        if( !empty($cpv->vendor) ) {
            $cpv->vendor_name = ($cpv->vendor->individual) 
                ? trim("{$cpv->vendor->first_name} {$cpv->vendor->middle_name} {$cpv->vendor->last_name}")
                : trim($cpv->vendor->company_name);
        }

        $vouchers = Voucher::where('module_alias', $cpv_alias)
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
            }
        }

        $cashpaymentvoucher = $cpv;
        
        return view('cash-payment-voucher.show', compact('cashpaymentvoucher', 'vouchers'));
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
        $cashpaymentvoucher = CashPaymentVoucher::findOrFail($id);
        $cpv_alias = CashPaymentVoucher::moduleAlias();

        $vouchers = Voucher::where('module_alias', $cpv_alias)
            ->where('ref_id', $id)
            ->orderBy('order', 'ASC') 
            ->get();
        
        $vouchers_by_key = [];
        foreach($vouchers as $v) {
            $vouchers_by_key[ $v['key'] ] = $v;
        }
        
        // default vouchers with empty values
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
        if(empty( $vouchers_by_key['coa_debit'] )) {
            $vouchers_by_key['coa_debit'] = (object) $voucher_data;
        }
        if(empty( $vouchers_by_key['coa_credit'] )) {
            $vouchers_by_key['coa_credit'] = (object) $voucher_data;
        }
        
        $invoices = (new SupplierInvoice)->getInvoicesByVendorId($cashpaymentvoucher->vendor_id);
        
        $coas = [];
        $coas['debit']   = $this->getCoaDebits();
        $coas['credit']  = $this->getCoaCredits();
        
        $coa_debit = $vouchers_by_key['coa_debit'];
        $coa_debit_id = $vouchers_by_key['coa_debit']->chart_account_id;
        
        $coa_credit = $vouchers_by_key['coa_credit'];
        $coa_credit_id = $vouchers_by_key['coa_credit']->chart_account_id;
        
        // dd(compact('cashpaymentvoucher', 'vouchers_by_key', 'invoices', 'coas', 'invoice', 'coa_debit', 'coa_credit', 'coa_debit_id', 'coa_credit_id'));
        
        return view('cash-payment-voucher.edit', compact('cashpaymentvoucher', 'vouchers_by_key', 'invoices', 'coas', 'coa_debit', 'coa_credit', 'coa_debit_id', 'coa_credit_id'));
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
        
        $cpv = CashPaymentVoucher::findOrFail($id);
        $cpv->update($request->all());
        $cpv_alias = CashPaymentVoucher::moduleAlias();

        if($cpv && count($request->vouchers)) {
            $order = 0;
            foreach($request->vouchers as $voucher) {
                $voucher['ref_id']       = $cpv->id;
                $voucher['module_alias'] = $cpv_alias;
                $voucher['order']        = $order;
                $voucher['debit']        = floatval($voucher['debit']);
                $voucher['credit']       = floatval($voucher['credit']);
                // 'chart_account_id', 'tax_id', 'key', 'ref_number'
                // dd($voucher);
                
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
                        'date'             => $cpv->date
                    ]
                );
                
                $order++;
            }
        }
        

        /* update status to paid in supplier invoice */
        $si = SupplierInvoice::findOrFail($request->supplier_invoice_id);
        $si->update(['status' => 'paid']);
        
        
        Session::flash('flash_message', 'Cash payment Voucher updated!');

        return redirect('cash-payment-voucher');
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
        $cpv_alias = CashPaymentVoucher::moduleAlias();
        Voucher::where('module_alias', $cpv_alias)->where('ref_id', $id)->delete();
        
        $cpv = CashPaymentVoucher::findOrFail($id);
        
        $si = SupplierInvoice::findOrFail($cpv->supplier_invoice_id);
        $si->update(['status' => '']);
        
        $cpv->delete();
        
        Session::flash('flash_message', 'Cash payment Voucher deleted!');

        return redirect('cash-payment-voucher');
    }
    
    public function getCvNumber()
    {
        $cpv = CashPaymentVoucher::select('cv_number')->orderBy('cv_number', 'DESC')->first();
        $last_cv_number = ($cpv && !empty($cpv->cv_number)) ? $cpv->cv_number : 0;
        
        /* fix our number format is we retrieved a cv_number else set to 0 */
        $last_cv_number = ($last_cv_number) ? ltrim($last_cv_number, '0') : 0;
        
        /* Add 1 to generate a new cv_number */
        $new_cv_number = $last_cv_number + 1;
        
        /* format the number to preferred length by adding zeros at the left side */
        $length = 6;
        $new_cv_number = str_pad($new_cv_number, $length, '0', STR_PAD_LEFT);
        
        // dd($last_cv_number);
        return $new_cv_number;
    }
    
    public function getCoaAccountsPayable()
    {
        $chart_accounts = ChartAccount::select('id', 'name', 'code')
                        ->where('code', '=', '2000')
                        ->orderBy('name', 'asc')->get();
        // dd($chart_accounts);
        
        return $chart_accounts;
    }
    
    public function getCoaCash()
    {
        $chart_accounts = ChartAccount::select('id', 'name', 'code')
                        ->whereRaw("(code like '1000%' AND name != 'PPE') OR code like '1001%' OR code like '1301%'")
                        ->orderBy('name', 'asc')->get();
        // dd($chart_accounts);
        
        return $chart_accounts;
    }
    
    function accountDetails()
    {
        $chart_accounts = ChartAccount::orderBy('name', 'asc')->lists('name', 'id');
        $chart_accounts_option = (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
        
        // $chart_accounts = ChartAccount::orderBy('name', 'asc')
            // ->where('tax_id', '!=', DB::raw(0))
            // ->lists('name', 'id');
        // $chart_account_taxes_option = (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
        
        $cashpaymentvoucher = new \stdClass;
        $cashpaymentvoucher->coa_debit = $this->getCoaOption('coa_debit');
        $cashpaymentvoucher->coa_credit = $this->getCoaOption('coa_credit');
        
        // dd( $cashpaymentvoucher );
        
        return view('cash-payment-voucher.account-details', 
            compact(
                'cashpaymentvoucher', 'chart_accounts_option'/* , 'chart_account_taxes_option' */
            )
        );
    }
    
    function accountDetailsUpdate(Request $request)
    {
        $options = [
            'coa_debit', 'coa_credit',
        ];
        
        /* Set a blank value for the options if not yet set */
        foreach($options as $option_name) {
            if( !isset($request->{ $option_name }) ) $request->merge([ $option_name => [] ]);
        }
        
        /* Start updating */
        foreach( $request->all() as $option_name => $option_value ) {
            /* Do not save this fields as option */
            if( in_array($option_name, ['_method', '_token']) ) 
                continue;
            
            /* Save, as Option, the values for this ff fields: */
            if( in_array($option_name, $options) ) {
                $this->saveCoaOption( $option_name, $option_value );
            }
        }
        
        Session::flash('flash_message', 'Options saved.');
        
        return redirect('cash-payment-voucher/account-details');
    }
    
    
    function getCoaOption( $option_name )
    {
        $option = Option::where('name', "cashpaymentvoucher_$option_name")->first();
        $coas = ($option && $option->value)? $option->value: [];
        
        if( gettype($coas) == 'string' ) 
            $coas = explode(',', $coas);
        
        return $coas;
    }
    
    function saveCoaOption( $option_name, $option_value )
    {
        $option = Option::firstOrNew(['name' => "cashpaymentvoucher_$option_name"]);
        $option->value = implode(',', $option_value);
        $option->save();
    }
    
    function customtFormValidation(Request $request)
    {
        // return $request->all(); 
        $validation = ['valid' => false, 'errors' => []];
        
        if(empty($request->vendor_id)) {
            $validation['errors'][] = ['field' => 'vendor_id', 'message' => 'Vendor is required'];
        }
        if(empty($request->date)) {
            $validation['errors'][] = ['field' => 'date', 'message' => 'Date is required'];
        }
        if(empty($request->cv_number)) {
            $validation['errors'][] = ['field' => 'cv_number', 'message' => 'CV # is required'];
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
        
        if(empty($request->sii[0]['supplier_invoice_id']) && empty($request->cpv_id)) {
            $validation['errors'][] = ['field' => 'supplier_invoice_id', 'message' => 'Please select an invoice.'];
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
        if( empty($request->vouchers[1]['chart_account_id']) || empty($request->vouchers[1]['code']) 
            || empty($request->vouchers[1]['credit']) || empty($request->vouchers[1]['ref_number']) ) {
            $is_valid_voucher2 = false;
            $validation['errors'][] = ['field' => 'voucher2', 'message' => 'Account Credit Row is missing some data. Please review.'];
        }
        
        if(count($validation['errors']) == 0)
            $validation['valid'] = true;
        
        return $validation;
    }
}
