<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\ChartAccount;
use App\Tax;
use Carbon\Carbon;
use Session;

class TaxController extends Controller
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
        $tax = Tax::paginate(15);

        return view('tax.index', compact('tax'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        $chart_accounts = ChartAccount::orderBy('name', 'asc')->lists('name', 'id');
        $chart_accounts_option = ['' => 'Select'] + (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
        
        return view('tax.create', compact('chart_accounts_option'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function store(Request $request)
    {
        Tax::create($request->all());

        Session::flash('flash_message', 'Tax added!');

        return redirect('tax');
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
        $tax = Tax::findOrFail($id);

        return view('tax.show', compact('tax'));
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
        $tax = Tax::findOrFail($id);

        $chart_accounts = ChartAccount::orderBy('name', 'asc')->lists('name', 'id');
        $chart_accounts_option = ['' => 'Select'] + (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
        
        return view('tax.edit', compact('tax', 'chart_accounts_option'));
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
        $tax = Tax::findOrFail($id);
        $tax->update($request->all());

        Session::flash('flash_message', 'Tax updated!');

        return redirect('tax');
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
        Tax::destroy($id);

        Session::flash('flash_message', 'Tax deleted!');

        return redirect('tax');
    }
}
