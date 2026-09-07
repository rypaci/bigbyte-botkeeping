@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection
<?php
use Carbon\Carbon;
?>

@section('header_style_preload')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="content-wrapper">

    <section class="content-header">
        
        <div class="pull-left">
            <h1>Absent Details Report</h1>
            <h2>Account: {{ $employee->id }} - {{ $employee->employee_name }}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ url('daily-time-record/dtr-account-list') }}"> Back</a>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row search-dtr">
                    {!! Form::open(array('url' => 'daily-time-record/search-date-absent','method'=>'GET')) !!}
                    <input type="hidden" name="e_id" value="{{ $employee->id }}"/>
                    <div class="col-md-8 col-sm-12" style="padding-right: 0px;">
                        <div class="row">
                            <div class="col-md-2 col-sm-12">
                                <input type="text" name="date_from" value="{{$datefrom}}" class="form-control datepicker ca-date_from" placeholder="Date From:" autocomplete="off" required>
                            </div>
                            <div class="col-md-2 col-sm-12">
                                <input type="text" name="date_to" value="{{$dateto}}" class="form-control datepicker ca-date_to" placeholder="Date To:" autocomplete="off" required>
                            </div>
                            <div class="col-md-2 col-sm-12">
                                <button type="submit" class="btn btn-primary">Search By Date</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
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
                                <th>Date of absent</th>
                                <th>Absent No.</th>
                                <th>Remarks</th>
                                <th>Inputted By:</th>
                                <th class="actions">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        {{-- */
                            $x=0;
                        /* --}}
                        @foreach ($dtrabsentlists as $dtrabsent)
                            {{-- */
                                $x++;
                            /* --}}
                            <tr>
                                <td>{{ $x }}</td>
                                <td>{{ $dtrabsent->date }}</td>
                                <td>@if($dtrabsent->absent_no == '1') Whole day @else Half-day @endif</td>
                                <td>{{ $dtrabsent->remarks }}</td>
                                <td>{{ $dtrabsent->name }}</td>
                                <td>
                                    <a class="btn btn-success btn-xs" href="{{ url('daily-time-record/edit-absent', $dtrabsent->id) }}"> Edit</a>
                                    <a class="btn btn-danger btn-xs" href="{{ url('daily-time-record/delete-absent', $dtrabsent->id) }}" onclick = 'return confirm("Are you sure to delete this record?")'> Delete</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination"> {!! $dtrabsentlists->render() !!} </div>
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
    }).datepicker('update');

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