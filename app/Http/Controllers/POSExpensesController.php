<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;

use App\Vendor;
use App\SupplierInvoice;
use App\POSExpenses;
use App\POSExpenseItem;
use App\Product;
use Carbon\Carbon;
use DateTime;
use DB;

class POSExpensesController extends Controller
{
    public function index(Request $request){

    }

    public function create(Request $request){
        // This will generate the latest entry_no from database
        $pos_expenses = POSExpenses::orderBy('id', 'DESC')
        ->where('remarks', '=', '')
        ->first();
        return view('pos-expenses.create',compact('pos_expenses'));
    }

    public function store(Request $request){

        $this->validate($request, [
            'vendor_id' => 'required',
            'invoice_no' => '',
            'terms' => 'required',
            'period' =>'',
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'remarks' => ''
        ]);

        // Create the expense
        $expense = POSExpenses::create($request->all());

        // Save associated products if provided
        if ($request->has('products_json')) {
            $products = json_decode($request->input('products_json'), true);
            
            if (is_array($products) && !empty($products)) {
                foreach ($products as $product) {
                    POSExpenseItem::create([
                        'pos_expense_id' => $expense->id,
                        'product_id' => $product['product_id'],
                        'quantity' => $product['quantity'],
                        'cost_price' => $product['cost_price'],
                        'total_price' => $product['total_price'],
                        'remarks' => $product['remarks'] ?? null
                    ]);
                }
            }
        }

        return redirect()->route('pos-expenses.create')
                        ->with('success','Expenses has been successfully added');

    }

    public function show($id){

        $vendor_id = 0;

        $vendor_expenses = POSExpenses::findOrFail($id);

        $vendor_id = $vendor_expenses->vendor_id;

        $vendor_info = Vendor::select('*')
        ->where('id', '=', $vendor_id)
        ->first();

        return view('pos-expenses.show', compact('vendor_expenses','vendor_info'));

    }
    
    public function edit($id){

        $vendor_id = 0;

        $vendor_expenses = POSExpenses::findOrFail($id);

        $vendor_id = $vendor_expenses->vendor_id;

        $vendor_info = Vendor::select('*')
        ->where('id', '=', $vendor_id)
        ->first();

        return view('pos-expenses.edit', compact('vendor_expenses','vendor_info'));
    }

    public function update($id, Request $request){
        $vendor_expenses = POSExpenses::findOrFail($id);
        $vendor_expenses->update($request->all());

        return redirect('pos-expenses/tracks-expenses')
            ->with('success','Expenses is successfully updated.');
    }

    function findVendorOnly(Request $request){

        if(!empty($request->vendor_id)) {
            $vendor = Vendor::select('*', DB::raw("TRIM(IF(individual, CONCAT(first_name, ' ', middle_name, ' ', last_name), company_name)) as name"))
                ->find($request->vendor_id);
            
            return $vendor? $this->formatVendorResponse($vendor): [];
        }
        
        $s = $request->s;
        
        if(!$s) return [];
        
        $vendor = Vendor::select('*', DB::raw('CONCAT(`first_name`," ",`middle_name`," ",`last_name`) as name'))
            ->where('individual', '=', 1)
            ->where(DB::raw('TRIM(CONCAT(first_name, " ", middle_name, " ", last_name))'), 'LIKE', "%$s%")
            ->first();
        
        if(!$vendor) {
            $vendor = Vendor::select('*', 'company_name as name')
                ->where('individual', '=', 0)
                ->where('company_name', 'LIKE', "%$s%")
                ->first();
        }
        return $vendor? $this->formatVendorResponse($vendor): [];
    }

    /**
     * Map DB column names to the keys the Vendor Info table expects (e.g. phone_number -> phone).
     */
    private function formatVendorResponse($vendor)
    {
        $data = $vendor->toArray();
        $data['phone'] = $data['phone_number'] ?? '';

        return $data;
    }

