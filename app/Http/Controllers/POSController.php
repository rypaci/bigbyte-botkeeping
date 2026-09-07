<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Customer;
use App\DtrPassword;
use App\Product;
use App\POS_sales;
use App\POS_soldstock;
use App\POSInventory;

use Carbon\Carbon;

use DateTime;
use DB;
use Cookie;

class POSController extends Controller
{
    public function __construct(){
        $this->middleware('auth', ['except' => [
            'publicFindCustomerOnly',
            'publicFindProductAutoSuggest',
            'publicFindProduct',
            'publicGetCustomersAutoSuggest',
            'savePOSentry',
            'posEntryAccess',
            'posEntryAccessVerified',
            'publicPOSentry',
            'assignShifting'
            ]
        ]);
    }

    // Builds the product dropdown options used by the sales dashboard filters
    private function productFilterOptions(){
        $products = Product::select('id', 'name', 'sr_priority')->orderBy('name')->get();
        $options = ['' => 'All Products'];
        foreach($products as $product){
            $options[$product->id] = empty($product->sr_priority) ? $product->name : $product->name.' - '.$product->sr_priority;
        }
        return $options;
    }

    public function index(Request $request){

        $date = Carbon::parse($request->ShootDateTime)->timezone('Asia/Manila');
        $date_today = $date->format('Y-m-d');
        $productId = $request->product_id;
        $products = $this->productFilterOptions();

        $query = DB::table('p_o_s_sales as pos_s');
        if ($productId) {
            $query->join('p_o_s_soldstocks as poss', 'pos_s.id', '=', 'poss.sales_id')
                ->where('poss.product_id', $productId);
        }
        $total_non_tax_sales_today = $query
        ->where('pos_s.sales_date', $date_today)
        ->sum($productId ? 'poss.amount' : 'pos_s.amount_due');

        $expenses_today = DB::table('p_o_s_expenses')->select(DB::raw('*'))
        ->where('date', $date_today)->get();
        
        $amount_expenses_today = 0;
        foreach($expenses_today as $value){

            $amount_expenses_today = $amount_expenses_today + $value->amount;
        }

        $currentMonth = date('m');
        $over_all_sales = DB::table('p_o_s_sales')
        ->whereRaw('MONTH(sales_date) = ?',[$currentMonth])
        ->sum('amount_due');

        $customer_unpaid = DB::table('p_o_s_sales')
        ->whereRaw('MONTH(sales_date) = ?',[$currentMonth])
        ->where('status', 'Unpaid')
        ->Orwhere('status', 'Balanced')
        ->sum('amt_balance');

        $cash_advance = DB::table('cashadvances')
        ->whereRaw('MONTH(date) = ?',[$currentMonth])
        ->where('keys', 'ca')
        ->sum('ca_amount');
        
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now();
        $ca_expenses = DB::table('chart_accounts as ca')
        ->join('vouchers as v', 'ca.id','=','v.chart_account_id')
        ->select(DB::raw('IFNULL(SUM(debit-credit), 0) as ca_expenses'))
        // ->whereBetween('v.date',[$start, $end])
        ->whereDate('v.date', '>=', $startDate)
        ->whereDate('v.date', '<=', $endDate)
        ->where('ca.account_type_id', 9)
        ->get();

        $query = DB::table('p_o_s_sales as pos_s');
        if ($productId) {
            $query->join('p_o_s_soldstocks as poss', 'pos_s.id', '=', 'poss.sales_id')
                ->where('poss.product_id', $productId);
        }
        $over_all_sales = $query
        ->whereRaw('MONTH(pos_s.sales_date) = ?',[$currentMonth])
        ->sum($productId ? 'poss.amount' : 'pos_s.amount_due');
        // dd($over_all_sales);

        $over_all_no_OR_expenses = DB::table('p_o_s_expenses')
        ->whereRaw('MONTH(date) = ?',[$currentMonth])
        ->sum('amount');

        // Sales Transaction Summary
        $trans_today = POS_sales::where('sales_date', $date_today)->sum('amount_due');
        $trans_count_today = POS_sales::where('sales_date', $date_today)->count();
        
        $date_yesterday = Carbon::now()->subDays(1)->format('Y-m-d');
        $trans_count_yesterday = POS_sales::where('sales_date', $date_yesterday)->count();
        $trans_yesterday = POS_sales::where('sales_date', $date_yesterday)->sum('amount_due');

        $date_week = Carbon::now()->subDays(7)->format('Y-m-d');
        $trans_count_week = POS_sales::whereDate('sales_date', '>=', $date_week)
        ->whereDate('sales_date', '<=', $date_today)
        ->count();
        $trans_week = POS_sales::whereDate('sales_date', '>=', $date_week)
        ->whereDate('sales_date', '<=', $date_today)
        ->sum('amount_due');

        $trans_count_curr_month = POS_sales::whereRaw('MONTH(sales_date) = ?',[$currentMonth])
        ->count();
        $trans_curr_month = POS_sales::whereRaw('MONTH(sales_date) = ?',[$currentMonth])
        ->sum('amount_due');

        $currentYear = date('Y');
        $trans_count_curr_year = POS_sales::whereRaw('YEAR(sales_date) = ?',[$currentYear])
        ->count();
        $trans_curr_year = POS_sales::whereRaw('YEAR(sales_date) = ?',[$currentYear])
        ->sum('amount_due');
        // End Sales Transaction Summary

        // Top 5 Selling Products
        $sold_qty  = "(SELECT SUM(qty) FROM p_o_s_soldstocks pos_ss where prod.id = pos_ss.product_id) as sold_qty";
        $total_amount  = "(SELECT SUM(amount) FROM p_o_s_soldstocks pos_ss where prod.id = pos_ss.product_id) as total_amount";

        $query = DB::table('p_o_s_soldstocks as poss')
        ->join('products as prod', 'poss.product_id','=','prod.id')
        ->select(DB::raw("poss.*, prod.name, prod.sr_priority, poss.id as pro_id, $sold_qty, $total_amount"));
        if ($productId) {
            $query->where('poss.product_id', $productId);
        }
        $top_5_products = $query
        ->groupBy('poss.product_id')
        ->orderBy(DB::raw('SUM(poss.qty)'), 'DESC')
        ->limit(5)
        ->get();
        // End of top selling 5 products

        // Recent 5 Sales
        $sold_qty  = "(SELECT COUNT(sales_id) FROM p_o_s_soldstocks pos_ss where pos_ss.sales_id = pos_s.id) as total_item";

        $query = DB::table('p_o_s_sales as pos_s')
        ->join('p_o_s_soldstocks as pos_ss', 'pos_s.id','=','pos_ss.sales_id')
        ->leftjoin('customers as cust', 'pos_s.customer_id','=','cust.id')
        ->select(DB::raw("pos_s.*, cust.*, cust.id as cust_id, pos_s.id as id, pos_ss.sales_id as poss_sales_id, $sold_qty"));
        if ($productId) {
            $query->where('pos_ss.product_id', $productId);
        }
        $recent_5_sales = $query
        ->groupBy('pos_ss.sales_id')
        ->orderBy('pos_s.id', 'DESC')
        ->limit(5)
        ->get();
        // End of Recent 5 Sales

        // Top 5 Customers
        $sales_count  = "(SELECT COUNT(customer_id) FROM p_o_s_sales pos_s where pos_s.customer_id = cust.id) as total_sales_count";
        $total_sales  = "(SELECT SUM(amount_due) FROM p_o_s_sales pos_s where pos_s.customer_id = cust.id) as total_amount";

        $query = DB::table('p_o_s_sales as pos_s')
        ->join('customers as cust', 'pos_s.customer_id','=','cust.id')
        ->select(DB::raw("pos_s.*, cust.*, cust.id as cust_id, pos_s.id as id, $sales_count, $total_sales"));
        if ($productId) {
            $query->join('p_o_s_soldstocks as poss', 'pos_s.id', '=', 'poss.sales_id')
                ->where('poss.product_id', $productId);
        }
        $top_5_customers = $query
        ->groupBy('pos_s.customer_id')
        ->orderBy(DB::raw('COUNT(pos_s.customer_id)'), 'DESC')
        ->limit(5)
        ->get();
        // End of Top 5 Customers
        
        // for popup notification query about to out of stock products
        $total_soldstock  = "(SELECT SUM(qty) FROM p_o_s_soldstocks poss where poss.product_id = prod.id) as total_soldstock";
        $total_stock  = "(SELECT SUM(added_qty) FROM inventories inv where inv.pro_id = prod.id) as total_stock";

        $popup_data = DB::table('products as prod')
        ->leftjoin('p_o_s_soldstocks as poss', 'poss.product_id','=','prod.id')
        ->select(DB::raw("poss.*, prod.*, prod.id as prod_id, poss.id as id, $total_soldstock, $total_stock"))
        ->where('inventory_status', 'Active')
        ->groupBy('prod.id')
        ->orderBy(DB::raw('prod.name'), 'ASC')
        ->get();
        // End for popup notification

        return view('point-of-sale.index', compact(
            'total_non_tax_sales_today',
            'amount_expenses_today',
            'over_all_no_OR_expenses',
            'over_all_sales',
            'ca_expenses',
            'customer_unpaid',
            'cash_advance',
            'startDate',
            'endDate',
            'trans_count_today',
            'trans_today',
            'trans_count_yesterday',
            'trans_yesterday',
            'trans_count_week',
            'trans_week',
            'trans_count_curr_month',
            'trans_curr_month',
            'trans_count_curr_year',
            'trans_curr_year',
            'top_5_products',
            'recent_5_sales',
            'top_5_customers',
            'popup_data',
            'products',
            'productId'
        ));
    }

