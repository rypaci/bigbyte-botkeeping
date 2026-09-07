<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Product;
use App\Inventory;
use DB;

class InventoryController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(Request $request){

        $startDate = $request->date_from;
        $endDate = $request->date_to;

        $search_products = $request->search_products;

        $total_added_qty = "(SELECT SUM(added_qty) FROM inventories inv where prod.id = inv.pro_id) as total_added_qty";
        $sold_qty  = "(SELECT SUM(container_qty + others_qty) FROM water_refillings wr where prod.id = wr.pro_id) as sold_qty";
        
        $invData  = Product::from('products as prod')
        ->leftjoin('vendors', 'vendors.id', '=', 'prod.vendor_id')
        ->select(DB::raw("prod.*, vendors.*, prod.id as pro_id, $sold_qty, $total_added_qty"))
        ->where('prod.inventory_status', '=', 'Active')
        ->orderBy('prod.name', 'ASC')
        ->orderBy('prod.sr_priority', 'ASC')
        ->paginate(10);

        $overAlltotal  = Product::from('products as prod')
        ->leftjoin('vendors', 'vendors.id', '=', 'prod.vendor_id')
        ->select(DB::raw("prod.*, vendors.*, prod.id as pro_id, $sold_qty, $total_added_qty"))
        ->where('prod.inventory_status', '=', 'Active')
        ->orderBy('prod.name', 'ASC')
        ->orderBy('prod.sr_priority', 'ASC')
        ->get();

        $total_cost_price = 0;
        $total_selling_price = 0;
        $total_profit = 0;
        $overall_total_profit = 0;

