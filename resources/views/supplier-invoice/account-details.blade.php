@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('header_style_preload')
<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection


@section('header_style_postload')
<style>
.nav-tabs-custom > .tab-content { background: #f9f5f5; }
.nav-tabs-custom .nav > li.active > a { padding-bottom: 9px; }
</style>
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Supplier Invoice <small>Account Details</small>
        </h1>
        
    </section>
    
    <!-- Main content -->
    <section class="content">
    
    {!! Form::model($supplierinvoicecoas, [
        'method' => 'PATCH',
        'url' => ['/supplier-invoice/account-details'],
        'class' => ''
    ]) !!}

      <div class="row">
        <div class="col-md-12">
          <!-- Custom Tabs -->
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#t_asset" data-toggle="tab" data-journal="asset">Asset</a></li>
              <li><a href="#t_purchases" data-toggle="tab" data-journal="purchases">Purchases</a></li>
              <li><a href="#t_expenses" data-toggle="tab" data-journal="expenses">Expenses</a></li>
            </ul>
            <div class="tab-content asset">
              <div class="tab-pane active" id="t_asset">
                {!! view('supplier-invoice.tab-content-asset', compact('supplierinvoicecoas', 'chart_accounts_option', 'chart_account_taxes_option')) !!}
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane purchases" id="t_purchases">
                {!! view('supplier-invoice.tab-content-purchases', compact('supplierinvoicecoas', 'chart_accounts_option', 'chart_account_taxes_option')) !!}
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane expenses" id="t_expenses">
                {!! view('supplier-invoice.tab-content-expenses', compact('supplierinvoicecoas', 'chart_accounts_option', 'chart_account_taxes_option')) !!}
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    
    
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
  /* For conditional use. Only allow the select2 to get reinitialize once */
  $('.nav-tabs li.active > a').data('isSelectSet', 1);
  
  /* This will fix the select2 width on tab contents not in active at page load */
  $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
    var journal = $(this).data('journal');
    
    if($(this).data('isSelectSet') != 1) {
        $('.tab-pane.'+journal+' .select2').select2();
        $(this).data('isSelectSet', 1);
    }
  });
</script>
@endsection