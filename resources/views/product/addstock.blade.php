@extends('layouts.adminlte')

@section('header_style_preload')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Add New Stock Quantity</h1>
            </div>
            <div class="pull-right">
                <a href="{{ url('/product/details', $product_ID->id) }}" class="btn btn-primary"><i class="fa fa-chevron-left"></i> Back</a>
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

    @if (count($errors) > 0)
    <div class="col-sm-12">
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {!! Form::open(array('url' => 'product/details/save-stock','method'=>'GET')) !!}
    {!! Form::hidden('pro_id', $product_ID->id, ['class' => 'form-control']) !!}
    <section class="content">
    <div class="row">
        <div class="col-xs-5 col-sm-12 col-md-5 product-input" style="background-color: #fff;">
            <h3 style="margin-bottom: 20px;">Product: <strong>{{ $product_ID->name }}</strong></h3>
            <div class="row" style="margin-bottom: 10px;">
                <div class="form-group">
                    <div class="col-sm-2">
                        <strong>Stock Qty:</strong>
                    </div>
                    <div class="col-sm-5">
                        {!! Form::text('added_qty', null, array('placeholder' => 'Stock Qty','class' => 'form-control')) !!}
                    </div>
                </div>
            </div>
            <div class="row" style="margin-bottom: 10px;">
                <div class="form-group">
                    <div class="col-sm-2">
                        <strong>Date:</strong>
                    </div>
                    <div class="col-sm-5">
                        {!! Form::text('date', null, array('placeholder' => 'Date','class' => 'form-control datepicker')) !!}
                    </div>
                </div>
            </div>
            <div class="row" style="margin-bottom: 10px; padding-bottom: 15px;">
                <div class="form-group">
                    <div class="col-sm-2">
                    </div>
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>
</div>
    {!! Form::close() !!}

@endsection

@section('footer_script_preload')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('footer_script') 
<script>
    $('.datepicker').datepicker('update', new Date());
</script>
@endsection