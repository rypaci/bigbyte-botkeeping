@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('header_style_preload')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ url('/') }}/assets/plugins/iCheck/square/blue.css">
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Edit Daily Payroll</h1>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    {{-- @if ($message = Session::get('success'))
        <div class="alert alert-success alert-employee">
            <p>{{ $message }}</p>
        </div>
    @endif --}}

    <section class="row payroll-section">
        {!! Form::open(array('url' => 'payroll/update-payroll','method'=>'GET')) !!}
            <div class="col-lg-12">
                <div class="row input-row">
                    <div class="col-md-5">
                        <label>Name:</label>
                        <input type="text" name="employee_name" value="{{$employee_info->employee_name}}" class="form-control" disabled>
                        <input type="hidden" name="e_id" value="{{$employee_info->id}}" class="form-control">
                        <input type="hidden" name="pay_id" value="{{$edit_payroll->pay_id}}" class="form-control">
                        <input type="hidden" name="salary_method" value="{{$edit_payroll->salary_method}}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>TIN:</label>
                        <input type="text" name="tin_no" value="{{$employee_info->tin_no}}" class="form-control" disabled>
                    </div>
                    <div class="col-md-2">
                        <label>Status:</label>
                        <input type="text" name="status" value="{{$employee_info->status}}" class="form-control" disabled>
                    </div>
                    <div class="col-md-2">
                        <label>Date:</label>
                        <input type="text" name="date" value="{{$edit_payroll->date}}" class="form-control datepicker ca-datepicker" required autocomplete="off">
                    </div>
                </div>
                <div class="row divider">
                    <label>Salary</label>
                </div>
                <div class="row input-row">
                    <div class="col-md-2">
                        <label>Days Present:</label>
                        <input type="text" name="salary_days_present" value="{{$edit_payroll->salary_days_present}}" class="form-control salary_days_present">
                    </div>
                    <div class="col-md-2">
                        <label>Salary Rate:</label>
                        <input type="text" name="salary_rate" value="{{$edit_payroll->salary_rate}}" class="form-control salary_rate" readonly>
                    </div>
                    <div class="col-md-2">
                        <label>Salary Total:</label>
                        <input type="text" name="salary_total" value="{{$edit_payroll->salary_total}}" class="form-control salary_total" readonly>
                    </div>
                </div>
                <div class="row divider">
                    <label>Overtime</label>
                </div>
                <div class="row input-row">
                    <div class="col-md-2">
                        <label>Overtime:</label>
                        <input type="text" name="overtime" value="{{$edit_payroll->overtime}}" class="form-control overtime">
                    </div>
                    <div class="col-md-2">
                        <label>Overtime Rate:</label>
                        <input type="text" name="overtime_rate" value="{{$edit_payroll->overtime_rate}}" class="form-control overtime_rate" readonly>
                    </div>
                    <div class="col-md-2">
                        <label>Overtime Total:</label>
                        <input type="text" name="overtime_total" value="{{$edit_payroll->overtime_total}}" class="form-control overtime_total" readonly>
                    </div>
                </div>
                <div class="row divider">
                    <label>COLA</label>
                </div>
                <div class="row input-row">
                    <div class="col-md-2">
                        <label>Cola:</label>
                        <input type="text" name="cola" value="{{$edit_payroll->cola}}" class="form-control cola">
                    </div>
                    <div class="col-md-2">
                        <label>Total:</label>
                        <input type="text" name="total" value="{{$edit_payroll->total}}" class="form-control total_cola" readonly>
                    </div>
                </div>
                <div class="row divider">
                    <label>Late</label>
                </div>
                <div class="row input-row">
                    <div class="col-md-2">
                        <label>Late:</label>
                        <input type="text" name="late" value="{{$edit_payroll->late}}" class="form-control late">
                    </div>
                    <div class="col-md-2">
                        <label>Late Rate:</label>
                        <input type="text" name="late_rate" value="{{$edit_payroll->late_rate}}" class="form-control late_rate" readonly>
                    </div>
                    <div class="col-md-2">
                        <label>Late Total:</label>
                        <input type="text" name="late_total" value="{{$edit_payroll->late_total}}" class="form-control late_total" readonly>
                    </div>
                </div>
                <div class="row divider">
                    <label>Employee's Contribution</label>
                </div>
                <div class="row input-row">
                    <div class="col-md-2">
                        <label>SSS:</label>
                        <input type="text" name="emp_con_sss" value="{{$edit_payroll->emp_con_sss}}" class="form-control emp_con_sss">
                    </div>
                    <div class="col-md-2">
                        <label>PHIC:</label>
                        <input type="text" name="emp_con_phic" value="{{$edit_payroll->emp_con_phic}}" class="form-control emp_con_phic">
                    </div>
                    <div class="col-md-2">
                        <label>PAG IBIG:</label>
                        <input type="text" name="emp_con_pagibig" value="{{$edit_payroll->emp_con_pagibig}}" class="form-control emp_con_pagibig">
                    </div>
                </div>
                <div class="row divider">
                    <label>Employer's Contribution</label>
                </div>
                <div class="row input-row">
                    <div class="col-md-2">
                        <label>SSS:</label>
                        <input type="text" name="employer_con_sss" value="{{$edit_payroll->employer_con_sss}}" class="form-control employer_con_sss">
                    </div>
                    <div class="col-md-2">
                        <label>PHIC:</label>
                        <input type="text" name="employer_con_phic" value="{{$edit_payroll->employer_con_phic}}" class="form-control employer_con_phic">
                    </div>
                    <div class="col-md-2">
                        <label>PAG IBIG:</label>
                        <input type="text" name="employer_con_pagibig" value="{{$edit_payroll->employer_con_pagibig}}" class="form-control employer_con_pagibig">
                    </div>
                </div>
                <div class="row divider">
                    <label>Holiday, Net Pay and Tax Withheld</label>
                </div>
                <div class="row input-row">
                    <div class="col-md-2">
                        <label>Holiday:</label>
                        <input type="text" name="holiday" value="{{$edit_payroll->holiday}}" class="form-control holiday">
                    </div>
                    <div class="col-md-2">
                        <label>Tax Withheld:</label>
                        <input type="text" name="tax_withheld" value="{{$edit_payroll->tax_withheld}}" class="form-control tax_withheld">
                    </div>
                    <div class="col-md-2">
                        <label>Net Pay:</label>
                        <input type="text" name="net_pay" value="{{$edit_payroll->net_pay}}" class="form-control net_pay" readonly>
                    </div>
                </div>
                @if(!empty($ca_deducted))
                <div class="row divider">
                    <label>Cash Advance Deduction</label>
                </div>
                <div class="row input-row">
                    <div class="col-md-2">
                        <label>Amount Deducted:</label>
                        <input type="text" name="ca_deducted" value="{{ floatval($ca_deducted->ca_deduction) }}" class="form-control ca_deducted">
                        <a class="btn btn-primary btn-xs" href="{{ url('cash-advance/details', $employee_info->id) }}" target="_blank" title="Check Cash Advance"><i class="fa fa-check"></i></a>
                    </div>
                </div>
                @else
                @endif

                <div class="box account-details-section">
                    <div class="box-header with-border">
                        <h3 class="box-title">Account Details</h3>
                        <div class="box-tools pull-right">
                            {{-- <a href="{{url('official-receipt/account-details')}}" target="_blank" class="btn btn-box-tool" data-toggle="tooltip" title="" data-original-title="Manage Account Details"><i class="fa fa-wrench"></i></a> --}}
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-account-details">
                        <tbody><tr class="head">
                            <th>Account #</th>
                            <th>Account Title</th>
                            <th></th>
                            <th class="debit">Debit</th>
                            <th class="credit">Credit</th>
                        </tr>
                        <tr class="account-row debit">
                            <td class="account-number"> 
                                {{-- <input type="text" name="vouchers[0][code]" value="" class="form-control input-sm debit code" readonly />  --}}
                            </td>
                            <td class="account-title"> 
                            <input type="hidden" value="0" name="vouchers[0][tax_id]" /> 
                            <select class="form-control input-sm chart-account-dropdown coa-accounts_payable" name="vouchers[0][chart_account_id]">
                                @foreach ($chart_accounts as $c_a)
                                    @if($c_a->code == '5000-00')
                                        <option value="{{ $c_a->id }}">{{ $c_a->name }}</option>
                                    @endif
                                @endforeach
                            </select> 
                            </td>
                            <td class="ref-number"> 
                                <input type="hidden" value="{{ $vouchers_rec[0]->id }}" name="vouchers[0][id]"/>
                                <input type="text" name="vouchers[0][ref_number]" value="" class="form-control input-sm" readonly /> 
                            </td>
                            <td class="debit"> <input type="text" name="vouchers[0][debit]" value="{{ number_format(floatval($vouchers_rec[0]->debit), 2) }}" class="form-control input-sm debit debit-field total_salary" readonly /> </td>
                            <td class="credit"> <input type="hidden" value="0" name="vouchers[0][credit]" /> </td>
                            <input type="hidden" value="coa_debit" name="vouchers[0][key]" />
                        </tr>
                        <tr class="tax-row debit">
                            <td class="account-number"> 
                            <input class="rate" type="hidden" value="0" name="vouchers[1][rate]" /> 
                            {{-- <input type="text" name="vouchers[1][code]" value="" class="form-control input-sm debit code" readonly /> --}}
                            </td>
                            <td class="tax"> 
                            <input type="hidden" value="" name="vouchers[1][tax_id]" class="debit tax_id" /> 
                            <select class="form-control input-sm tax-dropdown" name="vouchers[1][chart_account_id]">
                                @foreach ($chart_accounts as $c_a)
                                    @if($c_a->code == '5006-05')
                                        <option value="{{ $c_a->id }}">{{ $c_a->name }}</option>
                                    @endif
                                @endforeach
                            </select> 
                            </td>
                            <td class="ref-number"> 
                                <input type="hidden" value="{{ $vouchers_rec[1]->id }}" name="vouchers[1][id]"/>
                                <input type="text" name="vouchers[1][ref_number]" value="" class="form-control input-sm" readonly /> 
                            </td>
                            <td class="debit"> <input type="text" name="vouchers[1][debit]" value="{{ number_format(floatval($vouchers_rec[1]->debit), 2) }}" class="form-control input-sm debit debit-field total_employer_share" readonly /> </td>
                            <td class="credit"> <input type="hidden" value="0" name="vouchers[1][credit]" /> </td>
                            <input type="hidden" value="coa_debit2" name="vouchers[1][key]" />
                        </tr>
                        <tr class="discount-row credit">
                            <td class="account-number"> 
                                {{-- <input type="text" name="vouchers[2][code]" value="" class="form-control input-sm debit code" readonly />  --}}
                            </td>
                            <td class="account-title"> 
                            <input type="hidden" value="0" name="vouchers[2][tax_id]" /> 
                            <select class="form-control input-sm chart-account-dropdown coa-accounts_payable" name="vouchers[2][chart_account_id]">
                                @foreach ($chart_accounts as $c_a)
                                    @if($c_a->code == '5006-06')
                                        <option value="{{ $c_a->id }}">{{ $c_a->name }}</option>
                                    @endif
                                @endforeach
                            </select> 
                            </td>
                            <td class="ref-number"> 
                                <input type="hidden" value="{{ $vouchers_rec[2]->id }}" name="vouchers[2][id]"/>
                                <input type="text" name="vouchers[2][ref_number]" value="" class="form-control input-sm" readonly /> 
                            </td>
                            <td class="debit"> <input type="hidden" value="0" name="vouchers[2][debit]" /> </td>
                            <td class="credit"> <input type="text" name="vouchers[2][credit]" value="{{ number_format(floatval($vouchers_rec[2]->credit), 2) }}" class="form-control input-sm credit credit-field total_emp_share" readonly />(EE+ER)</td>
                            <input type="hidden" value="coa_credit" name="vouchers[2][key]" />
                        </tr>
                        <tr class="account-row credit">
                            <td class="account-number"> 
                                {{-- <input type="text" name="vouchers[3][code]" value="" class="form-control input-sm credit code" readonly />  --}}
                            </td>
                            <td class="account-title"> 
                            <input type="hidden" value="0" name="vouchers[3][tax_id]" /> 
                            <select class="form-control input-sm chart-account-dropdown coa-cash" name="vouchers[3][chart_account_id]">
                                @foreach ($chart_accounts as $c_a)
                                    @if($c_a->code == '2000')
                                        <option value="{{ $c_a->id }}">{{ $c_a->name }}</option>
                                    @endif
                                @endforeach
                            </select> 
                            </td>
                            <td class="ref-number"> 
                                <input type="hidden" value="{{ $vouchers_rec[3]->id }}" name="vouchers[3][id]"/>
                                <input type="text" name="vouchers[3][ref_number]" value="" class="form-control input-sm" readonly /> 
                            </td>
                            <td class="debit"> <input type="hidden" value="0" name="vouchers[3][debit]"/> </td>
                            <td class="credit"> <input type="text" name="vouchers[3][credit]" value="{{ number_format(floatval($vouchers_rec[3]->credit), 2) }}" class="form-control input-sm credit credit-field account_payable" readonly /> </td>
                            <input type="hidden" value="coa_credit2" name="vouchers[3][key]" />
                        </tr>
                        <tr class="total-row">
                            <td>  </td>
                            <td colspan="2" class="text-right"> Total: </td>
                            <td class="debit total_debit">{{ number_format(floatval($vouchers_rec[0]->debit + $vouchers_rec[1]->debit),2) }}</td>
                            <td class="credit total_credit">{{ number_format(floatval($vouchers_rec[2]->credit + $vouchers_rec[3]->credit),2) }}</td>
                        </tr>
                        </tbody></table>
                    </div>
                    <!-- ./box-body -->
                    <div class="box-footer clearfix">
                        <div class="col-sm-6 pull-right">
                        <!-- <table class="table lower-section"><tbody>
                            <tr><td class="text-right"><b>Total: </b></td><td><input type="text" name="total" class="text-right" readonly /></td></tr>
                        </tbody></table> -->
                        </div>
                    </div>
                </div>

                <div class="row btn-border">
                    <button type="submit" class="btn btn-primary">Update Payroll</button>
                </div>
            </div>
        {!! Form::close() !!}
    </section>
