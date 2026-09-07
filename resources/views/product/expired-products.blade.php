@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-3 margin-tb">
            <div class="pull-left">
                <h1>Expired Products and 1 Month before expiration <a href="{{ route('product.index') }}" class="btn btn-primary btn-xs" title="Add New Product"><i class="fa fa-plus" aria-hidden="true"></i></a></h1>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-product">
            <p>{{ $message }}</p>
        </div>
    @endif

    <section class="content">
        <div class="table-responsive">
            <table class="table table-hover outofstock-table" style="width:100%;">
                <tr>
                    <th style="text-align: left;">Product ID</th>
                    <th style="text-align: left;">Product Name</th>
                    <th style="text-align: left;">Vendors/Supplier</th>
                    <th style="text-align: right;">Stock Remaining</th>
                    <th style="text-align: right;">Expiration Date</th>
                </tr>
                @foreach ($expired_products as $key => $item)
                {{-- */
                    $remaining_stocks = $item->total_stock - $item->total_soldstock;
                /* --}}
                <tr>
                    <td>{{ $item->prod_id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->first_name }} {{ $item->middle_name }} {{ $item->last_name }}{{ $item->company_name }}</td>
                    <td align="right">{{$item->total_stock - $item->total_soldstock}}</td>
                    <td align="right" class="expired">@if($item->expiration_date == '') @else{{ date('m-d-Y', strtotime($item->expiration_date)) }}@endif</td>
                </tr>
                @endforeach
            </table>
            <div class="row" style="margin-right: 0px; margin-left: 0px;">
                <div class="col-md-6">
                    <div class="pagination" style="margin:0;">{!! $expired_products->render(); !!}</div>
                </div>
                <div class="col-md-6" style="text-align: right;">
                    <a href="{{ url('/product') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

<style type="text/css">
    ul.pagination{
        margin: 0px;
    }
    .table-responsive table{
        font-size: 14px;
    }
    td.expired{
        color: red;
        font-weight: bold;
    }
</style>