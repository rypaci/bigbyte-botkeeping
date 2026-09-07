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
                <h1>Issued Bottles Details</h1>
                <h3><strong>Customer:</strong> {{ $customer_info->first_name }} {{ $customer_info->middle_name }} {{ $customer_info->last_name }}{{ $customer_info->company_name }}</h3>
            </div>
        </div>
        {{-- <div style="clear: both; margin-bottom: 20px;"></div>
        <div class="col-lg-6 payroll-details">
            <h4>Track Issued Bottles by Date Range</h4>
            {!! Form::open(array('url' => 'wrm-issued-bottles/issued-bots-details','method'=>'POST')) !!}
            <input type="text" name="date_from" class="date-search form-control datepicker" placeholder="Date From:" autocomplete="off"/>
            <input type="text" name="date_to" class="date-search form-control datepicker" placeholder="Date To:" autocomplete="off"/>
            <button type="submit" class="btn btn-primary">Search</button>
            {!! Form::close() !!}
        </div> --}}
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-employee warning-msg-true">
            <p>{{ $message }}</p>
        </div>
    @endif

    <section class="content">
        <div class="table-responsive tableFixHead">
            <table class="table table-bordered table-striped table-hover payroll-table" style="display: inline-block;">
                <thead>
                    <tr>
                        <th align="center">List No.</th>
                        <th align="center">Entry No.</th>
                        <th align="center">Date</th>
                        <th align="center">No. of Issued bottles</th>
                        <th align="center">No. of Returned bottles</th>
                        <th align="center">Running Total of Issued Bottles</th>
                    </tr>
                </thead>
                {{--*/
                    $running_total_issued_bots = 0;
                    $total_issued_bots = 0;
                    $total_returned_bots = 0;
                /*--}}
                @foreach ($issued_bottles_details as $key => $value)
                {{--*/
                    $running_total_issued_bots = $running_total_issued_bots + ($value->order_qty - $value->return_bottle);
                    $total_issued_bots = $total_issued_bots + $value->order_qty;
                    $total_returned_bots = $total_returned_bots + $value->return_bottle;
                /*--}}
                <tr>
                    <td align="center">{{ $key + 1 }}</td>
                    <td align="right">{{ $value->entry_no }}</td>
                    <td align="right">{{ $value->date }}</td>
                    <td align="right">{{ $value->order_qty }}</td>
                    <td align="right">{{ $value->return_bottle }}</td>
                    <td align="right">{{ $running_total_issued_bots }}</td>
                </tr>
                @endforeach
                <tr>
                    <td align="right" colspan="3"><strong>Total:</strong></td>
                    <td align="right"><strong>{{ $total_issued_bots }}</strong></td>
                    <td align="right"><strong>{{ $total_returned_bots }}</strong></td>
                    <td align="right"><strong>{{ $running_total_issued_bots }}</strong></td>
                </tr>
            </table>
            <h3>Total Issued Bottles: <strong>{{ $running_total_issued_bots }}</strong></h3>
        </div>
        <div class="row" style="margin-right: 0px; margin-left: 0px;">
            <div class="pull-left" style="margin-bottom: 20px; margin-top: 20px;">
                <a href="{{ route('wrm-issued-bottles.index') }}" class="btn btn-primary">Back to issued bottles summary</a>
            </div>
            <div class="pull-right">
                {{-- {!! $issued_bottles_details->render() !!} --}}
            </div>
        </div>
    </section>
</div>
@endsection

@section('footer_script_preload')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('footer_script')
<script type="text/javascript">
    $('.datepicker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
    });
</script>

<style type="text/css">
    div.payroll-details h4{
        width: 265px;
    }
    .tableFixHead thead th { position: sticky; top: 0; }
    /* Just common table stuff. Really. */
    table  { border-collapse: collapse; width: 100%; overflow-y: auto; height: 500px;}
    th, td { padding: 8px 16px; }
    th{background-color: #eee}
</style>
@endsection