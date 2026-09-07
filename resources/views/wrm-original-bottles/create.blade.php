@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('header_style_preload')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection


@section('header_style_postload')
<style>
#tbl-items .actions { width: 24px; }
#tbl-items .fa-close { opacity: 0.5; }
#tbl-items .fa-close:hover, #tbl-items .fa-close:focus { opacity: 1; }
#tbl-items .fa-close.text-red:active { color: #b52e1e !important; } /* dark red */

.table-account-details tr.optional { display: none; }
</style>
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <div class="pull-left">
            <h1>Original Bottles Entry</h1>
        </div>

        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('water-refilling-monitoring.index') }}"> Back to dashboard</a>
        </div>
        <div style="clear: both;"></div>
        
    </section>

    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-employee warning-msg-true">
        <p>{{ $message }}</p>
    </div>
    @endif

    <!-- Main content -->
    <section class="content">

    {!! Form::open(array('route' => 'wrm-original-bottles.store','method'=>'POST')) !!}
    
    <div class="box" style="padding: 10px;">
        <div class="row">
            <div class="col-sm-3">
                <div class="input-group input-group-sm" style="margin-bottom: 10px;">
                    <label>Numbers of original bottles</label>
                    <input type="text" class="form-control" name="orig_bottles" placeholder="No. of original bottles">
                </div>
                <div class="input-group input-group-sm" style="margin-bottom: 10px;">
                    <label>Date</label>
                    <input type="text" name="date" class="form-control datepicker" required>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
      <div class="col-sm-3">
        <div class="form-group">
            {!! Form::submit('Save', ['class' => 'btn btn-primary form-control','style'=>'width:100px;']) !!}
        </div>
      </div>
      <div class="col-sm-9">
            <div class="form-group" style="text-align: right;">
                <a href="{{ url('wrm-original-bottles/track-original-bottles') }}" class="btn btn-primary">Track Original Bottles</a>
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
    $('.datepicker').datepicker({
        autoclose: true,
        todayBtn: "linked",
        todayHighlight: true,
        format: 'yyyy-mm-dd'
    }).datepicker('update', new Date());

    /* Trigger change to fill in Account # */
    // $('select.chart-account-dropdown, select.tax-dropdown').trigger('change');

    /*var validation_url = '{{url("water-refilling-monitoring/add-form-validate")}}';

    $(".withholding_tax").prop('selectedIndex', 1);*/
</script>
<style type="text/css">
div.typeofentry{
    float: left;
    margin-right: 10px;
}
.input-group {
    display: block!important;
}
.input-group .form-control{
    float: none;
}
</style>
@endsection