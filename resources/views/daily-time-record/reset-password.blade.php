@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('header_style_postload')
<link rel="stylesheet" href="{{ url('/') }}/assets/plugins/iCheck/square/blue.css">
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Reset password for username: <strong>{{ $get_account->username }}</strong></h1>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('daily-time-record.index') }}"> Back</a>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-customer alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <p>{{ $message }}</p>
    </div>
    @endif

    @if ($message = Session::get('warning'))
    <div class="alert alert-warning alert-customer alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <p>{{ $message }}</p>
    </div>
    @endif

    <section class="content">
        {!! Form::open(array('url' => 'daily-time-record/save-reset-password','method'=>'GET')) !!}
        <input type="hidden" name="e_id" value="{{ $get_account->e_id }}"/>
        <div class="row">
            <div class="col-md-6 dtr-createpass">
                <div class="row">
                    <div class="col-sm-12">
                        <label>New Password:</label>
                        <input type="password" name="password" class="form-control password" placeholder="Enter New Password" required/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <label>Confirm New Password:</label>
                        <input type="password" name="confirm_password" class="form-control confirm_password" placeholder="Confirm New Password" required/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary" onclick = 'return confirm("Are you sure to reset the password?")'>Update</button>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </section>
</div>

@endsection

@section('footer_script')
<script src="{{ url('/') }}/assets/plugins/iCheck/icheck.min.js"></script>
<script>

</script>

<style type="text/css">
.dtr-createpass{
    background-color: #fff;
    padding: 20px;
    margin: 15px;
}
.dtr-createpass .col-sm-12{
    margin-bottom: 10px;
}
</style>

@endsection