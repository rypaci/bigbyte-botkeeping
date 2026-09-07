@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection
<?php
use Carbon\Carbon;

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

?>

@section('header_style_preload')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="content-wrapper">

    <section class="content-header">
        
        <div class="pull-left">
            <h1>DTR Lunch/Break Details</h1>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ url('daily-time-record/dtr-details', $e_id->e_id) }}"> Back</a>
        </div>
        {{-- <div class="row">
            <div class="col-md-12">
                <div class="row search-dtr">
                    {!! Form::open(array('url' => 'daily-time-record/search-date-dtr-details','method'=>'GET')) !!}
                    <input type="hidden" name="id" value="{{ $employee->id }}"/>
                    <div class="col-md-8 col-sm-12" style="padding-right: 0px;">
                        <div class="row">
                            <div class="col-md-2 col-sm-12">
                                <input type="text" name="date_from" class="form-control datepicker ca-date_from" placeholder="Date From:" autocomplete="off" required>
                            </div>
                            <div class="col-md-2 col-sm-12">
                                <input type="text" name="date_to" class="form-control datepicker ca-date_to" placeholder="Date To:" autocomplete="off" required>
                            </div>
                            <div class="col-md-2 col-sm-12">
                                <button type="submit" class="btn btn-primary">Search By Date</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div> --}}
        <div style="clear: both;"></div>
        
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

    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row dtr-list">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Time Consumed</th>
                            </tr>
                        </thead>
                        <tbody>
                        {{-- */
                            $x=0;
                        /* --}}
                        @foreach ($lunch_break_recs as $lunch_break)
                            {{-- */
                                $x++;

                                $timein = new Carbon(date("Y-m-d H:i", strtotime($lunch_break->break_start)));
                                $timeout = new Carbon(date("Y-m-d H:i", strtotime($lunch_break->break_end)));
                                
                                $consumehours = $timein->diffInHours($timeout).':'.$timein->diff($timeout)->format('%I');
                            /* --}}
                            <tr>
                                <td>{{ $x }}</td>
                                <td align="right">{{ date('g:i A', strtotime($lunch_break->break_start)) }}</td>
                                <td align="right">{{ date('g:i A', strtotime($lunch_break->break_end)) }}</td>
                                <td align="right">{{ $consumehours }}</td>
                                </tr>
                                @endforeach
                        </tbody>
                    </table>
                    <div class="pagination"> {!! $lunch_break_recs->render() !!} </div>
                </div>
            </div>
        </div>
    </section>
</div>

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
    }).datepicker();

    /* Trigger change to fill in Account # */
    // $('select.chart-account-dropdown, select.tax-dropdown').trigger('change');

    /*var validation_url = '{{url("water-refilling-monitoring/add-form-validate")}}';

    $(".withholding_tax").prop('selectedIndex', 1);*/
</script>
@endsection

<style type="text/css">
.dtr-list{
    margin-right: 0!important;
    margin-left: 0!important;
}
.dtr-list .col-md-12{
    padding: 0px;
}
.dtr-list th{
    vertical-align: middle!important;
}
.red-text{
    color: #FD0017;
}
.no-password{
    float: right;
}
.table th{
    text-align: center;
}
.pagination{
    margin: 0px!important;
}
.search-dtr{
    margin-top: 20px;
}
span.red{
    color: #FF0C09
}
.search-dtr .col-md-2{
    padding-right: 0px;
}
</style>