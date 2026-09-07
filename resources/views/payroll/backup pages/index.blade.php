@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Payroll <a href="{{ route('list-employees.index') }}" class="btn btn-primary btn-xs" title="Add New Payroll"><i class="fa fa-plus" aria-hidden="true"></i></a></h1>
            </div>
            <div class="pull-right">
                <a href="{{ url('/payroll/payroll-details') }}" class="btn btn-primary" title="View Payroll Details">View Payroll Details <i class="fa fa-list" aria-hidden="true"></i></a>
            </div>
        </div>
        <div style="clear: both;"></div>
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

<div class="row panel-body-payroll">
    <div class="panel with-nav-tabs panel-default">
        <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab1default" data-toggle="tab" class="daily">Daily</a></li>
                    <li><a href="#tab2default" data-toggle="tab" class="monthly">Monthly</a></li>
                    <li class="salary-methods"><h2 class="salary-method"><span>Daily Salary</span></h2></li>
                </ul>
        </div>
    <div class="panel-body">
    <div class="tab-content">
        <div class="tab-pane fade in active" id="tab1default">
        <section class="content">
        {!! Form::model($employees_payrolls, ['method' => 'PATCH', 'url' => ['payroll/update'],'class' => '']) !!}
        <div class="table-responsive">
       
            <table class="table table-bordered table-striped table-hover payroll-table" style="width:100%;">
                <tr>
                    <th rowspan="2" align="center">Select</th>
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
                    <th rowspan="2" align="center">Net Pay</th>
                    <th rowspan="2" align="center">Tax Withheld</th>
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
            @if ($employees_payroll->salary_method == "D")
                <tr class="row_payroll">
                    <td>{{ Form::checkbox('payrow['.$x.'][e_id]', $employees_payroll->e_id, null, ['class' => 'emp_ids']) }}</td>
                    <td>{{ ++$i }}</td>
                    <td><p class="emp_name">{{ $employees_payroll->employee_name }}</p></td>
                    <td><p class="tin-no">{{ $employees_payroll->tin_no }}</p></td>
                    <td><p class="status">{{ $employees_payroll->status }}</p></td>
                    <td>{!! Form::text('payrow['.$x.'][salary_days_present]', $employees_payroll->salary_days_present, array('placeholder' => 'Days Present','class' => 'form-control payroll_input sdp')) !!}</td>
                    <td>{!! Form::text('payrow['.$x.'][salary_rate]', $employees_payroll->daily_rate, array('placeholder' => 'Rate','class' => 'form-control payroll_input sal_rate')) !!}</td>
                    
                    {{-- */$salary_total = $employees_payroll->salary_days_present * $employees_payroll->salary_rate; /* --}}
                    <td>{!! Form::text('payrow['.$x.'][salary_total]', $salary_total, array('placeholder' => 'Total','class' => 'form-control payroll_input sal_total totals')) !!}</td>
                    
                    <td>{!! Form::text('payrow['.$x.'][overtime]', $employees_payroll->overtime, array('placeholder' => 'Overtime','class' => 'form-control payroll_input')) !!}</td>
                    <td>{!! Form::text('payrow['.$x.'][overtime_rate]', $employees_payroll->overtime_rate, array('placeholder' => 'Overtime Rate','class' => 'form-control payroll_input')) !!}</td>
                    
                    {{-- */$overtime_total = $employees_payroll->overtime * $employees_payroll->overtime_rate; /* --}}
                    <td>{!! Form::text('payrow['.$x.'][overtime_total]', $overtime_total, array('placeholder' => 'Total','class' => 'form-control payroll_input totals')) !!}</td>
                    
                    <td>{!! Form::text('payrow['.$x.'][cola]', $employees_payroll->cola, array('placeholder' => 'Cola','class' => 'form-control payroll_input')) !!}</td>
                    
                    {{-- */$total = $salary_total + $overtime_total + $employees_payroll->cola; /* --}}
                    <td>{!! Form::text('payrow['.$x.'][total]', $total, array('placeholder' => 'Total','class' => 'form-control payroll_input totals')) !!}</td>
                    
                    {{-- */$emp_con_sss = $employees_payroll->emp_con_sss; /* --}}
                    <td>{!! Form::text('payrow['.$x.'][emp_con_sss]', $employees_payroll->emp_con_sss, array('placeholder' => 'SSS','class' => 'form-control payroll_input')) !!}</td>
                    
                    {{-- */$emp_con_phic = $employees_payroll->emp_con_phic; /* --}}
                    <td>{!! Form::text('payrow['.$x.'][emp_con_phic]', $employees_payroll->emp_con_phic, array('placeholder' => 'PHIC','class' => 'form-control payroll_input')) !!}</td>
                    
                    {{-- */$emp_con_pagibig = $employees_payroll->emp_con_pagibig; /* --}}
                    <td>{!! Form::text('payrow['.$x.'][emp_con_pagibig]', $employees_payroll->emp_con_pagibig, array('placeholder' => 'Pagibig','class' => 'form-control payroll_input')) !!}</td>
                    
                    <td>{!! Form::text('payrow['.$x.'][late]', $employees_payroll->late, array('placeholder' => 'Late','class' => 'form-control payroll_input')) !!}</td>
                    <td>{!! Form::text('payrow['.$x.'][late_rate]', $employees_payroll->late_rate, array('placeholder' => 'Rate','class' => 'form-control payroll_input')) !!}</td>
                    
                    {{-- */$late_total = $employees_payroll->late * $employees_payroll->late_rate; /* --}}
                    <td>{!! Form::text('payrow['.$x.'][late_total]', $late_total, array('placeholder' => 'Total','class' => 'form-control payroll_input totals')) !!}</td>
                    
                    <td>{!! Form::text('payrow['.$x.'][employer_con_sss]', $employees_payroll->employer_con_sss, array('placeholder' => 'SSS','class' => 'form-control payroll_input')) !!}</td>
                    <td>{!! Form::text('payrow['.$x.'][employer_con_phic]', $employees_payroll->employer_con_phic, array('placeholder' => 'PHIC','class' => 'form-control payroll_input')) !!}</td>
                    <td>{!! Form::text('payrow['.$x.'][employer_con_pagibig]', $employees_payroll->employer_con_pagibig, array('placeholder' => 'Pagibig','class' => 'form-control payroll_input')) !!}</td>
                    
                    <td>{!! Form::text('payrow['.$x.'][holiday]', $employees_payroll->holiday, array('placeholder' => 'Holiday','class' => 'form-control payroll_input')) !!}</td>

                    {{-- */$tax_withheld = $employees_payroll->tax_withheld; /* --}}
                    {{-- */$net_pay = ($total) - ($emp_con_sss + $emp_con_phic + $emp_con_pagibig + $late_total + $tax_withheld) /* --}}
                    <td>{!! Form::text('payrow['.$x.'][net_pay]', $net_pay, array('placeholder' => 'Net Pay','class' => 'form-control payroll_input net-pay-only totals')) !!}</td>
                    
                    <td>{!! Form::text('payrow['.$x.'][tax_withheld]', $employees_payroll->tax_withheld, array('placeholder' => 'Tax Withheld','class' => 'form-control payroll_input')) !!}</td>
                    <td style="display: none;">{!! Form::text('payrow['.$x.'][pay_id]', $employees_payroll->pay_id, array('class' => 'form-control payroll_input')) !!}</td>
                    <td>
                    <div class="action">
                        <a class="btn btn-danger btn-xs delete-payroll" href="{{ route('payroll.get.destroy', $employees_payroll->pay_id) }}" onclick = 'return confirm("Are you sure to remove this employee from the payroll?")'><i class="fa fa-trash"></i></a>
                    </div>
                    </td>
            </tr>
            {!! Form::hidden('payrow['.$x.'][salary_method]', $employees_payroll->salary_method, array('id' => 'invisible_id')) !!}
            @endif
                {{-- */$x++;/* --}}
            @endforeach
            </table>
        </div>
        <div class="row" style="margin-right: 0px; margin-left: 0px;">
            <div class="pull-left" style="margin-bottom: 20px; margin-top: 20px;">
             <button type="submit" class="btn btn-primary update-payroll">Save Payroll</button>
            </div>
            <div class="pull-right">
             {!! $employees_payrolls->render() !!}
            </div>
        </div>
        {!! Form::close() !!}
    </section>
    </div>
    <div class="tab-pane fade" id="tab2default">
        <section class="content">
        {!! Form::model($employees_payrolls, ['method' => 'PATCH', 'url' => ['payroll/update'],'class' => '']) !!}
        <div class="table-responsive">
       
            <table class="table table-bordered table-striped table-hover payroll-table" style="width:100%;">
                <tr>
                    <th rowspan="2" align="center">Select</th>
                    <th rowspan="2" align="center">No.</th>
                    <th rowspan="2" align="center" class="ename">Name</th>
                    <th rowspan="2" align="center" class="tin-no">TIN</th>
                    <th rowspan="2" align="center">Status</th>
                    <th colspan="2" align="center">Salary</th>
                    <th colspan="3" align="center">Overtime</th>
                    <th rowspan="2" align="center">COLA</th>
                    <th rowspan="2" align="center">Total</th>
                    <th colspan="3" align="center">Employee's Contribution</th>
                    <th colspan="3" align="center">Late</th>
                    <th colspan="3" align="center">Employer's Contribution</th>
                    <th colspan="3" align="center">Absent</th>
                    <th rowspan="2" align="center">Holiday</th>
                    <th rowspan="2" align="center">Net Pay</th>
                    <th rowspan="2" align="center">Tax Withheld</th>
                    <th rowspan="2" class="actions">Action</th>
                </tr>
                <tr>
                    <th>Monthly Rate</th>
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
                    <th>No. of days absent</th>
                    <th>Rate</th>
                    <th>Total</th>
                </tr>
            {{-- */$x=0;/* --}}

            @foreach ($employees_payrolls as $employees_payroll)
            @if ($employees_payroll->salary_method == "M")
                <tr class="row_payroll">
                    <td>{{ Form::checkbox('payrow['.$x.'][e_id]', $employees_payroll->e_id, null, ['class' => 'emp_ids']) }}</td>
                    <td>{{ ++$i }}</td>
                    <td><p class="emp_name">{{ $employees_payroll->employee_name }}</p></td>
                    <td><p class="tin-no">{{ $employees_payroll->tin_no }}</p></td>
                    <td><p class="status">{{ $employees_payroll->status }}</p></td>
                    <td>{!! Form::text('payrow['.$x.'][salary_rate]', $employees_payroll->monthly_rate, array('placeholder' => 'Rate','class' => 'form-control payroll_input sal_rate')) !!}</td>
                    {{-- */$salary_total = $employees_payroll->salary_rate; /* --}}
                    <td>{!! Form::text('payrow['.$x.'][salary_total]', $salary_total, array('placeholder' => 'Total','class' => 'form-control payroll_input sal_total totals')) !!}</td>
                    
                    <td>{!! Form::text('payrow['.$x.'][overtime]', $employees_payroll->overtime, array('placeholder' => 'Overtime','class' => 'form-control payroll_input')) !!}</td>
                    <td>{!! Form::text('payrow['.$x.'][overtime_rate]', $employees_payroll->overtime_rate, array('placeholder' => 'Overtime Rate','class' => 'form-control payroll_input')) !!}</td>
                    
                    {{-- */$overtime_total = $employees_payroll->overtime * $employees_payroll->overtime_rate; /* --}}
                    <td>{!! Form::text('payrow['.$x.'][overtime_total]', $overtime_total, array('placeholder' => 'Total','class' => 'form-control payroll_input totals')) !!}</td>
                    
                    <td>{!! Form::text('payrow['.$x.'][cola]', $employees_payroll->cola, array('placeholder' => 'Cola','class' => 'form-control payroll_input')) !!}</td>
                    
                    {{-- */$total = $salary_total + $overtime_total + $employees_payroll->cola; /* --}}
                    <td>{!! Form::text('payrow['.$x.'][total]', $total, array('placeholder' => 'Total','class' => 'form-control payroll_input totals')) !!}</td>
                    
                    {{-- */$emp_con_sss = $employees_payroll->emp_con_sss; /* --}}
                    <td>{!! Form::text('payrow['.$x.'][emp_con_sss]', $employees_payroll->emp_con_sss, array('placeholder' => 'SSS','class' => 'form-control payroll_input')) !!}</td>
                    
                    {{-- */$emp_con_phic = $employees_payroll->emp_con_phic; /* --}}
                    <td>{!! Form::text('payrow['.$x.'][emp_con_phic]', $employees_payroll->emp_con_phic, array('placeholder' => 'PHIC','class' => 'form-control payroll_input')) !!}</td>
                    
                    {{-- */$emp_con_pagibig = $employees_payroll->emp_con_pagibig; /* --}}
                    <td>{!! Form::text('payrow['.$x.'][emp_con_pagibig]', $employees_payroll->emp_con_pagibig, array('placeholder' => 'Pagibig','class' => 'form-control payroll_input')) !!}</td>
                    
                    <td>{!! Form::text('payrow['.$x.'][late]', $employees_payroll->late, array('placeholder' => 'Late','class' => 'form-control payroll_input')) !!}</td>
                    <td>{!! Form::text('payrow['.$x.'][late_rate]', $employees_payroll->late_rate, array('placeholder' => 'Rate','class' => 'form-control payroll_input')) !!}</td>
                    
                    {{-- */$late_total = $employees_payroll->late * $employees_payroll->late_rate; /* --}}
                    <td>{!! Form::text('payrow['.$x.'][late_total]', $late_total, array('placeholder' => 'Total','class' => 'form-control payroll_input totals')) !!}</td>
                    
                    <td>{!! Form::text('payrow['.$x.'][employer_con_sss]', $employees_payroll->employer_con_sss, array('placeholder' => 'SSS','class' => 'form-control payroll_input')) !!}</td>
                    <td>{!! Form::text('payrow['.$x.'][employer_con_phic]', $employees_payroll->employer_con_phic, array('placeholder' => 'PHIC','class' => 'form-control payroll_input')) !!}</td>
                    <td>{!! Form::text('payrow['.$x.'][employer_con_pagibig]', $employees_payroll->employer_con_pagibig, array('placeholder' => 'Pagibig','class' => 'form-control payroll_input')) !!}</td>
                    
                    <td>{!! Form::text('payrow['.$x.'][days_absent]', $employees_payroll->days_absent, array('placeholder' => 'No. of Days','class' => 'form-control payroll_input')) !!}</td>
                    <td>{!! Form::text('payrow['.$x.'][absent_rate]', $employees_payroll->absent_rate, array('placeholder' => 'Rate','class' => 'form-control payroll_input')) !!}</td>
                    
                    {{-- */ $absent_total = $employees_payroll->days_absent * $employees_payroll->absent_rate; /* --}}
                    <td>{!! Form::text('payrow['.$x.'][absent_total]', $absent_total, array('placeholder' => 'Total','class' => 'form-control payroll_input totals')) !!}</td>

                    <td>{!! Form::text('payrow['.$x.'][holiday]', $employees_payroll->holiday, array('placeholder' => 'Holiday','class' => 'form-control payroll_input')) !!}</td>

                    {{-- */$tax_withheld = $employees_payroll->tax_withheld; /* --}}
                    {{-- */$net_pay = ($total) - ($emp_con_sss + $emp_con_phic + $emp_con_pagibig + $late_total + $tax_withheld + $absent_total) /* --}}
                    <td>{!! Form::text('payrow['.$x.'][net_pay]', $net_pay, array('placeholder' => 'Net Pay','class' => 'form-control payroll_input net-pay-only totals')) !!}</td>
                    
                    <td>{!! Form::text('payrow['.$x.'][tax_withheld]', $employees_payroll->tax_withheld, array('placeholder' => 'Tax Withheld','class' => 'form-control payroll_input')) !!}</td>
                    <td style="display: none;">{!! Form::text('payrow['.$x.'][pay_id]', $employees_payroll->pay_id, array('class' => 'form-control payroll_input')) !!}</td>
                    <td>
                    <div class="action">
                        <a class="btn btn-danger btn-xs delete-payroll" href="{{ route('payroll.get.destroy', $employees_payroll->pay_id) }}" onclick = 'return confirm("Are you sure to remove this employee from the payroll?")'><i class="fa fa-trash"></i></a>
                    </div>
                    </td>
            </tr>
            {!! Form::hidden('payrow['.$x.'][salary_method]', $employees_payroll->salary_method, array('id' => 'invisible_id')) !!}
            @endif
                {{-- */$x++;/* --}}
            @endforeach
            </table>
            </div>
            <div class="row" style="margin-right: 0px; margin-left: 0px;">
                <div class="pull-left" style="margin-bottom: 20px; margin-top: 20px;">
                 <button type="submit" class="btn btn-primary update-payroll">Save Payroll</button>
                </div>
                <div class="pull-right">
                 {!! $employees_payrolls->render() !!}
                </div>
            </div>
            {!! Form::close() !!}
            </section>
            </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@section('footer_script')

<script type="text/javascript">
$(document).ready(function(){

    $("input.payroll_input").attr('disabled','disabled');
    //$("input.payroll_input").attr('readonly', true);
        $("input.emp_ids").change(function() {

        var counter = $('input[type="checkbox"]').length;
            for (var i = 0; i < counter; i++) {
                if ($("input[name='payrow["+i+"][e_id]']").is(':checked')) {
                    $("input[name='payrow["+i+"][salary_days_present]']").removeAttr('disabled');

                    $("input[name='payrow["+i+"][salary_rate]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][salary_rate]']").attr('readonly', true);
                    
                    $("input[name='payrow["+i+"][salary_total]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][overtime]']").removeAttr('disabled');

                    $("input[name='payrow["+i+"][overtime_rate]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][overtime_rate]']").attr('readonly', true);
                    
                    $("input[name='payrow["+i+"][overtime_total]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][cola]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][total]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][emp_con_sss]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][emp_con_phic]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][emp_con_pagibig]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][late]']").removeAttr('disabled');

                    $("input[name='payrow["+i+"][late_rate]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][late_rate]']").attr('readonly', true);
                    
                    $("input[name='payrow["+i+"][late_total]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][employer_con_sss]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][employer_con_phic]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][employer_con_pagibig]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][days_absent]']").removeAttr('disabled');

                    $("input[name='payrow["+i+"][absent_rate]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][absent_rate]']").attr('readonly', true);
                    
                    $("input[name='payrow["+i+"][absent_total]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][holiday]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][net_pay]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][tax_withheld]']").removeAttr('disabled');
                    $("input[name='payrow["+i+"][pay_id]']").removeAttr('disabled');
                }else{
                    $("input[name='payrow["+i+"][salary_days_present]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][salary_rate]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][salary_total]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][overtime]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][overtime_rate]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][overtime_total]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][cola]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][total]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][emp_con_sss]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][emp_con_phic]']").attr('disabled','disabled'); 
                    $("input[name='payrow["+i+"][emp_con_pagibig]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][late]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][late_rate]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][late_total]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][employer_con_sss]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][employer_con_phic]']").attr('disabled','disabled');   
                    $("input[name='payrow["+i+"][employer_con_pagibig]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][days_absent]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][absent_rate]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][absent_total]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][holiday]']").attr('disabled','disabled');  
                    $("input[name='payrow["+i+"][net_pay]']").attr('disabled','disabled');   
                    $("input[name='payrow["+i+"][tax_withheld]']").attr('disabled','disabled');
                    $("input[name='payrow["+i+"][pay_id]']").attr('disabled','disabled');      
                }
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
    $("a.daily").click(function() {
        //$("h2.salary-method").replaceWith("<span>Daily Salary Method</span>");
        var text = $("h2.salary-method").text();
        $("h2.salary-method").text(text.replace('Monthly', 'Daily')); 
    });
});
$(document).ready(function(){
    $("a.monthly").click(function() {
        //$("h2.salary-method").replaceWith("<span>Monthly Salary Method</span>");
        var text = $("h2.salary-method").text();
        $("h2.salary-method").text(text.replace('Daily', 'Monthly')); 
    });
});

$(document).ready(function(){
    $("a.daily").click(function() {
        $("input").change(function() {
            var counter = $('input[type="checkbox"]').length;
            for (var i = 0; i < counter; i++) {
                if ($("input[name='payrow["+i+"][e_id]']").is(':checked')) {

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

                var absent = $("input[name='payrow["+i+"][days_absent]']").val();
                var absent_rate = $("input[name='payrow["+i+"][absent_rate]']").val();
                var absent_total = parseFloat(absent) * parseFloat(absent_rate);
                $("input[name='payrow["+i+"][absent_total]']").val(commaSeparateNumber(absent_total.toFixed(2)));

                var emp_con_sss = $("input[name='payrow["+i+"][emp_con_sss]']").val();
                var emp_con_phic = $("input[name='payrow["+i+"][emp_con_phic]']").val();
                var emp_con_pagibig = $("input[name='payrow["+i+"][emp_con_pagibig]']").val();
                var tax_withheld = $("input[name='payrow["+i+"][tax_withheld]']").val();
                var holiday = $("input[name='payrow["+i+"][holiday]']").val();
                var net_pay = (parseFloat(total_cola) + parseFloat(holiday)) - (parseFloat(emp_con_sss) + parseFloat(emp_con_phic) + parseFloat(emp_con_pagibig) + parseFloat(late_total) + parseFloat(tax_withheld));
                $("input[name='payrow["+i+"][net_pay]']").val(commaSeparateNumber(net_pay.toFixed(2)));

                }
            }
        });
    });
});

$(document).ready(function(){
    $("a.monthly").click(function() {
        $("input").change(function() {
            var counter = $('input[type="checkbox"]').length;
            for (var i = 0; i < counter; i++) {
                if ($("input[name='payrow["+i+"][e_id]']").is(':checked')) {
                    var sal_rate = $("input[name='payrow["+i+"][salary_rate]']").val();
                    var sal_total = parseFloat(sal_rate);
                    $("input[name='payrow["+i+"][salary_total]']").val(commaSeparateNumber(sal_total.toFixed(2)));

                    var overtime = $("input[name='payrow["+i+"][overtime]']").val();             
                    var overtime_rate = $("input[name='payrow["+i+"][overtime_rate]']").val();
                    var overtime_total = parseFloat(overtime) * parseFloat(overtime_rate);
                    $("input[name='payrow["+i+"][overtime_total]']").val(commaSeparateNumber(overtime_total.toFixed(2)));

                    var cola = $("input[name='payrow["+i+"][cola]']").val();
                    var total_cola = parseFloat(sal_total) + parseFloat(overtime_total) + parseFloat(cola);
                    $("input[name='payrow["+i+"][total]']").val(commaSeparateNumber(total_cola.toFixed(2)));

                    var absent = $("input[name='payrow["+i+"][days_absent]']").val();
                    var absent_rate = $("input[name='payrow["+i+"][absent_rate]']").val();
                    var absent_total = parseFloat(absent) * parseFloat(absent_rate);
                    $("input[name='payrow["+i+"][absent_total]']").val(commaSeparateNumber(absent_total.toFixed(2)));

                    var late = $("input[name='payrow["+i+"][late]']").val();
                    var late_rate = $("input[name='payrow["+i+"][late_rate]']").val();
                    var late_total = parseFloat(late) * parseFloat(late_rate);
                    $("input[name='payrow["+i+"][late_total]']").val(commaSeparateNumber(late_total.toFixed(2)));

                    var emp_con_sss = $("input[name='payrow["+i+"][emp_con_sss]']").val();
                    var emp_con_phic = $("input[name='payrow["+i+"][emp_con_phic]']").val();
                    var emp_con_pagibig = $("input[name='payrow["+i+"][emp_con_pagibig]']").val();
                    var tax_withheld = $("input[name='payrow["+i+"][tax_withheld]']").val();
                    var holiday = $("input[name='payrow["+i+"][holiday]']").val();
                    var net_pay = (parseFloat(total_cola) + parseFloat(holiday)) - (parseFloat(emp_con_sss) + parseFloat(emp_con_phic) + parseFloat(emp_con_pagibig) + parseFloat(late_total) + parseFloat(tax_withheld) + parseFloat(absent_total));
                    $("input[name='payrow["+i+"][net_pay]']").val(commaSeparateNumber(net_pay.toFixed(2)));
                }
            }
        });
    });
});

$(document).ready(function(){
    $("input").change(function() {
    var counter = $('input[type="checkbox"]').length;
    for (var i = 0; i < counter; i++) {
        
            if ($("input[name='payrow["+i+"][e_id]']").is(':checked')) {

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

                var absent = $("input[name='payrow["+i+"][days_absent]']").val();
                var absent_rate = $("input[name='payrow["+i+"][absent_rate]']").val();
                var absent_total = parseFloat(absent) * parseFloat(absent_rate);
                $("input[name='payrow["+i+"][absent_total]']").val(commaSeparateNumber(absent_total.toFixed(2)));

                var emp_con_sss = $("input[name='payrow["+i+"][emp_con_sss]']").val();
                var emp_con_phic = $("input[name='payrow["+i+"][emp_con_phic]']").val();
                var emp_con_pagibig = $("input[name='payrow["+i+"][emp_con_pagibig]']").val();
                var tax_withheld = $("input[name='payrow["+i+"][tax_withheld]']").val();
                var holiday = $("input[name='payrow["+i+"][holiday]']").val();
                var net_pay = (parseFloat(total_cola) + parseFloat(holiday)) - (parseFloat(emp_con_sss) + parseFloat(emp_con_phic) + parseFloat(emp_con_pagibig) + parseFloat(late_total) + parseFloat(tax_withheld));
                $("input[name='payrow["+i+"][net_pay]']").val(commaSeparateNumber(net_pay.toFixed(2)));
            }
        }
    });
});

$(document).ready(function(){

    $('input.totals').attr('readonly', true);

    var counter = $('input[type="checkbox"]').length;
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

$('.update-payroll').click(checkFunction);

    function checkFunction() {

                if ($('input[type="checkbox"]').is(':checked')){
                    $( ".warning-msg-false" ).hide( "slow");
                    return true;
                }else{
                    $( ".warning-msg-true" ).hide( "slow");
                    $( ".warning-msg-false" ).show( "slow");
                    return false;
                }
        }

</script>
@endsection