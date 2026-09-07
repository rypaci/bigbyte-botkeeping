<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Company;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;

class CompanyController extends Controller
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
        return redirect('company/info');

        $company = Company::paginate(15);
        return view('company.index', compact('company'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        return view('company.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function store(Request $request)
    {
        /* 
        $this->validate($request, ['last_name' => '128', 'first_name' => '128', 'middle_name' => '128', 'gender' => '32', 'civil_status' => '16', 'zip' => '64', 'tin' => '64', 'branch_code' => '32', 'rdo_code' => '16', 'phone_number' => '32', 'fax' => '32', ]);
         */
         
        Company::create($request->all());

        Session::flash('flash_message', 'Company added!');

        return redirect('company');
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
        $company = Company::findOrFail($id);

        return view('company.show', compact('company'));
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
        $company = Company::findOrFail($id);

        return view('company.edit', compact('company'));
    }

    public function getInfo() 
    {
        $company = Company::info();

        return view('company.info', compact('company'));
    }

    public function saveInfo(Request $request)
    {
        if( $request && !empty($request->id) ) {
            $company = Company::find($request->id);
            $company->update($request->all());
        }
        else
            Company::create($request->all());

        Session::flash('flash_message', 'Company info saved!');

        return redirect('company/info');
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
        /* 
        $this->validate($request, ['last_name' => '128', 'first_name' => '128', 'middle_name' => '128', 'gender' => '32', 'civil_status' => '16', 'zip' => '64', 'tin' => '64', 'branch_code' => '32', 'rdo_code' => '16', 'phone_number' => '32', 'fax' => '32', ]);
         */

        $company = Company::findOrFail($id);
        $company->update($request->all());

        Session::flash('flash_message', 'Company updated!');

        return redirect('company');
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
        Company::destroy($id);

        Session::flash('flash_message', 'Company deleted!');

        return redirect('company');
    }
}
