<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\ChartAccountType;
use App\SubAccountType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;

class ChartAccountTypeController extends Controller
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
        $chartaccounttype = ChartAccountType::paginate(15);
		
		// foreach($chartaccounttype as &$acct_type) {
			// SubAccountType:where('account_type_id', '=', $acct_type)
			// $acct_type->sub_account_types = 
		// }

        return view('chart-account-type.index', compact('chartaccounttype'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        return view('chart-account-type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function store(Request $request)
    {
        
        ChartAccountType::create($request->all());

        Session::flash('flash_message', 'Chart Account Type added!');

        return redirect('chart-account-type');
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
        $chartaccounttype = ChartAccountType::findOrFail($id);

        return view('chart-account-type.show', compact('chartaccounttype'));
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
        $chartaccounttype = ChartAccountType::findOrFail($id);

        return view('chart-account-type.edit', compact('chartaccounttype'));
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
        
        $chartaccounttype = ChartAccountType::findOrFail($id);
        $chartaccounttype->update($request->all());

        Session::flash('flash_message', 'Chart Account Type updated!');

        return redirect('chart-account-type');
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
        ChartAccountType::destroy($id);

        Session::flash('flash_message', 'Chart Account Type deleted!');

        return redirect('chart-account-type');
    }
}
