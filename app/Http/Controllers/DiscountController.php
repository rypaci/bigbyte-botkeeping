<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\ChartAccount;
use App\Discount;
use Carbon\Carbon;
use Session;

class DiscountController extends Controller
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
        $discount = Discount::orderBy('id','DESC')->paginate(15);
        
        return view('discount.index',compact('discount'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $chart_accounts = ChartAccount::orderBy('name', 'asc')->lists('name', 'id');
        $chart_accounts_option = ['' => 'Select'] + (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
        
        return view('discount.create', compact('chart_accounts_option'));
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
            'name' => 'required',
            'rate' => 'required',
        ]);

        Discount::create($request->all());

        Session::flash('flash_message', 'Discount added!');

        return redirect('discount');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $discount = Discount::find($id);
        
        return view('discount.show', compact('discount'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $discount = Discount::find($id);

        $chart_accounts = ChartAccount::orderBy('name', 'asc')->lists('name', 'id');
        $chart_accounts_option = ['' => 'Select'] + (is_callable([$chart_accounts, 'toArray']) ? $chart_accounts->toArray() : []);
        
        return view('discount.edit', compact('discount', 'chart_accounts_option'));
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
            'name' => 'required',
        ]);

        Discount::find($id)->update($request->all());

        Session::flash('flash_message', 'Discount updated!');

        return redirect('discount');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Discount::destroy($id);

        Session::flash('flash_message', 'Discount deleted!');

        return redirect('discount');
    }
}