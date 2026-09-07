@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		@include('_includes.message')
		
		<h1>
			Create New SubAccountType
		</h1>
		
	</section>

	<!-- Main content -->
	<section class="content">
	
	{!! Form::open(['url' => '/sub-account-type']) !!}

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
                <div class="form-group {{ $errors->has('account_type_id') ? 'has-error' : ''}}">
                    {!! Form::label('account_type_id', 'Account Type', ['class' => 'control-label']) !!}
                    {!! Form::select('account_type_id', $account_types_option, '', ['class' => 'form-control']) !!}
                    {!! $errors->first('account_type_id', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
                    {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('description') ? 'has-error' : ''}}">
                    {!! Form::label('description', 'Description', ['class' => 'control-label']) !!}
                    {!! Form::text('description', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('description', '<p class="help-block">:message</p>') !!}
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
			{!! Form::submit('Create', ['class' => 'btn btn-primary form-control']) !!}
		</div>
	  </div>
	</div>
	{!! Form::close() !!}

	</section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection


@section('footer_script_preload')
@endsection