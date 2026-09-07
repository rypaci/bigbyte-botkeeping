<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon;
use DB;

class POS_sales extends Model
{
    public $fillable = ['customer_id','amount_due','amt_balance','sales_date','status','emp_id','comp_name'];

    public static function sales() {
		
        $firstDayofYear = new Carbon\Carbon('first day of January');
        $thisDay = Carbon\Carbon::now();
		
        $month = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $charts = $sale_arr = $cost_arr = $ca_expense_arr = $pos_expense_arr = $tax_arr = array();
        $s=$c=$ce=$t=$pos_e = 0;
        $dateFrom = date('n', strtotime($firstDayofYear));
		$dateTo = date('n', strtotime($thisDay));

        $sales = DB::table('p_o_s_sales')
                ->select(DB::raw('DATE_FORMAT(sales_date, "%b") as month'), 
                		DB::raw('IFNULL(SUM(amount_due), 0) as sales'))
                ->whereBetween('sales_date',[$firstDayofYear, $thisDay])
                ->orderBy('sales_date', 'asc')
                ->groupBy(DB::raw('DATE_FORMAT(sales_date, "%b")'))
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

        $pos_expenses = DB::table('p_o_s_expenses')
                ->select(DB::raw('DATE_FORMAT(date, "%b") as month'), 
                        DB::raw('IFNULL(SUM(amount), 0) as pos_expenses'))
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

            if(!empty($pos_expenses)) {
                if($pos_e<count($pos_expenses) && $month[$i]==$pos_expenses[$pos_e]->month) {
                    $pos_expense_arr[] = $pos_expenses[$pos_e]->pos_expenses;
                    $pos_e++;
                }else {
                    $pos_expense_arr[] = 0;
                }
            }else {
                $pos_expense_arr[] = 0;
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
            'pos_expenses' => $pos_expense_arr,
        ];
        //'2018-01-01 00:00:00' AND '2018-01-31 12:59:59'
    }
}
