<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\ChartAccount;
use App\ChartAccountType;
use App\SubAccountType;
use App\Tax;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use DB;

class ChartAccountController extends Controller
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
        $debit_column  = "(SELECT IFNULL(SUM(debit), 0) FROM vouchers v where ca.id = v.chart_account_id) as debit";
        $credit_column = "(SELECT IFNULL(SUM(credit), 0) FROM vouchers v where ca.id = v.chart_account_id) as credit";
        $num_entries   = "(SELECT count(id) FROM vouchers v where ca.id = v.chart_account_id) as num_entries";
        
        $chartaccount  = ChartAccount::from('chart_accounts as ca')
            ->select(DB::raw("ca.*, $debit_column, $credit_column, $num_entries"))
            ->orderBy('account_type_id', 'ASC')
            ->orderBy('sub_account_type_id', 'ASC')
            ->orderBy('code', 'ASC')
            // ->toSql();
            ->paginate(20);
        
        // dd( $chartaccount );
        
        $last_account_type_id = $last_sub_account_type_id = '';
        $accounts = array();
        
        foreach($chartaccount as $accnt) {
            // add main account type in the collection
            if($last_account_type_id != $accnt->account_type->id) {
                $accounts[] = (object) [
                    'id'    => 'MA-' . $accnt->account_type->id,
                    'level' => '0',
                    'code'  => "{$accnt->account_type->min} - {$accnt->account_type->max}",
                    'name'  => $accnt->account_type->name,
                    'num_entries'  => $accnt->num_entries,
                    'current_balance'  => '',
                ];
                $last_account_type_id = $accnt->account_type->id;
            }
            
            // add sub account type in the collection
            if($last_sub_account_type_id != "{$accnt->sub_account_type_id}") {
                $accounts[] = (object) [
                    'id'    => 'SA-' . $accnt->account_type->id . '-' . $accnt->sub_account_type_id,
                    'level' => '-1',
                    'code'  => "",
                    'name'  => isset($accnt->sub_account_type) ? $accnt->sub_account_type->name : '&nbsp;',
                    'num_entries'  => $accnt->num_entries,
                    'current_balance'  => '',
                ];
                $last_sub_account_type_id = $accnt->sub_account_type_id;
            }
            
            $accounts[] = (object) [
                'id'    => $accnt->id,
                'level' => $accnt->level,
                'code'  => $accnt->code,
                'name'  => $accnt->name,
                'num_entries'  => $accnt->num_entries,
                'current_balance'  => $accnt->debit - $accnt->credit,
            ];
        }
        
        // dd($chartaccount->all());
        // dd($accounts);
        return view('chart-account.index', compact('chartaccount', 'accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        $accounttypes = ChartAccountType::select('id', DB::raw('CONCAT(min, "-", max, " ", name) as option_name'))->orderBy('id', 'asc')->lists('option_name', 'id');
        $accounttypes_option = ['0' => 'Select'] + (is_callable([$accounttypes, 'toArray']) ? $accounttypes->toArray() : []);
        
        $taxes = Tax::select('id', DB::raw('CONCAT(name, " (", rate, " -- ", type, ")") as option_name'))
            ->where(DB::raw('(SELECT count(id) FROM chart_accounts WHERE tax_id = taxes.id)'), '=', DB::raw('0'))
            ->orderBy('id', 'asc')
            ->lists('option_name', 'id');
        
        $taxes_option = ['0' => 'Select'] + (is_callable([$taxes, 'toArray']) ? $taxes->toArray() : []);
        
        return view('chart-account.create', compact('accounttypes_option', 'taxes_option'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function store(Request $request)
    {
        ChartAccount::create($request->all());

        Session::flash('flash_message', 'Chart of Account added!');

        return redirect('chart-account');
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
        $chartaccount = ChartAccount::findOrFail($id);

        return view('chart-account.show', compact('chartaccount'));
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
        $chartaccount = ChartAccount::findOrFail($id);
        
        $accounttypes = ChartAccountType::select('id', DB::raw('CONCAT(min, "-", max, " ", name) as option_name'))->orderBy('id', 'asc')->lists('option_name', 'id');
        $accounttypes_option = ['0' => 'Select'] + (is_callable([$accounttypes, 'toArray']) ? $accounttypes->toArray() : []);
        
        $subaccounttypes = SubAccountType::select('id', 'name')->where('account_type_id', '=', $chartaccount->account_type_id)->orderBy('id', 'asc')->lists('name', 'id');
        $subaccounttypes_option = ['0' => 'Select'] + (is_callable([$subaccounttypes, 'toArray']) ? $subaccounttypes->toArray() : []);
        
        $subaccounts = ChartAccount::select('id', 'name')
            ->where('account_type_id', '=', $chartaccount->account_type_id)
            ->where('level', '=', $chartaccount->level-1)
            ->orderBy('id', 'asc')->lists('name', 'id');
        $subaccounts_option = (is_callable([$subaccounts, 'toArray']) ? $subaccounts->toArray() : []);
        // dd($subaccounts_option);
        
        /* For Taxes Dropdown */
        $taxes = Tax::select('id', DB::raw('CONCAT(name, " (", rate, " -- ", type, ")") as option_name'))
            ->where(DB::raw('(SELECT count(id) FROM chart_accounts WHERE tax_id = taxes.id)'), '=', DB::raw('0'))
            ->orWhere('id', '=', $chartaccount->tax_id)
            ->orderBy('id', 'asc')
            ->lists('option_name', 'id');
        
        $taxes_option = ['0' => 'Select'] + (is_callable([$taxes, 'toArray']) ? $taxes->toArray() : []);
        
        return view('chart-account.edit', 
            compact(
                'chartaccount', 'accounttypes_option', 'subaccounts_option', 'subaccounttypes_option',
                'taxes_option'
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
        $chartaccount = ChartAccount::findOrFail($id);
        $chartaccount->update($request->all());

        Session::flash('flash_message', 'Chart of Account updated!');

        return redirect('chart-account');
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
        ChartAccount::destroy($id);

        Session::flash('flash_message', 'Chart of Account deleted!');

        return redirect('chart-account');
    }
    
    public function getCodeAndSubAccounts(Request $request)
    {
        $sub_accounts = $this->getSubAccounts($request);
        $sub_accounts_length = count($sub_accounts);
        
        while($request->level > 1 && $sub_accounts_length == 0) {
            $request->level -= 1;
            $sub_accounts = $this->getSubAccounts($request);
            $sub_accounts_length = count($sub_accounts);
        }
        
        $level = $request->level;
        if($sub_accounts_length) {
            $request->parent_account_id = key( $sub_accounts );
        }
        $parent_account_id = $request->parent_account_id;
        
        /* Get the sub_account_type_id */
        
        // echo "level{$request->level} sub_accounts_length=$sub_accounts_length<br>";
        
        /* code */
        $new_code =  $this->getCode($request);
        
        $sub_account_types = $this->getSubAccountTypes($request);
        return compact('new_code', 'sub_accounts', 'sub_accounts_length', 'level', 'sub_account_types');
    }
    
    function getStartingCodeFormat( $request )
    {
        $accounttype = ChartAccountType::find( $request->id );
        if( is_null($accounttype) ) return [];
        
        $min = $accounttype->min;
        return str_replace("0", "_", $accounttype->min);
    }
    
    function getCode(Request $request )
    {
        if($request->parent_account_id) {
            $account = ChartAccount::find( $request->parent_account_id );
            $addon_formats = ['2' => '-__', '3' => '-___', '4' => '-____'];
            $code_format = $account->code . $addon_formats[ $account->level+1 ];
        }
        else {
            $prefix = $this->getStartingCodeFormat($request);
            $formats = ['1' => '', '2' => '-__', '3' => '-__-___', '4' => '-__-___-____'];
            $code_format = $prefix . $formats[ $request->level ];
        }
        // dd( $code_format );
        
        // echo "account_type_id={$request->id} level={$request->level} code_format=$code_format<br>";
        $parent_account_id = !empty($request->parent_account_id) ? $request->parent_account_id : 0;
        $account = ChartAccount::select('code')
            ->where('account_type_id', '=', $request->id)
            ->where('parent_account_id', '=', $parent_account_id)
            ->where('level', '=', $request->level)
            ->where('code', 'like', "$code_format")
            ->orderBy('code', 'desc')->limit(1)
            ->first();
        
        if(!$account) {
            $new_code = str_replace("_", "0", $code_format);
            // dd($new_code);
        }
        elseif($account->code) {
            $new_code = $account->code;
            // dd($new_code);
            if(strpos($new_code, '-') !== false) {
                $start_number = substr($new_code, 0, strrpos($new_code, '-') + 1);
                $end_number = substr($new_code, strrpos($new_code, '-') + 1);
                
                $formatted_end_number = str_pad(($end_number + 1), strlen($end_number), '0', STR_PAD_LEFT);
                
                $new_code = "$start_number$formatted_end_number";
            }
            else {
                $new_code = str_pad(($new_code + 1), strlen($new_code), '0', STR_PAD_LEFT);
            }
            // dd($new_code);
        }
        else $new_code = '';
        
        // echo "new_code=$new_code<br>";
        if(!empty($request->to_json) && $request->to_json) return compact('new_code');
        return $new_code;
    }
    
    function getCode_old( $request )
    {
        $accounttype = ChartAccountType::find( $request->id );
        if( is_null($accounttype) ) return [];
        
        // $code_format = '';
        
        $min = $accounttype->min;
        $code_format = str_replace("0", "_", $accounttype->min);
        $level1_code_format = $code_format;
        
        if($request->level > 1) {
            $code_format .= "-__";
            $level2_code_format = $code_format;
        }
        
        if($request->level > 2) {
            $code_format .= "-___";
            $level3_code_format = $code_format;
        }
        
        if($request->level > 3) {
            $code_format .= "-____";
            $level4_code_format = $code_format;
        }
        
        /* SELECT code FROm chart_accounts WHERE account_type_id = $account_type_id AND level = $level AND code like '$code_format' ORDER BY code DESC LIMIT 1 */
        $account = ChartAccount::select('code')
            ->where('account_type_id', '=', $request->id)
            ->where('level', '=', $request->level)
            ->where('code', 'like', "$code_format")
            ->orderBy('code', 'desc')->limit(1)
            ->first();
            
        $max_code = $account && $account->code ? $account->code+1 : $min;
        
        return $max_code;
    }
    
    function getSubAccounts( $request )
    {
        if($request->level == 1) {
            return [];
        }
        else {
            /* SELECT id, name FROM chart_accounts WHERE */
            $accounts = ChartAccount::select('id', 'name')
                ->where('account_type_id', '=', $request->id)
                ->where('sub_account_type_id', '=', $request->sub_account_type_id)
                ->where('level', '=', $request->level-1)
                ->orderBy('id', 'asc')->lists('name', 'id');
                
            $accounts_option = (is_callable([$accounts, 'toArray']) ? $accounts->toArray() : []);
            // dd($accounts);
            return $accounts_option;
        }
        
        return [];
    }
    
    function getSubAccountTypes( $request )
    {
        /* SELECT id, name FROM chart_accounts WHERE */
        $subaccounttypes = SubAccountType::select('id', 'name')
            ->where('account_type_id', '=', $request->id)
            ->orderBy('id', 'asc')->lists('name', 'id');
            
        $sub_account_types_option = (is_callable([$subaccounttypes, 'toArray']) ? $subaccounttypes->toArray() : []);
        // dd($accounts);
        return $sub_account_types_option;
    }
    
}
