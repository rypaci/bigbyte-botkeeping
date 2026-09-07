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
        <div class="col-lg-3 margin-tb">
            <div class="pull-left">
                <h1>Product Inventory</h1>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="row">
                <div class="col-md-8 date-range-details">
                    {!! Form::open(array('url' => 'inventory/inventory-date-range','method'=>'GET')) !!}
                    <input type="text" name="date_from" class="date-search form-control datepicker" placeholder="Date From:" autocomplete="off"/>
                    <input type="text" name="date_to" class="date-search form-control datepicker" placeholder="Date To:" autocomplete="off"/>
                    <button type="submit" class="btn btn-primary">Search by Date</button>
                    {!! Form::close() !!}
                </div>
                <div class="col-md-4 align-right">
                    {!! Form::open(array('url' => 'inventory/search-products','method'=>'GET')) !!}
                    <input type="text" name="search_products" class="search-products form-control" placeholder="Search Products"/>
                    <button type="submit" class="btn btn-primary">Search</button>
                    {!! Form::close() !!}
                </div>
                {{-- <div class="col-md-6 align-right">
                    {!! Form::open(array('url' => 'inventory/search-products','method'=>'GET')) !!}
                    {!! Form::select('inventory_status', ['All'=>'All', 'Active'=>'Active', 'Inactive'=>'Inactive'], null, ['class' => 'form-control view-by-status']) !!}
                    <button type="submit" class="btn btn-primary">View Status</button>
                    {!! Form::close() !!}
                </div> --}}
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-employee warning-msg-true">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($message = Session::get('warning'))
        <div class="alert alert-warning alert-employee warning-msg-true">
            <p>{{ $message }}</p>
        </div>
    @endif

    <section class="content">
        {{-- {!! Form::model(['method' => 'POST', 'url' => ['inventory'],'class' => '']) !!} --}}
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover payroll-table" style="width:100%;">
                <tr>
					<th align="center">Products</th>
                    <th align="center">Vendors / Suppliers</th>
                    <th align="center">Cost Price</th>
                    <th align="center">Selling Price</th>
                    <th align="center">Profit</th>
                    <th align="center">Total Profit</th>
                    <th align="center">Stocks Remaining</th>
                    <th align="center">Out of Stock Threshold</th>
                    <th align="center">SKU</th>
                    <th align="center">Inventory Active/Inactive</th>
                    <th align="center">Action</th>
                </tr>
                {{-- */
                    $x=0;
                    
                    $stock_qty = 0;
                /* --}}
                @foreach($invData as $value)
                {{-- */
                    $stock_qty_left = $value->total_added_qty - $value->sold_qty;
                /* --}}
                {!! Form::open(array('url' => 'inventory/save-srpriority','method'=>'POST')) !!}
                <tr class="main-content">
                    <td>{!! Form::hidden('pro_id', $value->pro_id, ['class' => 'form-control']) !!} {{ $value->name }}</td>
                    <td width = "150">{{$value->first_name}} {{$value->middle_name}} {{$value->last_name}}{{$value->company_name}}</td>
                    <td align="right">{{ number_format($value->cost_price, 2) }}</td>
                    <td align="right">{{ number_format($value->price, 2) }}</td>
                    <td align="right">{{ number_format($value->price - $value->cost_price, 2) }}</td>
                    <td align="right">{{ number_format($value->sold_qty * ($value->price - $value->cost_price), 2) }}</td>
                    <td width="100" align="right">@if($stock_qty_left <= $value->stock_threshold) <span style="color: red; font-weight: bold;">{{ number_format($stock_qty_left) }}</span> @else {{ number_format($stock_qty_left) }} @endif</td>
                    <td width="100" align="right">{!! Form::text('stock_threshold', $value->stock_threshold, ['class' => 'form-control',]) !!}</td>
                    <td width="100" align="right">{!! Form::text('sr_priority', $value->sr_priority, ['class' => 'form-control',]) !!}</td>
                    <td width="100" align="center">{{$value->inventory_status}}</td>
                    <td align="right">
                        <button type="submit" class="btn btn-primary btn-xs"><i class="fa fa-save"></i> Save SKU or Stock Threshold</button>
                        <a class="btn btn-primary btn-xs" href="{{ url('inventory/inventory-details', $value->pro_id) }}" title="Inventory Details"><i class="fa fa-th-list"></i> Inventory Details</a>
                        <a class="btn btn-primary btn-xs" href="{{ url('product/details', $value->pro_id) }}" title="Stock Details"><i class="fa fa-th-list"></i> Stock Details</a>
                        <a class="btn btn-primary btn-xs update-status-btn" href="{{ route('product.edit', $value->pro_id) }}"><i class="fa fa-pencil"></i> Edit Status</a>
                        <a class="btn btn-primary btn-xs update-status-btn" href="{{ url('/product/details/add-stock', $value->pro_id) }}"><i class="fa fa-pencil"></i> Add Qty</a>
                    </td>
                </tr>
                {!! Form::close() !!}
                {{-- */$x++;/* --}}
                @endforeach
                <tr>
                    <td colspan="2" align="right"><strong>Over all total:</strong></td>
                    <td align="right"><strong>{{ number_format($total_cost_price, 2) }}</strong></td>
                    <td align="right"><strong>{{ number_format($total_selling_price, 2) }}</strong></td>
                    <td align="right"><strong>{{ number_format($total_profit, 2) }}</strong></td>
                    <td align="right"><strong>{{ number_format($overall_total_profit, 2) }}</strong></td>
                    <td></td>
                </tr>
            </table>
        </div>
        
        <div class="pull-right">
            {{-- {!! $invData->render() !!} --}}
            {!! $invData->appends(['search_products' => $search_products, 'date_from' => $startDate, 'date_to' => $endDate])->render(); !!}
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
    .date-range-details input{
        max-width: 150px;
    }
    .date-range-details{
        text-align: right;
    }
    .main-content .btn{
        margin-bottom: 1px;
    }
</style>
@endsection