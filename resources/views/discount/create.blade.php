@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif discount
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		@include('_includes.message')
		
		<h1>
			Create New Discount
		</h1>
		
	</section>

	<!-- Main content -->
	<section class="content">
	
	{!! Form::open(['url' => '/discount']) !!}

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
                <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
                    {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('rate') ? 'has-error' : ''}}">
                    {!! Form::label('rate', 'Rate', ['class' => 'control-label']) !!}
                    {!! Form::number('rate', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('rate', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('chart_account_id') ? 'has-error' : ''}}">
                    {!! Form::label('chart_account_id', 'Chart Account', ['class' => 'control-label']) !!}
                    {!! Form::select('chart_account_id', $chart_accounts_option, null, ['class' => 'form-control']) !!}
                    {!! $errors->first('chart_account_id', '<p class="help-block">:message</p>') !!}
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