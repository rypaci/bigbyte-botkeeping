<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Adjustment;
use App\ChartAccount;
use App\Customer;
use App\Discount;
use App\Option;
use App\Tax;
use App\Vendor;
use App\Voucher;
use Carbon\Carbon;
use DB;
use Session;

class AdjustmentController extends Controller
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
        $adjustment = Adjustment::paginate(15);
        foreach($adjustment as &$adj) {
            $adj->entity_name = '';

            if( $adj->entity_type == 'customer' && $adj->customer ) 
                $adj->entity_name = $adj->customer->fixed_name();
            
            if( $adj->entity_type == 'vendor' && $adj->vendor ) 
                $adj->entity_name = $adj->vendor->fixed_name();
        }
        // dd( $adjustment->toArray() );

        return view('adjustment.index', compact('adjustment'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        $new_adj_number = $this->getAdjNumber();
        $accounts = $this->accounts();
        $discounts = $this->discounts();
        $taxes = $this->taxes();
        // dd( compact('new_adj_number', 'accounts', 'discounts', 'taxes') );

        return view('adjustment.create', compact('new_adj_number', 'accounts', 'discounts', 'taxes'));
    }

    public function accounts()
    {
        $accounts = ChartAccount::get();

        // if we have pulled any chart account then format it to an array with index set to Chart Account ID.
        $accounts_by_id = [];
        if($accounts) {
            foreach($accounts as $acc) {
                $accounts_by_id[ $acc->id ] = (object) $acc->toArray();
            }
        }

        return $accounts_by_id;
    }

    public function discounts()
    {
        $discounts = Discount::get();

        // if we have pulled any discount entry then format it to an array with index set to Discount ID.
        $discounts_by_id = [];
        if($discounts) {
            foreach($discounts as $disc) {
                $discounts_by_id[ $disc->id ] = (object) $disc->toArray();
            }
        }

        return $discounts_by_id;
    }

    public function taxes()
    {
        $taxes = Tax::get();

        // if we have pulled any discount entry then format it to an array with index set to Discount ID.
        $taxes_by_id = [];
        if($taxes) {
            foreach($taxes as $tax) {
                $taxes_by_id[ $tax->id ] = (object) $tax->toArray();
            }
        }

        return $taxes_by_id;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function store(Request $request)
    {
        // dd( $request->all() );
        $ad = Adjustment::create($request->all());
        $ad_alias = Adjustment::moduleAlias();

        if($ad && count($request->vouchers)) {
            $order = 0;
            foreach($request->vouchers as $voucher) {
                $voucher['ref_id']       = $ad->id;
                $voucher['module_alias'] = $ad_alias;
                $voucher['order']        = $order;
                $voucher['debit']        = floatval($voucher['debit']);
                $voucher['credit']       = floatval($voucher['credit']);
                $voucher['date']         = $ad->date;
                // 'chart_account_id', 'tax_id', 'discount_id', 'rate', 'key', 'ref_number'
                // dump($voucher);
                
                Voucher::create($voucher);
                $order++;
            }
        }

        Session::flash('flash_message', 'Adjustment added!');

        return redirect('adjusting');
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
        $adjustment = Adjustment::findOrFail($id);

        if($adjustment) {
            $adjustment->entity_name = '';

            if( $adjustment->entity_type == 'customer' && $adjustment->customer ) 
                $adjustment->entity_name = $adjustment->customer->fixed_name();

            if( $adjustment->entity_type == 'vendor' && $adjustment->vendor ) 
                $adjustment->entity_name = $adjustment->vendor->fixed_name();
        }

        $vouchers = $this->vouchers( $id ) OR [];

        return view('adjustment.show', compact('adjustment', 'vouchers'));
    }

    private function vouchers($id)
    {
        $ad_alias = Adjustment::moduleAlias();

        $vouchers = Voucher::where('module_alias', $ad_alias)
            ->where('ref_id', $id)
            ->orderBy('order', 'ASC') 
            ->get();

        return $vouchers;
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
        $adjustment = Adjustment::findOrFail($id);

        $accounts = $this->accounts();
        $discounts = $this->discounts();
        $taxes = $this->taxes();
        $vouchers = $this->vouchers( $id ) OR [];
        // dd( $taxes );

        return view('adjustment.edit', compact('adjustment', 'accounts', 'discounts', 'taxes', 'vouchers'));
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
        // dd( $request->all() );
        $ad = Adjustment::findOrFail($id);
        $ad->update($request->all());
        $ad_alias = Adjustment::moduleAlias();

        /* Get all old vouchers. Only IDs. 
           We do this because we need to delete the remaining orderitems soon */
        $old_vs = Voucher::select('id')->where('module_alias', $ad_alias)->where('ref_id', $id)->get();
        $v_ids = [];
        foreach($old_vs as $item) {
            $v_ids[] = $item->id;
        }

        if($ad && count($request->vouchers)) {
            $order = 0;
            foreach($request->vouchers as $voucher) {
                $voucher['id'] = (!empty($voucher['id'])) ? $voucher['id'] : 0;

                $voucher['ref_id']       = $ad->id;
                $voucher['module_alias'] = $ad_alias;
                $voucher['order']        = $order;
                $voucher['debit']        = floatval($voucher['debit']);
                $voucher['credit']       = floatval($voucher['credit']);

                Voucher::updateOrCreate(
                    // fields to match in DB
                    ['id' => $voucher['id']],

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
                        'date'             => $ad->date
                    ]
                );

                $order++;

                /* Remove the voucher ID so we can exclude from deleting old vouchers */
                if (($k = array_search($voucher['id'], $v_ids)) !== false) {
                    unset($v_ids[$k]);
                }
            }
        }

        /* Delete old vouchers */
        if(count($v_ids)) {
            Voucher::whereIn('id', $v_ids)->delete();
        }

        Session::flash('flash_message', 'Adjustment updated!');

        return redirect('adjusting');
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
        $ad_alias = Adjustment::moduleAlias();
        Voucher::where('module_alias', $ad_alias)->where('ref_id', $id)->delete();

        Adjustment::destroy($id);

        Session::flash('flash_message', 'Adjustment deleted!');

        return redirect('adjusting');
    }

    public function getAdjNumber()
    {
        $m = Adjustment::select('adj_number')->orderBy('adj_number', 'DESC')->first();
        $last_adj_number = ($m && !empty($m->adj_number)) ? $m->adj_number : 0;
        
        /* fix our number format is we retrieved a cv_number else set to 0 */
        $last_adj_number = ($last_adj_number) ? ltrim($last_adj_number, '0') : 0;
        
        /* Add 1 to generate a new cv_number */
        $new_adj_number = $last_adj_number + 1;
        
        /* format the number to preferred length by adding zeros at the left side */
        $length = 6;
        $new_adj_number = str_pad($new_adj_number, $length, '0', STR_PAD_LEFT);
        
        // dd($last_cv_number);
        return $new_adj_number;
    }
    
    function customtFormValidation(Request $request)
    {
        // return $request->all(); 
        $validation = ['valid' => false, 'errors' => []];
        
        if($request->entity_type == 'customer' && empty($request->entity_id)) {
            $validation['errors'][] = ['field' => 'entity_id', 'message' => 'Customer is required'];
        }
        if($request->entity_type == 'vendor' && empty($request->entity_id)) {
            $validation['errors'][] = ['field' => 'entity_id', 'message' => 'Vendor is required'];
        }
        if(empty($request->date)) {
            $validation['errors'][] = ['field' => 'date', 'message' => 'Date is required'];
        }
        if(empty($request->adj_number)) {
            $validation['errors'][] = ['field' => 'adj_number', 'message' => 'AE No. is required'];
        }

        $has_vouchers = (empty($request->vouchers)) ? false : true;
        if($has_vouchers) {
            foreach( $request->vouchers as $v ) {
                if( empty($v['chart_account_id']) || empty($v['key']) || empty($v['ref_number']) || ($v['tax_id'] && empty($v['rate'])) || ($v['discount_id'] && empty($v['rate'])) ) {
                    $validation['errors'][] = ['field' => $v['key'], 'message' => 'Account Row is missing some data.'];
                }
                elseif( floatval($v['debit']) <= 0 && floatval($v['credit']) <= 0 ) {
                    $validation['errors'][] = ['field' => $v['key'], 'message' => 'Account Row should have an amount.'];
                }
            }
        }
        else {
            $validation['errors'][] = ['field' => 'v_total', 'message' => 'Please add Account Details first.'];
        }

        /* voucher total */
        if(floatval($request->debit_total) != floatval($request->credit_total)) {
            $validation['errors'][] = ['field' => 'v_total', 'message' => 'Account Debit vs Credit is not balanced.'];
        }

        if(count($validation['errors']) == 0)
            $validation['valid'] = true;
        
        return $validation;
    }
    
}