    function findVendors(Request $request)
    {
        $s = $request->term ?? $request->s;
        
        if(!$s) return response()->json([]);
        
        $vendors = Vendor::select('*', DB::raw("TRIM(IF(individual, CONCAT(first_name, ' ', middle_name, ' ', last_name), company_name)) as name"))
            ->where(function($query) use ($s){
                $query->where('first_name', 'LIKE', "$s%")
                    ->orWhere('middle_name', 'LIKE', "$s%")
                    ->orWhere('last_name', 'LIKE', "$s%");
            })
            ->orWhere('company_name', 'LIKE', "$s%")
            ->limit(10)
            ->get();
        
        if(!$vendors || $vendors->isEmpty()) return response()->json([]);
        
        $response = [];
        foreach($vendors as $v){
            $response[] = [ 
                'id' => $v->id, 
                'label' => $v->name, 
                'value' => $v->name 
            ];
        }
        
        return response()->json($response);
    }

    /**
     * Find products for autocomplete
     */
    public function findProducts(Request $request)
    {
        $term = $request->term ?? $request->s;
        
        if(!$term) return response()->json([]);
        
        $products = Product::where('name', 'LIKE', "%$term%")
            ->select('id', 'name', 'cost_price')
            ->limit(10)
            ->get();
        
        if(!$products || $products->isEmpty()) return response()->json([]);
        
        $response = [];
        foreach($products as $p){
            $response[] = [ 
                'id' => $p->id, 
                'label' => $p->name, 
                'value' => $p->name,
                'cost_price' => $p->cost_price
            ];
        }
        
        return response()->json($response);
    }

    public function trackExpenses(Request $request){

        $search_vendor_name = $request->search_vendor_name;
        $start = $request->date_from;
        $end = $request->date_to;

        if(!empty($search_vendor_name)){

            $expenses_reports = DB::table('p_o_s_expenses')
            ->leftjoin('vendors', 'vendors.id', '=', 'p_o_s_expenses.vendor_id')
            ->select('vendors.*', 'p_o_s_expenses.*')
            ->where(DB::raw('TRIM(CONCAT(vendors.first_name, " ", vendors.middle_name, " ", vendors.last_name,"", vendors.company_name))'), 'LIKE', "%$search_vendor_name%")
            ->orderBy('p_o_s_expenses.date', 'ASC')
            ->paginate(10);

            return view('pos-expenses.tracks-expenses', compact(
               'expenses_reports',
               'start',
               'end',
               'search_vendor_name'
               ))
          ->with('i', ($request->input('page', 1) - 1) * 10);
        
        }elseif(!empty($start)){

            $expenses_reports = DB::table('p_o_s_expenses')
            ->leftjoin('vendors', 'vendors.id', '=', 'p_o_s_expenses.vendor_id')
            ->select('vendors.*', 'p_o_s_expenses.*')
            ->whereBetween('p_o_s_expenses.date', array($start, $end))
            ->orderBy('vendors.last_name','vendors.company_name', 'ASC')
            ->paginate(10);

            return view('pos-expenses.tracks-expenses', compact(
                'expenses_reports',
                'start',
                'end',
                'search_vendor_name'
               ))
          ->with('i', ($request->input('page', 1) - 1) * 10);

        }else{
            $expenses_reports = DB::table('p_o_s_expenses')
            ->leftjoin('vendors', 'vendors.id', '=', 'p_o_s_expenses.vendor_id')
            ->select('vendors.*', 'p_o_s_expenses.*', 'vendors.id as v_id')
            ->orderBy('vendors.last_name','vendors.company_name', 'ASC')
            ->paginate(10);

            return view('pos-expenses.tracks-expenses', compact(
                'expenses_reports',
                'start',
                'end',
                'search_vendor_name'
                ))
            ->with('i', ($request->input('page', 1) - 1) * 10);
        }    
    }

    public function getRecordDestroy($id){
        POSExpenses::find($id)->delete();
            return redirect('p_o_s_expenses/tracks-expenses')
                ->with('success','Record is successfully deleted');
    }
}