    public function create()
    {
        return view('point-of-sale/create');
    }

    public function store(Request $request){

        // dd($request->all());

        $emp_id = auth()->user()->id;
        $comp_name = gethostname();

        if(empty($request->products)){
            return back()->with('warning','No sales to be save!');
        }

        if(empty($request->customer_id)){

            $customer = Customer::where('last_name', 'Cash')
            ->orWhere('middle_name', 'Cash')
            ->orWhere('first_name', 'Cash')
            ->orWhere('company_name', 'Cash')
            ->first();
            
                if(empty($customer)){
                    $customer = new Customer();
                    $customer->last_name = 'Cash';
                    $customer->individual = '1';
                    $customer->save();

                    $customer_id = $customer->id;
                }
            $customer_id = $customer->id;
        }else{
            $customer_id = $request->customer_id;
        }

        $pos_sales = new POS_sales();
        $pos_sales->customer_id = $customer_id;
        $pos_sales->amount_due = $request->amount_due;
        $pos_sales->amt_balance = $request->amt_balance;
        $pos_sales->sales_date = $request->date;
        $pos_sales->status = $request->status;
        $pos_sales->emp_id = 'a-'.$emp_id;
        $pos_sales->comp_name = $comp_name;
        $pos_sales->save();

        $pos_sales = POS_sales::latest()->first();
        $sales_id = $pos_sales->id;

        foreach($request->products as $product) {

            $product['sales_id'] = $sales_id;
            $product['product_id'] = $product['product_id'];
            $product['qty'] = $product['qty'];
            $product['price'] = $product['price'];
            $product['amount'] = $product['amount'];
            $product['date'] = $request->date;

            POS_soldstock::create($product);
        }
    
        return back()->with('success','Sales entry is successfully saved.');
    }

