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
                    <h1>Show Service</h1>
                </div>
                <div class="pull-right">
                    <a class="btn btn-primary" href="{{ route('service-list.index') }}"> Back</a>
                </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>
    <section class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 service-show">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>Services:</strong>
            </div>
            <div class="col-sm-6">
                {{ $service->name }}
            </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 service-show">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>Rate:</strong>
            </div>
            <div class="col-sm-6">
                {{ $service->rate }}
            </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
        <div class="col-sm-2">
            <a class="btn btn-primary btn-xs" href="{{ route('service-list.edit',$service->id) }}"><i class="fa fa-pencil"></i></a>
                    {!! Form::open(['method' => 'DELETE','route' => ['service-list.destroy', $service->id],'style'=>'display:inline']) !!}
                    {!! Form::button('<i class="fa fa-trash"></i>', array(

                                    'type' => 'submit',

                                    'class' => 'btn btn-danger btn-xs',

                                    'title' => 'Delete Service',

                                    'onclick'=>'return confirm("Confirm delete?")'
                            ));!!}
                    {!! Form::close() !!}
        </div>
        </div>
        </div>
    </div>
    </section>
</div>
@endsection