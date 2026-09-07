@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('header_style_preload')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Payroll Details Report</h1>
            </div>
        </div>
        <div style="clear: both; margin-bottom: 20px;"></div>
        <div class="col-lg-6 payroll-details">
            <h4>View Report by Employee's Name</h4>
            {!! Form::open(array('url' => 'payroll/payroll-details','method'=>'POST')) !!}
            <input type="text" name="search_emp_name" class="search-emp-name form-control" placeholder="Search Employee Name"/>
            <button type="submit" class="btn btn-primary">Search</button>
            {!! Form::close() !!}
        </div>
        <div class="col-lg-6 payroll-details">
            <h4>View Report by Date Range</h4>
            {!! Form::open(array('url' => 'payroll/payroll-details','method'=>'POST')) !!}
            <input type="text" name="date_from" class="date-search form-control datepicker" placeholder="Date From:" autocomplete="off"/>
            <input type="text" name="date_to" class="date-search form-control datepicker" placeholder="Date To:" autocomplete="off"/>
            <button type="submit" class="btn btn-primary">Search</button>
            {!! Form::close() !!}
        </div>
        </section>
    </div>
        
        <div class="alert alert-warning alert-employee warning-msg-false" style="display: none;">
            <p>No Employee selected successfully</p>
        </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-employee warning-msg-true">
            <p>{{ $message }}</p>
        </div>
    @endif

    <section class="content">
        {{-- <!--{!! Form::model($employees_payrolls, ['method' => 'PATCH', 'url' => ['payroll/update'],'class' => '']) !!}--> --}}
        <div class="table-responsive">
       
            <table class="table table-bordered table-striped table-hover payroll-table" style="width:100%;">
                <tr>
                    <th rowspan="2" align="center">No.</th>
                    <th rowspan="2" align="center" class="ename">Name</th>
                    <th rowspan="2" align="center" class="tin-no">TIN</th>
                    <th rowspan="2" align="center">Status</th>
                    <th colspan="3" align="center">Salary</th>
                    <th colspan="3" align="center">Overtime</th>
                    <th rowspan="2" align="center">COLA</th>
                    <th rowspan="2" align="center">Total</th>
                    <th colspan="3" align="center">Employee's Contribution</th>
                    <th colspan="3" align="center">Late</th>
                    <th colspan="3" align="center">Employer's Contribution</th>
                    <th rowspan="2" align="center">Holiday</th>
                    <th rowspan="2" align="center">Tax Withheld</th>
                    <th rowspan="2" align="center">Net Pay</th>
                    <th rowspan="2" align="center">Date of Payroll</th>
                    <th rowspan="2" class="actions">Action</th>
                </tr>
                <tr>
                    <th>Days Present</th>
                    <th>Rate</th>
                    <th>Total</th>
                    <th>Overtime</th>
                    <th>Rate</th>
                    <th>Total</th>
                    <th>SSS</th>
                    <th>PHIC</th>
                    <th>PAG IBIG</th>
                    <th>Late</th>
                    <th>Rate</th>
                    <th>Total</th>
                    <th>SSS</th>
                    <th>PHIC</th>
                    <th>PAG IBIG</th>
                </tr>
			{{-- */$x=0;/* --}}
            @foreach ($employees_payrolls as $employees_payroll)
                <tr class="row_payroll">
                    <td>{{ ++$i }}</td>
                    <td><p class="emp_name">{{ $employees_payroll->employee_name }}</p></td>
                    <td><p class="tin-no">{{ $employees_payroll->tin_no }}</p></td>
                    <td><p class="status">{{ $employees_payroll->status }}</p></td>
                    <td class="td-right">@if(empty($employees_payroll->salary_days_present))@else {{ number_format($employees_payroll->salary_days_present) }} @endif</td>
                    <td class="td-right">@if(empty($employees_payroll->salary_rate))@else{{ number_format(floatval($employees_payroll->salary_rate), 2) }}@endif</td>                  
                    <td class="td-right"><strong>{{ $employees_payroll->salary_total }}</strong></td>
                    <td class="td-right">@if(empty($employees_payroll->overtime))@else{{ number_format($employees_payroll->overtime) }}@endif</td>
                    <td class="td-right">@if(empty($employees_payroll->overtime_rate))@else{{ number_format(floatval($employees_payroll->overtime_rate), 2) }}@endif</td>
                    <td class="td-right"><strong>@if(empty($employees_payroll->overtime_total))@else{{ number_format(floatval($employees_payroll->overtime_total), 2) }}@endif</strong></td>
                    <td class="td-right">@if(empty($employees_payroll->cola))@else{{ number_format(floatval($employees_payroll->cola), 2) }}@endif</td>
                    <td class="td-right"><strong>{{ $employees_payroll->total }}</strong></td>
                    <td class="td-right">@if(empty($employees_payroll->emp_con_sss))@else{{ number_format(floatval($employees_payroll->emp_con_sss), 2) }}@endif</td>
                    <td class="td-right">@if(empty($employees_payroll->emp_con_phic))@else{{ number_format(floatval($employees_payroll->emp_con_phic), 2) }}@endif</td>
                    <td class="td-right">@if(empty($employees_payroll->emp_con_pagibig))@else{{ number_format(floatval($employees_payroll->emp_con_pagibig), 2) }}@endif</td>
                    <td class="td-right">@if(empty($employees_payroll->late))@else{{ number_format($employees_payroll->late) }}@endif</td>
                    <td class="td-right">@if(empty($employees_payroll->late_rate))@else{{ number_format(floatval($employees_payroll->late_rate), 2) }}@endif</td>
                    <td class="td-right"><strong>@if(empty($employees_payroll->late_total))@else{{ number_format(floatval($employees_payroll->late_total), 2) }}@endif</strong></td>
                    <td class="td-right">@if(empty($employees_payroll->employer_con_sss))@else{{ number_format(floatval($employees_payroll->employer_con_sss), 2) }}@endif</td>
                    <td class="td-right">@if(empty($employees_payroll->employer_con_phic))@else{{ number_format(floatval($employees_payroll->employer_con_phic), 2) }}@endif</td>
                    <td class="td-right">@if(empty($employees_payroll->employer_con_pagibig))@else{{ number_format(floatval($employees_payroll->employer_con_pagibig), 2) }}@endif</td>
                    <td class="td-right">@if(empty($employees_payroll->holiday))@else{{ number_format(floatval($employees_payroll->holiday), 2) }}@endif</td>
                    <td class="td-right">@if(empty($employees_payroll->tax_withheld))@else{{ number_format(floatval($employees_payroll->tax_withheld), 2) }}@endif</td>
                    <td class="td-right"><strong>{{ $employees_payroll->net_pay }}</strong></td>
                    <td><label class="td-right date-payroll">{{ $employees_payroll->date }}</label></td>
                    <td>
                        <div class="action">
                            @if($employees_payroll->salary_method == 'D')
                                <a class="btn btn-primary btn-xs" href="{{ url('payroll/edit-daily-payroll', $employees_payroll->pay_id) }}"><i class="fa fa-pencil"></i></a>
                            @else
                                <a class="btn btn-primary btn-xs" href="{{ url('payroll/edit-monthly-payroll', $employees_payroll->pay_id) }}"><i class="fa fa-pencil"></i></a>
                            @endif
                            <a class="btn btn-danger btn-xs delete-payroll" href="{{ route('payroll.get.destroy', $employees_payroll->pay_id) }}" onclick = 'return confirm("Are you sure to remove this payroll record?")'><i class="fa fa-trash"></i></a>
                        </div>
                    </td>
            </tr>
				{{-- */$x++;/* --}}
            @endforeach
            </table>
        </div>
        <div class="row" style="margin-right: 0px; margin-left: 0px;">
            <div class="pull-left" style="margin-bottom: 20px; margin-top: 20px;">
             <a href="{{ route('payroll.index') }}" class="btn btn-primary">Back to create Payroll</a>
            </div>
            <div class="pull-right">
             {!! $employees_payrolls->render() !!}
            </div>
        </div>
        {{-- <!--{!! Form::close() !!}--> --}}
    </section>
</div>
@endsection

@section('footer_script_preload')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('footer_script')
<script type="text/javascript">

$(document).ready(function(){

    $("input").change(function() {

    var counter = $('.payroll-table tr').length;

    for (var i = 0; i < counter; i++) {
        
                var sal_days_pre = $("input[name='payrow["+i+"][salary_days_present]']").val();             
                var sal_rate = $("input[name='payrow["+i+"][salary_rate]']").val();
                var sal_total = parseFloat(sal_days_pre) * parseFloat(sal_rate);
                $("input[name='payrow["+i+"][salary_total]']").val(commaSeparateNumber(sal_total.toFixed(2)));

                var overtime = $("input[name='payrow["+i+"][overtime]']").val();             
                var overtime_rate = $("input[name='payrow["+i+"][overtime_rate]']").val();
                var overtime_total = parseFloat(overtime) * parseFloat(overtime_rate);
                $("input[name='payrow["+i+"][overtime_total]']").val(commaSeparateNumber(overtime_total.toFixed(2)));

                var cola = $("input[name='payrow["+i+"][cola]']").val();
                var total_cola = parseFloat(sal_total) + parseFloat(overtime_total) + parseFloat(cola);
                $("input[name='payrow["+i+"][total]']").val(commaSeparateNumber(total_cola.toFixed(2)));
                
                var late = $("input[name='payrow["+i+"][late]']").val();
                var late_rate = $("input[name='payrow["+i+"][late_rate]']").val();
                var late_total = parseFloat(late) * parseFloat(late_rate);
                $("input[name='payrow["+i+"][late_total]']").val(commaSeparateNumber(late_total.toFixed(2)));

                var emp_con_sss = $("input[name='payrow["+i+"][emp_con_sss]']").val();
                var emp_con_phic = $("input[name='payrow["+i+"][emp_con_phic]']").val();
                var emp_con_pagibig = $("input[name='payrow["+i+"][emp_con_pagibig]']").val();
                var tax_withheld = $("input[name='payrow["+i+"][tax_withheld]']").val();
                var holiday = $("input[name='payrow["+i+"][holiday]']").val();
                var net_pay = (parseFloat(total_cola) + parseFloat(holiday)) - (parseFloat(emp_con_sss) + parseFloat(emp_con_phic) + parseFloat(emp_con_pagibig) + parseFloat(late_total) + parseFloat(tax_withheld));
                $("input[name='payrow["+i+"][net_pay]']").val(commaSeparateNumber(net_pay.toFixed(2)));
        }
    });
});

function commaSeparateNumber(val){
    while (/(\d+)(\d{3})/.test(val.toString())){
      val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
    }
    return val;
  }

$(document).ready(function(){

    $('input.totals').attr('readonly', true);
    $('input.payroll_input').attr('readonly', true);

    var counter = $('.payroll-table tr').length;

    for (var i = 0; i < counter; i++) {

                var sal_days_pre = $("input[name='payrow["+i+"][salary_days_present]']").val() || 1;             
                var sal_rate = $("input[name='payrow["+i+"][salary_rate]']").val() || 1;
                var sal_total = parseFloat(sal_days_pre) * parseFloat(sal_rate);
                $("input[name='payrow["+i+"][salary_total]']").val(commaSeparateNumber(sal_total.toFixed(2)));

                var overtime = $("input[name='payrow["+i+"][overtime]']").val();             
                var overtime_rate = $("input[name='payrow["+i+"][overtime_rate]']").val();
                var overtime_total = parseFloat(overtime) * parseFloat(overtime_rate);
                $("input[name='payrow["+i+"][overtime_total]']").val(commaSeparateNumber(overtime_total.toFixed(2)));

                var cola = $("input[name='payrow["+i+"][cola]']").val();
                var total_cola = parseFloat(sal_total) + parseFloat(overtime_total) + parseFloat(cola);
                $("input[name='payrow["+i+"][total]']").val(commaSeparateNumber(total_cola.toFixed(2)));
                
                var late = $("input[name='payrow["+i+"][late]']").val();
                var late_rate = $("input[name='payrow["+i+"][late_rate]']").val();
                var late_total = parseFloat(late) * parseFloat(late_rate);
                $("input[name='payrow["+i+"][late_total]']").val(commaSeparateNumber(late_total.toFixed(2)));

                var emp_con_sss = $("input[name='payrow["+i+"][emp_con_sss]']").val();
                var emp_con_phic = $("input[name='payrow["+i+"][emp_con_phic]']").val();
                var emp_con_pagibig = $("input[name='payrow["+i+"][emp_con_pagibig]']").val();
                var tax_withheld = $("input[name='payrow["+i+"][tax_withheld]']").val();
                var holiday = $("input[name='payrow["+i+"][holiday]']").val();
                var net_pay = (parseFloat(total_cola) + parseFloat(holiday)) - (parseFloat(emp_con_sss) + parseFloat(emp_con_phic) + parseFloat(emp_con_pagibig) + parseFloat(late_total) + parseFloat(tax_withheld));
                $("input[name='payrow["+i+"][net_pay]']").val(commaSeparateNumber(net_pay.toFixed(2)));
        }
}); 

$('.datepicker').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
});

</script>
<style type="text/css">
tr.row_payroll td{
    text-align: unset;
}
.td-right{
    text-align: right!important;
}
</style>
@endsection