<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\BillingInvoice;
use App\CreditInvoice;
use App\Customer;
use App\OpenInvoice;
use App\WaterRefilling;

class CustomerCRUDController extends Controller
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
        /*$customers = Customer::orderBy('individual','ASC')->paginate(15);
        return view('customer.index',compact('customers'))
            ->with('i', ($request->input('page', 1) - 1) * 5);*/

        $customers = Customer::orderBy('company_name', 'ASC')
        ->orderBy('first_name', 'ASC')
        ->paginate(15);
        
        if($customers) {
            foreach($customers as &$item) {
                $item->fullname = ($item->individual)
                    ? trim("{$item->first_name} {$item->middle_name} {$item->last_name}")
                    : trim("{$item->company_name}");
            }
        }

        return view('customer.index', compact('customers'))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customer.create');
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
            'individual' => 'required',
            'business_name' => 'required',
            'business_address' => 'required',
            'city' => 'required',
            'country' => 'required',
            'tin' => 'required',
            'phone_no' => 'required',
        ]);

        Customer::create($request->all());
        return redirect()->route('customer.index')
                        ->with('success','Customer created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        
        return view('customer.show',compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customer.edit',compact('customer'));
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
            'individual' => 'required',
            'business_name' => 'required',
            'business_address' => 'required',
            'city' => 'required',
            'country' => 'required',
            'tin' => 'required',
            'phone_no' => 'required',
        ]);

        Customer::find($id)->update($request->all());
        return redirect()->route('customer.index')
                        ->with('success','Customer updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $check_id = WaterRefilling::where('customer_id', $id)->first();
        if(!empty($check_id)){
            return redirect()->route('customer.index')
            ->with('warning','Deleting customer with WRM transactions are not allowed...');
        }else{
            Customer::find($id)->delete();
            return redirect()->route('customer.index')
            ->with('success','Customer deleted successfully');
        }
    }
    
    public function getCreditInvoices(Request $request)
    {
        return (new CreditInvoice)->getInvoicesByCustomerId($request->customer_id);
    }
    
    public function getBillingInvoices(Request $request)
    {
        return (new BillingInvoice)->getInvoicesByCustomerId($request->customer_id);
    }
    
    public function getOpenInvoices(Request $request)
    {
        return (new OpenInvoice)->getInvoicesByCustomerId($request->customer_id);
    }
}