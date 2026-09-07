@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection

@section('content')
<div class="content-wrapper">

    <section class="content-header">
        <div class="row">
            <div class="col-md-6">
                @include('_includes.message')
                <h1 style="color: #20292D; margin-top: 0;">Point of Sale Dashboard</h1>
            </div>
            <div class="col-md-6" style="text-align: right;">
                <a href="{{ url('/point-of-sale') }}" class="btn btn-primary">Refresh POS Dashboard</a>
            </div>
        </div>
    </section>
    
    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-employee warning-msg-true">
        <p>{{ $message }}</p>
    </div>
    @endif
    <!-- Main content -->
    <section class="search-container">
        
    </section>
    <section class="content">
    {{-- */
        $x=0;
    /* --}}
        <div class="row wfm-dashboard">
            <div class="row search-date">
                <div class="col-lg-5 dateRange-result">
                    <div class="row">
                        <div class="col-md-12">
                            @if(empty($startDate))
                            <h3>Date Range Result: N/A</h3>
                            @else
                            <h3>Date Range Result: {{ date('F d, Y', strtotime($startDate)) }} to {{date('F d, Y', strtotime($endDate))}}</h3>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-7 col-sm-12 dateRange">
                    {!! Form::open(array('url' => 'point-of-sale/search-date-range','method'=>'GET')) !!}
                        <span id="product_filter">{!! Form::select('product_id', $products, $productId, ['class' => 'form-control product-filter-select']) !!}</span>
                        <span id="date_range"><input type="text" name="date_range" class="form-control date-range-search" placeholder="Date Range"></span>
                        <button Class="btn btn-primary btn-search-date">
                            <i class="fa fa-search"></i> <span>SEARCH</span>
                        </button>
                    {!! Form::close() !!}
                </div>
            </div>

            <div class="col-lg-4">
                <div class="col-lg-12 non-tax-sales-today">
                    <h3>TOTAL NON-TAX SALES TODAY</h3>
                    <h2>{{ number_format($total_non_tax_sales_today, 2) }}</h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12 expenses-today">
                    <h3>TOTAL EXPENSES TODAY</h3>
                    <h2>{{ number_format($amount_expenses_today, 2) }}</h2>
                    <a href="{{ route('pos-expenses.create') }}" class="btn regpro-btn btn-primary">Add Expenses</a>
                    <a href="{{ url('pos-expenses/tracks-expenses') }}" class="btn regpro-btn btn-primary">Track Expenses</a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12 sap-filter" style="background-color: #FFA07A;">
                    <h3>NET INCOME</h3>
                    <h2>{{ number_format(floatval($over_all_sales - $over_all_no_OR_expenses), 2) }}</h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12" style="background-color: #750775">
                    <h3>CASH ON HAND</h3>
                    <h2>{{ number_format(floatval($over_all_sales) - ($over_all_no_OR_expenses + $customer_unpaid), 2) }}</h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12" style="background-color: #FDB8C5">
                    <h3>CUSTOMER OWE</h3>
                    <h2>{{ number_format(floatval($customer_unpaid), 2) }}</h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12" style="background-color: #14A65B">
                    <h3>NON-TAX SALES THIS MONTH</h3>
                    <h2>{{ number_format(floatval($over_all_sales), 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="trans-summary row">
            <div class="col-lg-4">
                <div class="col-md-12 summary-box">
                    <h4>Sales Transaction Summary</h4>
                    <table class="table table-hover payroll-table" style="width:100%;">
                        <tr class="data-row">
                            <th style="text-align: left;">Sales</th>
                            <th>Count</th>
                            <th style="text-align: right;">Amount</th>
                        </tr>
                        <tr class="data-row">
                            <td>Today</td>
                            <td align="center">@if(!empty($trans_count_today)){{$trans_count_today}}@else 0 @endif</td>
                            <td align="right">@if(!empty($trans_today)){{number_format(floatval($trans_today),2)}}@else 0.00 @endif</td>
                        </tr>
                        <tr class="data-row">
                            <td>Yesterday</td>
                            <td align="center">@if(!empty($trans_count_yesterday)){{$trans_count_yesterday}}@else 0 @endif</td>
                            <td align="right">@if(!empty($trans_yesterday)){{number_format(floatval($trans_yesterday),2)}}@else 0.00 @endif</td>
                        </tr>
                        <tr class="data-row">
                            <td>Week</td>
                            <td align="center">@if(!empty($trans_count_week)){{$trans_count_week}}@else 0 @endif</td>
                            <td align="right">@if(!empty($trans_week)){{number_format(floatval($trans_week),2)}}@else 0.00 @endif</td>
                        </tr>
                        <tr class="data-row">
                            <td>Month</td>
                            <td align="center">@if(!empty($trans_count_curr_month)){{$trans_count_curr_month}}@else 0 @endif</td>
                            <td align="right">@if(!empty($trans_curr_month)){{number_format(floatval($trans_curr_month),2)}}@else 0.00 @endif</td>
                        </tr>
                        <tr class="data-row">
                            <td>Year</td>
                            <td align="center">@if(!empty($trans_count_curr_year)){{$trans_count_curr_year}}@else 0 @endif</td>
                            <td align="right">@if(!empty($trans_curr_year)){{number_format(floatval($trans_curr_year),2)}}@else 0.00 @endif</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="col-md-12 summary-box">
                    <h4>Top 5 Selling Products</h4>
                    <table class="table table-hover payroll-table" style="width:100%;">
                        <tr class="data-row">
                            <th style="text-align: left;">Item Name</th>
                            <th>Qty</th>
                            <th style="text-align: right;">Amount</th>
                        </tr>
                        @foreach($top_5_products as $top5product)
                        <tr class="data-row">
                            @if(empty($top5product->sr_priority))
                                <td>{{$top5product->name}}</td>
                            @else
                                <td>{{$top5product->name}} - {{$top5product->sr_priority}}</td>
                            @endif
                            <td align="center">{{round($top5product->sold_qty)}}</td>
                            <td align="right">{{number_format(floatval($top5product->total_amount),2)}}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="trans-summary row" style="margin-top: 0px">
            <div class="col-lg-12">
                <div class="col-md-12 summary-box">
                    <h4>5 Recent Sales</h4>
                    <table class="table table-hover payroll-table" style="width:100%;">
                        <tr class="data-row">
                            <th style="text-align: left;">Item Id</th>
                            <th style="text-align: left;">Customer Name</th>
                            <th style="text-align: right;">Date</th>
                            <th>Items No.</th>
                            <th style="text-align: right;">Sales</th>
                            <th style="text-align: right;">Balanced</th>
                            <th style="text-align: right;">Status</th>
                        </tr>
                        @foreach($recent_5_sales as $recent_5_sale)
                        <tr class="data-row">
                            <td align="left">{{$recent_5_sale->id}}</td>
                            <td align="left">{{$recent_5_sale->company_name}}{{$recent_5_sale->first_name}} {{$recent_5_sale->middle_name}} {{$recent_5_sale->last_name}}</td>
                            <td align="right">{{$recent_5_sale->sales_date}}</td>
                            <td align="center">{{$recent_5_sale->total_item}}</td>
                            <td align="right">{{number_format(floatval($recent_5_sale->amount_due),2)}}</td>
                            <td align="right">{{number_format(floatval($recent_5_sale->amt_balance),2)}}</td>
                            <td align="right">{{$recent_5_sale->status}}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="trans-summary row" style="margin-bottom: 25px;">
            <div class="col-lg-12">
                <div class="col-md-12 summary-box">
                    <h4>Top 5 Customers</h4>
                    <table class="table table-hover payroll-table" style="width:100%;">
                        <tr class="data-row">
                            <th style="text-align: left;">Customer Name</th>
                            <th style="text-align: left;">Email</th>
                            <th>Sales Count</th>
                            <th style="text-align: right;">Total Amount</th>
                        </tr>
                        @foreach($top_5_customers as $top_5_customer)
                        <tr class="data-row">
                            <td align="left">{{$top_5_customer->company_name}}{{$top_5_customer->first_name}} {{$top_5_customer->middle_name}} {{$top_5_customer->last_name}}</td>
                            <td align="right">{{$top_5_customer->email}}</td>
                            <td align="center">{{$top_5_customer->total_sales_count}}</td>
                            <td align="right">{{number_format(floatval($top_5_customer->total_amount),2)}}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="outofStocks" tabindex="-1" role="dialog" aria-labelledby="outofStocksLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="outofStocksLabel">List of out of stock products or below stock threshold</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-hover outofstock-table" style="width:100%;">
                            <tr class="data-row">
                                <th style="text-align: left;">Product ID</th>
                                <th style="text-align: left;">Product Name</th>
                                <th style="text-align: right;">Remaining Qty</th>
                                <th style="text-align: right;">Stock threshold</th>
                            </tr>
                            @foreach($popup_data as $data)
                                {{-- */
                                    $remaining_stocks = $data->total_stock - $data->total_soldstock;
                                /* --}}
                                @if($remaining_stocks <= $data->stock_threshold || $remaining_stocks <= 0)
                                <tr class="data-row count">
                                    <td align="left">{{$data->prod_id}}</td>
                                    <td align="left">{{$data->name}}</td>
                                    <td align="right">{{$data->total_stock - $data->total_soldstock}}</td>
                                    <td align="right">{{$data->stock_threshold}}</td>
                                </tr>
                                @endif
                            @endforeach
                        </table>
                        {{-- <div class="pagination" style="margin:0;">{!! $popup_data->render(); !!}</div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#outofStocks">Add to Favorites</button> --}}
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
@endsection

@section('footer_script_preload')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endsection

@section('footer_script')

<style type="text/css">
    div.wfm-dashboard{
        margin-right:0; margin-left:0; border: 1px solid #A9A9A9; padding-bottom: 15px; padding-left: 15px; border-radius: 5px; padding-right: 15px;
    }
    div.wfm-dashboard h2{
        font-size: 50px;
    }
    div.wfm-dashboard div.col-lg-12{
    min-height: 210px;
    padding-bottom: 10px;
    max-height: 210px;
    padding-top: 1px;
    }
    div.search-date{
        padding: 15px 0;
        margin-right: auto!important;
        margin-left: auto!important;
        border: 1px solid #A9A9A9;
        margin-top: 20px;
        border-radius: 5px;
    }
    div.search-date .dateRange{
        float: right;
        text-align: right;
    }
    .search-container{
        margin-right: 0;
        margin-left: 0;
        padding: 15px;
    }
    .date-range-search{
        max-width: 350px;
        display: inline-block!important;
        vertical-align: middle;
        cursor: default;
        font-size: 18px;
    }
    .product-filter-select{
        max-width: 220px;
        display: inline-block!important;
        vertical-align: middle;
        margin-right: 10px;
    }
    .daterangepicker.ltr .ranges {
        float: right!important;
    }
    .daterangepicker.show-ranges .drp-calendar.left {
        /*border-left: 0px solid #ddd!important;*/
        border-left: 0px!important;
    }
    .daterangepicker.ltr .drp-calendar.right {
        border-right: 1px solid #ddd!important;
    }
    .dateRange-result h3{
        margin-top: 0;
        margin-bottom: 0;
        color: #667EA3;
    }
    .trans-summary{
        margin-right: auto!important;
        margin-left: auto!important;
        margin-top: 20px;
    }
    .trans-summary .col-lg-4, .trans-summary .col-lg-8{
        margin: 20px 0;
    }
    .summary-box{
        background-color: #fff;
        border: 1px solid #aaa;
        border-radius: 10px;
        overflow-x: auto;
        min-height: 284px;
    }
    .summary-box h4{
        color: #000;
        text-align: left;
    }
    table {
        border: 0px solid #f4f4f4;
        color: #000;
    }
    .data-row td, .data-row th{
        border: none!important;
        border-bottom: 1px solid #ccc!important;
    }
    .data-row th{
        border-top: 1px solid #ccc!important;
    }
    div.wfm-dashboard div.col-lg-4 {
        padding: 0px 8px;
    }
    
    /*Pop up css*/
    td.expired{
        color: red;
        font-weight: bold;
    }
    #outofStocksLabel{
        font-weight: bold;
    }
    .modal-body{
        overflow-x: auto;
    }

    @media (min-width: 768px){
        .modal-dialog {
            width: 1100px;
        }
    }
</style>

<script type="text/javascript">

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end){
        $('.date-range-search').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    $('#date_range').daterangepicker({
    "alwaysShowCalendars": true,
    "opens": "left",
    startDate: start,
    endDate: end,
    ranges: {
       'Today': [moment(), moment()],
       'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
       'Last 7 Days': [moment().subtract(6, 'days'), moment()],
       'Last 30 Days': [moment().subtract(29, 'days'), moment()],
       'This Month': [moment().startOf('month'), moment().endOf('month')],
       'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);
    cb(start, end);
    
    // Popup script code
    $(document).ready(function(){
        
        var rowCount = $('.outofstock-table tr.count').length;

        if(rowCount >= 1){
           $("#outofStocks").modal('show');
        }else{
            $("#outofStocks").modal('hide');
        }
    });
</script>
@endsection