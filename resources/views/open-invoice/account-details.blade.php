@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('header_style_preload')
<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Open Invoice <small>Account Details</small>
        </h1>
        
    </section>
    
    <!-- Main content -->
    <section class="content">
    
    {!! Form::model($coa_options, [
        'method' => 'PATCH',
        'url' => ['/open-invoice/account-details'],
        'class' => ''
    ]) !!}

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"> Row 1 <small>(coa debit)</small>  </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Account Titles</label>
                  {!! Form::select('coa_debit[]', $chart_accounts_option, null, ['class' => 'form-control select2', 'multiple' => 'multiple']) !!}
                </div>
              <!-- /.form-group -->
              </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- ./box-body -->
    </div>
    
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"> Row 2 <small>(tax debit)</small> </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Account Titles</label>
                  {!! Form::select('tax_debit[]', $chart_account_taxes_option, null, ['class' => 'form-control select2', 'multiple' => 'multiple']) !!}
                </div>
              <!-- /.form-group -->
              </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- ./box-body -->
    </div>
    
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"> Row 3 <small>(discount debit)</small> </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Account Titles</label>
                  {!! Form::select('discount_debit[]', $chart_account_discounts_option, null, ['class' => 'form-control select2', 'multiple' => 'multiple']) !!}
                </div>
              <!-- /.form-group -->
              </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- ./box-body -->
    </div>
    
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"> Row 4 <small>(coa credit)</small> </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Account Titles</label>
                  {!! Form::select('coa_credit[]', $chart_accounts_option, null, ['class' => 'form-control select2', 'multiple' => 'multiple']) !!}
                </div>
              <!-- /.form-group -->
              </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- ./box-body -->
    </div>
    
    <div class="row">
      <div class="col-sm-3">
        <div class="form-group">
            {!! Form::submit('Save', ['class' => 'btn btn-primary form-control']) !!}
        </div>
      </div>
    </div>
    
    {!! Form::close() !!}
    
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection

@section('footer_script_preload')
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
@endsection


@section('footer_script')
<script>
  $('.select2').select2();
</script>
@endsection