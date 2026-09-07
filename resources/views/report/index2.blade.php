@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('header_style_preload')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection


@section('header_style_postload')
<style>
.old-report-notice { margin-bottom: 5px; }
</style>
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Reports
        </h1>
        
    </section>
    
    <!-- Main content -->
    <section class="content">
    
          <div class="box box-solid">
            <div class="box-body">
              <p class="lead old-report-notice">{{--This is temporary. --}}If you want to generate custom reports, <span class="text-aqua"><a href="{{ url('report-old') }}" target="_blank">GO HERE.</a></span></p>
                  {{--<p class="text-red">Partial page update. Below forms are still nonfunctional.</p>--}}
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

        <div class="row">
        <div class="col-md-4">
          @include('report.box.accounts-payable-summary')
        </div>
        <!-- /.col -->

        <div class="col-md-4">
          @include('report.box.vendor-accounts-payable-detail')
        </div>
        <!-- /.col -->

        <div class="col-md-4">
          @include('report.box.accounts-receivable-summary')
        </div>
        <!-- /.col -->
        </div>
        <!-- /.row -->

        <div class="row">
        <div class="col-md-4">
          @include('report.box.customer-accounts-receivable-detail')
        </div>
        <!-- /.col -->

        <div class="col-md-4">
          @include('report.box.statement-of-finance-position')
        </div>
        <!-- /.col -->

        <div class="col-md-4">
          @include('report.box.trial-balance')
        </div>
        <!-- /.col -->
        </div>
        <!-- /.row -->

        <div class="row">
        <div class="col-md-4">
          @include('report.box.income-statement')
        </div>
        <!-- /.col -->
        </div>
        <!-- /.row -->
    
    </section><!-- /.content -->
    
</div><!-- /.content-wrapper -->
@endsection


@section('footer_script_preload')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('footer_script')
<script>
$( function() {
    
$('.datepicker').datepicker({
  autoclose: true,
  todayBtn: "linked",
  todayHighlight: true,
  format: 'yyyy-mm-dd'
});

$( '[name=vendor]' ).autocomplete({
  source: '{{ url("ledger/get-vendors") }}',
  minLength: 3,
});

$( '[name=customer]' ).autocomplete({
  source: '{{ url("ledger/get-customers") }}',
  minLength: 3,
});

$( 'form' ).on('click', '[type="submit"]', function(){
    var $form = $(this).closest('form');
    
    $form.find('[name=vid]').val( '' );
    if($form.find('[name=vendor]').length){
        $vendor = $form.find('[name=vendor]');
        if($vendor.val().trim() && $vendor.data('ui-autocomplete') && $vendor.data('ui-autocomplete').selectedItem){
            $form.find('[name=vid]').val( $vendor.data('ui-autocomplete').selectedItem.id );
        }
    }
    
    $form.find('[name=cid]').val( '' );
    if($form.find('[name=customer]').length){
        $customer = $form.find('[name=customer]');
        if($customer.val().trim() && $customer.data('ui-autocomplete') && $customer.data('ui-autocomplete').selectedItem){
            $form.find('[name=cid]').val( $customer.data('ui-autocomplete').selectedItem.id );
        }
    }
});

});
</script>
@endsection
