@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Generated Report
        </h1>
        
    </section>
    
    <!-- Main content -->
    <section class="content">
    
    <div class="box">
        <div class="box-body">
            <div class="row">
            <div class="col-sm-12 col-md-8 col-md-offset-2">
              {!! $content !!}
            </div>
            <!-- /.row -->
        </div>
        <!-- ./box-body -->
    </div>
        
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
