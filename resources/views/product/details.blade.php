@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Product Details <a href="{{ route('product.create') }}" class="btn btn-primary btn-xs" title="Add New Product"><i class="fa fa-plus" aria-hidden="true"></i></a></h1>
            </div>
        </div>
        <div style="clear: both;"></div>

        <div class="col-lg-12">
            <h1><strong>{{ $productName->name }}</strong> ( Vendors/Suppliers: {{ $vendorName->first_name }} {{ $vendorName->middle_name }} {{ $vendorName->last_name }}{{ $vendorName->company_name }} )</h1>
        </div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-product">
            <p>{{ $message }}</p>
        </div>
    @endif

    <section class="content">
        <div class="table-responsive">
            {!! Form::open(array('url' => 'product/details','method'=>'POST')) !!}
            <table class="table table-bordered table-striped table-hover" style="width:100%">
                <tr>
                    <th>No</th>
                    <th>Quantity</th>
                    <th>Date</th>
                </tr>
            {{-- */ $x=0; /* --}}
            @foreach ($details as $key => $item)
                @if(empty($item->inv_id))
                @else
                <tr>
                    <td width = "150">{{ ++$i }} {!! Form::hidden('invrow['.$x.'][inv_id]', $item->inv_id, ['class' => 'form-control']) !!}</td>
                    <td width = "150">{!! Form::text('invrow['.$x.'][added_qty]', $item->added_qty, ['class' => 'form-control']) !!}</td>
                    <td width = "150" align="right">{{ $item->date }}</td>
                    <td>
                        <button type="submit" class="btn btn-primary btn-xs" title="Update Stocks"><i class="fa fa-save"></i></button>
                        <a class="btn btn-danger btn-xs" href="{{ route('product.get.detailsdestroy', $item->inv_id) }}" onclick = 'return confirm("Are you sure to remove this stock details?")'><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                @endif
                {{-- */ $x++; /* --}}
            @endforeach
            </table>
            {!! Form::close() !!}
            <div class="row" style="margin-right: 0px; margin-left: 0px;">
                <div class="col-md-6">
                    <div class="pagination" style="margin:0;">{!! $details->render() !!}</div>
                </div>
                <div class="col-md-6" style="text-align: right;">
                    <a href="{{ url('/product') }}" class="btn btn-primary"><i class="fa fa-chevron-left"></i> Back</a>
                    <a href="{{ url('/product/details/add-stock', $productID->id) }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add Stocks</a>
                    <!--<a href="{{ url('/inventory') }}" class="btn btn-primary">Go to stocks inventory</a>-->
                    <a href="{{ url('/point-of-sale/inventory') }}" class="btn btn-primary">Go to POS stocks inventory</a>
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
</style>