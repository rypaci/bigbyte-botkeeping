@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection

@section('header_style_postload')
<link rel="stylesheet" href="{{ url('/') }}/assets/plugins/iCheck/square/blue.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		@include('_includes.message')
		
		<h1>
			Edit Damage Bottles ({{ $damage_bottles->id }})
		</h1>
	
	</section>

	<!-- Main content -->
	<section class="content">
	
	{!! Form::model($damage_bottles, [
		'method' => 'PATCH',
		'url' => ['/wrm-damage-bottles', $damage_bottles->id],
		'class' => ''
	]) !!}
		
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">Damage Bottles Information</h3>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>
		<div class="box-body">
			<div class="row">
                <div class="col-sm-3">
                    <div class="form-group {{ $errors->has('dmg_bottles') ? 'has-error' : ''}}">
                        {!! Form::label('dmg_bottles', 'No. of bottles', ['class' => 'control-label']) !!}
                        {!! Form::text('dmg_bottles', null, ['class' => 'form-control']) !!}
                        {!! $errors->first('dmg_bottles', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-3">
                    <div class="form-group {{ $errors->has('date') ? 'has-error' : ''}}">
                        {!! Form::label('date', 'Date', ['class' => 'control-label']) !!}
                        {!! Form::text('date', null, ['class' => 'form-control', 'rows' => '12']) !!}
                        {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
			</div>
			<!-- /.row -->
		</div>
		<!-- ./box-body -->
	</div>

	<div class="row">
	  <div class="col-sm-1">
		<div class="form-group">
			{!! Form::submit('Update', ['class' => 'btn btn-primary form-control']) !!}
		</div>
	  </div>
	</div>
	{!! Form::close() !!}

	</section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection


@section('footer_script_preload')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('footer_script')
<script src="{{ url('/') }}/assets/plugins/iCheck/icheck.min.js"></script>
<script>
	$('input[type="radio"].square, input[type="checkbox"].square').iCheck({ 
		radioClass: 'iradio_square-blue', 
		checkboxClass: 'iradio_square-blue' 
	});
	$('input').on('ifChanged', function (event) {
		if( $(event.target).attr('name') == "individual" && $(event.target).is(':checked')) {
			if( $(event.target).val() == "1" ) {
				$('.non-individual-option').addClass('hidden');
				$('.individual-option').removeClass('hidden');
			}
			else {
				$('.individual-option').addClass('hidden');
				$('.non-individual-option').removeClass('hidden');
			}
		}
	});
	
	//Date picker
	$('.datepicker').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd'
	});
</script>
@endsection