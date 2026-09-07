@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection

@section('header_style_preload')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Create Cash Advance</h1>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('cash-advance.index') }}"> Back</a>
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

    {!! Form::open(array('route' => 'cash-advance.store','method'=>'POST')) !!}
    <section class="content">
        <input type="hidden" name="keys" value="ca" class="form-control" required>
        <div class="row ca">
            <div class="col-xs-12 col-sm-12 col-md-12 ca-input">
                <div class="form-group">
                    <div class="col-sm-2">
                        <strong>Employee Name:</strong>
                    </div>
                    <div class="col-sm-6">
                        <select class="form-control input-sm" name="e_id">
                            <option value="">Please Select Employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->employee_name }}</option>
                            @endforeach
                        </select> 
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 ca-input">
                <div class="form-group">
                    <div class="col-sm-2">
                    <strong>Date:</strong>
                    </div>
                    <div class="col-sm-6">
                        <input type="text" name="date" class="form-control datepicker ca-datepicker" required autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 ca-input">
                <div class="form-group">
                    <div class="col-sm-2">
                    <strong>Cash Amount:</strong>
                    </div>
                    <div class="col-sm-6">
                    {!! Form::text('ca_amount', null, array('placeholder' => 'Cash Amount','class' => 'form-control ca-amount')) !!}
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 ca-input">
                <div class="form-group">
                    <div class="col-sm-2">
                        <strong>CA Description:</strong>
                    </div>
                    <div class="col-sm-6">
                        {!! Form::text('ca_description', null, array('placeholder' => 'CA Description','class' => 'form-control')) !!}
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 ca-input">
                <div class="form-group">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-primary">Save Cash Advance</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
    {!! Form::close() !!}

@endsection

@section('footer_script_preload')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('footer_script')
<script>
    $('.datepicker').datepicker({
        autoclose: true,
        todayBtn: "linked",
        todayHighlight: true,
        format: 'yyyy-mm-dd'
    }).datepicker('update', new Date());

    /* Trigger change to fill in Account # */
    // $('select.chart-account-dropdown, select.tax-dropdown').trigger('change');

    /*var validation_url = '{{url("water-refilling-monitoring/add-form-validate")}}';

    $(".withholding_tax").prop('selectedIndex', 1);*/
</script>
@endsection

<style type="text/css">
.ca{
    margin: 0!important;
    background-color: #fff;
    padding: 30px 0 20px;
}
.ca-input{
    margin-bottom: 10px;
}
input.ca-amount, input.ca-datepicker{
    max-width: 200px;
}
</style>