    public function show($id){

        $pos_records = POS_sales::findOrFail($id);
        $pos_sold_products = POS_soldstock::from('p_o_s_soldstocks as sp')
        ->leftjoin('products as p', 'sp.product_id', '=', 'p.id')
        ->select('p.name', 'sp.qty', 'sp.price')
        ->where('sp.sales_id', $id)
        ->get();

        $customer_id = $pos_records->customer_id;

        $customer_info = Customer::select('*')
        ->where('id', '=', $customer_id)
        ->first();

        return view('point-of-sale.show', compact('pos_records','customer_info','pos_sold_products'));
	}

    public function reports(Request $request){ 

        $search_cust_name = $request->search_cust_name;
        $start = $request->date_from;
        $end = $request->date_to;
        $viewstatus = $request->viewstatus;

        if(!empty($search_cust_name)){
            $pos_reports = DB::table('p_o_s_sales')
            ->leftjoin('customers', 'customers.id', '=', 'p_o_s_sales.customer_id')
            ->select('customers.*', 'p_o_s_sales.*', 'p_o_s_sales.id as id', 'customers.id as cust_id')
            ->where(DB::raw('TRIM(CONCAT(customers.first_name, " ", customers.middle_name, " ", customers.last_name, " ", customers.company_name))'), 'LIKE', "%$search_cust_name%")
            ->orderBy('p_o_s_sales.sales_date', 'DESC')
            ->paginate(10);
    
            $pos_reports_no_pagination = DB::table('p_o_s_sales')
            ->leftjoin('customers', 'customers.id', '=', 'p_o_s_sales.customer_id')
            ->select('customers.*', 'p_o_s_sales.*', 'p_o_s_sales.id as id', 'customers.id as cust_id')
            ->where(DB::raw('TRIM(CONCAT(customers.first_name, " ", customers.middle_name, " ", customers.last_name, " ", customers.company_name))'), 'LIKE', "%$search_cust_name%")
            // ->orderBy('p_o_s_sales.sales_date', 'ASC')
            ->get();
    
            return view('point-of-sale.reports', compact(
                'pos_reports',
                'pos_reports_no_pagination',
                'search_cust_name',
                'start',
                'end',
                'viewstatus'
                )
            )->with('i', ($request->input('page', 1) - 1) * 5); 
        }

        if(!empty($start) && !empty($end)){
            $pos_reports = DB::table('p_o_s_sales')
            ->leftjoin('customers', 'customers.id', '=', 'p_o_s_sales.customer_id')
            ->select('customers.*', 'p_o_s_sales.*', 'p_o_s_sales.id as id', 'customers.id as cust_id')
            ->whereDate('p_o_s_sales.sales_date', '>=', $start)
            ->whereDate('p_o_s_sales.sales_date', '<=', $end)
            ->orderBy('p_o_s_sales.sales_date', 'DESC')
            ->paginate(10);
    
            $pos_reports_no_pagination = DB::table('p_o_s_sales')
            ->leftjoin('customers', 'customers.id', '=', 'p_o_s_sales.customer_id')
            ->select('customers.*', 'p_o_s_sales.*', 'p_o_s_sales.id as id', 'customers.id as cust_id')
            ->whereDate('p_o_s_sales.sales_date', '>=', $start)
            ->whereDate('p_o_s_sales.sales_date', '<=', $end)
            // ->orderBy('customers.last_name', 'ASC')
            // ->orderBy('customers.company_name', 'ASC')
            ->get();
    
            return view('point-of-sale.reports', compact(
                'pos_reports',
                'pos_reports_no_pagination',
                'search_cust_name',
                'start',
                'end',
                'viewstatus'
                )
            )->with('i', ($request->input('page', 1) - 1) * 5); 
        }

        if($viewstatus){
           
            $pos_reports = DB::table('p_o_s_sales')
            ->leftjoin('customers', 'customers.id', '=', 'p_o_s_sales.customer_id')
            ->select('customers.*', 'p_o_s_sales.*', 'p_o_s_sales.id as id', 'customers.id as cust_id')
            ->where('p_o_s_sales.status', '=', $viewstatus)
            ->orderBy('p_o_s_sales.sales_date', 'DESC')
            // ->orderBy('customers.last_name', 'ASC')
            ->paginate(10);
    
            $pos_reports_no_pagination = DB::table('p_o_s_sales')
            ->leftjoin('customers', 'customers.id', '=', 'p_o_s_sales.customer_id')
            ->select('customers.*', 'p_o_s_sales.*', 'p_o_s_sales.id as id', 'customers.id as cust_id')
            ->where('p_o_s_sales.status', '=', $viewstatus)
            ->orderBy('p_o_s_sales.sales_date', 'DESC')
            // ->orderBy('customers.last_name', 'ASC')
            ->get();
    
            return view('point-of-sale.reports', compact(
                'pos_reports',
                'pos_reports_no_pagination',
                'search_cust_name',
                'start',
                'end',
                'viewstatus'
                )
            )->with('i', ($request->input('page', 1) - 1) * 5); 
        }

        $pos_reports = DB::table('p_o_s_sales')
        ->leftjoin('customers', 'customers.id', '=', 'p_o_s_sales.customer_id')
        ->select('customers.*', 'p_o_s_sales.*', 'p_o_s_sales.id as id', 'customers.id as cust_id')
        ->orderBy('p_o_s_sales.sales_date', 'DESC')
        // ->orderBy('customers.last_name', 'ASC')
        ->paginate(10);

        $pos_reports_no_pagination = DB::table('p_o_s_sales')
        ->leftjoin('customers', 'customers.id', '=', 'p_o_s_sales.customer_id')
        ->select('customers.*', 'p_o_s_sales.*', 'p_o_s_sales.id as id', 'customers.id as cust_id')
        // ->orderBy('customers.last_name', 'ASC')
        ->get();

        return view('point-of-sale.reports', compact(
            'pos_reports',
            'pos_reports_no_pagination',
            'search_cust_name',
            'start',
            'end',
            'viewstatus'
            )
        )->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function inventory(Request $request){

        $startDate = $request->date_from;
        $endDate = $request->date_to;

        $search_products = $request->search_products;

        $total_added_qty = "(SELECT SUM(added_qty) FROM inventories inv where prod.id = inv.pro_id) as total_added_qty";
        $sold_qty  = "(SELECT SUM(qty) FROM p_o_s_soldstocks pos_ss where prod.id = pos_ss.product_id) as sold_qty";
        
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
        return view('point-of-sale.inventory', compact(
            'invData',
            // 'inventory_status',
            'search_products',
            'total_cost_price',
            'total_selling_price',
            'total_profit',
            'overall_total_profit',
            'startDate',
            'endDate'

        ))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function inventoryDateRange(Request $request){

        $startDate = $request->date_from;
        $endDate = $request->date_to;

        $search_products = $request->search_products;
        
        $total_cost_price = 0;
        $total_selling_price = 0;
        $total_profit = 0;
        $overall_total_profit = 0;

        $sold_qty  = "(SELECT SUM(qty) FROM p_o_s_soldstocks pos_ss where prod.id = pos_ss.product_id AND pos_ss.date BETWEEN '$startDate' AND '$endDate') as sold_qty";
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

       return view('point-of-sale.inventory', compact(
            'invData',
            'search_products',
            // 'inventory_status',
            'total_cost_price',
            'total_selling_price',
            'total_profit',
            'overall_total_profit',
            'startDate',
            'endDate'

        ))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function searchFilter(Request $request){
        
        $startDate = $request->date_from;
        $endDate = $request->date_to;

        $search_products = $request->search_products;

        $total_added_qty = "(SELECT SUM(added_qty) FROM inventories inv where prod.id = inv.pro_id) as total_added_qty";
        $sold_qty  = "(SELECT SUM(qty) FROM p_o_s_soldstocks pos_ss where prod.id = pos_ss.product_id) as sold_qty";
        
        $invData  = Product::from('products as prod')
        ->leftjoin('vendors', 'vendors.id', '=', 'prod.vendor_id')
        ->select(DB::raw("prod.*, vendors.*, prod.id as pro_id, $sold_qty, $total_added_qty"))
        ->where('prod.name', 'LIKE', "%$search_products%")
        ->where('prod.inventory_status', '=', 'Active')
        ->orderBy('prod.name', 'ASC')
        ->orderBy('prod.sr_priority', 'ASC')
        ->paginate(10);

        $overAlltotal  = Product::from('products as prod')
        ->leftjoin('vendors', 'vendors.id', '=', 'prod.vendor_id')
        ->select(DB::raw("prod.*, vendors.*, prod.id as pro_id, $sold_qty, $total_added_qty"))
        ->where('prod.name', 'LIKE', "%$search_products%")
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
        return view('point-of-sale.inventory', compact(
            'invData',
            // 'inventory_status',
            'search_products',
            'total_cost_price',
            'total_selling_price',
            'total_profit',
            'overall_total_profit',
            'startDate',
            'endDate'

        ))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function saveSRpriority(Request $request){

        $stock_threshold = $request->stock_threshold;
        // dd($stock_threshold);
        if(is_numeric($stock_threshold)){

            $pro_id = $request->pro_id;
            $update = Product::find($pro_id);
            $update->sr_priority = $request->sr_priority;
            $update->stock_threshold = $request->stock_threshold;
            $update->save();
    
            return back()->with('success','Stock reduction priority or Stock threshold is successfully updated');

        }else{
            
            return back()->with('warning','Stock threshold inputted is not a number. Please input in number only.');
        }
    }

    public function inventoryDetails($id){

        $products = Product::where('id', $id)->first();
        $profit = $products->price - $products->cost_price;

        $pos_ss_data = DB::table('p_o_s_soldstocks as pos_ss')
        ->select(DB::raw("product_id, date, qty, 0 as added_qty, 0 as cost_price, 0 as price, created_at"))
        ->where('product_id', $id);

        $inv_data = DB::table('inventories as inv')
        ->leftjoin('products as pro', 'pro.id', '=', 'inv.pro_id')
        ->select(DB::raw("inv.pro_id, inv.date, 0 as qty, inv.added_qty, pro.cost_price, pro.price, inv.created_at"))
        ->where('pro_id', $id);

        $inventory_details = $pos_ss_data->union($inv_data)
        ->orderby('date', 'ASC')
        ->orderby('created_at', 'ASC')
        ->get();

        // dd($inventory_details);
        return view('point-of-sale.inventory-details',compact('inventory_details','products','profit'));
    }

    public function paid($id){
        $paid = POS_sales::find($id)->update([
            'amt_balance' => null,
            'status' => 'Paid']
        );
        return back()->with('success', 'Paying balance is done.'); 
    }

    public function deletePos($id){

        POS_sales::where('id', $id)->delete();
        POS_soldstock::where('sales_id', $id)->delete();
        return back()->with('success','Record has been successfully deleted');
    }

    function findCustomerOnly(Request $request)
    {

        $s = $request->cust_name;

        if(!$s) return [];

        $customer = Customer::select('*',DB::raw('CONCAT(`first_name`," ",`middle_name`," ",`last_name`) as full_name'))
            ->where('individual', '=', 1)
            ->where(DB::raw('TRIM(CONCAT(first_name, " ", middle_name, " ", last_name))'), 'LIKE', "%$s%")
            ->first();

        if(!$customer) {

        $customer = Customer::select('*', DB::raw('company_name as full_name'))
            ->where('individual', '=', 0)
            ->where('company_name', 'LIKE', "%$s%")
            ->first();
        }
        return $customer? $customer: [];
    }

    function findProductAutoSuggest(Request $request){

        $s = $request->term;
        $product = Product::select('id', 'name', 'sr_priority')
            ->where('name', 'LIKE', "%$s%")
            ->orderBy('name')
            ->orderBy('sr_priority','ASC');
        $products = $product->get();
        $response = [];
        foreach($products as $c){
            if(empty($c->sr_priority)){
                $response[] = [ 'id' => $c->id, 'label' => $c->name, 'value' => $c->name ];
            }else{
                $response[] = [ 'id' => $c->id, 'label' => $c->name.' -> '.$c->sr_priority, 'value' => $c->name ];
            }
        }
        return $response;
    }

    function findProduct(Request $request){

        $s = $request->s;

        $total_added_qty = "(SELECT SUM(added_qty) FROM inventories inv where prod.id = inv.pro_id) as total_added_qty";
        $sold_qty  = "(SELECT SUM(qty) FROM p_o_s_soldstocks pos_s where prod.id = pos_s.product_id) as sold_qty";
    
        if(!empty($request->prod_id)) {

            $searchproduct  = Product::from('products as prod')
            ->select(DB::raw("prod.*, prod.id as pro_id, $sold_qty, $total_added_qty"))
            ->where('prod.id', $request->prod_id)
            ->first();

            // $searchproduct = Product::find($request->prod_id);
            if($searchproduct) 
                $searchproduct->name = trim( "{$searchproduct->name}");

            return $searchproduct? $searchproduct: [];
        }

        if(!$s) return [];

        // $searchproduct = Product::where('name', 'LIKE', "$s%")
        $searchproduct  = Product::from('products as prod')
        ->select(DB::raw("prod.*, prod.id as pro_id, $sold_qty, $total_added_qty"))
        ->where('prod.name', 'LIKE', "$s%")
        ->orWhere('prod.barcode', $s)
        ->first();

        return $searchproduct? $searchproduct: [];
    }

    public function searchDateRange(Request $request){
        
        $date = Carbon::parse($request->ShootDateTime)->timezone('Asia/Manila');
        $date = $date->format('Y-m-d');

        $dateRange = trim($request->date_range);
        // dd($dateRange);
        $arrayDate = array();

        if($dateRange) { 
            $arrayDate = explode('-', $dateRange);
        }

        $startDate = date('Y-m-d', strtotime($arrayDate[0]));
        $endDate = date('Y-m-d', strtotime($arrayDate[1]));
        $productId = $request->product_id;
        $products = $this->productFilterOptions();

        $expenses_today = DB::table('p_o_s_expenses')->select(DB::raw('*'))
        ->whereDate('date', '>=', $startDate)
        ->whereDate('date', '<=', $endDate)
        // ->whereBetween('date', array($startDate, $endDate))
        ->get();

        $amount_expenses_today = 0;
        foreach($expenses_today as $value){

            $amount_expenses_today = $amount_expenses_today + $value->amount;
        }

        $query = DB::table('p_o_s_sales as pos_s');
        if ($productId) {
            $query->join('p_o_s_soldstocks as poss', 'pos_s.id', '=', 'poss.sales_id')
                ->where('poss.product_id', $productId);
        }
        $total_non_tax_sales_today = $query
        ->whereDate('pos_s.sales_date', '>=', $startDate)
        ->whereDate('pos_s.sales_date', '<=', $endDate)
        ->sum($productId ? 'poss.amount' : 'pos_s.amount_due');

        $query = DB::table('p_o_s_sales as pos_s');
        if ($productId) {
            $query->join('p_o_s_soldstocks as poss', 'pos_s.id', '=', 'poss.sales_id')
                ->where('poss.product_id', $productId);
        }
        $over_all_sales = $query
        ->whereDate('pos_s.sales_date', '>=', $startDate)
        ->whereDate('pos_s.sales_date', '<=', $endDate)
        ->sum($productId ? 'poss.amount' : 'pos_s.amount_due');

        $over_all_no_OR_expenses = DB::table('p_o_s_expenses')
        ->whereDate('date', '>=', $startDate)
        ->whereDate('date', '<=', $endDate)
        // ->whereBetween('date', array($startDate, $endDate))
        ->sum('amount');

        $ca_expenses = DB::table('chart_accounts as ca')
        ->join('vouchers as v', 'ca.id','=','v.chart_account_id')
        ->select(DB::raw('IFNULL(SUM(debit-credit), 0) as ca_expenses'))
        ->whereDate('v.date', '>=', $startDate)
        ->whereDate('v.date', '<=', $endDate)
        // ->whereBetween('v.date',[$startDate, $endDate])
        ->where('ca.account_type_id', 9)
        ->get();

        $customer_unpaid = DB::table('p_o_s_sales')
        ->whereDate('sales_date', '>=', $startDate)
        ->whereDate('sales_date', '<=', $endDate)
        // ->whereBetween('sales_date', array($startDate, $endDate))
        ->where('status', 'Unpaid')
        ->Orwhere('status', 'Balanced')
        ->sum('amt_balance');

        $cash_advance = DB::table('cashadvances')
        ->whereDate('date', '>=', $startDate)
        ->whereDate('date', '<=', $endDate)
        // ->whereBetween('date', array($startDate, $endDate))
        ->where('keys', 'ca')
        ->sum('ca_amount');
        // dd($cash_advance);

        $datetoday = Carbon::parse($request->ShootDateTime)->timezone('Asia/Manila');
        $date_today = $datetoday->format('Y-m-d');
        $currentMonth = date('m');

        // Sales Transaction Summary
        $trans_today = POS_sales::where('sales_date', $date_today)->sum('amount_due');
        $trans_count_today = POS_sales::where('sales_date', $date_today)->count();
        
        $date_yesterday = Carbon::now()->subDays(1)->format('Y-m-d');
        $trans_count_yesterday = POS_sales::where('sales_date', $date_yesterday)->count();
        $trans_yesterday = POS_sales::where('sales_date', $date_yesterday)->sum('amount_due');

        $date_week = Carbon::now()->subDays(7)->format('Y-m-d');
        $trans_count_week = POS_sales::whereDate('sales_date', '>=', $date_week)
        ->whereDate('sales_date', '<=', $date_today)
        ->count();
        $trans_week = POS_sales::whereDate('sales_date', '>=', $date_week)
        ->whereDate('sales_date', '<=', $date_today)
        ->sum('amount_due');

        $trans_count_curr_month = POS_sales::whereRaw('MONTH(sales_date) = ?',[$currentMonth])
        ->count();
        $trans_curr_month = POS_sales::whereRaw('MONTH(sales_date) = ?',[$currentMonth])
        ->sum('amount_due');

        $currentYear = date('Y');
        $trans_count_curr_year = POS_sales::whereRaw('YEAR(sales_date) = ?',[$currentYear])
        ->count();
        $trans_curr_year = POS_sales::whereRaw('YEAR(sales_date) = ?',[$currentYear])
        ->sum('amount_due');
        // End Sales Transaction Summary

        // Top 5 Selling Products
        $sold_qty  = "(SELECT SUM(qty) FROM p_o_s_soldstocks pos_ss where prod.id = pos_ss.product_id) as sold_qty";
        $total_amount  = "(SELECT SUM(amount) FROM p_o_s_soldstocks pos_ss where prod.id = pos_ss.product_id) as total_amount";

        $query = DB::table('p_o_s_soldstocks as poss')
        ->join('products as prod', 'poss.product_id','=','prod.id')
        ->select(DB::raw("poss.*, prod.name, prod.sr_priority, poss.id as pro_id, $sold_qty, $total_amount"));
        if ($productId) {
            $query->where('poss.product_id', $productId);
        }
        $top_5_products = $query
        ->groupBy('poss.product_id')
        ->orderBy(DB::raw('SUM(poss.qty)'), 'DESC')
        ->limit(5)
        ->get();
        // End of top 5 selling products

        // Recent 5 Sales
        $sold_qty  = "(SELECT COUNT(sales_id) FROM p_o_s_soldstocks pos_ss where pos_ss.sales_id = pos_s.id) as total_item";

        $query = DB::table('p_o_s_sales as pos_s')
        ->join('p_o_s_soldstocks as pos_ss', 'pos_s.id','=','pos_ss.sales_id')
        ->rightjoin('customers as cust', 'pos_s.customer_id','=','cust.id')
        ->select(DB::raw("pos_s.*, cust.*, cust.id as cust_id, pos_s.id as id, pos_ss.sales_id as poss_sales_id, $sold_qty"));
        if ($productId) {
            $query->where('pos_ss.product_id', $productId);
        }
        $recent_5_sales = $query
        ->groupBy('pos_ss.sales_id')
        ->orderBy('pos_s.id', 'DESC')
        ->limit(5)
        ->get();
        // End of Recent 5 Sales

        // Top 5 Customers
        $sales_count  = "(SELECT COUNT(customer_id) FROM p_o_s_sales pos_s where pos_s.customer_id = cust.id) as total_sales_count";
        $total_sales  = "(SELECT SUM(amount_due) FROM p_o_s_sales pos_s where pos_s.customer_id = cust.id) as total_amount";

        $query = DB::table('p_o_s_sales as pos_s')
        ->join('customers as cust', 'pos_s.customer_id','=','cust.id')
        ->select(DB::raw("pos_s.*, cust.*, cust.id as cust_id, pos_s.id as id, $sales_count, $total_sales"));
        if ($productId) {
            $query->join('p_o_s_soldstocks as poss', 'pos_s.id', '=', 'poss.sales_id')
                ->where('poss.product_id', $productId);
        }
        $top_5_customers = $query
        ->groupBy('pos_s.customer_id')
        ->orderBy('pos_s.customer_id', 'DESC')
        ->limit(5)
        ->get();
        // End of Top 5 Customers

        // for popup notification query about to out of stock products
        $total_soldstock  = "(SELECT SUM(qty) FROM p_o_s_soldstocks poss where poss.product_id = prod.id) as total_soldstock";
        $total_stock  = "(SELECT SUM(added_qty) FROM inventories inv where inv.pro_id = prod.id) as total_stock";

        $popup_data = DB::table('products as prod')
        ->leftjoin('p_o_s_soldstocks as poss', 'poss.product_id','=','prod.id')
        ->select(DB::raw("poss.*, prod.*, prod.id as prod_id, poss.id as id, $total_soldstock, $total_stock"))
        ->where('inventory_status', 'Active')
        ->groupBy('prod.id')
        ->orderBy(DB::raw('prod.name'), 'ASC')
        ->get();
        // End for popup notification

        return view('point-of-sale.index', compact(
            'total_non_tax_sales_today',
            'amount_expenses_today',
            'over_all_no_OR_expenses',
            'over_all_sales',
            'ca_expenses',
            'customer_unpaid',
            'cash_advance',
            'startDate',
            'endDate',
            'trans_count_today',
            'trans_today',
            'trans_count_yesterday',
            'trans_yesterday',
            'trans_count_week',
            'trans_week',
            'trans_count_curr_month',
            'trans_curr_month',
            'trans_count_curr_year',
            'trans_curr_year',
            'top_5_products',
            'recent_5_sales',
            'top_5_customers',
            'popup_data',
            'products',
            'productId'
        ));
    }


    // This codes going to bottom are used for public POS entry
    function publicFindCustomerOnly(Request $request)
    {

        $s = $request->cust_name;

        if(!$s) return [];

        $customer = Customer::select('*',DB::raw('CONCAT(`first_name`," ",`middle_name`," ",`last_name`) as full_name'))
            ->where('individual', '=', 1)
            ->where(DB::raw('TRIM(CONCAT(first_name, " ", middle_name, " ", last_name))'), 'LIKE', "%$s%")
            ->first();

        if(!$customer) {

        $customer = Customer::select('*', DB::raw('company_name as full_name'))
            ->where('individual', '=', 0)
            ->where('company_name', 'LIKE', "%$s%")
            ->first();
        }
        return $customer? $customer: [];
    }

    function publicFindProductAutoSuggest(Request $request){

        $s = $request->term;
        $product = Product::select('id', 'name', 'sr_priority')
            ->where('name', 'LIKE', "%$s%")
            ->orderBy('name')
            ->orderBy('sr_priority','ASC');
        $products = $product->get();
        $response = [];
        foreach($products as $c){
            if(empty($c->sr_priority)){
                $response[] = [ 'id' => $c->id, 'label' => $c->name, 'value' => $c->name ];
            }else{
                $response[] = [ 'id' => $c->id, 'label' => $c->name.' -> '.$c->sr_priority, 'value' => $c->name ];
            }
        }
        return $response;
    }

    function publicFindProduct(Request $request){

        $s = $request->s;

        $total_added_qty = "(SELECT SUM(added_qty) FROM inventories inv where prod.id = inv.pro_id) as total_added_qty";
        $sold_qty  = "(SELECT SUM(qty) FROM p_o_s_soldstocks pos_s where prod.id = pos_s.product_id) as sold_qty";
    
        if(!empty($request->prod_id)) {

            $searchproduct  = Product::from('products as prod')
            ->select(DB::raw("prod.*, prod.id as pro_id, $sold_qty, $total_added_qty"))
            ->where('prod.id', $request->prod_id)
            ->first();

            // $searchproduct = Product::find($request->prod_id);
            if($searchproduct) 
                $searchproduct->name = trim( "{$searchproduct->name}");

            return $searchproduct? $searchproduct: [];
        }

        if(!$s) return [];

        // $searchproduct = Product::where('name', 'LIKE', "$s%")
        $searchproduct  = Product::from('products as prod')
        ->select(DB::raw("prod.*, prod.id as pro_id, $sold_qty, $total_added_qty"))
        ->where('prod.name', 'LIKE', "$s%")
        ->orWhere('prod.barcode', $s)
        ->first();

        return $searchproduct? $searchproduct: [];
    }

    public function publicGetCustomersAutoSuggest(Request $request)
    {
        $s = $request->term;
        $customer = Customer::select('*', DB::raw('TRIM(CONCAT(first_name, " ", middle_name, " ", last_name)) as `name`'))

            ->where('individual', '=', 1)
            ->where(function($query) use ($s){

                $query->where('first_name', 'LIKE', "$s%")
                    ->orWhere('middle_name', 'LIKE', "$s%")
                    ->orWhere('last_name', 'LIKE', "$s%");
            });
        $customer = Customer::select('*', 'company_name as name')

            ->where('individual', '=', 0)
            ->where('company_name', 'LIKE', "$s%")
            ->unionAll($customer);

        $customers = $customer->get();

        $response = [];
        foreach($customers as $c){

            $response[] = [ 'id' => $c->id, 'label' => $c->name, 'value' => $c->name ];
        }

        return $response;
    }

    public function savePOSentry(Request $request){
        
        // dd($request->all());
        $username = Cookie::get('Username');

        $confirm_username = DtrPassword::where('username', $username)
        ->first();

        $emp_id = $confirm_username->e_id;
        $comp_name = gethostname();

        if(empty($request->products)){
            return back()->with('warning','No sales to be save!');
        }

        if(empty($request->customer_id)){

            $customer = Customer::where('last_name', 'Cash')
            ->orWhere('middle_name', 'Cash')
            ->orWhere('first_name', 'Cash')
            ->orWhere('company_name', 'Cash')
            ->first();
            
                if(empty($customer)){
                    $customer = new Customer();
                    $customer->last_name = 'Cash';
                    $customer->individual = '1';
                    $customer->save();

                    $customer_id = $customer->id;
                }
            $customer_id = $customer->id;
        }else{
            $customer_id = $request->customer_id;
        }

        $pos_sales = new POS_sales();
        $pos_sales->customer_id = $customer_id;
        $pos_sales->amount_due = $request->amount_due;
        $pos_sales->amt_balance = $request->amt_balance;
        $pos_sales->sales_date = $request->date;
        $pos_sales->status = $request->status;
        $pos_sales->emp_id = $emp_id;
        $pos_sales->comp_name = $comp_name;
        $pos_sales->save();

        $pos_sales = POS_sales::latest()->first();
        $sales_id = $pos_sales->id;

        foreach($request->products as $product) {

            $product['sales_id'] = $sales_id;
            $product['product_id'] = $product['product_id'];
            $product['qty'] = $product['qty'];
            $product['price'] = $product['price'];
            $product['amount'] = $product['amount'];
            $product['date'] = $request->date;

            POS_soldstock::create($product);
        }
    
        return back()->with('success','Sales entry is successfully saved.');
    }

    public function posEntryAccess(){

        $value = Cookie::get('Username');

        if(!empty($value)){
            return redirect('point-of-sale/pos-entry/sales-entry');
        }else{
            return view('point-of-sale/pos-entry/access');
        }
    }

    public function publicPOSentry(){

        $value = Cookie::get('Username');

        if(!empty($value)){
            return view('point-of-sale/pos-entry/sales-entry');
        }else{
            return redirect('point-of-sale/pos-entry/access');
        }
    }

    public function posEntryAccessVerified(Request $request){

        $qrcode = $request->token;
        $username = $request->username;
        $password = $request->password;

        if(!empty($qrcode)){

            $confirm_access = DtrPassword::where('token', $qrcode)->first();

            if(empty($confirm_access)){
                return back()->with('warning', 'Sorry!!! QR code not found... Please try again...');
            }
        }else{

            $confirm_access = DtrPassword::where('username', $username)
            ->where('password', $password)
            ->first();
    
            if(empty($confirm_access)){
                return back()->with('warning', 'Sorry!!! Username or password not found... Please try again...');
            }
        }
            
        $name = 'Username';
        $value = $confirm_access->username;
        $minutes = 1440;

        Cookie::queue($name, $value, $minutes);
        // Cookie::queue(Cookie::forget('Username'));
        // $value = Cookie::get('Username');
        return redirect('point-of-sale/pos-entry/sales-entry');
    }

    public function salesReports(Request $request){

        $date_from = 0;
        $date_to = 0;

        $total_amount  = "(SELECT SUM(amount) FROM p_o_s_soldstocks soldstocks where soldstocks.product_id = products.id) as total_amount";
        $total_solds  = "(SELECT SUM(qty) FROM p_o_s_soldstocks soldstocks where soldstocks.product_id = products.id) as total_solds";

        $salesReports = DB::table('products')
        ->leftjoin('p_o_s_soldstocks as soldstocks', 'soldstocks.product_id', '=', 'products.id')
        ->leftjoin('p_o_s_sales as sales', 'sales.id', '=', 'soldstocks.sales_id')
        ->select(DB::raw("products.*, soldstocks.*, sales.*, products.id as prod_id, $total_amount, $total_solds"))
        ->groupBy('products.id')
        ->orderBy('products.name', 'ASC')
        ->get();

        // dd($salesReports);
        return view('point-of-sale.sales-reports', compact('salesReports','date_from','date_to'));
    }

    public function searchReports(Request $request){

        $search = $request->search;
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        if(empty($date_from) && empty($date_to)){
            $date_from = 0;
            $date_to = 0;
        }

        if(!empty($search) && !empty($date_from)){

            $total_amount  = "(SELECT SUM(amount) FROM p_o_s_soldstocks soldstocks where soldstocks.product_id = products.id AND soldstocks.date BETWEEN '$date_from' AND '$date_to') as total_amount";
            $total_solds  = "(SELECT SUM(qty) FROM p_o_s_soldstocks soldstocks where soldstocks.product_id = products.id AND soldstocks.date BETWEEN '$date_from' AND '$date_to') as total_solds";
    
            $salesReports = DB::table('products')
            ->leftjoin('p_o_s_soldstocks as soldstocks', 'soldstocks.product_id', '=', 'products.id')
            ->leftjoin('p_o_s_sales as sales', 'sales.id', '=', 'soldstocks.sales_id')
            ->select(DB::raw("products.*, soldstocks.*, sales.*, products.id as prod_id, $total_amount, $total_solds"))
            ->whereDate('soldstocks.date', '>=', $date_from)
            ->whereDate('soldstocks.date', '<=', $date_to)
            ->where('products.name', 'LIKE', "%$search%")
            ->groupBy('products.id')
            ->orderBy('products.name', 'ASC')
            ->get();

            return view('point-of-sale.sales-reports', compact('salesReports','date_from','date_to'));

        }elseif(empty($search) && !empty($date_from)){

            $total_amount  = "(SELECT SUM(amount) FROM p_o_s_soldstocks soldstocks where soldstocks.product_id = products.id AND soldstocks.date BETWEEN '$date_from' AND '$date_to') as total_amount";
            $total_solds  = "(SELECT SUM(qty) FROM p_o_s_soldstocks soldstocks where soldstocks.product_id = products.id AND soldstocks.date BETWEEN '$date_from' AND '$date_to') as total_solds";
    
            $salesReports = DB::table('products')
            ->leftjoin('p_o_s_soldstocks as soldstocks', 'soldstocks.product_id', '=', 'products.id')
            ->leftjoin('p_o_s_sales as sales', 'sales.id', '=', 'soldstocks.sales_id')
            ->select(DB::raw("products.*, soldstocks.*, sales.*, products.id as prod_id, $total_amount, $total_solds"))
            ->whereDate('soldstocks.date', '>=', $date_from)
            ->whereDate('soldstocks.date', '<=', $date_to)
            ->groupBy('products.id')
            ->orderBy('products.name', 'ASC')
            ->get();

            return view('point-of-sale.sales-reports', compact('salesReports','date_from','date_to'));

        }else{

            $total_amount  = "(SELECT SUM(amount) FROM p_o_s_soldstocks soldstocks where soldstocks.product_id = products.id) as total_amount";
            $total_solds  = "(SELECT SUM(qty) FROM p_o_s_soldstocks soldstocks where soldstocks.product_id = products.id) as total_solds";
    
            $salesReports = DB::table('products')
            ->leftjoin('p_o_s_soldstocks as soldstocks', 'soldstocks.product_id', '=', 'products.id')
            ->leftjoin('p_o_s_sales as sales', 'sales.id', '=', 'soldstocks.sales_id')
            ->select(DB::raw("products.*, soldstocks.*, sales.*, products.id as prod_id, $total_amount, $total_solds"))
            ->where('products.name', 'LIKE', "%$search%")
            ->groupBy('products.id')
            ->orderBy('products.name', 'ASC')
            ->get();

            return view('point-of-sale.sales-reports', compact('salesReports','date_from','date_to'));
        }
    }

    public function productSalesDetails($id, $datefrom, $dateto){
        
        $product = Product::where('id', $id)->first();

        if(empty($datefrom) && empty($dateto)){

            $product_detail_sales = POS_soldstock::where('product_id', $id)
            ->get();
    
            return view('point-of-sale.product-detail-sales', compact('product_detail_sales','product')); 

        }else{

            $product_detail_sales = POS_soldstock::where('product_id', $id)
            ->whereDate('date', '>=', $datefrom)
            ->whereDate('date', '<=', $dateto)
            ->get();
    
            return view('point-of-sale.product-detail-sales', compact('product_detail_sales','product'));
        }
    }

    public function bulkPay(Request $request){

        if(empty($request->pay)){
            return redirect('point-of-sale/reports')
            ->with('warning', 'Please select transaction first to perform bulk pay!!!');
        }

        foreach($request->pay as $pay) {

            $paid = POS_sales::find($pay)->update([
                'amt_balance' => null,
                'status' => 'Paid']
            ); 
        }
        return redirect('point-of-sale/reports')
            ->with('success', 'Paying balance is done.');
    }
}
