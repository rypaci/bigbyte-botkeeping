@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('header_style_postload')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Report Filter
        </h1>
        
    </section>
    
    <!-- Main content -->
    <section class="content">
    
    {!! Form::open(['url' => '/report-old', 'method' => 'GET']) !!}

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Filters</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
            
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('from', 'From', ['class' => 'control-label']) !!}
                    {!! Form::text('from', null, ['class' => 'form-control datepicker', 'required']) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('to', 'To', ['class' => 'control-label']) !!}
                    {!! Form::text('to', null, ['class' => 'form-control datepicker', 'required']) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('chart_account_id', 'Chart Account', ['class' => 'control-label']) !!}
                    {!! Form::select('chart_account_id', $chartaccounts_option, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('vendor', 'Vendor', ['class' => 'control-label']) !!}
                    {!! Form::text('vendor', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('customer', 'Customer', ['class' => 'control-label']) !!}
                    {!! Form::text('customer', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('ref_number', 'Ref #', ['class' => 'control-label']) !!}
                    {!! Form::text('ref_number', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="clearfix"></div>
            
            </div>
            <!-- /.row -->
        </div>
        <!-- ./box-body -->
    </div>
        
    <div class="row">
      <div class="col-sm-3">
        <div class="form-group">
            {!! Form::submit('Submit', ['class' => 'btn btn-primary form-control']) !!}
        </div>
      </div>
    </div>
    {!! Form::close() !!}
        
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection


@section('footer_script_preload')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('footer_script')
<script>
$( function() {
    
$('.datepicker').datepicker({
    autoclose: true,
    todayBtn: "linked",
    todayHighlight: true,
    format: 'yyyy-mm-dd'
});

$( '[name=vendor]' ).autocomplete({
  source: '{{ url("ledger/get-vendors") }}',
  minLength: 3,
});

$( '[name=customer]' ).autocomplete({
  source: '{{ url("ledger/get-customers") }}',
  minLength: 3,
});

});
</script>
@endsection
