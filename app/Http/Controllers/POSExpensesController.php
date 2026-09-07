<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;

use App\Vendor;
use App\SupplierInvoice;
use App\POSExpenses;
use Carbon\Carbon;
use DateTime;
use DB;

class POSExpensesController extends Controller
{
    public function index(Request $request){

    }

    public function create(Request $request){
        // This will generate the latest entry_no from database
        $pos_expenses = POSExpenses::orderBy('id', 'DESC')
        ->where('remarks', '=', '')
        ->first();
        return view('pos-expenses.create',compact('pos_expenses'));
    }

    public function store(Request $request){

        $this->validate($request, [
            'vendor_id' => 'required',
            'invoice_no' => '',
            'terms' => 'required',
            'period' =>'',
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'remarks' => ''
        ]);
        POSExpenses::create($request->all());

        return redirect()->route('pos-expenses.create')
                        ->with('success','Expenses has been successfully added');

    }

    public function show($id){

        $vendor_id = 0;

        $vendor_expenses = POSExpenses::findOrFail($id);

        $vendor_id = $vendor_expenses->vendor_id;

        $vendor_info = Vendor::select('*')
        ->where('id', '=', $vendor_id)
        ->first();

        return view('pos-expenses.show', compact('vendor_expenses','vendor_info'));

    }
    
    public function edit($id){

        $vendor_id = 0;

        $vendor_expenses = POSExpenses::findOrFail($id);

        $vendor_id = $vendor_expenses->vendor_id;

        $vendor_info = Vendor::select('*')
        ->where('id', '=', $vendor_id)
        ->first();

        return view('pos-expenses.edit', compact('vendor_expenses','vendor_info'));
    }

    public function update($id, Request $request){
        $vendor_expenses = POSExpenses::findOrFail($id);
        $vendor_expenses->update($request->all());

        return redirect('pos-expenses/tracks-expenses')
            ->with('success','Expenses is successfully updated.');
    }

    function findVendorOnly(Request $request){

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

    public function trackExpenses(Request $request){

        $search_vendor_name = $request->search_vendor_name;
        $start = $request->date_from;
        $end = $request->date_to;

        if(!empty($search_vendor_name)){

            $expenses_reports = DB::table('p_o_s_expenses')
            ->leftjoin('vendors', 'vendors.id', '=', 'p_o_s_expenses.vendor_id')
            ->select('vendors.*', 'p_o_s_expenses.*')
            ->where(DB::raw('TRIM(CONCAT(vendors.first_name, " ", vendors.middle_name, " ", vendors.last_name,"", vendors.company_name))'), 'LIKE', "%$search_vendor_name%")
            ->orderBy('p_o_s_expenses.date', 'ASC')
            ->paginate(10);

            return view('pos-expenses.tracks-expenses', compact(
               'expenses_reports',
               'start',
               'end',
               'search_vendor_name'
               ))
          ->with('i', ($request->input('page', 1) - 1) * 10);
        
        }elseif(!empty($start)){

            $expenses_reports = DB::table('p_o_s_expenses')
            ->leftjoin('vendors', 'vendors.id', '=', 'p_o_s_expenses.vendor_id')
            ->select('vendors.*', 'p_o_s_expenses.*')
            ->whereBetween('p_o_s_expenses.date', array($start, $end))
            ->orderBy('vendors.last_name','vendors.company_name', 'ASC')
            ->paginate(10);

            return view('pos-expenses.tracks-expenses', compact(
                'expenses_reports',
                'start',
                'end',
                'search_vendor_name'
               ))
          ->with('i', ($request->input('page', 1) - 1) * 10);

        }else{
            $expenses_reports = DB::table('p_o_s_expenses')
            ->leftjoin('vendors', 'vendors.id', '=', 'p_o_s_expenses.vendor_id')
            ->select('vendors.*', 'p_o_s_expenses.*', 'vendors.id as v_id')
            ->orderBy('vendors.last_name','vendors.company_name', 'ASC')
            ->paginate(10);

            return view('pos-expenses.tracks-expenses', compact(
                'expenses_reports',
                'start',
                'end',
                'search_vendor_name'
                ))
            ->with('i', ($request->input('page', 1) - 1) * 10);
        }    
    }

    public function getRecordDestroy($id){
        POSExpenses::find($id)->delete();
            return redirect('p_o_s_expenses/tracks-expenses')
                ->with('success','Record is successfully deleted');
    }
}
