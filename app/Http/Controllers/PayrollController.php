<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Employee;
use App\Payroll;
use App\Option;
use App\Voucher;
use App\ChartAccount;
use App\Cashadvance;
use DB;
use App\DailyTimeRecord;
use App\DTRabsent;
use App\DtrPassword;
use Carbon\Carbon;
use DateTime;
// use Artisan;

class PayrollController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        // Artisan::call('config:clear');
        // Artisan::call('config:cache');

        $employees = Employee::orderBy('employee_name', 'ASC')
        ->orderBy('employee_name', 'ASC')
        ->where('employee_status', 'Active')
        ->get();
        return view('payroll.index', compact('employees'));
    }

    // Function to get hours and minutes
    function getHoursMinutes($seconds, $format = '%02d:%02d') {

        if (empty($seconds) || ! is_numeric($seconds)) {
            return false;
        }
    
        $minutes = round($seconds / 60);
        $hours = floor($minutes / 60);
        $remainMinutes = ($minutes % 60);
    
        return sprintf($format, $hours, $remainMinutes);
    }

    // Function to get hourMinute2Minutes
    function hourMinute2Minutes($strHourMinute) {
        $from = date('Y-m-d 00:00:00');
        $to = date('Y-m-d '.$strHourMinute.':00');
        $diff = strtotime($to) - strtotime($from);
        $minutes = $diff / 60;
        return (int) $minutes;
    }

    public function createDailyPayroll(Request $request){

        $emp_id = $request->employee_id;
        if(!is_numeric($emp_id)){
            return back()->with('warning', 'Please select an Employee to create Payroll!');
        }

        $datefrom = date('Y-m-d', strtotime($request->date_from));
        $dateto = date('Y-m-d', strtotime($request->date_to));

        if(empty($datefrom) || empty($dateto)){
            return back()->with('warning', 'Please select Date From and Date To first to create Payroll!');
        }

        $employees = Employee::where('id', $emp_id)->first();
        $min_hours = $employees->min_hours_per_day;

        // getting hours of work
        $getDTRs = DailyTimeRecord::where('e_id', $emp_id)
        ->whereDate('time_in', '>=', $datefrom)
        ->whereDate('time_in', '<=', $dateto)
        ->orderby('id', 'asc')
        ->get();
        // ->limit(4)

        // $time_in_seconds = 0;
        $late = 0;
        
        $total_overtime = 0;
        $get_total_mins = 0;

        $total_hours = 0;
        $total_mins = 0;

        $x = 0;

        foreach($getDTRs as $getDTR){
            
            $timein = new Carbon($getDTR->time_in);
            $timeout = new Carbon($getDTR->time_out);

            // Getting the late
            $get_timein = date("Y-m-d H:i", strtotime($timein));
            $get_start_time = date("Y-m-d H:i", strtotime($getDTR->start_time));
            $late =  $late + (date('i', strtotime($get_timein) - strtotime($get_start_time)));

            // Getting the overtime
            $total_overtime = $total_overtime + $getDTR->over_time;
            $get_total_mins = $get_total_mins + date('i', strtotime($getDTR->over_time));

            // Get the total overtime per day
            $mintohrs = date('i', strtotime($getDTR->over_time)) / 60;
            $totalhoursovertime = number_format(floatVal($getDTR->over_time + $mintohrs), 2);

            // Getting the hours per day
            $get_timein = date("Y-m-d H:i", strtotime($getDTR->time_in));
            $get_timeout = date("Y-m-d H:i", strtotime($getDTR->time_out));

            $timein = new Carbon($get_timein);
            $timeout = new Carbon($get_timeout);

            $brk_time = $this->hourMinute2Minutes($getDTR->break_time);

            if($brk_time < 0){
                $brk_time = 0;
            }

            $time_consume_mins =  (($timeout->diffInMinutes($timein) - $brk_time) / 60);
            // echo $x++." ".$time_consume_mins." ".$brk_time."<br/>";
            // Get the min hours registered in DTR not from the employee information
            $min_hrs_per_day = $getDTR->min_hours;
            $total_hours = $total_hours + (($time_consume_mins - $totalhoursovertime) / $min_hrs_per_day);
            // dd(round($total_hours), $brk_time, $min_hrs_per_day, $totalhoursovertime, round($time_consume_mins), $timeout->diffInMinutes($timein), $time_consume_mins - $totalhoursovertime);
        }

        // Getting the overall overtime in hours
        $min_to_hrs = $get_total_mins / 60;
        $total_hours_overtime = number_format(floatVal($total_overtime + $min_to_hrs), 2);

        // Convert total hours of work to days 
        $total_days = floatVal($total_hours);
        // dd(number_format(floatVal($total_hours), 2), $total_hours_overtime);
        // End of getting hours of work

        $chart_accounts = ChartAccount::orderby('name')->get();
        
        $ca_amount = Cashadvance::where('e_id', $emp_id)->sum('ca_amount');
        $ca_deduction = Cashadvance::where('e_id', $emp_id)->sum('ca_deduction');
        $total_ca_amount = $ca_amount - $ca_deduction;

        $employees_data = DB::table('employees')
        ->where('id', $emp_id)
        ->first();
        return view('payroll.create-daily-payroll', compact('employees_data','chart_accounts','total_ca_amount','total_days','total_hours_overtime','late'));
    }

    public function createMonthlyPayroll(Request $request){

        $emp_id = $request->employee_id;
        if(!is_numeric($emp_id)){
            return back()->with('warning', 'Please select an Employee to create Payroll!');
        }


        $datefrom = date('Y-m-d', strtotime($request->date_from));
        $dateto = date('Y-m-d', strtotime($request->date_to));

        if(empty($datefrom) || empty($dateto)){
            return back()->with('warning', 'Please select Date From and Date To first to create Payroll!');
        }

        $employees = Employee::where('id', $emp_id)->first();
        $min_hours = $employees->min_hours_per_day;

        // getting hours of work
        $getDTRs = DailyTimeRecord::where('e_id', $emp_id)
        ->whereDate('time_in', '>=', $datefrom)
        ->whereDate('time_in', '<=', $dateto)
        ->orderby('id', 'asc')
        ->get();

        $getDTRabsents = DTRabsent::where('e_id', $emp_id)
        ->whereDate('date', '>=', $datefrom)
        ->whereDate('date', '<=', $dateto)
        ->orderby('id', 'asc')
        ->get();

        $absent = 0;
        foreach($getDTRabsents as $dtrabsent){
            $absent = $absent + $dtrabsent->absent_no;
        }

        $late = 0;     
        $total_overtime = 0;
        $get_total_mins = 0;
        $total_hours = 0;
        $total_mins = 0;
        foreach($getDTRs as $getDTR){
            
            $timein = new Carbon($getDTR->time_in);
            $timeout = new Carbon($getDTR->time_out);

            // Getting the late
            $get_timein = date("Y-m-d H:i", strtotime($timein));
            $get_start_time = date("Y-m-d H:i", strtotime($getDTR->start_time));
            $late =  $late + (date('i', strtotime($get_timein) - strtotime($get_start_time)));

            // Getting the overtime
            $total_overtime = $total_overtime + $getDTR->over_time;
            $get_total_mins = $get_total_mins + date('i', strtotime($getDTR->over_time));

            // Get the total overtime per day
            $mintohrs = date('i', strtotime($getDTR->over_time)) / 60;
            $totalhoursovertime = number_format(floatVal($getDTR->over_time + $mintohrs), 2);

            // Getting the hours per day
            $get_timein = date("Y-m-d H:i", strtotime($getDTR->time_in));
            $get_timeout = date("Y-m-d H:i", strtotime($getDTR->time_out));

            $timein = new Carbon($get_timein);
            $timeout = new Carbon($get_timeout);

            // $time_consume_mins =  $timeout->diffInMinutes($timein) / 60;
            $brk_time = $this->hourMinute2Minutes($getDTR->break_time);

            if($brk_time < 0){
                $brk_time = 0;
            }

            $time_consume_mins =  (($timeout->diffInMinutes($timein) - $brk_time) / 60);
            
            // Get the min hours registered in DTR not from the employee information
            $min_hrs_per_day = $getDTR->min_hours;

            $total_hours = $total_hours + (($time_consume_mins - $totalhoursovertime) / $min_hrs_per_day);
            // dd($total_hours, $time_consume_mins - $totalhoursovertime);
        }
        // Getting the overall overtime in hours
        $min_to_hrs = $get_total_mins / 60;
        $total_hours_overtime = number_format(floatVal($total_overtime + $min_to_hrs), 2);

        // Convert total hours of work to days 
        $total_days = $total_hours;
        // dd(number_format(floatVal($total_hours), 2), $total_hours_overtime);
        // End of getting hours of work


        $chart_accounts = ChartAccount::orderby('name')->get();

        $ca_amount = Cashadvance::where('e_id', $emp_id)->sum('ca_amount');
        $ca_deduction = Cashadvance::where('e_id', $emp_id)->sum('ca_deduction');
        $total_ca_amount = $ca_amount - $ca_deduction;

        $employees_data = DB::table('employees')
        ->where('id', $emp_id)
        ->first();
        return view('payroll.create-monthly-payroll', compact('employees_data','chart_accounts','total_ca_amount','total_days','total_hours_overtime','late','absent'));
    }

    public function savePayroll(Request $request){

        $pr = Payroll::create($request->all());
        
        $pr_last_rec = DB::table('payrolls')->latest()->first();
        $pay_id = 'pr-'.$pr_last_rec->pay_id;
        
        $pr_date = date('Y-m-d', strtotime($pr->date));
        $pr_alias = 'pr';
        $order = 0;

        $ca_deducted = $request->ca_deducted;
        if(!empty($ca_deducted)){
            $ca_key = 'cd-'.$pr_last_rec->pay_id;
            $cashAdvance = new Cashadvance();
            $cashAdvance->ca_deduction = $ca_deducted;
            $cashAdvance->keys = $ca_key;
            $cashAdvance->e_id = $request->e_id;
            $cashAdvance->date = $pr_date;
            $cashAdvance->save();
        }

        foreach($request->vouchers as $voucher){

            $float_debit  = str_replace( ',', '', $voucher['debit']);
            $float_credit  = str_replace( ',', '', $voucher['credit']);

            $voucher['ref_id']       = $voucher['chart_account_id'];
            $voucher['ref_number']   = $pay_id;
            $voucher['module_alias'] = $pr_alias;
            $voucher['order']        = $order;
            $voucher['debit']        = $float_debit;
            $voucher['credit']       = $float_credit;
            $voucher['date']         = $pr_date;

            Voucher::create($voucher);
            $order++;
        }

        return redirect()->route('payroll.index')
        ->with('success','Employee payroll is successfully created.');
    }

    public function editDailyPayroll($id){

        $edit_payroll = Payroll::where('pay_id', $id)
        ->first();
        
        $chart_accounts = ChartAccount::orderby('name')->get();

        $key = 'cd-'.$id;
        $ca_deducted = Cashadvance::where('keys', $key)->first();

        $pr_id = 'pr-'.$id;
        $vouchers_rec = Voucher::where('ref_number', $pr_id)
        ->orderby('order')
        ->get();

        $employee_id = $edit_payroll->e_id;
        $employee_info = Employee::where('id', $employee_id)
        ->first();
        return view('payroll.edit-daily-payroll', compact(
            'edit_payroll',
            'employee_info',
            'chart_accounts',
            'vouchers_rec',
            'ca_deducted'
        ));
    }

    public function editMonthlyPayroll($id){
        $edit_payroll = Payroll::where('pay_id', $id)
        ->first();
    
        $chart_accounts = ChartAccount::orderby('name')->get();

        $key = 'cd-'.$id;
        $ca_deducted = Cashadvance::where('keys', $key)->first();

        $pr_id = 'pr-'.$id;
        $vouchers_rec = Voucher::where('ref_number', $pr_id)
        ->orderby('order')
        ->get();

        $employee_id = $edit_payroll->e_id;
        $employee_info = Employee::where('id', $employee_id)
        ->first();
        return view('payroll.edit-monthly-payroll', compact(
            'edit_payroll',
            'employee_info',
            'chart_accounts',
            'vouchers_rec',
            'ca_deducted'
        ));
    }

    public function updatePayroll(Request $request)
    {
        // dd($request->vouchers);
        // $ca_deducted = str_replace( ',', '', $request->ca_deducted);
        // dd($ca_deducted);
        $pay_id = $request->pay_id;
        $pr_alias = 'pr-'.$pay_id;
        
        $order = 0;

        foreach($request->vouchers as $voucher){

            $v_id = $voucher['id'];

            $float_debit  = str_replace( ',', '', $voucher['debit']);
            $float_credit  = str_replace( ',', '', $voucher['credit']);
            // dd($float_credit);

            $voucher['ref_number']   = $pr_alias;
            $voucher['ref_id']       = $voucher['chart_account_id'];
            $voucher['order']        = $order;
            $voucher['debit']        = $float_debit;
            $voucher['credit']       = $float_credit;
            $voucher['date']         = $request->date;

            Voucher::where('id', $v_id)->update($voucher);
            $order++;
        }

        $ca_deducted = str_replace( ',', '', $request->ca_deducted);
        $key = 'cd-'.$pay_id;
        if(!empty($ca_deducted)){
            Cashadvance::where('keys', $key)->update([
                'ca_deduction' => $ca_deducted,
                'date' => $request->date,
                'keys' =>  $key
            ]);
        }

        if (!empty($request->e_id) && $request->salary_method == "D") {

            Payroll::where('pay_id', $pay_id)->update([
                'e_id' => $request->e_id,
                'salary_days_present' =>  $request->salary_days_present,
                'salary_rate' => $request->salary_rate,
                'salary_total' => $request->salary_total,
                'overtime' => $request->overtime,
                'overtime_rate' => $request->overtime_rate,
                'overtime_total' => $request->overtime_total,
                'cola' => $request->cola,
                'total' => $request->total,
                'emp_con_sss' => $request->emp_con_sss,
                'emp_con_phic' => $request->emp_con_phic,
                'emp_con_pagibig' => $request->emp_con_pagibig,
                'late' => $request->late,
                'late_rate' => $request->late_rate,
                'late_total' => $request->late_total,
                'employer_con_sss' => $request->employer_con_sss,
                'employer_con_phic' => $request->employer_con_phic,
                'employer_con_pagibig' => $request->employer_con_pagibig,
                'holiday' => $request->holiday,
                'net_pay' => $request->net_pay,
                'tax_withheld' => $request->tax_withheld,
                'salary_method' => $request->salary_method,
                'date' => $request->date,
            ]);

        }elseif (!empty($request->e_id) && $request->salary_method == "M"){

            Payroll::where('pay_id', $pay_id)->update([
                'e_id' => $request->e_id,
                'salary_rate' => $request->salary_rate,
                'salary_total' => $request->salary_total,
                'overtime' => $request->overtime,
                'overtime_rate' => $request->overtime_rate,
                'overtime_total' => $request->overtime_total,
                'cola' => $request->cola,
                'total' => $request->total,
                'emp_con_sss' => $request->emp_con_sss,
                'emp_con_phic' => $request->emp_con_phic,
                'emp_con_pagibig' => $request->emp_con_pagibig,
                'late' => $request->late,
                'late_rate' => $request->late_rate,
                'late_total' => $request->late_total,
                'employer_con_sss' => $request->employer_con_sss,
                'employer_con_phic' => $request->employer_con_phic,
                'employer_con_pagibig' => $request->employer_con_pagibig,
                'days_absent' => $request->days_absent,
                'absent_rate' => $request->absent_rate,
                'absent_total' => $request->absent_total,
                'holiday' => $request->holiday,
                'net_pay' => $request->net_pay,
                'tax_withheld' => $request->tax_withheld,
                'salary_method' => $request->salary_method,
                'date' => $request->date,
            ]);
        }

        return redirect()->route('payroll.index')
        ->with('success','Employee Payroll is successfully updated.');
    }

    public function show($id)
    {
        // $employee_payroll = Payroll::find($id);
        // return view('payroll.show',compact('employee_payroll'));
    }

    public function getPayrollDestroy($pay_id=null)
        {
            $pr_alias = 'pr-'.$pay_id;
            Voucher::where('ref_number', $pr_alias)->delete();

            $key = 'cd-'.$pay_id;
            Cashadvance::where('keys', $key)->delete();
            
            Payroll::where('pay_id', $pay_id)->delete();
            
            return back()->with('success', 'Employee Payroll is successfully deleted...');
        }

    public function payrollDetails(Request $request)
    {
       $employees_payrolls = DB::table('employees')
            ->join('payrolls', 'employees.id', '=', 'payrolls.e_id')
            ->select('employees.*', 'payrolls.*', 'employees.id as id')
            ->orderBy('employee_name', 'ASC')
            ->orderBy('payrolls.created_at', 'ASC')
            ->paginate(10);
            // dd($employees_payrolls);
           return view('payroll.payroll-details', compact('employees_payrolls'))
          ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function payrollDetailsByName(Request $request)
    {

        if(!empty($request->search_emp_name)){

            $employees_payrolls = DB::table('employees')
            ->join('payrolls', 'employees.id', '=', 'payrolls.e_id')
            ->select('employees.*', 'payrolls.*', 'employees.id as id')
            ->where('employee_name', 'LIKE', '%'.$request->search_emp_name.'%')
            ->orderBy('payrolls.created_at', 'ASC')
            ->paginate(10);

           return view('payroll.payroll-details', compact('employees_payrolls'))
          ->with('i', ($request->input('page', 1) - 1) * 5);
        
        }else{

            $start = $request->date_from;
            $end = $request->date_to;

            $employees_payrolls = DB::table('payrolls')
            ->join('employees', 'payrolls.e_id', '=', 'employees.id')
            ->select('employees.*', 'payrolls.*')
            ->whereBetween('payrolls.created_at', array($start, $end))
            ->orderBy('employee_name', 'ASC')
            ->orderBy('payrolls.created_at', 'ASC')
            ->paginate(10);

           return view('payroll.payroll-details', compact('employees_payrolls'))
          ->with('i', ($request->input('page', 1) - 1) * 5);
        }
           
    }

}
