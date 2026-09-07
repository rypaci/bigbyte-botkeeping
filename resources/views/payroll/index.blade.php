@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('header_style_preload')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ url('/') }}/assets/plugins/iCheck/square/blue.css">
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Searching employee for creating payroll</h1>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if ($message = Session::get('warning'))
        <div class="alert alert-warning alert-employee">
            <p>{{ $message }}</p>
        </div>
    @endif
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-employee">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="row panel-body-payroll">
        <div class="panel with-nav-tabs panel-default">
            <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab1default" data-toggle="tab" class="daily">Daily Payroll</a></li>
                    <li><a href="#tab2default" data-toggle="tab" class="monthly">Monthly Payroll</a></li>
                    <li class="salary-methods"><h2 class="salary-method"><span>You Select Daily Payroll Method</span></h2></li>
                </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="tab1default">
                        <section class="content">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 search-employee">
                                    {!! Form::open(array('url' => 'payroll/create-daily-payroll','method'=>'GET')) !!}
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <label>Select Employee:</label>
                                            <select class="form-control" name="employee_id">
                                                <option>Select employee here</option>
                                                @foreach($employees as $employee)
                                                @if($employee->salary_method == 'D')
                                                <option value="{{$employee->id}}">{{$employee->employee_name}}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="salary_method" class="id" value="D">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Date From:</label>
                                            <input type="text" name="date_from" value="" class="form-control date_from datepicker" required autocomplete="off">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Date To:</label>
                                            <input type="text" name="date_to" value="" class="form-control date_to datepicker" required autocomplete="off">
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 25px;">Create a Payroll</button>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                        </section>
                    </div>
                    <div class="tab-pane fade" id="tab2default">
                        <section class="content">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 search-employee">
                                    {!! Form::open(array('url' => 'payroll/create-monthly-payroll','method'=>'GET')) !!}
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <label>Select Employee:</label>
                                            <select class="form-control" name="employee_id">
                                                <option>Select employee here</option>
                                                @foreach($employees as $employee)
                                                @if($employee->salary_method == 'M')
                                                    <option value="{{$employee->id}}">{{$employee->employee_name}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="salary_method" class="id" value="M">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Date From:</label>
                                            <input type="text" name="date_from" value="" class="form-control date_from datepicker" required autocomplete="off">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Date To:</label>
                                            <input type="text" name="date_to" value="" class="form-control date_to datepicker" required autocomplete="off">
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 25px;">Create a Payroll</button>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                        </section>
                    </div>
                </div>
                <div class="pull-right">
                    <a href="{{ url('/payroll/payroll-details') }}" class="btn btn-primary" title="View Payroll Details" style="margin: 25px;">View Payroll Details <i class="fa fa-list" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_script_preload')
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
}).datepicker();

$(document).ready(function(){
    $("a.daily").click(function() {
        var text = $("h2.salary-method").text();
        $("h2.salary-method").text(text.replace('Monthly', 'Daily')); 
    });
});
$(document).ready(function(){
    $("a.monthly").click(function() {
        var text = $("h2.salary-method").text();
        $("h2.salary-method").text(text.replace('Daily', 'Monthly')); 
    });
});
</script>

<style type="text/css">
.search-employee{
    padding-left: 5px;
}
li.salary-methods{
    width: 50%!important;
}
</style>

@endsection