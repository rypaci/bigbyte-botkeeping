<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Mail;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Employee;
use App\DailyTimeRecord;
use App\DtrPassword;
use App\DTRHoursshifting;
use App\User;
use App\DTRabsent;
use Carbon\Carbon;
use DateTime;
use DB;

// use Illuminate\Support\Facades\Cookie;

class DTRController extends Controller
{
    public function __construct(){
        $this->middleware('auth', ['except' => ['timeInTimeOut','saveTimeInTimeOut','viewDtrHistory','dtrVerification','generateEmpQrCode','accessVerification','qrTimeInTimeOut']]);
    }

    public function index(Request $request){

        $employees = DB::table('employees as emp')
        ->leftjoin('dtr_passwords as dtrpass', 'dtrpass.e_id', '=', 'emp.id')
        ->orderby('dtrpass.username')
        ->get();

        $current_dtrs = DB::table('daily_time_records as dtr')
        ->whereIn("dtr.id", function ($query) {
            $query->select(DB::RAW("MAX(id) as max_id"))
                ->from('daily_time_records')
                ->where('keys', 'dtr-in')
                ->Orwhere('keys', '')
                ->groupby('e_id');
            })
        ->leftjoin('employees as emp', 'dtr.e_id', '=', 'emp.id')
        ->select(DB::raw("dtr.*, emp.employee_name"))
        ->where('emp.employee_status', 'Active')
        ->orderby('emp.employee_name', 'asc')
        ->get();
        // dd($current_dtrs);
        return view('daily-time-record.index',compact('current_dtrs', 'employees'));
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

    public function store(Request $request){

        $user = auth()->user();
        $username = $user->name;
        $status = $request->status;

        $e_id = $request->e_id;
        $login_pass = $request->password;

        $dtrpass_info = DtrPassword::where('e_id', $e_id)
        ->first();

        if(empty($dtrpass_info)){
            return redirect()->route('daily-time-record.index')
            ->with('warning','The employee selected has not yet a username and password... Please click the Create username and password? Click here! button below...');
        }

        $get_start_time = DTRHoursshifting::where('e_id', $e_id)->first();
        $start_time = "";
        if(empty($get_start_time)){
            $start_time = "";
        }else{
            $start_time = $get_start_time->start_time;
        }
        
        $get_min_hours = Employee::where('id', $e_id)->first();
        $min_hours = $get_min_hours->min_hours_per_day;

        $confirm_in_first = DB::table('daily_time_records as dtr')
            ->whereIn("dtr.id", function ($query) {
            $query->select(DB::RAW("MAX(id) as max_id"))
                ->from('daily_time_records')
                ->where('keys', 'dtr-in')
                ->groupby('e_id');
            })
            ->where('e_id', $e_id)
            ->first();

        $currentpass = $dtrpass_info->password;

        if($currentpass != $login_pass){

            return redirect()->route('daily-time-record.index')
                ->with('warning','Password not found... Please enter your password again...');
        }

        if(empty($confirm_in_first)){
            $current_status = 'In';
        }else{
            $current_status = $confirm_in_first->status;
        }
        
        if($current_status == 'Break' || $current_status == 'Lunch'){

            return redirect()->route('daily-time-record.index')
                ->with('warning','You are already at Lunch/Quick Break status!!!');
        }

        if($status == 'Lunch' || $status == 'Break'){
            
            if(empty($confirm_in_first)){
    
                return redirect()->route('daily-time-record.index')
                ->with('warning',"No existing TIME IN records in your DTR account!!! TIME IN first...");
            }

            if($confirm_in_first->time_out == '0000-00-00 00:00:00'){

                $dtr = new DailyTimeRecord();
                $dtr->e_id = $request->e_id;
                $dtr->break_start = date("Y-m-d H:i");
                $dtr->status = $status;
                $dtr->date = date("Y-m-d");
                $dtr->user_account = $username;
                $dtr->ref_id = $confirm_in_first->id;
                $dtr->keys = 'brk';
                $dtr->save();
                
                $id = $confirm_in_first->id;
                DailyTimeRecord::where('id', $id)->update([
                    'status' => $status,
                ]);

                return redirect()->route('daily-time-record.index')
                ->with('success','You are now at Lunch/Quick break status...');

            }else{

                return redirect()->route('daily-time-record.index')
                ->with('warning',"Lunch/Quick break is not allowed for those who has not yet in TIME IN status...\nMake sure you are currently in TIME IN status...");
            }
        }

        if(empty($confirm_in_first)){

            $dtr = new DailyTimeRecord();
            $dtr->e_id = $request->e_id;
            $dtr->time_in = date("Y-m-d H:i");
            $dtr->notes = $request->notes;
            $dtr->status = $status;
            $dtr->date = date("Y-m-d");
            $dtr->user_account = $username;
            $dtr->start_time = $start_time;
            $dtr->min_hours = $min_hours;
            $dtr->keys = 'dtr-in';
            $dtr->save();

            return redirect()->route('daily-time-record.index')
            ->with('success','Time In is successfully granted...');

        }else{

            $date_timein = $confirm_in_first->time_in;
            $timein = $confirm_in_first->time_in;
            $warningMessage = "The system detect that you are already in TIME IN...\nDate and Time: ".date('m-d-Y', strtotime($date_timein))." - ".date('g:i a', strtotime($timein));
            
            if($confirm_in_first->time_out == '0000-00-00 00:00:00'){
                return back()->with('warning', $warningMessage);
            }

            $dtr = new DailyTimeRecord();
            $dtr->e_id = $request->e_id;
            $dtr->time_in = date("Y-m-d H:i");
            $dtr->notes = $request->notes;
            $dtr->status = $status;
            $dtr->date = date("Y-m-d");
            $dtr->user_account = $username;
            $dtr->start_time = $start_time;
            $dtr->min_hours = $min_hours;
            $dtr->keys = 'dtr-in';
            $dtr->save();

            return redirect()->route('daily-time-record.index')
            ->with('success','Time In is successfully granted...');
        }
    }

    public function finishBreak($id){

        $break_time = DailyTimeRecord::where('ref_id', $id)->latest()->first();

        $break_start = new Carbon(date("Y-m-d H:i", strtotime($break_time->break_start)));
        $break_end = new Carbon(date("Y-m-d H:i"));

        $brk_time = $break_end->diffInMinutes($break_start);
        $break_time = $this->getHoursMinutes($brk_time * 60);

        $check_first_time_in = DailyTimeRecord::where('id', $id)->latest()->first();
        DailyTimeRecord::where('id', $check_first_time_in->id)->update([
            'status' => 'In',
        ]);

        $check_latest_breaktime = DailyTimeRecord::where('e_id', $check_first_time_in->e_id)->latest()->first();
        DailyTimeRecord::where('id', $check_latest_breaktime->id)->update([
            'break_end' => date("Y-m-d H:i:s"),
            'status' => 'Done',
            'break_time' => $break_time
        ]);

        return redirect()->route('daily-time-record.index')
        ->with('success','Lunch/Quick break is done...');
    }

    public function logOut($id){
        
        $user = auth()->user();
        $username = $user->name;

        $break_time = DailyTimeRecord::where('ref_id', $id)->get();

        $breaktime = 0;
        foreach($break_time as $break){
    
            $breaktime = $breaktime + $this->hourMinute2Minutes($break->break_time);
        }

        $useraccount = DailyTimeRecord::where('id', $id)
        ->first();
        $storedusername = $useraccount->user_account;

        $min_hours_per_day = $useraccount->min_hours;
        $hours_to_mins = $min_hours_per_day * 60;

        $get_timein = date("Y-m-d H:i", strtotime($useraccount->time_in));
        $get_timeout = date("Y-m-d H:i");

        $timein = new Carbon($get_timein);
        $timeout = new Carbon($get_timeout);

        $time_consume_mins =  $timeout->diffInMinutes($timein);
        $overtime = ($time_consume_mins - $hours_to_mins) - $breaktime;

        if($overtime <= 0){
            $get_over_time = "";
        }else{
            $get_over_time = $this->getHoursMinutes(($overtime) * 60);
        }

        DailyTimeRecord::where('id', $id)->update([
            'time_out' =>  $get_timeout,
            'status' => 'Out',
            'date' => date("Y-m-d"),
            'user_account' => $storedusername.'-'.$username,
            'over_time' => $get_over_time,
            'break_time' => $this->getHoursMinutes($breaktime * 60)
        ]);

        return redirect()->route('daily-time-record.index')
        ->with('success','Time Out is successfully granted...');
    }

    public function createPassword(Request $request){
        
        $employees = DB::table('employees as emp')
        ->leftjoin('dtr_passwords as dtrpass', 'dtrpass.e_id', '=', 'emp.id')
        ->select(DB::raw('emp.*, dtrpass.*, emp.id as e_id'))
        ->orderby('dtrpass.username')
        ->get();

        return view('daily-time-record.create-password', compact('employees'));
    }

    public function savePassword(Request $request){

        $password = $request->password;
        $confirmpassword = $request->confirm_password;
        $e_id = $request->e_id;
        $username = $request->username;

        $checkusername_duplication = DtrPassword::where('username', $username)->first();
        if(!empty($checkusername_duplication)){
            return back()->with('warning', 'Username is already exist... Please create a unique username...');
        }

        $emp_id_duplication = DtrPassword::where('e_id', $e_id)->first();
        if(!empty($emp_id_duplication)){
            return back()->with('warning', 'This employee has already exist...');
        }

        if(empty($e_id)){
            return back()->with('warning', 'No employee name selected... Please select employee name carefully...'); 
        }

        if($password != $confirmpassword){
            return back()->with('warning', 'Confirm password not match...');
        }

        DtrPassword::create($request->all());
        return back()->with('success', 'Password is successfully created...');
    }

    public function editPassword(){

        $employees = DB::table('employees as emp')
        ->leftjoin('dtr_passwords as dtrpass', 'dtrpass.e_id', '=', 'emp.id')
        ->orderby('dtrpass.username')
        ->get();
        return view('daily-time-record.edit-password', compact('employees'));
    }
    
    public function updatePassword(Request $request){

        $oldpassinput = $request->old_password;
        $newpass = $request->password;
        $confirmnewpass = $request->confirm_password;
        $emp_id = $request->e_id;
        $email = $request->email;

        if (strpos($newpass,' ') !== false) {

            return back()->with('warning', 'Opss!!! Spaces are not allowed in creating password... Please create password without spaces...'); 
        }

        if(empty($emp_id)){
            return back()->with('warning', 'No employee name selected... Please select employee name carefully...'); 
        }

        $checkemployee = DtrPassword::where('e_id', $emp_id)->first(); 
        $oldpassword = $checkemployee->password;

        if($oldpassword != $oldpassinput){

            return back()->with('warning', 'Old password not match...');
        }else{
            if($newpass == $confirmnewpass){

                if(empty($email)){
                    DtrPassword::where('e_id', $emp_id)->update([
                        'password' => $newpass
                    ]);
                    return back()->with('success', 'Old password is successfully updated...');
                }else{
                    DtrPassword::where('e_id', $emp_id)->update([
                        'password' => $newpass,
                        'email' => $email
                    ]);
                    return back()->with('success', 'Old password is successfully updated...');
                }
            }else{
                return back()->with('warning', 'New password and confirm new password not match... Please try again...');
            }
        }
    }

    public function timeInTimeOut(){

        return view('time-in-time-out');
    }

    public function saveTimeInTimeOut(Request $request){

        $username = $request->username;
        $password = $request->password;
        $status = $request->status;
        $token = $request->token;
        
        $confirmed = DtrPassword::where('username', $username)
        ->orWhere('token', $token)
        ->first(); 

        if(empty($confirmed)){
            return back()->with('warning', 'Username or QR code not found! Please enter your username or QR code again or contact your system administrator');
        }
        
        $confirmpass = $confirmed->password;
        if(empty($token)){
            if($confirmpass != $password){
                return back()->with('warning', 'Password not found! Please enter your password again or contact your system administrator');
            }
        }
        
        // Code to finish lunch or break time
        if($status == 'Finish'){

            $break_time_rec = DailyTimeRecord::where('e_id', $confirmed->e_id)->latest()->first();
            
            if(empty($break_time_rec)){
                return back()->with('warning','Opss!!! No DTR record found...');
            }

            if($break_time_rec->status == 'Done'){
                return back()->with('warning','Opss!!! No current Lunch/Break status found...');
            }
            
            $break_start = new Carbon(date("Y-m-d H:i", strtotime($break_time_rec->break_start)));
            $break_end = new Carbon(date("Y-m-d H:i"));
    
            $brk_time = $break_end->diffInMinutes($break_start);
            $break_time = $this->getHoursMinutes($brk_time * 60);
            
            $check_first_time_in = DailyTimeRecord::where('id', $break_time_rec->ref_id)->latest()->first();
            
            if(empty($check_first_time_in)){
                return back()->with('warning','Opss!!! No current Lunch/Break status found...');
            }

            DailyTimeRecord::where('id', $check_first_time_in->id)->update([
                'status' => 'In',
            ]);
    
            $check_latest_breaktime = DailyTimeRecord::where('e_id', $check_first_time_in->e_id)->latest()->first();
            
            DailyTimeRecord::where('id', $check_latest_breaktime->id)->update([
                'break_end' => date("Y-m-d H:i"),
                'status' => 'Done',
                'break_time' => $break_time
            ]);
    
            return back()->with('success','Lunch/Break is done...');
        }
        // End of code to finish lunch or break time

        // Code to TIME OUT
        if($status == 'Out'){

            $rec = DailyTimeRecord::where('e_id', $confirmed->e_id)->latest()->first();

            if(empty($rec)){
                return back()->with('warning','Opss!!! No DTR record found...');
            }

            $break_time_rec = DailyTimeRecord::where('ref_id', $rec->ref_id)->get();

            $breaktime = 0;
            foreach($break_time_rec as $break){

                $breaktime = $breaktime + $this->hourMinute2Minutes($break->break_time);
            }

            $check_breaktime = DailyTimeRecord::where('e_id', $rec->e_id)->latest()->first();

            if($check_breaktime->status == 'Break' || $check_breaktime->status == 'Lunch'){

                return back()->with('warning','Opss!!! Just finish first your Lunch/Break before proceed to TIME OUT...');
            }

            $dtr_account = DB::table('daily_time_records as dtr')
            ->whereIn("dtr.id", function ($query) {
            $query->select(DB::RAW("MAX(id) as max_id"))
                ->from('daily_time_records')
                ->where('keys', 'dtr-in')
                ->groupby('e_id');
            })
            ->where('e_id', $rec->e_id)
            ->first();
            
            if($dtr_account->status == 'Out'){

                return back()->with('warning','Opss!!! You are not yet in TIME IN status...');
            }

            $min_hours_per_day = $dtr_account->min_hours;
            $hours_to_mins = $min_hours_per_day * 60;
            // dd($hours_to_mins);

            $get_timein = date("Y-m-d H:i", strtotime($dtr_account->time_in));
            $get_timeout = date("Y-m-d H:i");

            $timein = new Carbon($get_timein);
            $timeout = new Carbon($get_timeout);

            if($breaktime <= 0){
                $breaktime = 0;
            }else{
                $breaktime = $breaktime;
            }

            $time_consume_mins =  $timeout->diffInMinutes($timein);
            $overtime = ($time_consume_mins - $hours_to_mins) - $breaktime;
            
            if($overtime <= 0){
                $get_over_time = "";
            }else{
                $get_over_time = $this->getHoursMinutes(($overtime) * 60);
            }

            if($breaktime <= 0){

                DailyTimeRecord::where('id', $dtr_account->id)->update([
                    'time_out' =>  $get_timeout,
                    'status' => 'Out',
                    'date' => date("Y-m-d"),
                    'over_time' => $get_over_time,
                    'break_time' => '',
                    'notes' => $dtr_account->notes." --- ".$request->notes
                ]);
            }else{
        
                DailyTimeRecord::where('id', $dtr_account->id)->update([
                    'time_out' =>  $get_timeout,
                    'status' => 'Out',
                    'date' => date("Y-m-d"),
                    'over_time' => $get_over_time,
                    'break_time' => $this->getHoursMinutes($breaktime * 60),
                    'notes' => $dtr_account->notes." --- ".$request->notes
                ]);
            }
            return back()->with('success','Time Out is successfully granted...');
        }
        // End code to TIME OUT

        $get_start_time = DTRHoursshifting::where('e_id', $confirmed->e_id)->first();
        $start_time = "";
        if(empty($get_start_time)){
            $start_time = "";
        }else{
            $start_time = $get_start_time->start_time;
        }

        $checkemployee_status = Employee::where('id', $confirmed->e_id)->first();
        $getstatus = $checkemployee_status->employee_status;
        $min_hours = $checkemployee_status->min_hours_per_day;
        
        if($getstatus == 'Inactive'){
            return back()->with('warning', 'Opss!!! Employee status is not active anymore...');
        }

        $confirm_e_id = $confirmed->e_id;

        $confirm_in_first = DB::table('daily_time_records as dtr')
        ->whereIn("dtr.id", function ($query) {
        $query->select(DB::RAW("MAX(id) as max_id"))
            ->from('daily_time_records')
            ->where('keys', 'dtr-in')
            ->groupby('e_id');
        })
        ->where('e_id', $confirm_e_id)
        ->first();

        if(empty($confirm_in_first)){
            $current_status = 'In';
        }else{
            $current_status = $confirm_in_first->status;
        }

        if($current_status == 'Break' || $current_status == 'Lunch'){
    
            return back()->with('warning','Opss!!! You are already at Lunch/Break status!!!');
        }
        
        if($status == 'Lunch' || $status == 'Break'){

            if(empty($confirm_in_first)){
    
                return back()->with('warning',"Opss!!! No existing TIME IN records in your DTR account!!! TIME IN first...");
            }

            if($confirm_in_first->time_out == '0000-00-00 00:00:00'){
    
                $dtr = new DailyTimeRecord();
                $dtr->e_id = $confirm_e_id;
                $dtr->break_start = date("Y-m-d H:i");
                $dtr->status = $status;
                $dtr->date = date("Y-m-d");
                $dtr->ref_id = $confirm_in_first->id;
                $dtr->keys = 'brk';
                $dtr->save();
                
                $id = $confirm_in_first->id;
                DailyTimeRecord::where('id', $id)->update([
                    'status' => $status,
                ]);
    
                return back()->with('success','You are now at Lunch/Break status...');
    
            }else{
    
                return back()->with('warning',"Lunch/Break is not allowed for those who has not yet in TIME IN status...\nMake sure you are currently in TIME IN status...");
            }
        }

        if(empty($confirm_in_first)){
            
            $dtr = new DailyTimeRecord();
            $dtr->e_id = $confirm_e_id;
            $dtr->time_in = date("Y-m-d H:i");
            $dtr->notes = $request->notes;
            $dtr->status = $status;
            $dtr->date = date("Y-m-d");
            $dtr->start_time = $start_time;
            $dtr->min_hours = $min_hours;
            $dtr->keys = 'dtr-in';
            $dtr->save();

            return back()->with('success','Time In is successfully granted...');

        }else{
            
            $date_timein = $confirm_in_first->time_in;
            $timein = $confirm_in_first->time_in;
            $warningMessage = "The system detect that you are already in TIME IN...\nDate and Time: ".date('m-d-Y', strtotime($date_timein))." - ".date('g:i a', strtotime($timein));
            if($confirm_in_first->time_out == '0000-00-00 00:00:00'){
                return back()->with('warning', $warningMessage);
            }

            $dtr = new DailyTimeRecord();
            $dtr->e_id = $confirm_e_id;
            $dtr->time_in = date("Y-m-d H:i");
            $dtr->notes = $request->notes;
            $dtr->status = 'In';
            $dtr->date = date("Y-m-d");
            $dtr->start_time = $start_time;
            $dtr->min_hours = $min_hours;
            $dtr->keys = 'dtr-in';
            $dtr->save();
            return back()->with('success','Time In is successfully granted...');
        }
    }

    public function lunchBreak($id){

        $lunch_break_recs = DailyTimeRecord::where('ref_id', $id)
        ->orderby('break_start', 'asc')
        ->paginate(10);

        $e_id = DailyTimeRecord::where('id', $id)->first();

        return view('daily-time-record.lunch-break-details', compact('lunch_break_recs', 'e_id'));

    }

    // public function timeOut(){

    //     return view('time-out');
    // }

    // public function saveTimeOut(Request $request){

    //     $username = $request->username;
    //     $password = $request->password;

    //     $confirmed = DtrPassword::where('username', $username)->first();
        
    //     if(empty($confirmed)){
    //         return back()->with('warning', 'Username not found... Please enter your username again...');
    //     }

    //     $confirmpass = $confirmed->password;
    //     $confirme_id = $confirmed->e_id;

    //     $confirm_in_first = DailyTimeRecord::where('e_id', $confirme_id)->latest()->first();

    //     $min_hours_per_day = $confirm_in_first->min_hours;
    //     $hours_to_mins = $min_hours_per_day * 60;

    //     $get_timein = date("Y-m-d H:i", strtotime($confirm_in_first->time_in));
    //     $get_timeout = date("Y-m-d H:i");

    //     $timein = new Carbon($get_timein);
    //     $timeout = new Carbon($get_timeout);

    //     $time_consume_mins =  $timeout->diffInMinutes($timein);
    //     $get_overtime = $time_consume_mins - $hours_to_mins;

    //     if($get_overtime <= 0){
    //         $overtime = "";
    //     }else{
    //         $overtime = $this->getHoursMinutes($get_overtime * 60);
    //     }

    //     if($confirm_in_first->time_out != '0000-00-00 00:00:00'){
    //         return back()->with('warning', 'The system detect that you are not yet in TIME IN...');
    //     }

    //     $id = $confirm_in_first->id;

    //     if($confirmpass != $password){
    //         return back()->with('warning', 'Password not found... Please enter your password again...');
    //     }else{
    //         DailyTimeRecord::where('id', $id)->update([
    //         'time_out' =>  date("Y-m-d H:i:s"),
    //         'status' => 'Out',
    //         'date' => date("Y-m-d"),
    //         'over_time' => $overtime
    //         ]);
    //     return back()->with('success','Time Out is successfully granted...');
    //     }
    // }

    public function dtrAccountList(){

        $list_of_dtr_accounts = DB::table('dtr_passwords as dtrpass')
        ->leftjoin('employees as emp', 'dtrpass.e_id', '=', 'emp.id')
        ->leftjoin('d_t_r_hoursshiftings as dtrhs', 'dtrpass.e_id', '=', 'dtrhs.e_id')
        ->select(DB::raw('emp.*, dtrpass.*, dtrhs.*, emp.id as e_id, dtrpass.id as id'))
        ->orderby('emp.employee_name')
        ->paginate(10);

        return view('daily-time-record.dtr-account-list',compact('list_of_dtr_accounts'));
    }

    public function dtrAccountSearch(Request $request){
        
        $search_name = $request->employee_name;

        $list_of_dtr_accounts = DB::table('dtr_passwords as dtrpass')
        ->leftjoin('employees as emp', 'dtrpass.e_id', '=', 'emp.id')
        ->where('emp.employee_name', 'LIKE', "%$search_name%")
        ->orderby('emp.employee_name')
        ->paginate(10);

        return view('daily-time-record.dtr-account-list',compact('list_of_dtr_accounts'));
    }

    public function dtrDetails($id){
        
        $employee = DB::table('employees as emp')
        ->where('emp.id', $id)
        ->first();

        $dtrdetails = DB::table('daily_time_records as dtr')
        ->leftjoin('employees as emp', 'dtr.e_id', '=', 'emp.id')
        ->select(DB::raw('emp.*, dtr.*, emp.id as e_id, dtr.id as id'))
        ->where('dtr.keys', 'dtr-in')
        ->where('dtr.e_id', $id)
        ->orderby('dtr.time_in','asc')
        ->paginate(10);

        return view('daily-time-record.dtr-details',compact('dtrdetails','employee'));
    }

    public function searchDateDtrDetails(Request $request){

        $startDate = $request->date_from;
        $endDate = $request->date_to;
        $id = $request->id;

        $employee = DB::table('employees as emp')
        ->where('emp.id', $id)
        ->first();

        $dtrdetails = DB::table('daily_time_records as dtr')
        ->leftjoin('employees as emp', 'dtr.e_id', '=', 'emp.id')
        ->select(DB::raw('emp.*, dtr.*, emp.id as e_id, dtr.id as id'))
        ->whereDate('dtr.time_in', '>=', $startDate)
        ->whereDate('dtr.time_in', '<=', $endDate)
        ->where('dtr.keys', 'dtr-in')
        ->where('dtr.e_id', $id)
        ->orderby('dtr.time_in','asc')
        ->paginate(25);

        return view('daily-time-record.dtr-details',compact('dtrdetails','employee'));
    }

    public function deleteDtrDetails($id){

        DailyTimeRecord::where('id', $id)->delete();
        DailyTimeRecord::where('ref_id', $id)->delete();
        return back()->with('success','Record has been successfully deleted');
    }

    public function resetPassword($id){

        $get_account = DtrPassword::where('e_id', $id)->first();
        return view('daily-time-record.reset-password',compact('get_account'));
    }

    public function saveResetPassword(Request $request){
        
        $id = $request->e_id;
        $newpass = $request->password;
        $confirmpassword = $request->confirm_password;

        if($newpass != $confirmpassword){
            return back()->with('warning', 'Confirm password not match...');
        }else{
            DtrPassword::where('e_id', $id)->update([
                'password' => $newpass
            ]);
            return back()->with('success', 'Done resetting the password...');
        }
    }

    public function timeAdjustment($id){

        $get_dtr = DailyTimeRecord::where('id', $id)->first();
        return view('daily-time-record.time-adjustment',compact('get_dtr'));
    }

    public function saveTimeAdjustment(Request $request){

        $user = auth()->user();
        $current_useraccount = $user->name;

        $id = $request->id;
        $time_in = date("H:i:s", strtotime($request->time_in));
        $date_in = $request->date_in;
        $time_out = date("H:i:s", strtotime($request->time_out));
        $date_out = $request->date_out;

        $dtr_account = DailyTimeRecord::where('id', $id)->first();
        $username = $dtr_account->user_account;
        $current_timeout = $dtr_account->time_out;

        if(empty($dtr_account->break_time)){
            $breaktime = '';
        }else{
            $breaktime = $this->hourMinute2Minutes($dtr_account->break_time);
        }

        $min_hours_per_day = $request->min_hours;
        $hours_to_mins = $min_hours_per_day * 60;

        $get_datein_timein = trim($date_in.' '.$time_in);
        $get_timein = date("Y-m-d H:i", strtotime($get_datein_timein));

        $get_dateout_timeout = trim($date_out.' '.$time_out);
        $get_timeout = date("Y-m-d H:i", strtotime($get_dateout_timeout));

        $timein = new Carbon($get_timein);
        $timeout = new Carbon($get_timeout);

        $time_consume_mins =  $timeout->diffInMinutes($timein);
        $get_overtime = ($time_consume_mins - $hours_to_mins) - $breaktime;

        if($get_overtime <= 0){
            $get_over_time = "";
        }else{
            $get_over_time = $this->getHoursMinutes(($get_overtime) * 60);
        }

        if($current_timeout == '0000-00-00 00:00:00'){
            return back()->with('warning', 'Note: You cannot make time adjustment until TIME OUT is successfully done...');
        }

        if(empty($request->notes)){
            DailyTimeRecord::where('id', $id)->update([
                'time_in' => $date_in.' '.$time_in,
                'time_out' => $date_out.' '.$time_out,
                'user_account' => $username.'-'.$current_useraccount,
                'start_time' => $request->start_time,
                'min_hours' => $request->min_hours,
                'over_time' => $get_over_time
            ]);
        }else{
            DailyTimeRecord::where('id', $id)->update([
                'time_in' => $date_in.' '.$time_in,
                'time_out' => $date_out.' '.$time_out,
                'user_account' => $username.'-'.$current_useraccount,
                'notes' => $request->notes,
                'start_time' => $request->start_time,
                'min_hours' => $request->min_hours,
                'over_time' => $get_over_time
            ]);
        }
        return back()->with('success', 'Done time adjustment');
    }

    public function sendPassword($id){
        
        $user = auth()->user();

        $get_account = DtrPassword::where('e_id', $id)->first();
        
        $email_receiver = $get_account->email;
        $password = $get_account->password;

        $adminEmail = $user->email;

        $bodytext = "Your password is: ".$password;

        Mail::raw($bodytext, function($message) use ($email_receiver, $adminEmail){
            $message->from($adminEmail,'Botkeeping');
            $message->to($email_receiver)->subject('Sending DTR account password');
        });

        return back()->with('success', 'Password was successfully send to email...');
    }

    public function hoursShifting($id){
        
        $hours_shifting_rec = Employee::where('id', $id)->first();
        
        $rec = DTRHoursshifting::where('e_id', $id)->first();

        return view('daily-time-record.hours-shifting',compact('hours_shifting_rec','rec'));
    }

    public function saveHoursShifting(Request $request){

        $id = $request->e_id;

        $rec = DTRHoursshifting::where('e_id', $id)->first();
        // dd($rec);
        if(empty($rec)){
            DTRHoursshifting::create($request->all());
            return back()->with('success', 'Time schedule is save...');
        }else{
            DTRHoursshifting::where('e_id', $id)->update([
                'start_time' => $request->start_time,
                'end_time' => $request->end_time
            ]);
            return back()->with('success', 'Time schedule has been successfully updated');
        }
    }

    public function absent(Request $request){

        $employee_info = Employee::where('employee_status', 'Active')
        ->orderby('employee_name')
        ->get();
        
        return view('daily-time-record.absent',compact('employee_info'));
    }

    public function saveAbsent(Request $request){

        if(empty($request->e_id && $request->absent_no)){
            return back()->with('warning',"Please select employee's name and select absent!!!");
        }

        $user = auth()->user();

        $dtrabsent = new DTRabsent();
        $dtrabsent->e_id = $request->e_id;
        $dtrabsent->date = date("Y-m-d");
        $dtrabsent->absent_no = $request->absent_no;
        $dtrabsent->remarks = $request->remarks;
        $dtrabsent->user_account_id = $user->id;
        $dtrabsent->save();
        return back()->with('success','Absent is successfully created...');
    }

    public function absentList($id){

        $employee = Employee::where('id', $id)->first();
        $datefrom = "";
        $dateto ="";

        $dtrabsentlists = DB::table('d_t_rabsents as dtrabsent')
        ->leftjoin('users as user', 'dtrabsent.user_account_id', '=', 'user.id')
        ->select(DB::raw('user.*, dtrabsent.*, user.id as user_id, dtrabsent.id as id'))
        ->where('dtrabsent.e_id', $id)
        ->orderby('dtrabsent.date', 'asc')
        ->paginate(10);

        return view('daily-time-record.absent-list',compact('dtrabsentlists','employee','datefrom','dateto'));
    }

    public function editAbsent($id){
        
        $getdtr_absent = DTRabsent::where('id', $id)->first();

        $get_emp_name = Employee::where('id', $getdtr_absent->e_id)->first();
        
        return view('daily-time-record.edit-absent',compact('getdtr_absent','get_emp_name'));
    }

    public function updateAbsent(Request $request){

        $user = auth()->user();

        if(empty($request->remarks)){
            DTRabsent::where('id', $request->id)->update([
                'date' => $request->date,
                'absent_no' => $request->absent_no,
                'user_account_id' => $user->id
            ]);
        }else{
            DTRabsent::where('id', $request->id)->update([
                'date' => $request->date,
                'absent_no' => $request->absent_no,
                'remarks' => $request->remarks,
                'user_account_id' => $user->id
            ]);
        }
        return back()->with('success','Absent is successfully updated...');
    }

    public function deleleteAbsent($id){

        DTRabsent::where('id', $id)->delete();
        return back()->with('success','Absent has been successfully deleted');
    }

    public function searchDateAbsent(Request $request){

        $employee = Employee::where('id', $request->e_id)->first();

        $datefrom = $request->date_from;
        $dateto = $request->date_to;

        $dtrabsentlists = DB::table('d_t_rabsents as dtrabsent')
        ->leftjoin('users as user', 'dtrabsent.user_account_id', '=', 'user.id')
        ->select(DB::raw('user.*, dtrabsent.*, user.id as user_id, dtrabsent.id as id'))
        ->whereDate('dtrabsent.date', '>=', $datefrom)
        ->whereDate('dtrabsent.date', '<=', $dateto)
        ->where('dtrabsent.e_id', $request->e_id)
        ->orderby('dtrabsent.date', 'asc')
        ->paginate(25);

        return view('daily-time-record.absent-list',compact('dtrabsentlists','employee','datefrom','dateto'));
    }

    public function dtrVerification(){
        // $name = 'tokens';
        // $value = base64_encode(random_bytes(10));
        // $mins = 5;
        // Cookie::queue($name, $value, $mins);
        return view('dtr-verification');
    }

    public function viewDtrHistory(Request $request){
        
        $username = $request->username;
        $password = $request->password;
        $token = $request->token;

        $confirmed = DtrPassword::where('username', $username)
        ->orWhere('token',$token)
        ->first();
        if(empty($confirmed)){
            return back()->with('warning', 'Username or QR Code not found! Please enter your username or QR Code again or contact your system administrator');
        }
        // $name = 'tokens';
        // $value = base64_encode(random_bytes(10));
        // $mins = 5;
        // Cookie::queue($name, $value, $mins);
        if(($confirmed->username == $username && $confirmed->password == $password) || $confirmed->token == $token)
        {
            $view_dtr_history = DailyTimeRecord::where('e_id', $confirmed->e_id)
            ->where('keys', 'dtr-in')
            ->orderby('id','DESC')
            ->get();
    
            return view('view-dtr-history', compact('view_dtr_history'));
        }else{
            return back()->with('warning', 'Sorry password not found! Please enter your password again or contact your system administrator');
        }
    }

    public function qrCode(Request $request){
        $employee_info = Employee::where('employee_status', 'Active')
        ->orderby('employee_name')
        ->get();
        
        return view('daily-time-record.qr-code',compact('employee_info'));
    }

    public function generateQrCode(Request $request){

        $emp_id = $request->e_id;
        
        $getusername = DtrPassword::where('e_id', $emp_id)->first();

        $token_qrcode = $getusername->token;

        if(empty($token_qrcode)){

            $random_string = md5(microtime());

            $qrcode = DtrPassword::where('token', $random_string)->first();

            if(!empty($qrcode)){
                $random_string = md5(microtime());

                $qrcode = DtrPassword::where('e_id', $emp_id)->update([
                    'token' => $random_string
                ]);
                $token_qrcode = $random_string;
            }else{

                $qrcode = DtrPassword::where('e_id', $emp_id)->update([
                    'token' => $random_string
                ]);
                $token_qrcode = $random_string;
            }
        }
        $get_employee = Employee::where('id', $emp_id)->first();

        return view('daily-time-record.generate-qr-code',compact('token_qrcode','get_employee'));
    }

    public function accessVerification(Request $request){
        return view('access-verification');
    }

    public function generateEmpQrCode(Request $request){

        $password = $request->password;
        $username = $request->username;
        
        $checkuser = DtrPassword::where('username', $username)->first();
        
        if(empty($checkuser)){
            return back()->with('warning', 'Sorry!!! Username not found... Please try again...');
        }

        $storedusername = $checkuser->username;
        $storedpassword = $checkuser->password;
        $token_qrcode = $checkuser->token;
        $emp_id = $checkuser->e_id;

        if($storedusername == $username && $storedpassword == $password){

            if(empty($token_qrcode)){

                $random_string = md5(microtime());
    
                $qrcode = DtrPassword::where('token', $random_string)->first();
    
                if(!empty($qrcode)){
                    $random_string = md5(microtime());
    
                    $qrcode = DtrPassword::where('e_id', $emp_id)->update([
                        'token' => $random_string
                    ]);
                    $token_qrcode = $random_string;
                }else{
    
                    $qrcode = DtrPassword::where('e_id', $emp_id)->update([
                        'token' => $random_string
                    ]);
                    $token_qrcode = $random_string;
                }
            }

            return view('generate-employee-qrcode',compact('token_qrcode','storedusername'));
        }else{
            return back()->with('warning', 'Sorry!!! Username or password not found...');
        }
    }

    // public function qrTimeInTimeOut(Request $request){
        
    // }
    
    // public function mailQrCode($id){

    //     $user = auth()->user();

    //     $get_account = DtrPassword::where('e_id', $id)->first();
        
    //     $get_employee = Employee::where('id', $id)->first();

    //     $fullname = $get_employee->employee_name;

    //     $email_receiver = $get_account->email;
    //     $token = $get_account->token;

    //     $adminEmail = $user->email;

    //     $png = QrCode::format('png')->size(300)->generate($token);
    //     $png = base64_encode($png);
    //     $qrcode = "<img src='data:image/png;base64,".$png."'>";

    //     $bodytext = "Your QR code is: ".$qrcode;
    //     $subject = "Sending QR Code";

    //     $data = [
    //         'bodytext' => $bodytext,
    //         'subject' => $subject,
    //         'to_email' => $email_receiver,
    //         'to_name' => $fullname,
    //         'from_email' => $adminEmail
    //     ];

    //     Mail::send([], [], function($message) use ($data){

    //     $message->from($data['from_email'],'Botkeeping');
    //     $message->to($data['to_email'], $data['to_name'])
    //         ->subject($data['subject'])
    //         ->setBody($data['bodytext'], 'text/html');
    //     });

    //     return back()->with('success', 'QR Code was successfully send to email...');
    // }
}