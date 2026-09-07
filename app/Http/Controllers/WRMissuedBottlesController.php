<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\WaterRefilling;
use App\Customer;
use App\Product;
use Carbon\Carbon;
use DateTime;
use DB;

class WRMissuedBottlesController extends Controller
{
    public function index(Request $request){

        $overall_issued_bottles = DB::table('water_refillings')
        ->leftjoin('customers', 'customers.id', '=', 'water_refillings.customer_id')
        ->select('customers.*', 'water_refillings.*', 'water_refillings.id as wrm_id', 'customers.id as cust_id',
        DB::raw('sum(order_qty) as delivered_bots'),
        DB::raw('sum(return_bottle) as returned_bots'))
        ->where('order_qty', '!=' , 0)
        ->orWhere('return_bottle', '!=', 0)
        ->orderBy('customers.last_name', 'ASC')
        ->orderBy('customers.company_name', 'ASC')
        ->groupBy(DB::raw('customers.id'))
        ->get();

        $issued_bottles = DB::table('water_refillings')
        ->leftjoin('customers', 'customers.id', '=', 'water_refillings.customer_id')
        ->select('customers.*', 'water_refillings.*', 'water_refillings.id as wrm_id', 'customers.id as cust_id',
        DB::raw('sum(order_qty) as delivered_bots'),
        DB::raw('sum(return_bottle) as returned_bots'))
        ->where('order_qty', '!=' , 0)
        ->orWhere('return_bottle', '!=', 0)
        ->orderBy('customers.last_name', 'ASC')
        ->orderBy('customers.company_name', 'ASC')
        ->groupBy(DB::raw('customers.id'))
        ->paginate(10);

        return view('wrm-issued-bottles.index',compact('issued_bottles', 'overall_issued_bottles'))
        ->with('i', ($request->input('page', 1) - 1) * 5);;
    }

    public function issuedBottlesDetails(Request $request, $id){

        $customer_info = Customer::find($id);

        $issued_bottles_details = DB::table('water_refillings')
        ->where('customer_id', $id)
        ->orderBy('date', 'ASC')
        ->get();
        // ->paginate(10);
        
        return view('wrm-issued-bottles/issued-bots-details',compact('issued_bottles_details', 'customer_info'));
        // ->with('i', ($request->input('page', 1) - 1) * 5));

    }

    public function searchResults(Request $request){

        $cust_name = $request->cust_name;

        $overall_issued_bottles = DB::table('water_refillings')
        ->leftjoin('customers', 'customers.id', '=', 'water_refillings.customer_id')
        ->select('customers.*', 'water_refillings.*', 'water_refillings.id as wrm_id', 'customers.id as cust_id',
        DB::raw('sum(order_qty) as delivered_bots'),
        DB::raw('sum(return_bottle) as returned_bots'))
        ->where(DB::raw('TRIM(CONCAT(customers.first_name, " ", customers.last_name, " ", customers.company_name))'), 'LIKE', "%$cust_name%")
        ->where('order_qty', '!=' , 0)
        ->orderBy('customers.last_name', 'ASC')
        ->orderBy('customers.company_name', 'ASC')
        ->groupBy(DB::raw('customers.id'))
        ->get();

        $issued_bottles = DB::table('water_refillings')
        ->leftjoin('customers', 'customers.id', '=', 'water_refillings.customer_id')
        ->select('customers.*', 'water_refillings.*', 'water_refillings.id as wrm_id', 'customers.id as cust_id',
        DB::raw('sum(order_qty) as delivered_bots'),
        DB::raw('sum(return_bottle) as returned_bots'))
        ->where(DB::raw('TRIM(CONCAT(customers.first_name, " ", customers.last_name, " ", customers.company_name))'), 'LIKE', "%$cust_name%")
        ->where('order_qty', '!=' , 0)
        ->orderBy('customers.last_name', 'ASC')
        ->orderBy('customers.company_name', 'ASC')
        ->groupBy(DB::raw('customers.id'))
        ->paginate(10);

        return view('wrm-issued-bottles.index',compact('issued_bottles', 'overall_issued_bottles'))
        ->with('i', ($request->input('page', 1) - 1) * 5);;
    }
}
