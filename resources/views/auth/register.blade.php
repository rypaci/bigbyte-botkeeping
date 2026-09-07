@extends('layouts.guest')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif hold-transition login-page
@endsection


@section('content')
<div class="register-box">
  <div class="register-logo">
    <a href="{{ url('/') }}">Bigbyte Aqua Ventures</a>
  </div>

  <div class="register-box-body">
    <p class="login-box-msg">Register</p>

    <form action="{{ url('/register') }}" method="post">
      {{ csrf_field() }}
      <div class="form-group{{ $errors->has('lastname') ? ' has-error' : '' }} has-feedback">
        <input type="text" class="form-control" placeholder="Last Name" name="lastname" value="{{ old('lastname') }}">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                @if ($errors->has('lastname'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lastname') }}</strong>
                                    </span>
                                @endif
      </div>
      <div class="form-group{{ $errors->has('firstname') ? ' has-error' : '' }} has-feedback">
        <input type="text" class="form-control" placeholder="First Name" name="firstname" value="{{ old('firstname') }}">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                @if ($errors->has('firstname'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('firstname') }}</strong>
                                    </span>
                                @endif
      </div>
      <div class="form-group{{ $errors->has('middlename') ? ' has-error' : '' }} has-feedback">
        <input type="text" class="form-control" placeholder="Middle Name" name="middlename" value="{{ old('middlename') }}">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                @if ($errors->has('middlename'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('middlename') }}</strong>
                                    </span>
                                @endif
      </div>
      <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }} has-feedback">
                                <!-- <input id="address" type="text" class="form-control" name="address" value="{{ old('address') }}"> -->
								{{ Form::textarea('address', old('address'), ['class' => "form-control" , 'rows' => 4,  'placeholder' => "Address"]) }}
                                @if ($errors->has('address'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                @endif
      </div>
      <div class="form-group{{ $errors->has('country') ? ' has-error' : '' }} has-feedback">
        <input type="text" class="form-control" placeholder="Country" name="country" value="{{ old('country') }}">
            {{-- <span class="glyphicon glyphicon-user form-control-feedback"></span> --}}
                                @if ($errors->has('country'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('country') }}</strong>
                                    </span>
                                @endif
      </div>
      <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }} has-feedback">
        <input type="text" class="form-control" placeholder="Phone" name="phone" value="{{ old('phone') }}">
        <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
      </div>
      <div class="form-group{{ $errors->has('dob') ? ' has-error' : '' }} has-feedback">
        <input type="text" class="form-control" placeholder="Date of Birth" name="dob" value="{{ old('dob') }}">
                                @if ($errors->has('dob'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('dob') }}</strong>
                                    </span>
                                @endif
      </div>
      <div class="form-group{{ $errors->has('gender') ? ' has-error' : '' }} has-feedback">
        {!! Form::select('gender', array('male'=>'Male', 'female'=>'Female'), null, ['class' => 'form-control', 'placeholder' => 'Gender']) !!}
                                @if ($errors->has('gender'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('gender') }}</strong>
                                    </span>
                                @endif
      </div>
	  
      <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} has-feedback">
        <input type="text" class="form-control" placeholder="Username" name="name" value="{{ old('name') }}">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
      </div>
      <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} has-feedback">
        <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
      </div>
	  
      <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} has-feedback">
        <input type="password" class="form-control" placeholder="Password" name="password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
      </div>
      <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }} has-feedback">
        <input type="password" class="form-control" placeholder="Retype password" name="password_confirmation">
        <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
      </div>
      <div class="row">
        <div class="col-xs-8">
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

    <a href="{{ url('/login') }}">I already have registered</a>

  </div>
  <!-- /.login-box-body -->
</div>
@endsection

