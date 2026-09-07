<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Employee;

class EmployeeCRUDController extends Controller
{

    public function __construct()

    {

        $this->middleware('auth');

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $employees = Employee::orderBy('employee_name','ASC')->paginate(5);
        //return view('ItemCRUD.index',compact('items'))
        return view('list-employees.index',compact('employees'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('list-employees.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'employee_name' => 'required',
            'tin_no' => 'required',
            'address' => 'required',
            'birthday' => 'required',
            'sex' => 'required',
            'status' => 'required',
            'dependents' => 'required',
            'daily_rate' => 'required',
            'monthly_rate' => 'required',
            'overtime_rate' => 'required',
            'late_rate' => 'required',
        ]);

        Employee::create($request->all());
        //return redirect()->route('itemCRUD.index')
        return redirect()->route('list-employees.index')
                        ->with('success','Employee created successfully');
    }

    /**
     * Display payroll
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function payroll($id)
    {

        $employee = Employee::find($id);
        return view('list-employees.payroll',compact('employee'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employee = Employee::find($id);
        return view('list-employees.show',compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = Employee::find($id);
        return view('list-employees.edit',compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'employee_name' => 'required',
            'tin_no' => 'required',
            'address' => 'required',
            'birthday' => 'required',
            'sex' => 'required',
            'status' => 'required',
            'dependents' => 'required',
            'daily_rate' => 'required',
            'monthly_rate' => 'required',
            'overtime_rate' => 'required',
            'late_rate' => 'required',
        ]);

        Employee::find($id)->update($request->all());
        return redirect()->route('list-employees.index')
                        ->with('success','Employee updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Employee::find($id)->delete();
        return redirect()->route('list-employees.index')
                        ->with('success','Employee deleted successfully');
    }
}