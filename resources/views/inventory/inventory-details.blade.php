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
        <div class="col-md-6 margin-tb">
            <div class="pull-left">
                <h1>Stock Inventory Details</h1>
                <h3><strong>Product Name:</strong> {{ $products->name }}</h3>
            </div>
        </div>
        <div class="col-md-6 date-range-details">
            <h4>Search by Date Range</h4>
            {!! Form::open(array('url' => 'inventory/inventory-details','method'=>'GET')) !!}
            {!! Form::hidden('id', $products->id, ['class' => 'form-control']) !!}
            <input type="text" name="date_from" class="date-search form-control datepicker" placeholder="Date From:" autocomplete="off"/>
            <input type="text" name="date_to" class="date-search form-control datepicker" placeholder="Date To:" autocomplete="off"/>
            <button type="submit" class="btn btn-primary">Search</button>
            {!! Form::close() !!}
        </div>
        </section>
    </div>

    {{-- @if ($message = Session::get('success'))
        <div class="alert alert-success alert-employee warning-msg-true">
            <p>{{ $message }}</p>
        </div>
    @endif --}}

    <section class="content">
        {{-- {!! Form::model(['method' => 'POST', 'url' => ['inventory'],'class' => '']) !!} --}}
        
        <div class="table-responsive tableFixHead" style="height: 500px; margin-bottom: 10px;">
            <table class="table table-bordered table-striped table-hover payroll-table" style="margin-bottom: 0px; width: 100%;">
                <thead>
                    <tr>
                        <th align="center">Date</th>
                        <th align="center">Stock Qty</th>
                        <th align="center">Total Profit</th>
                        <th align="center">Container</th>
                        <th align="center">Others</th>
                        <th align="center">Stocks running total</th>
                    </tr>
                </thead>
                {{-- */
                    $total_stock_qty = 0;
                    $total_profit = 0;
                    $sold_qty = 0;
                    $total_container = 0;
                    $total_others = 0;
                    $running_total = 0;
                /* --}}
                @foreach($inventory_details as $value)
                {{-- {!! Form::open(array('url' => 'inventory/save-srpriority','method'=>'POST')) !!} --}}
                {{-- */
                    $total_stock_qty = $total_stock_qty + $value->added_qty;
                    $sold_qty = $value->container_qty + $value->others_qty;
                    $total_profit = $total_profit + ($sold_qty * $profit);
                    $total_container = $total_container + $value->container_qty;
                    $total_others = $total_others + $value->others_qty;
                    $running_total = $running_total + (($value->added_qty) - ($value->container_qty + $value->others_qty));
                /* --}}
                <tr class="inactive">
                    <td> {!! Form::hidden('pro_id', $value->pro_id, ['class' => 'form-control']) !!} {{ $value->date }} </td>
                    <td align="right"> @if($value->added_qty == 0) @else {{ $value->added_qty }} @endif </td>
                    <td align="right"> {{ number_format($sold_qty * $profit, 2) }} </td>
                    <td align="right"> @if($value->container_qty == 0) @else {{ $value->container_qty }} @endif </td>
                    <td align="right"> @if($value->others_qty == 0) @else {{ $value->others_qty }} @endif </td>
                    <td align="right"> {{ number_format($running_total) }} </td>
                </tr>
                {{-- {!! Form::close() !!} --}}

                @endforeach
                <tr>
                    <td align="center"><strong>Overall Total:</strong></td>
                    <td align="right"><strong> {{ number_format($total_stock_qty) }} </strong></td>
                    <td align="right"><strong> {{ number_format($total_profit, 2) }} </strong></td>
                    <td align="right"><strong> {{ number_format($total_container) }} </strong></td>
                    <td align="right"><strong> {{ number_format($total_others) }} </strong></td>
                    <td align="right"><strong>{{ number_format($running_total) }}</strong></td>
                <tr>
            </table>
        </div>
        <div class="pull-left">
            <a class="btn btn-primary" href="{{ url('inventory') }}" title="Inventory Details"><i class="fa fa-chevron-left"></i> Back</a>
        </div>
        <div class="pull-right">
            {{-- {!! $inventory_details->render() !!} --}}
            {{-- {!! $invData->appends(['inventory_status' => $inventory_status, 'search_products' => $search_products])->render(); !!} --}}
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
    .search-products{
        max-width: 290px;
        display: inline-block!important;
        vertical-align: middle;
    }
    .view-by-status{
        max-width: 320px;
        display: inline-block!important;
        vertical-align: middle; 
    }
    .align-right{
        text-align: right;
    }
    .date-range-details{
        width: 47%;
        border: 1px solid #ddd;
        margin: 15px 15px 0;
        padding-bottom: 15px;
    }
    .tableFixHead thead th { position: sticky; top: 0; }
    /* Just common table stuff. Really. */
    table.table-striped  { border-collapse: collapse; width: 100%; overflow-y: auto;}
    .table-striped th, .table-striped td { padding: 8px 16px; }
    .table-striped th{background-color: #eee}
</style>
@endsection