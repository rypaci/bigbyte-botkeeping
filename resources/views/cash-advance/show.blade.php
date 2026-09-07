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
                    <h1>Show Product</h1>
                </div>
                <div class="pull-right">
                    <a class="btn btn-primary" href="{{ route('product.index') }}"> Back</a>
                </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>
    <section class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 product-show">
            <div class="form-group">
                <div class="col-sm-2">
                    <strong>Product Name:</strong>
                </div>
                <div class="col-sm-6">
                    {{ $product->name }}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 product-show">
            <div class="form-group">
                <div class="col-sm-2">
                    <strong>Sell Price:</strong>
                </div>
                <div class="col-sm-6">
                    {{ $product->price }}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 product-show">
            <div class="form-group">
                <div class="col-sm-2">
                    <strong>Cost Price:</strong>
                </div>
                <div class="col-sm-6">
                    {{ $product->cost_price }}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 product-show">
            <div class="form-group">
                <div class="col-sm-2">
                    <strong>Vendors/Suppliers:</strong>
                </div>
                <div class="col-sm-6">
                    {{ $product->first_name }} {{ $product->middle_name }} {{ $product->last_name }}{{ $product->company_name }}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 product-show">
            <div class="form-group">
                <div class="col-sm-2">
                    <strong>Inventory Status:</strong>
                </div>
                <div class="col-sm-6">
                    {{ $product->inventory_status }}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
        <div class="col-sm-2">
            <a class="btn btn-primary btn-xs" href="{{ route('product.edit',$product->id) }}"><i class="fa fa-pencil"></i></a>
                    {!! Form::open(['method' => 'DELETE','route' => ['product.destroy', $product->id],'style'=>'display:inline']) !!}
                    {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Product',
                                    'onclick'=>'return confirm("Confirm delete?")'
                    )); !!}
                    {!! Form::close() !!}
        </div>
        </div>
        </div>
    </div>
    </section>
</div>
@endsection