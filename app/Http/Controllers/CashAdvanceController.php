<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;

use App\Employee;
use App\Cashadvance;
use DateTime;
use DB;

class CashAdvanceController extends Controller
{
    //
    public function index(Request $request){

        $emp_name = $request->employee_name;

        $total_current_amount = "(SELECT SUM(ca_amount - ca_deduction) FROM cashadvances ca where emp.id = ca.e_id) as total_current_amount";

        $cash_advances = DB::table('cashadvances as ca')
        ->leftjoin('employees as emp', 'emp.id', '=', 'ca.e_id')
        ->select(DB::raw("ca.*, emp.*, $total_current_amount"))
        ->orderby('emp.employee_name')
        ->orderby('ca.date')
        ->groupby('ca.e_id')
        ->paginate(10);
        return view('cash-advance.index',compact('cash_advances', 'emp_name'))
        ->with('i', ($request->input('page', 1) - 1) * 5);
    }
    
    public function create(){
        $employees = Employee::orderby('employee_name')->get();
        return view('cash-advance.create',compact('employees'));
    }
    
    public function store(Request $request){

        $e_id = $request->e_id;
        if(empty($e_id)){
            return back()->with('warning', 'Please select an Employee to create Cash Advance...');
        }

        $ca_amount = $request->ca_amount;
        if(!is_numeric($ca_amount)){
            return back()->with('warning', 'Please enter CA amount properly...');
        }

        Cashadvance::create($request->all());

        return back()->with('success', 'Employee cash advance is successfully created...');
    }

    public function cashadvanceDetails(Request $request, $id){
        
        $emp_data = Employee::where('id', $id)->first();

        $ca_data = DB::table('cashadvances')
        ->where('e_id', $id)
        ->orderby('date', 'ASC')
        ->get();

        return view('cash-advance.details',compact('ca_data','emp_data'))->with('i');
    }

    public function dateResults(Request $request){

        $start = $request->date_from;
        $end = $request->date_to;
        $id = $request->e_id;

        $emp_data = Employee::where('id', $id)->first();

        $ca_data = DB::table('cashadvances')
        ->whereBetween('date', array($start, $end))
        ->where('e_id', $id)
        ->orderby('date', 'ASC')
        ->get();

        return view('cash-advance.details',compact('ca_data', 'emp_data'))
        ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function edit($id){
        $ca = Cashadvance::find($id);
        $e_id = $ca->e_id;
        $employee = Employee::find($e_id);

        return view('cash-advance.edit',compact('employee','ca'));
    }

    public function update(Request $request){

        Cashadvance::where('id', $request->id)->update([
            'e_id' => $request->e_id,
            'ca_amount' =>  $request->ca_amount,
            'date' => $request->date,
            'ca_description' => $request->ca_description
        ]);

        return back()->with('success', 'Employee cash advance is successfully updated...');
    }

    public function searchResults(Request $request){
        // dd($request->all());

        $emp_name = $request->employee_name;

        $total_current_amount = "(SELECT SUM(ca_amount - ca_deduction) FROM cashadvances ca where emp.id = ca.e_id) as total_current_amount";

        $cash_advances = DB::table('cashadvances as ca')
        ->leftjoin('employees as emp', 'emp.id', '=', 'ca.e_id')
        ->select(DB::raw("ca.*, emp.*, $total_current_amount"))
        ->where('emp.employee_name', 'like', "%$emp_name%")
        ->orderby('emp.employee_name')
        ->orderby('ca.date')
        ->groupby('ca.e_id')
        ->paginate(10);
        return view('cash-advance.index',compact('cash_advances','emp_name'))
        ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function destroy($id)
    {
        Cashadvance::find($id)->delete();
        return back()->with('success', 'Employee cash advance is successfully deleted...');
    }
}
