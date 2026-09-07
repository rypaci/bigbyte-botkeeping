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
                <h1>Product's sales details</h1>
                <h3>Product: {{$product->id}} - {{$product->name}}</h3>
            </div>
        </div>
        <div style="clear: both; margin-bottom: 20px;"></div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-employee warning-msg-true">
            <p>{{ $message }}</p>
        </div>
    @endif

    <section class="content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover payroll-table" style="width:100%;">
                <tr>
                    <th style="text-align: right;">Date</th>
                    <th style="text-align: right;">Qty Sold</th>
                    <th style="text-align: right;">Running Total Qty Sold</th>
                    <th style="text-align: right;">Amount</th>
                    <th style="text-align: right;">Running Total Amount</th>
                </tr>

                {{--*/
                    $total_amount = 0;
                    $total_sold = 0;
                /*--}}
                @foreach($product_detail_sales as $data)
                {{--*/
                    $total_amount = $total_amount + $data->amount;
                    $total_sold = $total_sold + $data->qty;
                /*--}}
                <tr>
                    <td align="right">{{ date('m-d-Y', strtotime($data->date)) }}</td>
                    <td align="right">{{ $data->qty }}</td>
                    <td align="right" style="font-weight: bold;">{{ $total_sold }}</td>
                    <td align="right">{{ number_format($data->amount, 2) }}</td>
                    <td align="right" style="font-weight: bold;">{{ number_format($total_amount, 2) }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        <div class="row" style="margin-right: 0px; margin-left: 0px;">
            <div class="pull-left" style="margin-bottom: 20px; margin-top: 20px;">
             <a href="{{ url('point-of-sale/sales-reports') }}" class="btn btn-primary">Back to Sales Report</a>
            </div>
            <div class="pull-right">
             {{-- {!! $salesReports->render(); !!} --}}
             {{-- {!! $pos_reports->appends(['date_from' => $start, 'date_to' => $end, 'viewstatus' => $viewstatus, 'search_cust_name' => $search_cust_name])->render(); !!} --}}
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
    div.total-issued-bots ul{padding-left: 0;}
    div.total-issued-bots ul li{
        list-style: none;
        font-size: 22px;
    }
    li.total-bots-issued{border-top: 1px solid #000;}
    div.payroll-details{width: 30%;}
    input.date-search{width: 36.5%; margin-right: 5px;}
    input.search-emp-name{width: 75%; margin-right: 5px;}
    select.view-all-paid-unpaid{width: 75%; margin-right: 7px; float: left;}
    div.payroll-details button{width: 21%;}
    tr.overall-total{background-color: #f1f1f1!important;}
</style>
@endsection