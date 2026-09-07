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
			Edit Vendor ({{ $vendor->id }})
		</h1>
	
	</section>

	<!-- Main content -->
	<section class="content">
	
	{!! Form::model($vendor, [
		'method' => 'PATCH',
		'url' => ['/vendor', $vendor->id],
		'class' => ''
	]) !!}

	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">Options</h3>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>
		<div class="box-body">
			<div class="row">
			
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('individual') ? 'has-error' : ''}}">
                    {!! Form::label('individual', 'Type', ['class' => 'control-label']) !!}
                    <div class="icheck-radio icheck-square">
                        <label><input type="radio" class="square" name="individual" value="1" {{ $vendor->individual? 'checked': '' }}> Individual</label>
                        <label><input type="radio" class="square" name="individual" value="0" {{ $vendor->individual? '': 'checked' }}> Non-individual</label>
                    </div>
                    {!! $errors->first('individual', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="clearfix"></div>
			<div class="col-sm-6 individual-option {{ $vendor->individual? '': 'hidden' }}">
                <div class="form-group {{ $errors->has('last_name') ? 'has-error' : ''}}">
                    {!! Form::label('last_name', 'Last Name', ['class' => 'control-label']) !!}
                    {!! Form::text('last_name', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('last_name', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6 individual-option {{ $vendor->individual? '': 'hidden' }}">
                <div class="form-group {{ $errors->has('first_name') ? 'has-error' : ''}}">
                    {!! Form::label('first_name', 'First Name', ['class' => 'control-label']) !!}
                    {!! Form::text('first_name', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('first_name', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6 individual-option {{ $vendor->individual? '': 'hidden' }}">
                <div class="form-group {{ $errors->has('middle_name') ? 'has-error' : ''}}">
                    {!! Form::label('middle_name', 'Middle Name', ['class' => 'control-label']) !!}
                    {!! Form::text('middle_name', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('middle_name', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6 non-individual-option {{ $vendor->individual? 'hidden': '' }}">
                <div class="form-group {{ $errors->has('company_name') ? 'has-error' : ''}}">
                    {!! Form::label('company_name', 'Company Name', ['class' => 'control-label']) !!}
                    {!! Form::text('company_name', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('company_name', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
			</div>
			<!-- /.row -->
		</div>
		<!-- ./box-body -->
	</div>
		
		
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">Business Information</h3>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>
		<div class="box-body">
			<div class="row">
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('business_name') ? 'has-error' : ''}}">
                    {!! Form::label('business_name', 'Business Name', ['class' => 'control-label']) !!}
                    {!! Form::text('business_name', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('business_name', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('business_address') ? 'has-error' : ''}}">
                    {!! Form::label('business_address', 'Business Address', ['class' => 'control-label']) !!}
                    {!! Form::textarea('business_address', null, ['class' => 'form-control', 'rows' => '12']) !!}
                    {!! $errors->first('business_address', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('city') ? 'has-error' : ''}}">
                    {!! Form::label('city', 'City', ['class' => 'control-label']) !!}
                    {!! Form::text('city', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('city', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('country') ? 'has-error' : ''}}">
                    {!! Form::label('country', 'Country', ['class' => 'control-label']) !!}
                    {!! Form::text('country', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('country', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('tin') ? 'has-error' : ''}}">
                    {!! Form::label('tin', 'Tin', ['class' => 'control-label']) !!}
                    {!! Form::text('tin', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('tin', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('branch_code') ? 'has-error' : ''}}">
                    {!! Form::label('branch_code', 'Branch Code', ['class' => 'control-label']) !!}
                    {!! Form::text('branch_code', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('branch_code', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('opening_balance') ? 'has-error' : ''}}">
                    {!! Form::label('opening_balance', 'Opening Balance', ['class' => 'control-label']) !!}
                    {!! Form::number('opening_balance', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('opening_balance', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group {{ $errors->has('as_of') ? 'has-error' : ''}}">
                <label for="as_of" class="control-label">As Of</label>
                {!! Form::input('as_of', 'as_of', null, ['class' => 'form-control datepicker', 'required' => 'required']) !!}
                {!! $errors->first('as_of', '<p class="help-block">:message</p>') !!}
              </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('phone_number') ? 'has-error' : ''}}">
                    {!! Form::label('phone_number', 'Phone Number', ['class' => 'control-label']) !!}
                    {!! Form::text('phone_number', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('phone_number', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('fax') ? 'has-error' : ''}}">
                    {!! Form::label('fax', 'Fax', ['class' => 'control-label']) !!}
                    {!! Form::text('fax', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('fax', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
                    {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
                    {!! Form::text('email', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
				
			</div>
			<!-- /.row -->
		</div>
		<!-- ./box-body -->
	</div>

	<div class="row">
	  <div class="col-sm-3">
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