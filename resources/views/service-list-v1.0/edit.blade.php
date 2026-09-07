@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">
    <section class="content-header">
    
        @include('_includes.message')
        
        <div class="pull-left">
            <h1>Edit New Service</h1>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('service-list.index') }}"> Back</a>
        </div>
        <div style="clear: both;"></div>
        
    </section>
    
    
    <section class="content">
    {!! Form::model($service, ['method' => 'PATCH','route' => ['service-list.update', $service->id]]) !!}
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 service-input">
            <div class="form-group">
                <div class="col-sm-2">
                    <strong>Services:</strong>
                </div>
                <div class="col-sm-6">
                    {!! Form::text('name', null, array('placeholder' => 'Services','class' => 'form-control')) !!}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 service-input">
            <div class="form-group">
                <div class="col-sm-2">
                    <strong>Rate:</strong>
                </div>
                <div class="col-sm-6">
                    {!! Form::text('rate', null, array('placeholder' => 'Rate','class' => 'form-control')) !!}
                    <div class="col-xs-12 col-sm-12 col-md-12 text-left">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    </section>

</div>

@endsection