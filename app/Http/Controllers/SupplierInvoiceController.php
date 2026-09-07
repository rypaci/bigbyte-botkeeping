<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Vendor;
use App\Option;
use App\ChartAccount;
use App\Tax;
use App\SupplierInvoice;
use App\CashPaymentVoucher;
use App\Voucher;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use DB;

class SupplierInvoiceController extends Controller
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
        /* This is the original SQL where it displays all 15 rows */
        // $supplierinvoice = SupplierInvoice::paginate(15);
        
        /* This is a customed query in order to show only those supplier invoice entries where there is no CPV created yet for them. */
        $supplierinvoice = SupplierInvoice::select('*')
                // ->from("supplier_invoices as si")
                // ->where(DB::raw("(SELECT COUNT(amount) FROM cash_payment_vouchers WHERE supplier_invoice_id = si.id)"), '=', '0')
                ->where('status', '!=', 'paid')
                ->paginate(15);
        // dd( $supplierinvoice->toArray() );
        
        /* Add Custom fields */
        foreach($supplierinvoice as &$item) {
            $item->vendor_name = '';
            
            if( !empty($item->vendor) )
                $item->vendor_name = ($item->vendor->individual)
                    ? trim("{$item->vendor->first_name} {$item->vendor->middle_name} {$item->vendor->last_name}") 
                    : trim("{$item->vendor->company_name}");
        }
        
        return view('supplier-invoice.index', compact('supplierinvoice'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        $cpvcoas = []; $cpvCon = new CashPaymentVoucherController;
        $cpvcoas['debit']   = $cpvCon->getCoaDebits();
        $cpvcoas['credit']  = $cpvCon->getCoaCredits();
        
        return view('supplier-invoice.create', compact('cpvcoas'));
    }

    function getCoaDebits($journal)
    {
        $coa_debit_ids = $this->getCoaOption($journal.'coa_debit');
        
        $chart_accounts = ChartAccount::select('id', 'name', 'code')
                        ->whereIn('id', $coa_debit_ids)
                        ->orderBy('name', 'asc')->get();
                        
        return (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
    }
    
    function getCoaCredits($journal)
    {
        $coa_credit_ids = $this->getCoaOption($journal.'coa_credit');
        
        $chart_accounts = ChartAccount::select('id', 'name', 'code')
                        ->whereIn('id', $coa_credit_ids)
                        ->orderBy('name', 'asc')->get();
                        
        return (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
    }
    
    function getTaxDebits($journal)
    {
        $tax_debit_ids = $this->getCoaOption($journal.'tax_debit');
        
        $chart_accounts = ChartAccount::select('id', 'name', 'code', 'tax_id', DB::raw('(SELECT rate FROM taxes WHERE id = chart_accounts.tax_id LIMIT 1) as rate'))
                        ->whereIn('id', $tax_debit_ids)
                        ->orderBy('name', 'asc')->get();
                        
        return (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
    }
    
    function getTaxCredits($journal)
    {
        $tax_credit_ids = $this->getCoaOption($journal.'tax_credit');
        
        $chart_accounts = ChartAccount::select('id', 'name', 'code', 'tax_id', DB::raw('(SELECT rate FROM taxes WHERE id = chart_accounts.tax_id LIMIT 1) as rate'))
                        ->whereIn('id', $tax_credit_ids)
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
        // dd($request->all());
        
        $supplier_invoice_data = [
            'vendor_id'      => $request->vendor_id,
            'date'           => $request->date,
            'invoice_number' => $request->invoice_number,
            'terms'          => $request->terms,
            'period'         => $request->period,
            'journal_entry'  => $request->journal_entry,
            'description'    => $request->description,
            'amount'         => $request->amount,
            'amount_subj_to_vat' => $request->amount_subj_to_vat,
        ];
        if($request->terms == 'cod') {
            $supplier_invoice_data['status'] = 'paid';
        }
        // dd($supplier_invoice_data);
        
        $supplierinvoice = SupplierInvoice::create($supplier_invoice_data);
        $si_alias = SupplierInvoice::moduleAlias();
        
        $cpv_amount = $supplier_invoice_data['amount'];
        
        // save our vouchers if there are
        if($supplierinvoice && count($request->vouchers)) {
            $order = 0;
            foreach($request->vouchers as $voucher) {
                if($voucher['key'] == 'coa_debit_other' && (floatval($request->amount_subj_to_vat) == 0 || floatval($voucher['debit']) == 0)) {
                    /* Ensure that amount_subj_to_vat is Zero if we do not create a voucher2 */
                    $request->amount_subj_to_vat = 0;
                    
                    /* we do not create a voucher for the other debit Row if no debit amount set to this field */
                    continue;
                }
                
                if(in_array($voucher['key'], ['tax_debit', 'tax_credit']) && empty($voucher['tax_id'])) {
                    /* do not create tax voucher */
                    continue;
                }
                
                if($voucher['key'] == 'coa_credit') {
                    $cpv_amount = $voucher['credit'];
                }
                
                $voucher['ref_id']       = $supplierinvoice->id;
                $voucher['ref_number']   = $request->invoice_number;
                $voucher['module_alias'] = $si_alias;
                $voucher['order']        = $order;
                $voucher['debit']        = floatval($voucher['debit']);
                $voucher['credit']       = floatval($voucher['credit']);
                $voucher['date']         = $supplierinvoice->date;
                // 'chart_account_id', 'tax_id', 'key', 
                // dd($voucher);
                
                Voucher::create($voucher);
                $order++;
            }
        }
        
        
        
        /* Save Cash Payment Voucher if in COD */
        if($request->terms == 'cod') {
            $cpv_data = [
                'vendor_id'           => $request->vendor_id,
                'supplier_invoice_id' => $supplierinvoice->id,
                'date'                => $request->date,
                'amount'              => $cpv_amount,
                'payment_method'      => 'cash',
            ];
            
            $cpvCon = new CashPaymentVoucherController;
            $cpv_data['cv_number'] = $cpvCon->getCvNumber();
            
            $cpv = CashPaymentVoucher::create($cpv_data);
            $cpv_alias = CashPaymentVoucher::moduleAlias();
            
            if($cpv && count($request->cpvvouchers)) {
                $order = 0;
                foreach($request->cpvvouchers as $voucher) {
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
            
            Session::flash('flash_message', 'Cash payment Voucher created!');

            return redirect('cash-payment-voucher');
        }
        
        Session::flash('flash_message', 'Supplier\'s Invoice created!');

        return redirect('supplier-invoice');
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
        $supplierinvoice = SupplierInvoice::findOrFail($id);
        $si_alias = SupplierInvoice::moduleAlias();
        
        if($supplierinvoice) {
            $supplierinvoice->vendor_name = ($supplierinvoice->vendor->individual)
                ? trim("{$supplierinvoice->vendor->first_name} {$supplierinvoice->vendor->middle_name} {$supplierinvoice->vendor->last_name}")
                : trim($supplierinvoice->vendor->company_name);
        }
        
        $vouchers = Voucher::where('module_alias', $si_alias)
            ->where('ref_id', $id)
            ->orderBy('order', 'ASC') 
            ->get();
        foreach($vouchers as &$item){
            // dd( $item->account );
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

        return view('supplier-invoice.show', compact('supplierinvoice', 'vouchers'));
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
        $supplierinvoice = SupplierInvoice::findOrFail($id);
        $si_alias = SupplierInvoice::moduleAlias();
        
        $vouchers = Voucher::where('module_alias', $si_alias)
            ->where('ref_id', $id)
            ->orderBy('order', 'ASC') 
            ->get();
        
        $vouchers_by_key = [];
        foreach($vouchers as $v) {
            $vouchers_by_key[ $v['key'] ] = $v;
        }
        
        $debit_coa_id = $vouchers_by_key["coa_debit"]->chart_account_id;
        $debit_other_coa_id = isset($vouchers_by_key["coa_debit_other"]) ? $vouchers_by_key["coa_debit_other"]->chart_account_id: 0;
        $debit_other_coa_voucher_id = isset($vouchers_by_key["coa_debit_other"]) ? $vouchers_by_key["coa_debit_other"]->id: 0;
        
        $credit_coa_id = $vouchers_by_key["coa_credit"]->chart_account_id;
        
        $debit_tax_id = isset($vouchers_by_key["tax_debit"]) ? $vouchers_by_key["tax_debit"]->tax_id: 0;
        $debit_tax_voucher_id = isset($vouchers_by_key["tax_debit"]) ? $vouchers_by_key["tax_debit"]->id: 0;
        $debit_tax_coa_id = isset($vouchers_by_key["tax_debit"]) ? $vouchers_by_key["tax_debit"]->chart_account_id: 0;
        
        $credit_tax_id = isset($vouchers_by_key["tax_credit"]) ? $vouchers_by_key["tax_credit"]->tax_id: 0;
        $credit_tax_voucher_id = isset($vouchers_by_key["tax_credit"]) ? $vouchers_by_key["tax_credit"]->id: 0;
        $credit_tax_coa_id = isset($vouchers_by_key["tax_credit"]) ? $vouchers_by_key["tax_credit"]->chart_account_id: 0;
        
        $accountsTaxes = $this->getAccountsTaxes( new Request(['journal' => $supplierinvoice->journal_entry]) );
        extract( $accountsTaxes ); // sets variables coas and taxes
        // dd( compact('coas', 'taxes') );
        
        // dd($vouchers_by_key);
        return view('supplier-invoice.edit', 
            compact(
                'supplierinvoice', 'vouchers', 'vouchers_by_key', 
                'debit_coa_id', 'debit_other_coa_id', 'debit_other_coa_voucher_id', 
                'credit_coa_id', 
                'debit_tax_id', 'debit_tax_voucher_id', 'debit_tax_coa_id', 
                'credit_tax_id', 'credit_tax_voucher_id', 'credit_tax_coa_id', 
                'coas', 'taxes'
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
        
        $supplier_invoice_data = [
            'vendor_id'      => $request->vendor_id,
            'date'           => $request->date,
            'invoice_number' => $request->invoice_number,
            'terms'          => $request->terms,
            'period'         => $request->period,
            'journal_entry'  => $request->journal_entry,
            'description'    => $request->description,
            'amount'         => $request->amount,
            'amount_subj_to_vat' => $request->amount_subj_to_vat,
        ];
        // dd($supplier_invoice_data);
        // dd($request->vouchers);
        
        $supplierinvoice = SupplierInvoice::findOrFail($id);
        $supplierinvoice->update($supplier_invoice_data);
        $si_alias = SupplierInvoice::moduleAlias();

        // save our vouchers if there are
        if($supplierinvoice && count($request->vouchers)) {
            $order = 0;
            foreach($request->vouchers as $voucher) {
                if($voucher['key'] == 'coa_debit_other') {
                    /* if voucher coa_debit_other exists, and amount_subj_to_vat is changed to zero, then delete this voucher  */
                    if($voucher['id'] && floatval($request->amount_subj_to_vat) == 0) {
                        Voucher::destroy($voucher['id']);
                        continue;
                    }
                    
                    /* if voucher coa_debit_other do not exist, and amount_subj_to_vat is changed to zero, then do not create a voucher  */
                    if(empty($voucher['id']) && floatval($request->amount_subj_to_vat) == 0) {
                        continue;
                    }
                }
                if(in_array($voucher['key'], ['tax_debit', 'tax_credit'])) {
                    /* if voucher tax exists, and there is no tax_id, then we need to delete this voucher. */
                    if($voucher['id'] && empty($voucher['tax_id'])) {
                        Voucher::destroy($voucher['id']);
                        continue;
                    }
                    
                    /* if voucher tax do not exist, and there is no tax_id, then do not create a voucher */
                    if(empty($voucher['id']) && empty($voucher['tax_id'])) {
                        continue;
                    }
                }
                
                $voucher['ref_id']       = $supplierinvoice->id;
                $voucher['ref_number']   = $request->invoice_number;
                $voucher['module_alias'] = $si_alias;
                $voucher['order']        = $order;
                $voucher['debit']        = floatval($voucher['debit']);
                $voucher['credit']       = floatval($voucher['credit']);
                // other fields: chart_account_id, tax_id, id, key
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
                        'date'             => $supplierinvoice->date
                    ]
                );
                
                $order++;
            }
        }
        
        Session::flash('flash_message', 'Supplier\'s Invoice updated!');

        return redirect('supplier-invoice');
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
        $si_alias = SupplierInvoice::moduleAlias();
        $vouchers = Voucher::where('module_alias', $si_alias)->where('ref_id', $id)->delete();
        
        SupplierInvoice::destroy($id);

        Session::flash('flash_message', 'Supplier\'s Invoice deleted!');

        return redirect('supplier-invoice');
    }
    
    function findVendor(Request $request)
    {
        if(!empty($request->vendor_id)) {
            $vendor = Vendor::select('*', DB::raw("TRIM(IF(individual, CONCAT(first_name, ' ', middle_name, ' ', last_name), company_name)) as name"))
                ->find($request->vendor_id);
            
            return $vendor? $vendor: [];
        }
        
        $s = $request->s;
        
        if(!$s) return [];
        
        $vendor = Vendor::select('*', DB::raw('CONCAT(`first_name`," ",`middle_name`," ",`last_name`) as name'))
            ->where('individual', '=', 1)
            ->where(DB::raw('TRIM(CONCAT(first_name, " ", middle_name, " ", last_name))'), 'LIKE', "%$s%")
            ->first();
        
        if(!$vendor) {
            $vendor = Vendor::select('*', 'company_name as name')
                ->where('individual', '=', 0)
                ->where('company_name', 'LIKE', "%$s%")
                ->first();
        }
        
        return $vendor? $vendor: [];
    }
    
    function findVendors(Request $request)
    {
        $s = $request->term;
        
        if(!$s) return [];
        
        $vendors = Vendor::select('*', DB::raw("TRIM(IF(individual, CONCAT(first_name, ' ', middle_name, ' ', last_name), company_name)) as name"))
            ->where(function($query) use ($s){
                $query->where('first_name', 'LIKE', "$s%")
                    ->orWhere('middle_name', 'LIKE', "$s%")
                    ->orWhere('last_name', 'LIKE', "$s%");
            })
            ->orWhere('company_name', 'LIKE', "$s%")
            ->get();
        
        if(!$vendors) return [];
        
        $response = [];
        foreach($vendors as $v){
            $response[] = [ 'id' => $v->id, 'label' => $v->name, 'value' => $v->name ];
        }
        
        return $response;
    }
    
    function getAccountDetails(Request $request)
    {
        if(empty($request->supplier_invoice_id))
            return [];
            
        $voucher = Voucher::where('supplier_invoice_id', '=', $request->supplier_invoice_id)->get();
        if(!$voucher) return [];
        
        $items = is_callable([$voucher, 'toArray']) ? $voucher->toArray() : [];
        return $items;
    }
    
    /* function getAccountDetailsByJournal(Request $request)
    {
        if( $request->journal == 'asset') $chart_account_type_ids = [1,2];
        if( $request->journal == 'purchases') $chart_account_type_ids = [5];
        if( $request->journal == 'accruals') $chart_account_type_ids = [6,2];
        
        $chart_accounts = ChartAccount::whereIn('account_type_id', $chart_account_type_ids)->orderBy('name', 'asc')->lists('name', 'id');
        $chart_accounts_option = ['0' => 'Select'] + (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
        
        return $chart_accounts_option;
    } */
    
    /* function getAccountDetailsByJournal(Request $request)
    {
        if( $request->journal == 'asset') $chart_account_type_ids = [1,2];
        // if( $request->journal == 'purchases') $chart_account_type_ids = [5];
        if( $request->journal == 'accruals') $chart_account_type_ids = [8,6,2];
        
        if( $request->journal == 'purchases') {
            $arr = [];
            
            $chart_accounts = ChartAccount::select('id', 'name', 'code')
                            ->where('code', 'LIKE', '5000-00%')
                            ->orderBy('code', 'asc')->get();
            $arr['purchases'] = (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
            
            $chart_accounts = ChartAccount::select('id', 'name', 'code')
                            ->where('code', '=', '2000')
                            ->orderBy('name', 'asc')->get();
            $arr['accounts_payable'] = (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
            
            return $arr;
        }
        else {
            $chart_accounts = ChartAccount::select('id', 'name', 'code')
                                ->whereIn('account_type_id', $chart_account_type_ids)
                                ->orderBy('name', 'asc')->get();
            return (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
        }
        
    } */
    
    /* function getAccountDetailsByJournal(Request $request)
    {
        $arr = [];
        $prefix = 'si_'.$request->journal;
        
        $chart_accounts = ChartAccount::select('id', 'name', 'code')
                        ->where($prefix.'_debit', '=', '1')
                        ->where($prefix, '=', '1')
                        ->orderBy('name', 'asc')->get();
        $arr['debit'] = (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
        
        $chart_accounts = ChartAccount::select('id', 'name', 'code')
                        ->where($prefix.'_credit', '=', '1')
                        ->where($prefix, '=', '1')
                        ->orderBy('name', 'asc')->get();
        $arr['credit'] = (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
        
        return $arr;
    }
    
    function getTaxes(Request $request)
    {
        $taxes = Tax::select('id', 'name', 'rate')
                            ->where('type', '=', $request->type)
                            ->orderBy('name', 'asc')->get();
        // dd($chart_accounts->toArray());
        return (is_callable([$taxes, 'toArray']) ? $taxes->toArray() : []);
        
        // return $chart_accounts_option;
    } */
    
    function getAccountsByJournal(Request $request)
    {
        $coas = [];
        $coas['debit']   = $this->getCoaDebits( $request->journal );
        $coas['credit']  = $this->getCoaCredits( $request->journal );
        
        return $coas;
    }
    
    function getTaxesByJournal(Request $request)
    {
        $taxes = [];
        $taxes['debit']  = $this->getTaxDebits( $request->journal );
        $taxes['credit'] = $this->getTaxCredits( $request->journal );
        
        return $taxes;
    }
    
    function getAccountsTaxes(Request $request)
    {
        $arr = [];
        $arr['coas']  = $this->getAccountsByJournal( $request );
        $arr['taxes'] = $this->getTaxesByJournal( $request );
        
        return $arr;
    }
    
    function customtFormValidation($request)
    {
        // return $request->all(); 
        $validation = ['valid' => false, 'errors' => []];
        
        if(empty($request->vendor_id)) {
            $validation['errors'][] = ['field' => 'vendor_id', 'message' => 'Vendor is required'];
        }
        if(empty($request->date)) {
            $validation['errors'][] = ['field' => 'date', 'message' => 'Date is required'];
        }
        if(empty($request->invoice_number)) {
            $validation['errors'][] = ['field' => 'invoice_number', 'message' => 'Invoice # is required'];
        }
        if(empty($request->terms)) {
            $validation['errors'][] = ['field' => 'terms', 'message' => 'Term is required'];
        }
        if(empty($request->amount)) {
            $validation['errors'][] = ['field' => 'amount', 'message' => 'Amount is required'];
        }
        if(empty($request->journal_entry)) {
            $validation['errors'][] = ['field' => 'journal_entry', 'message' => 'Journal Entry is required'];
        }
        
        if($request->journal_entry) {
            
            $is_valid_voucher1 = true;
            if( empty($request->vouchers[0]['chart_account_id']) || empty($request->vouchers[0]['code']) 
                || empty($request->vouchers[0]['debit']) || empty($request->vouchers[0]['ref_number']) ) {
                $is_valid_voucher1 = false;
                $validation['errors'][] = ['field' => 'voucher1', 'message' => 'Account Debit Row is missing some data. Please review.'];
            }
            
            $is_valid_voucher2 = true;
            if($request->amount_subj_to_vat > 0) {
                if( empty($request->vouchers[1]['chart_account_id']) || empty($request->vouchers[1]['code']) 
                    || empty($request->vouchers[1]['debit']) || empty($request->vouchers[1]['ref_number']) ) {
                    $is_valid_voucher2 = false;
                    $validation['errors'][] = ['field' => 'voucher2', 'message' => 'Other Account Debit Row is missing some data. Please review.'];
                }
            }
            
            $is_valid_voucher3 = true;
            if( $request->vouchers[2]['tax_id'] ) {
                if( empty($request->vouchers[2]['tax_id']) || empty($request->vouchers[2]['rate']) 
                    || empty($request->vouchers[2]['debit']) || empty($request->vouchers[2]['ref_number']) ) {
                    $is_valid_voucher3 = false;
                    $validation['errors'][] = ['field' => 'voucher3', 'message' => 'Tax Debit Row is missing some data. Please review.'];
                }
            }
            
            $is_valid_voucher4 = true;
            if( empty($request->vouchers[3]['chart_account_id']) || empty($request->vouchers[3]['code']) 
                || empty($request->vouchers[3]['credit']) || empty($request->vouchers[3]['ref_number']) ) {
                $is_valid_voucher4 = false;
                $validation['errors'][] = ['field' => 'voucher4', 'message' => 'Account Credit Row is missing some data. Please review.'];
            }
        
            // This row is not required so only check for missing data if the row has a tax selected
            $is_valid_voucher5 = true;
            if( $request->vouchers[4]['tax_id'] ) {
                if( empty($request->vouchers[4]['tax_id']) || empty($request->vouchers[4]['rate']) 
                    || empty($request->vouchers[4]['credit']) || empty($request->vouchers[4]['ref_number']) ) {
                    $is_valid_voucher5 = false;
                    $validation['errors'][] = ['field' => 'voucher5', 'message' => 'Tax Credit Row is missing some data. Please review.'];
                }
            }
            
        }
        
        /* This validation applies to Add Form only */
        if($request->isMethod('post') && $request->journal_entry && $request->terms == 'cod') {
            if( empty($request->cpvvouchers[0]['chart_account_id']) || empty($request->cpvvouchers[0]['code']) 
                || empty($request->cpvvouchers[0]['debit']) || empty($request->cpvvouchers[0]['ref_number']) ) {
                $validation['errors'][] = ['field' => 'cpvvoucher1', 'message' => 'CPV Account Debit Row is missing some data. Please review.'];
            }
            
            if( empty($request->cpvvouchers[1]['chart_account_id']) || empty($request->cpvvouchers[1]['code']) 
                || empty($request->cpvvouchers[1]['credit']) || empty($request->cpvvouchers[1]['ref_number']) ) {
                $validation['errors'][] = ['field' => 'cpvvoucher2', 'message' => 'CPV Account Credit Row is missing some data. Please review.'];
            }
        }
        
        if(count($validation['errors']) == 0)
            $validation['valid'] = true;
        
        return $validation;
    }
    
    function addFormValidation(Request $request) 
    {
        $validation = $this->customtFormValidation($request);
        
        return $validation;
    }
    
    function accountDetails()
    {
        $chart_accounts = ChartAccount::orderBy('name', 'asc')->lists('name', 'id');
        $chart_accounts_option = (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
        
        $chart_accounts = ChartAccount::orderBy('name', 'asc')
            ->where('tax_id', '!=', DB::raw(0))
            ->lists('name', 'id');
        $chart_account_taxes_option = (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
        
        $options = [
            'assetcoa_debit', 'assetcoa_credit', 'assettax_debit', 'assettax_credit',
            'purchasescoa_debit', 'purchasescoa_credit', 'purchasestax_debit', 'purchasestax_credit',
            'expensescoa_debit', 'expensescoa_credit', 'expensestax_debit', 'expensestax_credit',
        ];
        $supplierinvoicecoas = new \stdClass;
        foreach($options as $option_name) {
            $supplierinvoicecoas->{ $option_name } = $this->getCoaOption($option_name);
        }
        
        // dd( $supplierinvoicecoas );
        
        return view('supplier-invoice.account-details', 
            compact(
                'supplierinvoicecoas', 'chart_accounts_option', 'chart_account_taxes_option'
            )
        );
    }
    
    function accountDetailsUpdate(Request $request)
    {
        $options = [
            'assetcoa_debit', 'assetcoa_credit', 'assettax_debit', 'assettax_credit',
            'purchasescoa_debit', 'purchasescoa_credit', 'purchasestax_debit', 'purchasestax_credit',
            'expensescoa_debit', 'expensescoa_credit', 'expensestax_debit', 'expensestax_credit',
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
        
        return redirect('supplier-invoice/account-details');
    }
    
    function getCoaOption( $option_name )
    {
        $option = Option::where('name', "supplierinvoice_$option_name")->first();
        $coas = ($option && $option->value)? $option->value: [];
        
        if( gettype($coas) == 'string' ) 
            $coas = explode(',', $coas);
        
        return $coas;
    }
    
    function saveCoaOption( $option_name, $option_value )
    {
        $option = Option::firstOrNew(['name' => "supplierinvoice_$option_name"]);
        $option->value = implode(',', $option_value);
        $option->save();
    }
    
}
