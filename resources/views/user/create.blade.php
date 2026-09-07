@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('header_style_postload')
<style>
.btn-access_gen { margin-top: 6px; }
span.spinner {
    padding: 5px;
    margin-top: 6px;
    display: inline-block;
    vertical-align: middle;
}
</style>
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
	
		@include('_includes.message')
		
        <h1>
            Create New User
        </h1>

    </section>

    <!-- Main content -->
    <section class="content">

		{!! Form::open(['url' => '/user', 'class' => 'form-horizontal user']) !!}

                <div class="form-group {{ $errors->has('firstname') ? 'has-error' : ''}}">
                    {!! Form::label('name', trans('Name'), ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-6">
                        {!! Form::text('name', $value = null, ['class' => 'form-control', 'required' => 'required']) !!}
                        {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
                    {!! Form::label('email', trans('user.email'), ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-6">
                        {!! Form::email('email', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
				
                <div class="form-group {{ $errors->has('access_token') ? 'has-error' : ''}}">
                    {!! Form::label('access_token', trans('user.access_token'), ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-6">
                        {!! Form::text('access_token', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        <button type="button" class="btn btn-default btn-access_gen">Generate</button><span class="spinner hidden"><i class="fa fa-refresh fa-spin"></i><span>
                        {!! $errors->first('access_token', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>


        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                {!! Form::submit('Create', ['class' => 'btn btn-primary form-control']) !!}
            </div>
        </div>
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
