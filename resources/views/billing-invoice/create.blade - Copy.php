@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		@include('_includes.message')
		
		<h1>
			Create New Billing Invoice
		</h1>
		
	</section>

	<!-- Main content -->
	<section class="content">
	
	{!! Form::open(['url' => '/billing-invoice']) !!}

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
                <div class="form-group {{ $errors->has('customer_id') ? 'has-error' : ''}}">
                    {!! Form::label('customer_id', 'Customer Id', ['class' => 'control-label']) !!}
                    {!! Form::number('customer_id', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('customer_id', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('invoice_number') ? 'has-error' : ''}}">
                    {!! Form::label('invoice_number', 'Invoice Number', ['class' => 'control-label']) !!}
                    {!! Form::text('invoice_number', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('invoice_number', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('date') ? 'has-error' : ''}}">
                    {!! Form::label('date', 'Date', ['class' => 'control-label']) !!}
                    {!! Form::date('date', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('amount') ? 'has-error' : ''}}">
                    {!! Form::label('amount', 'Amount', ['class' => 'control-label']) !!}
                    {!! Form::number('amount', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('amount', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('amount_due') ? 'has-error' : ''}}">
                    {!! Form::label('amount_due', 'Amount Due', ['class' => 'control-label']) !!}
                    {!! Form::number('amount_due', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('amount_due', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('vat_amount') ? 'has-error' : ''}}">
                    {!! Form::label('vat_amount', 'Vat Amount', ['class' => 'control-label']) !!}
                    {!! Form::number('vat_amount', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('vat_amount', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('net_of_vat') ? 'has-error' : ''}}">
                    {!! Form::label('net_of_vat', 'Net Of Vat', ['class' => 'control-label']) !!}
                    {!! Form::number('net_of_vat', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('net_of_vat', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('no_of_person') ? 'has-error' : ''}}">
                    {!! Form::label('no_of_person', 'No Of Person', ['class' => 'control-label']) !!}
                    {!! Form::number('no_of_person', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('no_of_person', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('no_of_scpwd') ? 'has-error' : ''}}">
                    {!! Form::label('no_of_scpwd', 'No Of Scpwd', ['class' => 'control-label']) !!}
                    {!! Form::number('no_of_scpwd', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('no_of_scpwd', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('discounted') ? 'has-error' : ''}}">
                    {!! Form::label('discounted', 'Discounted', ['class' => 'control-label']) !!}
                                <div class="checkbox">
                <label>{!! Form::radio('discounted', '1') !!} Yes</label>
            </div>
            <div class="checkbox">
                <label>{!! Form::radio('discounted', '0', true) !!} No</label>
            </div>
                    {!! $errors->first('discounted', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('discount_amount') ? 'has-error' : ''}}">
                    {!! Form::label('discount_amount', 'Discount Amount', ['class' => 'control-label']) !!}
                    {!! Form::number('discount_amount', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('discount_amount', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('discount_perc') ? 'has-error' : ''}}">
                    {!! Form::label('discount_perc', 'Discount Perc', ['class' => 'control-label']) !!}
                    {!! Form::number('discount_perc', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('discount_perc', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('net_sales') ? 'has-error' : ''}}">
                    {!! Form::label('net_sales', 'Net Sales', ['class' => 'control-label']) !!}
                    {!! Form::number('net_sales', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('net_sales', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('add_vat') ? 'has-error' : ''}}">
                    {!! Form::label('add_vat', 'Add Vat', ['class' => 'control-label']) !!}
                    {!! Form::number('add_vat', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('add_vat', '<p class="help-block">:message</p>') !!}
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