        foreach($overAlltotal as $value){

            $total_cost_price = $total_cost_price + $value->cost_price;
            $total_selling_price = $total_selling_price + $value->price;
            $total_profit = $total_profit + ($value->price - $value->cost_price);
            $overall_total_profit = $overall_total_profit + ($value->sold_qty * ($value->price - $value->cost_price));
        }
        // dd($overall_total_profit);
        return view('inventory.index', compact(
            'invData',
            // 'inventory_status',
            'search_products',
            'total_cost_price',
            'total_selling_price',
            'total_profit',
            'overall_total_profit',
            'startDate',
            'endDate'
        ));
    }

    public function create(Request $request){
        // This will generate the latest entry_no from database
        // $water_refillings = WRMExpenses::orderBy('id', 'DESC')
        // ->where('remarks', '=', '')
        // ->first();
        // return view('inventory.create',compact('water_refillings'));
        return view('inventory.create');
    }

    public function srPriority(Request $request){
        // foreach ($request->invrow as $rowdata) {

        //     $sr_priority = $rowdata['sr_priority'];

        //     if(!empty($sr_priority)){
        //         Product::where('id',$pro_id)->update(
        //             array(
        //                 'sr_priority' => $rowdata['sr_priority']
        //             )
        //         );
        //     }
        // }

        $stock_threshold = $request->stock_threshold;

        if(is_numeric($stock_threshold)){

            $pro_id = $request->pro_id;
            $update = Product::find($pro_id);
            $update->sr_priority = $request->sr_priority;
            $update->stock_threshold = $request->stock_threshold;
            $update->save();
    
            return redirect()->route('inventory.index')
            ->with('success','Stock reduction priority or Stock threshold is successfully updated');

        }else{
            
            return redirect()->route('inventory.index')
            ->with('warning','Stock threshold inputted is not a number. Please input in number only.');
        }

    }

    public function store(Request $request){

    }

    public function show($id){

    }
    
    // public function edit($id){

    //     $vendor_id = 0;

    //     $vendor_expenses = WRMExpenses::findOrFail($id);

    //     $vendor_id = $vendor_expenses->vendor_id;

    //     $vendor_info = Vendor::select('*')
    //     ->where('id', '=', $vendor_id)
    //     ->first();

    //     return view('wrmexpenses.edit', compact('vendor_expenses','vendor_info'));
    // }

    // public function status($id){
    //     $paid = Inventory::find($pro_id)->update([
    //         'status' => 'Inactive']
    //     );
    //     return redirect('water-refilling-monitoring/reports')
    //     ->with('success', 'Paying balance is done.'); 
    // }

    public function searchFilter(Request $request){

        $startDate = $request->date_from;
        $endDate = $request->date_to;

        $search_products = $request->search_products;
        // dd($inventory_status);

        $total_added_qty = "(SELECT SUM(added_qty) FROM inventories inv where prod.id = inv.pro_id) as total_added_qty";
        $sold_qty  = "(SELECT SUM(container_qty + others_qty) FROM water_refillings wr where prod.id = wr.pro_id) as sold_qty";

        $total_cost_price = 0;
        $total_selling_price = 0;
        $total_profit = 0;
        $overall_total_profit = 0;

        if(!empty($search_products)){
            
            $invData  = Product::from('products as prod')
            ->select(DB::raw("prod.*, prod.id as pro_id, $sold_qty, $total_added_qty"))
            ->where('prod.name', 'LIKE', "%$search_products%")
            ->where('prod.inventory_status', '=', "Active")
            ->orderBy('prod.name', 'ASC')
            ->paginate(10);

            $overAlltotal  = Product::from('products as prod')
            ->select(DB::raw("prod.*, prod.id as pro_id, $sold_qty, $total_added_qty"))
            ->where('prod.name', 'LIKE', "%$search_products%")
            ->where('prod.inventory_status', '=', "Active")
            ->orderBy('prod.name', 'ASC')
            ->get();
    
            foreach($overAlltotal as $value){
    
                $total_cost_price = $total_cost_price + $value->cost_price;
                $total_selling_price = $total_selling_price + $value->price;
                $total_profit = $total_profit + ($value->price - $value->cost_price);
                $overall_total_profit = $overall_total_profit + ($value->sold_qty * ($value->price - $value->cost_price));
            }

           return view('inventory.index', compact(
                'invData',
                'search_products',
                'total_cost_price',
                'total_selling_price',
                'total_profit',
                'overall_total_profit',
                'startDate',
                'endDate'
                ))
                ->with('i', ($request->input('page', 1) - 1) * 5);
        }
        // else{

        //     $invData  = Product::from('products as prod')
        //     ->select(DB::raw("prod.*, prod.id as pro_id, $sold_qty, $total_added_qty"))
        //     ->where('prod.inventory_status', '=', "Active")
        //     ->orderBy('prod.name', 'ASC')
        //     ->paginate(10);

        //     $overAlltotal  = Product::from('products as prod')
        //     ->select(DB::raw("prod.*, prod.id as pro_id, $sold_qty, $total_added_qty"))
        //     ->where('prod.inventory_status', '=', "Active")
        //     ->orderBy('prod.name', 'ASC')
        //     ->get();
    
        //     foreach($overAlltotal as $value){
    
        //         $total_cost_price = $total_cost_price + $value->cost_price;
        //         $total_selling_price = $total_selling_price + $value->price;
        //         $total_profit = $total_profit + ($value->price - $value->cost_price);
        //         $overall_total_profit = $overall_total_profit + ($value->sold_qty * ($value->price - $value->cost_price));
        //     }

        //    return view('inventory.index', compact(
        //         'invData',
        //         'search_products',
        //         'total_cost_price',
        //         'total_selling_price',
        //         'total_profit',
        //         'overall_total_profit',
        //         'startDate',
        //         'endDate'
        //         ))
        //         ->with('i', ($request->input('page', 1) - 1) * 5);
        // }
    }

    public function inventoryDateRange(Request $request){

        $startDate = $request->date_from;
        $endDate = $request->date_to;

        $search_products = $request->search_products;
        
        $total_cost_price = 0;
        $total_selling_price = 0;
        $total_profit = 0;
        $overall_total_profit = 0;

        $sold_qty  = "(SELECT SUM(container_qty + others_qty) FROM water_refillings wr where prod.id = wr.pro_id AND wr.date BETWEEN '$startDate' AND '$endDate') as sold_qty";
        $total_added_qty = "(SELECT SUM(added_qty) FROM inventories inv where prod.id = inv.pro_id) as total_added_qty";

        $invData  = Product::from('products as prod')
        ->select(DB::raw("prod.*, prod.id as pro_id, $sold_qty, $total_added_qty"))
        ->where('prod.inventory_status', '=', "Active")
        ->orderBy('prod.name', 'ASC')
        ->paginate(10);

        $overAlltotal  = Product::from('products as prod')
        ->select(DB::raw("prod.*, prod.id as pro_id, $sold_qty, $total_added_qty"))
        ->where('prod.inventory_status', '=', "Active")
        ->orderBy('prod.name', 'ASC')
        ->get();

        foreach($overAlltotal as $value){

            $total_cost_price = $total_cost_price + $value->cost_price;
            $total_selling_price = $total_selling_price + $value->price;
            $total_profit = $total_profit + ($value->price - $value->cost_price);
            $overall_total_profit = $overall_total_profit + ($value->sold_qty * ($value->price - $value->cost_price));
        }

       return view('inventory.index', compact(
            'invData',
            'search_products',
            'total_cost_price',
            'total_selling_price',
            'total_profit',
            'overall_total_profit',
            'startDate',
            'endDate'
            ))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function inventoryDetails(Request $request, $id){
        
        $products = Product::where('id', $id)->first();
        $profit = $products->price - $products->cost_price;

        $wrm_data = DB::table('water_refillings as wrm')
        ->select(DB::raw("pro_id, date, 0 as added_qty, 0 as cost_price, 0 as price, container_qty, others_qty, created_at"))
        ->where('pro_id', $id);

        $inv_data = DB::table('inventories as inv')
        ->leftjoin('products as pro', 'pro.id', '=', 'inv.pro_id')
        ->select(DB::raw("inv.pro_id, inv.date, inv.added_qty, pro.cost_price, pro.price, 0 as container_qty, 0 as others_qty, inv.created_at"))
        ->where('pro_id', $id);

        $inventory_details = $wrm_data->union($inv_data)
        ->orderby('date', 'ASC')
        ->orderby('created_at', 'ASC')
        ->get();

        return view('inventory.inventory-details',compact('inventory_details','products','profit'));
    }

    public function dateRange(Request $request){

        $startDate = $request->date_from;
        $endDate = $request->date_to;
        $id = $request->id;

        $products = Product::where('id', $id)->first();
        $profit = $products->price - $products->cost_price;

        $wrm_data = DB::table('water_refillings as wrm')
        ->select(DB::raw("pro_id, date, 0 as added_qty, 0 as cost_price, 0 as price, container_qty, others_qty, created_at"))
        ->whereBetween('date', array($startDate, $endDate))
        ->where('pro_id', $id);

        $inv_data = DB::table('inventories as inv')
        ->leftjoin('products as pro', 'pro.id', '=', 'inv.pro_id')
        ->select(DB::raw("inv.pro_id, inv.date, inv.added_qty, pro.cost_price, pro.price, 0 as container_qty, 0 as others_qty, inv.created_at"))
        ->whereBetween('date', array($startDate, $endDate))
        ->where('pro_id', $id);

        $inventory_details = $wrm_data->union($inv_data)
        ->orderby('date', 'ASC')
        ->orderby('created_at', 'ASC')
        ->get();

        return view('inventory.inventory-details',compact('inventory_details','products','profit'));
    }

    // function findVendorOnly(Request $request){
    //     if(!empty($request->vendor_id)) {
    //         $vendor = Vendor::select('*', DB::raw("TRIM(IF(individual, CONCAT(first_name, ' ', middle_name, ' ', last_name), company_name)) as name"))
    //             ->find($request->vendor_id);
            
    //         return $vendor? $vendor: [];
    //     }
        
    //     $s = $request->s;
        
    //     if(!$s) return [];
        
    //     $vendor = Vendor::select('*', DB::raw('CONCAT(`first_name`," ",`middle_name`," ",`last_name`) as name'))
    //         ->where('individual', '=', 1)
    //         ->where(DB::raw('TRIM(CONCAT(first_name, " ", middle_name, " ", last_name))'), 'LIKE', "%$s%")
    //         ->first();
        
    //     if(!$vendor) {
    //         $vendor = Vendor::select('*', 'company_name as name')
    //             ->where('individual', '=', 0)
    //             ->where('company_name', 'LIKE', "%$s%")
    //             ->first();
    //     }
    //     return $vendor? $vendor: [];
    // }

    // public function reportsByDetails(Request $request){
    //     $expenses_reports = DB::table('w_r_m_expenses')
    //     ->join('vendors', 'vendors.id', '=', 'w_r_m_expenses.vendor_id')
    //     ->select('vendors.*', 'w_r_m_expenses.*', 'vendors.id as v_id')
    //     ->orderBy('vendors.last_name','vendors.company_name', 'ASC')
    //     ->paginate(10);

    //     return view('wrmexpenses.tracks-expenses', compact('expenses_reports'))
    //     ->with('i', ($request->input('page', 1) - 1) * 10);
    // }

    // public function reportsByVendorNameAndDateRange(Request $request){

    //     $search_vendor_name = $request->search_vendor_name;

    //     if(!empty($search_vendor_name)){

    //         //$customer_reports = Customer::select('*', DB::raw('(SELECT * FROM water_refillings WHERE customer_id = customers.id) as return_bottle')

    //         $expenses_reports = DB::table('vendors')
    //         ->join('w_r_m_expenses', 'vendors.id', '=', 'w_r_m_expenses.vendor_id')
    //         ->select('vendors.*', 'w_r_m_expenses.*')
    //         ->where(DB::raw('TRIM(CONCAT(vendors.first_name, " ", vendors.middle_name, " ", vendors.last_name,"", vendors.company_name))'), 'LIKE', "%$search_vendor_name%")
    //         ->orderBy('w_r_m_expenses.date', 'ASC')
    //         //->toSql();
    //         ->paginate(10);
    //         //dd( $customer_reports );

    //        return view('wrmexpenses.tracks-expenses', compact('expenses_reports'))
    //       ->with('i', ($request->input('page', 1) - 1) * 10);
        
    //     }else{

    //         $start = $request->date_from;
    //         $end = $request->date_to;

    //         $expenses_reports = DB::table('vendors')
    //         ->join('w_r_m_expenses', 'vendors.id', '=', 'w_r_m_expenses.vendor_id')
    //         ->select('vendors.*', 'w_r_m_expenses.*')
    //         ->whereBetween('w_r_m_expenses.date', array($start, $end))
    //         ->orderBy('vendors.last_name','vendors.company_name', 'ASC')
    //         ->paginate(10);

    //        return view('wrmexpenses.tracks-expenses', compact('expenses_reports'))
    //       ->with('i', ($request->input('page', 1) - 1) * 10);
    //     }    
    // }

    // public function getRecordDestroy($id){
    //     WRMExpenses::find($id)->delete();
    //         return redirect('wrmexpenses/tracks-expenses')
    //             ->with('success','Record is successfully deleted');
    // }
}
