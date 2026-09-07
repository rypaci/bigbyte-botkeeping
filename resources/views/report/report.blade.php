@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('header_style_postload')
<style>
</style>
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <div class="pull-left">
            <h1>Report</h1>
        </div>
        
        <div class="pull-right">
            @include('report.right_links')
        </div>
        
        <div style="clear: both;"></div>
        
    </section>
    
    <!-- Main content -->
    <section class="content">
    
    @if(empty($vouchers) || count($vouchers) == 0)
        <br>
        <p class="lead text-center">Result not found</p>
    @else
        @include('report.table')
    @endif

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
