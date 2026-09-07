@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		@include('_includes.message')
		
		<h1>
			Edit Company ({{ $company->id }})
		</h1>
	
	</section>

	<!-- Main content -->
	<section class="content">
	
	{!! Form::model($company, [
		'method' => 'PATCH',
		'url' => ['/company', $company->id],
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
                    {!! Form::label('individual', 'Individual', ['class' => 'control-label']) !!}
                    {!! Form::number('individual', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('individual', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
            </div>
	        <div class="col-sm-6">
                <div class="form-group {{ $errors->has('last_name') ? 'has-error' : ''}}">
                    {!! Form::label('last_name', 'Last Name', ['class' => 'control-label']) !!}
                    {!! Form::text('last_name', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('last_name', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('first_name') ? 'has-error' : ''}}">
                    {!! Form::label('first_name', 'First Name', ['class' => 'control-label']) !!}
                    {!! Form::text('first_name', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('first_name', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('middle_name') ? 'has-error' : ''}}">
                    {!! Form::label('middle_name', 'Middle Name', ['class' => 'control-label']) !!}
                    {!! Form::text('middle_name', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('middle_name', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('gender') ? 'has-error' : ''}}">
                    {!! Form::label('gender', 'Gender', ['class' => 'control-label']) !!}
                    {!! Form::select('gender', ['male'=>'Male', 'female'=>'Female'], null, ['class' => 'form-control']) !!}
                    <?php // {!! Form::text('gender', null, ['class' => 'form-control']) !!} ?>
                    {!! $errors->first('gender', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('civil_status') ? 'has-error' : ''}}">
                    {!! Form::label('civil_status', 'Civil Status', ['class' => 'control-label']) !!}
                    {!! Form::text('civil_status', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('civil_status', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6 col-spouse">
                <div class="form-group {{ $errors->has('spouse') ? 'has-error' : ''}}">
                    {!! Form::label('spouse', 'Spouse', ['class' => 'control-label']) !!}
                    {!! Form::text('spouse', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('spouse', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('business_name') ? 'has-error' : ''}}">
                    {!! Form::label('business_name', 'Business Name', ['class' => 'control-label']) !!}
                    {!! Form::text('business_name', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('business_name', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('business_id') ? 'has-error' : ''}}">
                    {!! Form::label('business_id', 'Business Id', ['class' => 'control-label']) !!}
                    {!! Form::text('business_id', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('business_id', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('business_address') ? 'has-error' : ''}}">
                    {!! Form::label('business_address', 'Business Address', ['class' => 'control-label']) !!}
                    {!! Form::textarea('business_address', null, ['class' => 'form-control']) !!}
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
                <div class="form-group {{ $errors->has('zip') ? 'has-error' : ''}}">
                    {!! Form::label('zip', 'Zip', ['class' => 'control-label']) !!}
                    {!! Form::text('zip', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('zip', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('business_type') ? 'has-error' : ''}}">
                    {!! Form::label('business_type', 'Business Type', ['class' => 'control-label']) !!}
                    {!! Form::text('business_type', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('business_type', '<p class="help-block">:message</p>') !!}
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
                <div class="form-group {{ $errors->has('rdo_code') ? 'has-error' : ''}}">
                    {!! Form::label('rdo_code', 'Rdo Code', ['class' => 'control-label']) !!}
                    {!! Form::text('rdo_code', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('rdo_code', '<p class="help-block">:message</p>') !!}
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
@endsection