<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon;
use DB;

class WaterRefilling extends Model
{
	public $fillable = [
	'id',
	'pro_id',
	'entry_no', 
	'customer_id', 
	'return_bottle', 
	'order_qty', 
	'amount_due', 
	'date',
	'refill_bottle',
	'others_qty',
	'dealer_qty',
	'container_qty',
	'status',
	'amt_balance',
    'emp_id',
    'comp_name'
	];

	public static function sales() {
		
        $firstDayofYear = new Carbon\Carbon('first day of January');
        $thisDay = Carbon\Carbon::now();
		
        $month = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $charts = $sale_arr = $cost_arr = $ca_expense_arr = $wrm_expense_arr = $tax_arr = array();
        $s=$c=$ce=$t=$wrm_e = 0;
        $dateFrom = date('n', strtotime($firstDayofYear));
		$dateTo = date('n', strtotime($thisDay));

        $sales = DB::table('water_refillings')
                ->select(DB::raw('DATE_FORMAT(date, "%b") as month'), 
                		DB::raw('IFNULL(SUM(amount_due), 0) as sales'))
                ->whereBetween('date',[$firstDayofYear, $thisDay])
                ->orderBy('date', 'asc')
                ->groupBy(DB::raw('DATE_FORMAT(date, "%b")'))
                ->get();
				
        $costs = DB::table('chart_accounts as ca')
                ->join('vouchers as v', 'ca.id','=','v.chart_account_id')
                ->select(DB::raw('DATE_FORMAT(v.date, "%b") as month'),
                        DB::raw('IFNULL(SUM(debit-credit), 0) as costs'))
                ->whereBetween('v.date',[$firstDayofYear, $thisDay])
                ->where('ca.sub_account_type_id', 8)
                // ->orWhere('ca.sub_account_type_id', 8)
                ->orderBy('v.date', 'asc')
                ->groupBy(DB::raw('DATE_FORMAT(v.date, "%b")'))
                ->get();
            // dd($costs);

        $ca_expenses = DB::table('chart_accounts as ca')
                ->join('vouchers as v', 'ca.id','=','v.chart_account_id')
                ->select(DB::raw('DATE_FORMAT(v.date, "%b") as month'),
                        DB::raw('IFNULL(SUM(debit-credit), 0) as ca_expenses'))
                ->whereBetween('v.date',[$firstDayofYear, $thisDay])
                ->where('ca.account_type_id', 9)
                ->orderBy('v.date', 'asc')
                ->groupBy(DB::raw('DATE_FORMAT(v.date, "%b")'))
                ->get();

        $wrm_expenses = DB::table('w_r_m_expenses')
                ->select(DB::raw('DATE_FORMAT(date, "%b") as month'), 
                        DB::raw('IFNULL(SUM(amount), 0) as wrm_expenses'))
                ->whereBetween('date',[$firstDayofYear, $thisDay])
                ->orderBy('date', 'asc')
                ->groupBy(DB::raw('DATE_FORMAT(date, "%b")'))
                ->get();

        $taxes = DB::table('chart_accounts as ca')
                ->join('vouchers as v', 'ca.id','=','v.chart_account_id')
                ->select(DB::raw('DATE_FORMAT(v.date, "%b") as month'),
                        DB::raw('IFNULL(SUM(debit), 0) as taxes'))
                ->whereBetween('v.date',[$firstDayofYear, $thisDay])
                ->where('v.tax_id','>',0)
                ->where('v.key' , '=' ,'tax_debit')
                ->orderBy('v.date', 'asc')
                ->groupBy(DB::raw('DATE_FORMAT(v.date, "%b")'))
                ->get();
				
        for($i=$dateFrom-1;$i<$dateTo;$i++){
            
            if(!empty($sales)){
                if($s<count($sales) && $month[$i]==$sales[$s]->month) {
                    $sale_arr[] = $sales[$s]->sales;
                    $s++;
                }else {
                    $sale_arr[] = 0;
                }
            }else{
                $sale_arr[] = 0;
            }
            
            if(!empty($costs)) {
                if($c<count($costs) && $month[$i]==$costs[$c]->month) {
                    $cost_arr[] = $costs[$c]->costs;
                    $c++;
                }else {
                    $cost_arr[] = 0;
                }
            }else {
                $cost_arr[] = 0;
            }
            
            if(!empty($ca_expenses)) {
                if($ce<count($ca_expenses) && $month[$i]==$ca_expenses[$ce]->month) {
                    $ca_expense_arr[] = $ca_expenses[$ce]->ca_expenses;
                    $ce++;
                }else {
                    $ca_expense_arr[] = 0;
                }
            }else {
                $ca_expense_arr[] = 0;
            }

            if(!empty($wrm_expenses)) {
                if($wrm_e<count($wrm_expenses) && $month[$i]==$wrm_expenses[$wrm_e]->month) {
                    $wrm_expense_arr[] = $wrm_expenses[$wrm_e]->wrm_expenses;
                    $wrm_e++;
                }else {
                    $wrm_expense_arr[] = 0;
                }
            }else {
                $wrm_expense_arr[] = 0;
            }

            if(!empty($taxes)) {
                if($t<count($taxes) && $month[$i]==$taxes[$t]->month) {
                    $tax_arr[] = $taxes[$t]->taxes;
                    $t++;
                }else {
                    $tax_arr[] = 0;
                }
            }else {
                $tax_arr[] = 0;
            }
        }
        
        return $charts[] = (object) [
            'sales' => $sale_arr,
            'costs' => $cost_arr,
            'taxes' => $tax_arr,
            'ca_expenses' => $ca_expense_arr,
            'wrm_expenses' => $wrm_expense_arr,
        ];
        //'2018-01-01 00:00:00' AND '2018-01-31 12:59:59'
    }
}