</div>
@endsection

@section('footer_script_preload')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
<script src="{{ url('/') }}/assets/plugins/iCheck/icheck.min.js"></script>
@endsection

@section('footer_script')

<script type="text/javascript">

$('.datepicker').datepicker({
    autoclose: true,
    todayBtn: "linked",
    todayHighlight: true,
    format: 'yyyy-mm-dd'
}).datepicker('update');

function commaSeparateNumber(val){
    while (/(\d+)(\d{3})/.test(val.toString())){
        val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
    }
    return val;
}

$(document).ready(function(){
    $("input").change(function(){
        var salary_days_present = $("input.salary_days_present").val() || 0;
        var salary_rate = $("input.salary_rate").val() || 0;
        var salary_total = parseFloat(salary_days_present) * parseFloat(salary_rate);
        $("input.salary_total").val(commaSeparateNumber(salary_total.toFixed(2)));

        var overtime = $("input.overtime").val() || 0;             
        var overtime_rate = $("input.overtime_rate").val() || 0;
        var overtime_total = parseFloat(overtime) * parseFloat(overtime_rate);
        $("input.overtime_total").val(commaSeparateNumber(overtime_total.toFixed(2)));

        var cola = $("input.cola").val() || 0;
        var total_cola = parseFloat(salary_total) + parseFloat(overtime_total) + parseFloat(cola);
        $("input.total_cola").val(commaSeparateNumber(total_cola.toFixed(2)));

        var late = $("input.late").val() || 0;
        var late_rate = $("input.late_rate").val() || 0;
        var late_total = parseFloat(late) * parseFloat(late_rate);
        $("input.late_total").val(commaSeparateNumber(late_total.toFixed(2)));

        // Employees shares
        var emp_con_sss = $("input.emp_con_sss").val() || 0;
        var emp_con_phic = $("input.emp_con_phic").val() || 0;
        var emp_con_pagibig = $("input.emp_con_pagibig").val() || 0;
        var total_emp_share = parseFloat(emp_con_sss) + parseFloat(emp_con_phic) + parseFloat(emp_con_pagibig);

        // Employer shares
        var employer_con_sss = $("input.employer_con_sss").val() || 0;
        var employer_con_phic = $("input.employer_con_phic").val() || 0;
        var employer_con_pagibig = $("input.employer_con_pagibig").val() || 0;
        var total_employer_share = parseFloat(employer_con_sss) + parseFloat(employer_con_phic) + parseFloat(employer_con_pagibig);
        $("input.total_employer_share").val(commaSeparateNumber(total_employer_share.toFixed(2)));

        var ee_er = total_emp_share + total_employer_share;
        $("input.total_emp_share").val(commaSeparateNumber(ee_er.toFixed(2)));

        var tax_withheld = $("input.tax_withheld").val() || 0;
        var holiday = $("input.holiday").val() || 0;

        var ca_deducted = $("input.ca_deducted").val() || 0;

        var net_pay = (parseFloat(total_cola) + parseFloat(holiday)) - (parseFloat(emp_con_sss) + parseFloat(emp_con_phic) + parseFloat(emp_con_pagibig) + parseFloat(late_total) + parseFloat(tax_withheld) + parseFloat(ca_deducted));
        $("input.net_pay").val(commaSeparateNumber(net_pay.toFixed(2)));

        var employee_salary = total_cola - late_total;

        $("input.total_salary").val(commaSeparateNumber(employee_salary.toFixed(2)));

        var account_payable = employee_salary - total_emp_share;
        $("input.account_payable").val(commaSeparateNumber(account_payable.toFixed(2)));

        var total_debit = employee_salary + total_employer_share;
        $("td.total_debit").html(commaSeparateNumber(total_debit.toFixed(2)));

        var total_credit = account_payable + ee_er;
        $("td.total_credit").html(commaSeparateNumber(total_credit.toFixed(2)));
    });  
});
</script>

<style type="text/css">
section.payroll-section{
    margin: 15px 15px 0;
    max-width: 1024px;
    padding-bottom: 15px;
}
section.payroll-section .col-lg-12{
    background-color: #fff;
    padding: 15px;
}
/* section.payroll-section .col-md-2{
    padding-right: 5px;
    padding-left: 5px;
} */
.input-row{
    padding: 5px 0;
}
.divider{
    background-color: #3c8dbc;
    text-align: left;
    color: #fff;
    padding: 5px 0 0 10px;
    margin: 10px 0 5px;
    text-transform: uppercase;
}
.btn-border{
    margin: 10px 0 0;
    border-top: 1px solid #3c8dbc;
    padding-top: 15px;
}
.account-details-section{
    margin-top: 20px;
}
th.debit, th.credit{text-align: center;}
td.debit input, td.credit input, td.debit, td.credit{text-align: right;}
input.ca_deducted{
    text-align: right;
}
input.ca_deducted{
    width: 75%;
    display: inline;
}
</style>

@endsection