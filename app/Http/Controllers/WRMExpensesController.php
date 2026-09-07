<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Vendor;
use App\SupplierInvoice;
use App\WRMExpenses;
use Carbon\Carbon;
use DateTime;
use DB;
//use Session;

class WRMExpensesController extends Controller
{

    public function index(Request $request){

    }

    public function create(Request $request){
        // This will generate the latest entry_no from database
        $water_refillings = WRMExpenses::orderBy('id', 'DESC')
        ->where('remarks', '=', '')
        ->first();
        return view('wrmexpenses.create',compact('water_refillings'));
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
        WRMExpenses::create($request->all());

        return redirect()->route('wrmexpenses.create')
                        ->with('success','Expenses has been successfully added');

    }

    public function show($id){

        $vendor_id = 0;

        $vendor_expenses = WRMExpenses::findOrFail($id);

        $vendor_id = $vendor_expenses->vendor_id;

        $vendor_info = Vendor::select('*')
        ->where('id', '=', $vendor_id)
        ->first();

        return view('wrmexpenses.show', compact('vendor_expenses','vendor_info'));

    }
    
    public function edit($id){

        $vendor_id = 0;

        $vendor_expenses = WRMExpenses::findOrFail($id);

        $vendor_id = $vendor_expenses->vendor_id;

        $vendor_info = Vendor::select('*')
        ->where('id', '=', $vendor_id)
        ->first();

        return view('wrmexpenses.edit', compact('vendor_expenses','vendor_info'));
    }

    public function update($id, Request $request){
        $vendor_expenses = WRMExpenses::findOrFail($id);
        $vendor_expenses->update($request->all());

        return redirect('wrmexpenses/tracks-expenses')
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

    public function trackExpenses(Request $request){

        $search_vendor_name = $request->search_vendor_name;
        $start = $request->date_from;
        $end = $request->date_to;

        if(!empty($search_vendor_name)){

            $expenses_reports = DB::table('w_r_m_expenses')
            ->leftjoin('vendors', 'vendors.id', '=', 'w_r_m_expenses.vendor_id')
            ->select('vendors.*', 'w_r_m_expenses.*')
            ->where(DB::raw('TRIM(CONCAT(vendors.first_name, " ", vendors.middle_name, " ", vendors.last_name,"", vendors.company_name))'), 'LIKE', "%$search_vendor_name%")
            ->orderBy('w_r_m_expenses.date', 'ASC')
            ->paginate(10);

            return view('wrmexpenses.tracks-expenses', compact(
               'expenses_reports',
               'start',
               'end',
               'search_vendor_name'
               ))
          ->with('i', ($request->input('page', 1) - 1) * 10);
        
        }elseif(!empty($start)){

            $expenses_reports = DB::table('w_r_m_expenses')
            ->leftjoin('vendors', 'vendors.id', '=', 'w_r_m_expenses.vendor_id')
            ->select('vendors.*', 'w_r_m_expenses.*')
            ->whereBetween('w_r_m_expenses.date', array($start, $end))
            ->orderBy('vendors.last_name','vendors.company_name', 'ASC')
            ->paginate(10);

            return view('wrmexpenses.tracks-expenses', compact(
                'expenses_reports',
                'start',
                'end',
                'search_vendor_name'
               ))
          ->with('i', ($request->input('page', 1) - 1) * 10);

        }else{
            $expenses_reports = DB::table('w_r_m_expenses')
            ->leftjoin('vendors', 'vendors.id', '=', 'w_r_m_expenses.vendor_id')
            ->select('vendors.*', 'w_r_m_expenses.*', 'vendors.id as v_id')
            ->orderBy('vendors.last_name','vendors.company_name', 'ASC')
            ->paginate(10);

            return view('wrmexpenses.tracks-expenses', compact(
                'expenses_reports',
                'start',
                'end',
                'search_vendor_name'
                ))
            ->with('i', ($request->input('page', 1) - 1) * 10);
        }    
    }

    public function getRecordDestroy($id){
        WRMExpenses::find($id)->delete();
            return redirect('wrmexpenses/tracks-expenses')
                ->with('success','Record is successfully deleted');
    }
}