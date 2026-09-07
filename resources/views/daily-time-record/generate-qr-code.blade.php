@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('header_style_postload')
<link rel="stylesheet" href="{{ url('/') }}/assets/plugins/iCheck/square/blue.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Generate QR Code</h1>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ url('daily-time-record') }}"> Back</a>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-customer alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <p>{{ $message }}</p>
    </div>
    @endif

    @if ($message = Session::get('warning'))
    <div class="alert alert-warning alert-customer alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <p>{{ $message }}</p>
    </div>
    @endif

    <section class="content">
        {{-- {!! Form::open(array('url' => 'daily-time-record/save-qrcode','method'=>'GET')) !!} --}}
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <h2>Employee's Name: {{$get_employee->employee_name}}</h2>
                        <input type="hidden" value="{{$get_employee->id}}"/>
                        <?php
                            // $png = QrCode::format('png')->size(300)->generate($token_qrcode);
                            // $png = base64_encode($png);
                            // echo "<img src='data:image/png;base64,".$png."'>";
                            // //pass the strings
                            // QrCode::email('brysons1986@gmail.com', 'Message Subject', 'Message body.');
                        ?>
                        <div id="qr-example"></div>
                        <input type="hidden" value="{{ $token_qrcode }}" id="gn-val"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12" style="margin-top: 20px;">
                        {{-- <button type="submit" class="btn btn-primary">Save QR Code</button> --}}
                        <!--<a href=<?php //echo "'data:image/png;base64,".$png."'";?> class="btn btn-primary" download="{{$token_qrcode}}">Download</a>-->
                        <a id="download" class="btn btn-primary" download="{{$token_qrcode}}" href="" onclick="download_img(this);">Download</a>
                    </div>
                </div>
            </div>
        </div>
        {{-- {!! Form::close() !!} --}}
    </section>
</div>

@endsection


@section('footer_script_preload')
<script src="{{ url('/') }}/assets/plugins/iCheck/icheck.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script src="/assets/js/qrcode.min.js"></script>
@endsection

@section('footer_script')
<script>
    $(document).ready(function(){
        $('.datepicker').datepicker({
        autoclose: true,
        todayBtn: "linked",
        todayHighlight: true,
        format: 'yyyy-mm-dd'
        }).datepicker('update', new Date());
    });

    $(window).load(function(){
        var val = $('#gn-val').val();
        
        if(val){
            $('#qr-example').qrcode(val);

            var canvas = document.querySelector("canvas");
            var ctx = canvas.getContext("2d");
            var ox = canvas.width / 2;
            var oy = canvas.height / 2;
            // ctx.font = "42px serif";
            // ctx.textAlign = "center";
            // ctx.textBaseline = "middle";
            // ctx.fillStyle = "#800";
            // ctx.fillRect(ox / 2, oy / 2, ox, oy);

            download_img = function(el) {
            // get image URI from canvas object
            var imageURI = canvas.toDataURL("image/jpg");
            el.href = imageURI;
            };
        }else{
            alert("Please provide some text or url !");
        }  	
    });
</script>

<style type="text/css">
.dtr-absent{
    background-color: #fff;
    padding: 20px;
    margin: 15px;
}
.dtr-absent .col-sm-12{
    margin-bottom: 10px;
}
select, input, textarea{
    max-width: 640px;
    width: 100%;
}
</style>

@endsection
