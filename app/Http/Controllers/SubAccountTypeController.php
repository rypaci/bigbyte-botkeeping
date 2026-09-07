<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\SubAccountType;
use App\ChartAccountType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;

class SubAccountTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index()
    {
        $subaccounttype = SubAccountType::paginate(15);

        return view('sub-account-type.index', compact('subaccounttype'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        $account_types = ChartAccountType::orderBy('name', 'asc')->lists('name', 'id');
        $account_types_option = ['0' => 'Select'] + (is_callable([$account_types, 'toArray']) ? $account_types->toArray() : []);
		
        return view('sub-account-type.create', compact('account_types_option'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function store(Request $request)
    {
        
        SubAccountType::create($request->all());

        Session::flash('flash_message', 'SubAccountType added!');

        return redirect('sub-account-type');
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
        $subaccounttype = SubAccountType::findOrFail($id);

        return view('sub-account-type.show', compact('subaccounttype'));
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
        $subaccounttype = SubAccountType::findOrFail($id);

        $account_types = ChartAccountType::orderBy('name', 'asc')->lists('name', 'id');
        $account_types_option = ['0' => 'Select'] + (is_callable([$account_types, 'toArray']) ? $account_types->toArray() : []);
		
        return view('sub-account-type.edit', compact('subaccounttype', 'account_types_option'));
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
        
        $subaccounttype = SubAccountType::findOrFail($id);
        $subaccounttype->update($request->all());

        Session::flash('flash_message', 'SubAccountType updated!');

        return redirect('sub-account-type');
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
        SubAccountType::destroy($id);

        Session::flash('flash_message', 'SubAccountType deleted!');

        return redirect('sub-account-type');
    }
}
