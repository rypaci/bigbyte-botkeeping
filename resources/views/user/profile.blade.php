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
        {{ $user->name }}'s Profile
        </h1>

    </section>

    <!-- Main content -->
    <section class="content">
	
        {!! Form::model($user, [
            'method' => 'POST',
            'url' => ['/profile'],
            'class' => 'form-horizontal user',
			'files' => true
        ]) !!}

                <div class="form-group {{ $errors->has('firstname') ? 'has-error' : ''}}">
                    {!! Form::label('firstname', trans('Name'), ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-6">
                        {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
                    {!! Form::label('email', trans('user.email'), ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-6">
                        {!! Form::text('email', null, ['class' => 'form-control', 'disabled'=>'disabled']) !!}
                        {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="form-group {{ $errors->has('password') ? 'has-error' : ''}}">
                    {!! Form::label('password', 'Password', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-6">
                        {!! Form::password('password', ['class'=>'form-control', 'placeholder'=>'Leave this blank to unchange']) !!}
                        {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : ''}}">
                    {!! Form::label('password_confirmation', 'Password Confirmation', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-6">
                        {!! Form::password('password_confirmation', ['class'=>'form-control', 'placeholder'=>'Retype your password']) !!}
                        {!! $errors->first('password_confirmation', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>


                <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
                    {!! Form::label('avatar-', 'Avatar', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-6">
                        {!! Form::file('avatar'); !!}
                        @if($user->avatar)
                          <img src="{{ asset('/uploads/images/' . $user->avatar ) }}" class="user-avatar" />
                        @else
                          <img src="{{ asset('/uploads/images/no-avatar.png') }}" class="user-avatar" />
                        @endif
                        {!! $errors->first('avatar', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>


        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                {!! Form::submit('Update', ['class' => 'btn btn-primary form-control']) !!}
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
