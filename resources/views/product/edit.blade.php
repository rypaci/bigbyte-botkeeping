@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection

@section('header_style_preload')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="content-wrapper">
 <div class="row">
     <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Edit Product</h1>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('product.index') }}"> Back</a>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

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

    {!! Form::model($product, ['method' => 'PATCH','route' => ['product.update', $product->id]]) !!}
<section class="content">
    <div class="row">

        <div class="col-xs-12 col-sm-12 col-md-12 product-input">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>Product Name:</strong>
            </div>
            <div class="col-sm-6">
                {!! Form::text('name', null, array('placeholder' => 'Product Name','class' => 'form-control')) !!}
            </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 product-input">
            <div class="form-group">
                <div class="col-sm-2">
                    <strong>Sell Price:</strong>
                </div>
                <div class="col-sm-6">
                    {!! Form::text('price', null, array('placeholder' => 'Price','class' => 'form-control')) !!}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 product-input">
            <div class="form-group">
                <div class="col-sm-2">
                    <strong>Cost Price:</strong>
                </div>
                <div class="col-sm-6">
                    {!! Form::text('cost_price', null, array('placeholder' => 'Cost Price','class' => 'form-control')) !!}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 product-input">
            <div class="form-group">
                <div class="col-sm-2">
                    <strong>Vendors/Suppliers:</strong>
                </div>
                <div class="col-sm-6">
                    <select class="form-control" name="vendor_id">
                        <option value="">Select Vendors/Suppliers</option>
                        @foreach($vendors as $vendor)
                          <option value="{{$vendor->id}}" {{$vendor->id == $product->vendor_id  ? 'selected' : ''}}>{{$vendor->first_name}} {{$vendor->middle_name}} {{$vendor->last_name}} {{$vendor->company_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 product-input">
            <div class="form-group">
                <div class="col-sm-2">
                    <strong>Inventory Status:</strong>
                </div>
                <div class="col-sm-6">
                    {!! Form::select('inventory_status', ['Active'=>'Active', 'Inactive'=>'Inactive'], null, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 product-input">
            <div class="form-group">
                <div class="col-sm-2">
                    <strong>Barcode:</strong>
                </div>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-5" id="barcode-div">
                            <button type="button" class="btn btn-primary" id="scan-barcode" onclick="scanBarcode()">Scan Barcode</button> Or 
                            <button type="button" class="btn btn-primary" id="gen-barcode" onclick="generateBarcode()">Generate Barcode</button>
                        </div>
                        <div class="col-sm-7">
                            {!! Form::text('barcode', null, array('placeholder' => 'Barcode','class' => 'form-control','id' => 'barcodeText')) !!}
                            <img id="barcode">
                        </div>
                    </div>
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

@section('footer_script_preload')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('footer_script')
<script>
    $( document ).ready(function() {

        var barcode = $("#barcodeText").val();

        if(!!barcode){
            $("#barcode").JsBarcode(barcode);
            $("#barcode-div").hide();
        }
    });

    function generateBarcode() {
        var randomNumber = Math.floor((Math.random() * 10000000000) + 1);
        $("#barcodeText").val(randomNumber);
        $("#barcodeText").focus();
        $("#barcodeText").attr("readonly", false);
        // $("#barcode").JsBarcode(randomNumber);
        // $("#barcode").show();
    }
    
    function scanBarcode() {
        $("#barcodeText").attr("readonly", false);
        $("#barcodeText").focus();
        $("#barcodeText").val("");
        // $("#barcode").hide();
    }
    
    $(document).keyup(function(e) {
        // if (e.keyCode === 13) $('.save').click();     // enter
        if (e.keyCode === 27) // esc
        {
            $("#barcodeText").val("");
        }
    });
    
    // $('.datepicker').datepicker({
    //     autoclose: true,
    //     todayBtn: "linked",
    //     todayHighlight: true,
    //     format: 'yyyy-mm-dd'
    // }).datepicker();
</script>
@endsection