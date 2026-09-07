@extends('layouts.guest')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif hold-transition login-page
@endsection

@section('header_style_postload')
<link rel="stylesheet" href="{{ url('/') }}/assets/plugins/iCheck/square/blue.css">
@endsection


@section('content')
<div class="login-box">
  <div class="login-logo">
    <a href="{{ url('/') }}">Quicktax</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Reset Password</p>

    <form action="{{ url('/password/email') }}" method="post" role="form" >
      {{ csrf_field() }}
      <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} has-feedback">
        <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        @if ($errors->has('email'))
            <span class="help-block">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
      </div>
	  
      <div class="row">
        <div class="col-xs-offset-4 col-xs-8">
          <button type="submit" class="btn btn-primary btn-block btn-flat"><i class="fa fa-btn fa-envelope"></i> Send Password Reset Link</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

  </div>
  <!-- /.login-box-body -->
</div>
@endsection


@section('footer_script')
<script src="{{ url('/') }}/assets/plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
@endsection
