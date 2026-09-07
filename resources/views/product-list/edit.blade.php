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
                <h1>Edit New Product</h1>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('product-list.index') }}"> Back</a>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {!! Form::model($item, ['method' => 'PATCH','route' => ['product-list.update', $item->id]]) !!}
<section class="content">
    <div class="row">

        <div class="col-xs-12 col-sm-12 col-md-12 product-input">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>Product Name:</strong>
            </div>
            <div class="col-sm-6">
                {!! Form::text('product_name', null, array('placeholder' => 'Product Name','class' => 'form-control')) !!}
            </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 product-input">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>Price:</strong>
            </div>
            <div class="col-sm-6">
                {!! Form::text('price', null, array('placeholder' => 'price','class' => 'form-control')) !!}
                <div class="col-xs-12 col-sm-12 col-md-12 text-left">
                <button type="submit" class="btn btn-primary">Update</button>
        </div>
            </div>
            </div>
        </div>
    </div>
</section>
</div>
    {!! Form::close() !!}

@endsection