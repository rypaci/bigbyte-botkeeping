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
                <h1 style="color: #20292D; margin-top: 0;">Water Refilling Dashboard</h1>
            </div>
            <div class="col-md-6" style="text-align: right;">
                <a href="{{ url('/water-refilling-monitoring') }}" class="btn btn-primary">Back Default to WRM Dashboard</a>
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
        <div class="row search-date">
            <div class="col-lg-7 dateRange-result">
                <div class="row">
                    <div class="col-lg-12">
                        @if(empty($startDate))
                        <h3>Date Range Result: N/A</h3>
                        @else
                        <h3>Date Range Result: {{ date('F d, Y', strtotime($startDate)) }} to {{date('F d, Y', strtotime($endDate))}}</h3>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-5 dateRange">
                {!! Form::open(array('url' => 'water-refilling-monitoring/search-date-range','method'=>'GET')) !!}
                    <span id="date_range"><input type="text" name="date_range" class="form-control date-range-search" placeholder="Date Range"></span>
                    <button Class="btn btn-primary btn-search-date">
                        <i class="fa fa-search"></i> <span>SEARCH</span>
                    </button>
                {!! Form::close() !!}
            </div>
        </div>
    </section>
    <section class="content">
    {{-- */
        $x=0;
        
        $total_bots = 0;
        $order_qty = 0;
        $refill_bots = 0;
        $container_bots = 0;
        $dealer_bots = 0;

        $total_non_tax_sales_today = 0;
        $amount_expenses_today = 0;

        $order_bots = 0;
        $issued_bots = 0;
        $return_bots = 0;
        $container_bots_qty = 0;

        $change_sap_filter = 0;

    /* --}}
    @foreach($bots_regenerates as $value)
        {{-- */
        $x++;
        $order_qty = $order_qty + $value->order_qty;
        $refill_bots = $refill_bots + $value->refill_bottle;
        $container_bots = $container_bots + $value->container_qty;
        $dealer_bots = $dealer_bots + $value->dealer_qty;

        $total_bots = $order_qty + $refill_bots + $container_bots +  $dealer_bots;
        /* --}}
    @endforeach
    
    @foreach($amount_due as $value)
    {{-- */
        $total_non_tax_sales_today = $total_non_tax_sales_today + $value->amount_due;
    /* --}}
    @endforeach

    @foreach($bottles_issued as $value)
    {{-- */
        $order_bots = $order_bots + $value->order_qty;
        $return_bots = $return_bots + $value->return_bottle;
        $container_bots_qty = $container_bots_qty + $value->container_qty;

        $issued_bots = $order_bots - $return_bots;
    /* --}}
    @endforeach

    @foreach($expenses_today as $value)
    {{-- */
        $amount_expenses_today = $amount_expenses_today + $value->amount;
    /* --}}
    @endforeach

    {{-- */
        $sum_bottle_issued = 0;
        $sum_bottle_issued = $sum_order_bottles - $sum_return_bottles;

        $bottles_onhand = 0;
        $bottles_onhand = ($sum_original_bottles) - ($sum_container_sold + $sum_damage_bottles + $sum_bottle_issued) 
    /* --}}
        <div class="row wfm-dashboard" style="margin-right:0; margin-left:0; border: 1px solid #A9A9A9; padding-bottom: 15px; padding-left: 15px; border-radius: 5px;">
            <div class="col-lg-4">
                <div class="col-lg-12 reg-proc">
                    <h3>REGENERATE PROCESS</h3>
                    <input type="hidden" name="total_bots_reg" value="{{ $total_bots }}">
                    @if (!empty($regenerate_settings))
                    <h2>{{ $total_bots }}/{{ $regenerate_settings->reg_bots_value }}</h2>
                    @else
                    <h2>{{ $total_bots }}/0</h2>
                    @endif
                    {!! Form::model($bots_regenerates, array('route' => 'water-refilling-monitoring.truncate','method'=>'DELETE')) !!}
                        <button type="submit"  class="btn regpro-btn btn-primary" id="reg_pro_btn" onclick = 'return confirm("Confirm regenerate bottles?")'>Reset</button>
                        <a href="{{ route('wrm-regenerate-settings.create') }}" class="btn regpro-btn btn-primary">Settings</a>
                    {!! Form::close()!!}
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
                    <h3>TOTAL NO OR EXPENSES TODAY</h3>
                    <h2>{{ number_format($amount_expenses_today, 2) }}</h2>
                    <a href="{{ route('wrmexpenses.create') }}" class="btn regpro-btn btn-primary">Add Expenses</a>
                    <a href="{{ url('wrmexpenses/tracks-expenses') }}" class="btn regpro-btn btn-primary">Track Expenses</a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12 sap-filter">
                    <h3>CHANGE SAP FILTER PURIFIED</h3>
                    <input type="hidden" name="bots_no" value="{{ $change_sap_filter }}">
                    @if (!empty($changesapfilter_settings))
                    <h2>{{ number_format($sum_purified_bottles->purified) }}/{{ $changesapfilter_settings->bots_no }}</h2>
                    @else
                    <h2>{{ number_format($sum_purified_bottles->purified) }}/0</h2>
                    @endif
                    {!! Form::model($bots_regenerates, array('route' => 'water-refilling-monitoring.reset','method'=>'DELETE')) !!}
                        <button type="submit"  class="btn regpro-btn btn-primary" id="reg_pro_btn" onclick = 'return confirm("Confirm change SAP Purified filter?")'>Reset</button>
                        <a href="{{ route('wrm-change-sap-filter.create') }}" class="btn regpro-btn btn-primary">Settings</a>
                    {!! Form::close()!!}
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12 sap-filter" style="background-color: #19C520;">
                    <h3>CHANGE SAP FILTER ALKALINE</h3>
                    <input type="hidden" name="bots_no" value="{{ $change_sap_filter }}">
                    @if (!empty($changesapfilteralkaline_settings))
                    <h2>{{ number_format($sum_alkaline_bottles->alkaline) }}/{{ $changesapfilteralkaline_settings->bots_no }}</h2>
                    @else
                    <h2>{{ number_format($sum_alkaline_bottles->alkaline) }}/0</h2>
                    @endif
                    {!! Form::model($bots_regenerates, array('route' => 'water-refilling-monitoring.resetalk','method'=>'DELETE')) !!}
                        <button type="submit"  class="btn regpro-btn btn-primary" id="reg_pro_btn" onclick = 'return confirm("Confirm change SAP Alkaline filter?")'>Reset</button>
                        <a href="{{ route('wrm-change-sap-alkaline-filter.create') }}" class="btn regpro-btn btn-primary">Settings</a>
                    {!! Form::close()!!}
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12 sap-filter" style="background-color: #8B0ABE;">
                    <h3>CHANGE SAP FILTER MINERAL</h3>
                    <input type="hidden" name="bots_no" value="{{ $change_sap_filter }}">
                    @if (!empty($changesapfiltermineral_settings))
                    <h2>{{ number_format($sum_mineral_bottles->mineral) }}/{{ $changesapfiltermineral_settings->bots_no }}</h2>
                    @else
                    <h2>{{ number_format($sum_mineral_bottles->mineral) }}/0</h2>
                    @endif
                    {!! Form::model($bots_regenerates, array('route' => 'water-refilling-monitoring.resetmin','method'=>'DELETE')) !!}
                        <button type="submit"  class="btn regpro-btn btn-primary" id="reg_pro_btn" onclick = 'return confirm("Confirm change 2 SAP filter?")'>Reset</button>
                        <a href="{{ route('wrm-change-sap-mineral-filter.create') }}" class="btn regpro-btn btn-primary">Settings</a>
                    {!! Form::close()!!}
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12 sap-filter" style="background-color: #FFA07A;">
                    <h3>NET INCOME</h3>
                    <h2>{{ number_format(floatval($over_all_sales) - ($over_all_no_OR_expenses + $ca_expenses[0]->ca_expenses), 2) }}</h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12 bottle-issued">
                    <h3>TOTAL BOTTLES ISSUED</h3>
                    <h2>{{ number_format($issued_bots) }}</h2>
                    <a href="{{ route('wrm-issued-bottles.index') }}" class="btn regpro-btn btn-primary">Track Issued Bottles</a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12 orig-bottle-qty">
                    <h3>ORIGINAL BOTTLE QTY</h3>
                    <h2>{{ number_format($sum_original_bottles) }}</h2>
                    <a href="{{ route('wrm-original-bottles.create') }}" class="btn regpro-btn btn-primary">Add Bottles</a>
                    <a href="{{ url('wrm-original-bottles/track-original-bottles') }}" class="btn regpro-btn btn-primary">Track Original Bottles</a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12 bottles-onhand">
                    <h3>BOTTLES ON HAND</h3>
                    @if($bottles_onhand < 0)
                        <h2>0</h2>
                    @else
                        <h2>{{ number_format($bottles_onhand) }}</h2>
                    @endif
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12 bottles-damage">
                    <h3>BOTTLE DAMAGE</h3>
                    <h2>{{ number_format($sum_damage_bottles) }}</h2>
                    <a href="{{ route('wrm-damage-bottles.create') }}" class="btn regpro-btn btn-primary">Add Bottles</a>
                    <a href="{{ url('wrm-damage-bottles/track-damage-bottles') }}" class="btn regpro-btn btn-primary">Track Damage Bottles</a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12 container-sold">
                    <h3>CONTAINER SOLD</h3>
                    <h2>{{ number_format($container_bots_qty) }}</h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12" style="background-color: #750775">
                    <h3>CASH ON HAND</h3>
                    <h2>{{ number_format(floatval($over_all_sales) - ($over_all_no_OR_expenses + $ca_expenses[0]->ca_expenses + $customer_unpaid + $cash_advance), 2) }}</h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12" style="background-color: #FDB8C5">
                    <h3>CUSTOMER OWE</h3>
                    <h2>{{ number_format(floatval($customer_unpaid), 2) }}</h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12" style="background-color: #FD9B21">
                    <h3>NO. OF UNPAID BOTTLES</h3>
                    <h2>{{ number_format($num_unpaid_bottles->num_unpaid_bottles) }}</h2>
                </div>
            </div>
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
@endsection

@section('footer_script_preload')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endsection

@section('footer_script')

<style type="text/css">
    div.wfm-dashboard h2{
        font-size: 50px;
    }
    div.wfm-dashboard div.col-lg-12{
    min-height: 210px;
    padding-bottom: 10px;
    max-height: 210px;
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
        max-width: 320px;
        display: inline-block!important;
        vertical-align: middle;
        cursor: default;
        font-size: 18px;
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
</script>
@endsection