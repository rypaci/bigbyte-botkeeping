@if(!empty($options)) <?php extract( $options ) ?> @endif

@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('header_style_postload')
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
	
		@include('_includes.message')
		
        <h1>
            Tax Settings
        </h1>

    </section>

    <!-- Main content -->
    <section class="content">
	
    {!! Form::open(['url' => '/tax-setting']) !!}

	<div class="col-md-6">
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title"> &nbsp; </h3>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>
		<div class="box-body">
			<div class="row">
			
            <div class="col-sm-12">
                <div class="form-group">
                    {!! Form::label('tax_name', 'Tax Name', ['class' => 'control-label']) !!}
                    {!! Form::text('tax_name', isset($tax_name) ? $tax_name : null , ['class' => 'form-control', 'class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    {!! Form::label('tax_description', 'Tax Description', ['class' => 'control-label']) !!}
                    {!! Form::text('tax_description', isset($tax_description) ? $tax_description : null , ['class' => 'form-control', 'class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    {!! Form::label('sales_tax', 'Sales Tax (%)', ['class' => 'control-label']) !!}
                    {!! Form::text('sales_tax', isset($sales_tax) ? $sales_tax : null , ['class' => 'form-control', 'class' => 'form-control']) !!}
                </div>
            </div>
			
            <div class="col-md-6">
              <div class="form-group">
                <input class="btn btn-primary form-control" type="submit" value="Save">
              </div>
            </div>
			</div>
			<!-- /.row -->
		</div>
		<!-- ./col-md-6 -->
	</div>
	</div>
	<!-- ./box-body -->

    {!! Form::close() !!}

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection


@section('footer_script')
<script>
  $(function () {
    $('.btn-access_gen').click(function(){
      $('span.spinner').removeClass('hidden');
      $.get("{{url('user/access-token')}}", {}, function(data){
		$('[name=access_token]').val( data );
		$('span.spinner').addClass('hidden');
	  });
    });
	
    $('form.user').on('submit', function(){
	  if($('[name=access_token]').val().length == 0) {
		  alert( 'Please generate a Access Token so you can use your popup.' );
		  return false;
	  }
    });
  });
</script>
@endsection
