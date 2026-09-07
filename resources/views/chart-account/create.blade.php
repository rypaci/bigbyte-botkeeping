@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('header_style_preload')
<link rel="stylesheet" href="{{ url('/') }}/assets/plugins/iCheck/square/blue.css">
@endsection


@section('header_style_postload')
<style>
.entry { padding: 6px 0 5px; }
.entry .checkbox.icheck { display: inline; margin-right: 30px; }
</style>
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Create New Account
        </h1>
        
    </section>

    <!-- Main content -->
    <section class="content">
    
    {!! Form::open(['url' => '/chart-account']) !!}

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
                    {!! Form::label('account_type_id', 'Main Account Type', ['class' => 'control-label']) !!}
                    {!! Form::select('account_type_id', $accounttypes_option, null, ['class' => 'form-control']) !!}
                    {!! $errors->first('account_type_id', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('sub_account_type_id') ? 'has-error' : ''}}">
                    {!! Form::label('sub_account_type_id', 'Sub Account Type', ['class' => 'control-label']) !!}
                    {!! Form::select('sub_account_type_id', ['Select'], null, ['class' => 'form-control']) !!}
                    {!! $errors->first('sub_account_type_id', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('level') ? 'has-error' : ''}}">
                    {!! Form::label('level', 'Account Level', ['class' => 'control-label']) !!}
                    {!! Form::select('level', array('1'=>'Level 1', '2'=>'Level 2', '3'=>'Level 3', '4'=>'Level 4'), null, ['class' => 'form-control']) !!}
                    {!! $errors->first('level', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6 col-sub-account hidden">
                <div class="form-group {{ $errors->has('parent_account_id') ? 'has-error' : ''}}">
                    {!! Form::label('parent_account_id', 'Sub Account of', ['class' => 'control-label']) !!}
                    {!! Form::select('parent_account_id', [], null, ['class' => 'form-control']) !!}
                    {!! $errors->first('parent_account_id', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
                    {!! Form::label('name', 'Account Name', ['class' => 'control-label']) !!}
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('code') ? 'has-error' : ''}}">
                    {!! Form::label('code', 'Account Code', ['class' => 'control-label']) !!}
                    {!! Form::text('code', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                    {!! $errors->first('code', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('normal_balance') ? 'has-error' : ''}}">
                    {!! Form::label('normal_balance', 'Normal Balance', ['class' => 'control-label']) !!}
                    {!! Form::text('normal_balance', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('normal_balance', '<p class="help-block">:message</p>') !!}
                </div>
            </div>

                
            </div>
            <!-- /.row -->
        </div>
        <!-- ./box-body -->
        <div class="overlay hidden">
          <i class="fa fa-refresh fa-spin"></i>
        </div>
    </div>

    <div class="box collapsed-box">
        <div class="box-header with-border">
            <h3 class="box-title">Tax</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('tax_id') ? 'has-error' : ''}}">
                    {!! Form::label('tax_id', 'Tax', ['class' => 'control-label']) !!}
                    {!! Form::select('tax_id', $taxes_option, null, ['class' => 'form-control']) !!}
                    {!! $errors->first('tax_id', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-sm-6">
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
<script src="{{ url('/') }}/assets/plugins/iCheck/icheck.min.js"></script>
@endsection


@section('footer_script')
<script>
    function fill_sub_accounts_option( sub_accounts ){
        var options = '';
        
        for(var i in sub_accounts){
            options += '<option value="'+ i +'">'+ sub_accounts[i] +'</option>';
        }
        
        $('select#parent_account_id').html( options );
    }
    
    function fill_sub_account_types_option( data ){
        var options = '<option value="">Select</option>';
        
        for(var i in data){
            options += '<option value="'+ i +'">'+ data[i] +'</option>';
        }
        
        $('select#sub_account_type_id').html( options );
    }
    
    var ddata;
    
    /* Main account and Level dropdown on change */
    $('select#account_type_id, select#level, select#sub_account_type_id').on('change', function(){
        $input = $(this);
        
        if( $input.is('#account_type_id') ) $('select#level').val(1);
        
        if( $('select#account_type_id').val() <= 0 ) return;
        if( $('select#level').val() == 1 ) $('select#parent_account_id').val('');
        
        var request = { 
            id: $('select#account_type_id').val(), 
            level: $('select#level').val(), 
            sub_account_type_id: $('select#sub_account_type_id').val() || 0, 
            parent_account_id: $('select#parent_account_id').val() || 0 
        };
        
        $('.box .overlay').removeClass('hidden');
        
        $.get('{{url("chart-account/code-and-sub-accounts")}}', request, function(data){
            if(data){
                if(data.sub_accounts_length > 0){
                    fill_sub_accounts_option(data.sub_accounts);
                    $('.col-sub-account').removeClass('hidden');
                }
                else{
                    $('select#parent_account_id').html( '' );
                    $('.col-sub-account').addClass('hidden');
                }
                
                if(data.level){
                    $('select#level').val(data.level);
                }
                
                if(data.new_code){
                    $('input[name=code]').val(data.new_code);
                }
                
                if(data.sub_account_types && typeof data.sub_account_types == 'object'){
                    // only run below script if the current input is the dd account_type_id
                    if($input.is('#account_type_id'))
                        fill_sub_account_types_option(data.sub_account_types);
                }
            }
            
            $('.box .overlay').addClass('hidden');
        }, 'json');
    });
    
    /* Sub Account dropdown on change */
    $('select#parent_account_id').on('change', function(){
        if( $(this).attr('id') == 'account_type_id' ) $('select#level').val(1);
        
        var request = { id: $('select#account_type_id').val(), level: $('select#level').val(), parent_account_id: $('select#parent_account_id').val() || 0, to_json: 1 };
        
        $('.box .overlay').removeClass('hidden');
        
        $.get('{{url("chart-account/code")}}', request, function(data){
            if(data){
                if(data.new_code){
                    $('input[name=code]').val(data.new_code);
                }
            }
            
            $('.box .overlay').addClass('hidden');
        }, 'json');
    });
    
    
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
    
</script>
@endsection