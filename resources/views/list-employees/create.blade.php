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
                <h1>Create New Employee</h1>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('list-employees.index') }}"> Back</a>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {!! Form::open(array('route' => 'list-employees.store','method'=>'POST')) !!}
    <section class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 employee-input">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>Name Of Employee:</strong>
            </div>
            <div class="col-sm-6">
                {!! Form::text('employee_name', null, array('placeholder' => 'Name Of Employee','class' => 'form-control')) !!}
            </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 employee-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>TIN:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('tin_no', null, array('placeholder' => 'TIN','class' => 'form-control')) !!}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 employee-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Address:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('address', null, array('placeholder' => 'Address','class' => 'form-control')) !!}
                </div>
            </div>
        </div>
        
        <div class="col-xs-12 col-sm-12 col-md-12 employee-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Birthday:</strong>
                </div>
                <div class="col-sm-6">
                    <!--<input type="text" name="date" class="form-control datepicker" required>-->
                    {!! Form::text('birthday', null, array('placeholder' => 'Birthday','class' => 'form-control datepicker')) !!}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 employee-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Sex:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::select('sex', [''=>'Select', 'Male'=>'Male', 'Female'=>'Female'], '', ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Status:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::select('status', [''=>'Select', 'Single'=>'Single', 'Married'=>'Married', 'Separated'=>'Separated', 'Divorced'=>'Divorced', 'Widowed'=>'Widowed'], '', ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Dependents:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('dependents', null, array('placeholder' => 'Dependents','class' => 'form-control')) !!}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Salary Method:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::select('salary_method', ['S'=>'Select', 'D'=>'Daily', 'M'=>'Monthly'], '', ['class' => 'form-control salary_method']) !!}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-input daily-rate" style="display: none;">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Daily Rate:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('daily_rate', null, array('placeholder' => 'Daily Rate','class' => 'form-control daily_rate')) !!}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-input monthly-rate" style="display: none;">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Monthly Rate:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('monthly_rate', null, array('placeholder' => 'Monthly Rate','class' => 'form-control monthly_rate')) !!}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-input absent-rate" style="display: none;">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Absent Rate:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('absent_rate', null, array('placeholder' => 'Absent Rate','class' => 'form-control absent_rate')) !!}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Overtime Rate:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('overtime_rate', null, array('placeholder' => 'Overtime Rate','class' => 'form-control')) !!}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Late Rate:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('late_rate', null, array('placeholder' => 'Late Rate','class' => 'form-control')) !!}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Minimum Hours/Day:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('min_hours_per_day', null, array('placeholder' => 'Minimum Hours/Day','class' => 'form-control')) !!}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Employee Status:</strong>
                </div>
                <div class="col-sm-6">
                    {!! Form::select('employee_status', [''=>'Select Employee Status', 'Active'=>'Active', 'Inactive'=>'Inactive'], '', ['class' => 'form-control']) !!}
                <div class="col-xs-12 col-sm-12 col-md-12 text-left">
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
                </div>
            </div>
        </div>

    </div>
    </section>
</div>
    {!! Form::close() !!}

@endsection

@section('footer_script_preload')

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>

@endsection

@section('footer_script')

<script>

$( document ).ready(function() {
    $(".salary_method").change(function() {
    
    var zero = "0";

    if ($(".salary_method").val() == "D") {
        $(".daily-rate").fadeIn();
        $(".monthly-rate").hide();
        $(".absent-rate").hide();
        $("input.absent_rate").val(zero);
        $("input.monthly_rate").val(zero);
    }else if($(".salary_method").val() == "M") {
        $(".daily-rate").hide();
        $(".monthly-rate").fadeIn();
        $(".absent-rate").fadeIn();
        $("input.daily_rate").val(zero);
    }
    else{
        $(".daily-rate").hide(zero);
        $(".absent-rate").hide(zero);
        $(".monthly-rate").hide(zero);
    }

    });
});

    $('.datepicker').datepicker({
        autoclose: true,
        format: 'mm-dd-yyyy'
    });
</script>
@endsection