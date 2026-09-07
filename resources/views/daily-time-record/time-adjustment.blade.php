@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('header_style_postload')
<link rel="stylesheet" href="{{ url('/') }}/assets/plugins/iCheck/square/blue.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Time Adjustment</h1>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ url('daily-time-record/dtr-details', $get_dtr->e_id) }}"> Back</a>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-customer alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <p>{{ $message }}</p>
    </div>
    @endif

    @if ($message = Session::get('warning'))
    <div class="alert alert-warning alert-customer alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <p>{{ $message }}</p>
    </div>
    @endif

    <section class="content">
        {!! Form::open(array('url' => 'daily-time-record/save-time-adjustment','method'=>'GET')) !!}
        <input type="hidden" name="id" value="{{ $get_dtr->id }}" required/>
        <div class="row">
            <div class="col-md-6 dtr-time-adjustment">
                <div class="row">
                    <div class="col-md-3 col-sm-12">
                        <label>Start Time:</label>
                        <input type="text" name="start_time" value="{{ $get_dtr->start_time }}" class="form-control timepicker" placeholder="Enter the start time" autocomplete="off" required/>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <label>Min. Hours/Day:</label>
                        <input type="text" name="min_hours" value="{{ $get_dtr->min_hours }}" class="form-control" placeholder="Enter the start time" autocomplete="off" required/>
                    </div>
                </div>
                <div class="row">
                    <hr/>
                </div>
                <div class="row">
                    <div class="col-md-3 col-sm-12">
                        <label>Time In:</label>
                        <input type="text" name="time_in" value="{{ date('g:i A', strtotime($get_dtr->time_in)) }}" class="form-control timepicker" placeholder="Enter Time In" required/>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <label>Date In:</label>
                        <input type="text" name="date_in" value="{{ date('Y-m-d', strtotime($get_dtr->time_in)) }}" class="form-control datepicker" placeholder="Enter Date In" required/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-sm-12">
                        <label>Time Out:</label>
                        <input type="text" name="time_out" value="@if($get_dtr->time_out == '0000-00-00 00:00:00') @else {{ date('g:i A', strtotime($get_dtr->time_out)) }} @endif"  class="form-control timepicker" placeholder="Enter Time Out" required/>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <label>Date Out:</label>
                        <input type="text" name="date_out" value="@if($get_dtr->time_out == '0000-00-00 00:00:00') @else {{ date('Y-m-d', strtotime($get_dtr->time_out)) }} @endif" class="form-control datepicker" placeholder="Enter Date Out" required/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <label>Notes:</label>
                        <input type="text" name="notes" class="form-control" placeholder="Optional only" data-lpignore="true"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </section>
</div>

@endsection


@section('footer_script_preload')
<script src="{{ url('/') }}/assets/plugins/iCheck/icheck.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
@endsection

@section('footer_script')
<script>
$(document).ready(function(){
    $('.datepicker').datepicker({
        autoclose: true,
        todayBtn: "linked",
        todayHighlight: true,
        format: 'yyyy-mm-dd'
    }).datepicker();

    $('.timepicker').timepicker({
    timeFormat: 'h:mm p',
    interval: 1,
    minTime: '24',
    maxTime: '11:59pm',
    dynamic: false,
    dropdown: true,
    scrollbar: true
    });
});
</script>

<style type="text/css">
.dtr-time-adjustment{
    background-color: #fff;
    padding: 20px;
    margin: 15px;
}
.dtr-time-adjustment .col-sm-12{
    margin-bottom: 10px;
}
</style>

@endsection
