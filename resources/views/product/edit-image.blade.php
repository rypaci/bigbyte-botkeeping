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
                <h1>Edit Product Image</h1>
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

    {!! Form::open(array('url' => 'product/update-image','method'=>'POST', 'enctype'=>'multipart/form-data')) !!}
    {{-- {!! Form::model($image, ['method' => 'PATCH','route' => ['product/image-update', $image->id]]) !!} --}}
    <section class="content">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 product-input">
                <div class="form-group">
                    <div class="col-sm-2">
                        <strong>Old Image:</strong>
                    </div>
                    <div class="col-sm-6">
                        <div class="input-group control-group increment">
                            <div id="old_image_preview">
                                <img class="imageresource" src="{{ asset('public/uploads/images/'.$image->image_name) }}"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 product-input">
                <div class="form-group">
                    <div class="col-sm-2">
                        <strong>New Image:</strong>
                    </div>
                    <div class="col-sm-6">
                        <div class="input-group control-group increment">
                            <input type="file" id="filename" name="filename" class="form-control">
                            <input type="hidden" name="id" value="{{$image->id}}">
                        </div>
                        <div id="image_preview"></div>
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

@section('footer_script')
<style>
    input[type=file]{
        display: inline;
    }
    #image_preview, #old_image_preview{
        border: 1px solid #d2d6de;
        padding: 10px;
        margin-top: 10px;
        max-width: 212px;
    }
    #image_preview img, #old_image_preview img{
        width: 190px;
        padding: 5px;
        height: 190px;
    }
    div.product-input{
        background-color: #fff;
        padding: 20px 0;
    }
</style>
<script>
    $("#filename").change(function(){

        $('#image_preview').html("");
        var total_file=document.getElementById("filename").files.length;

        $('#image_preview').append("<img src='"+URL.createObjectURL(event.target.files[0])+"'>");
        // for(var i=0;i<total_file;i++){
        //     $('#image_preview').append("<img src='"+URL.createObjectURL(event.target.files[i])+"'>");
        // }
    });
</script>
@endsection