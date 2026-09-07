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
#tbl-items .actions { width: 24px; }
#tbl-items .fa-close { opacity: 0.5; }
#tbl-items .fa-close:hover, #tbl-items .fa-close:focus { opacity: 1; }
#tbl-items .fa-close.text-red:active { color: #b52e1e !important; } /* dark red */

.table-account-details tr.optional { display: none; }
</style>
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <div class="pull-left">
            <h1>Add new entry</h1>
        </div>
        
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('water-refilling-monitoring.index') }}"> Back</a>
        </div>
        <div style="clear: both;"></div>
        
    </section>

    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-employee warning-msg-true">
        <p>{{ $message }}</p>
    </div>
    @endif

    <!-- Main content -->
    <section class="content">
    
    {!! Form::open(array('route' => 'water-refilling-monitoring.store','method'=>'POST')) !!}
    
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"></h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                
            <div class="col-sm-6">
                <div class="input-group input-group-sm">
                    <div class="input-group-btn"><button type="button" class="btn">Customer</button></div>
                    
                    <input type="text" size="250" class="form-control customer_name" name="customer_name" id="cust_ajax" value="" placeholder="Customer Name" autocomplete="off">
                    <input type="hidden" name="customer_id" class="id" value="">
                    
                    <span class="input-group-btn"><button type="button" class="btn btn-info btn-flat btn-search">Go!</button></span>
                </div>
        
                <div class="box box-info box-solid customer-info-box">
                    <div class="box-header with-border">
                        <h3 class="box-title text-sm">Customer Info</h3>
                        <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button></div>
                    </div>
                    
                    <div class="box-body">
                        <div class="box-body table-responsive no-padding">
                            <input type="hidden" name="full_name" class="full_name" value="">
                            <table class="table table-hover table-customer-info"><tbody>
                                <tr class="individual_name"><th>Name</th><td class="full_name"></td></tr>
                                <tr><th>Individual?</th><td class="individual"></td></tr>
                                <tr><th>City</th><td class="city"></td></tr>
                                <tr><th>Country</th><td class="country"></td></tr>
                                <tr><th>TIN</th><td class="tin"></td></tr>
                                <tr><th>Branch Code</th><td class="branch_code"></td></tr>
                                <tr><th>Phone</th><td class="phone_no"></td></tr>
                                <tr><th>Fax</th><td class="fax"></td></tr>
                                <tr><th>Email</th><td class="email"></td></tr>
                            </tbody></table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="overlay hidden">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </div>
            </div>
                    
            <div class="col-sm-6 right-info">
                <div class="input-group input-group-sm">
                    <div class="input-group-btn"><button type="button" class="btn">Entry No.</button></div>
                    @if (!empty($water_refillings))
                    <input type="text" id="entry_no" class="form-control entry-no" name="entry_no" value="{{$water_refillings->entry_no}}">
                    @else
                    <input type="text" id="entry_no" class="form-control entry-no" name="entry_no" value="0000000000">
                    @endif
                </div>
                <div class="input-group input-group-sm" style="margin-bottom: 10px;">
                    <div class="input-group-btn"><button type="button" class="btn">Date</button></div>
                    <input type="text" class="form-control datepicker" name="date">
                </div>
            </div>
            
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title err-title"> &nbsp; </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="col-lg-12">
            <label class="label-select_entry" style="display: none;">Please Select type of Entry</label>
        </div>
        <div class="box-body">
            <div class="typeofentry">
                <select class="type-entry form-control" name="type_entry">
                    <option value="0">Select type of Entry</option>
                    <option value="5">Container</option>
                    <option value="6">Dealer</option>
                    <option value="1">Delivery</option>
                    <option value="2">Refill</option>
                    <option value="3">Return</option>
                    <option value="4">Others</option>
                </select>
            </div>
            <div class="searh-div" style="margin-bottom: 10px;">
                <input type="text" name="search_prod" class="search_prod form-control" id="prod_ajax" placeholder="Search Product Name" autocomplete="off"/>
                <input type="text" name="qty" class="prod-qty form-control" placeholder="Qty"/>
                <input type="hidden" name="pro_id" class="pro_id" value=""/>
                <button type="button" class="btn btn-primary btn-enter">Enter</button>
            </div>
            <div style="clear: both;"></div>
            <div class="table-responsive">
                <table id="tbl-items" class="table table-bordered table-striped table-hover table-product-info">
                  <thead>
                    <tr>
                        <th class="text-right actions"></th>
                        <th style="width: 60%; text-align: center;">Item</th>
                        <th style="text-align: center; width: 70px;">Qty</th>
                        <th style="text-align: center; width: 70px;">Price</th>
                        <th style="text-align: center; width: 70px;">Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                  <tfoot>
                    <tr class="total-row">
                      <td colspan="4" class="text-right"> <input type="hidden" name="amount" class="form-control amount" value="0.00" readonly=""> Total: </td>
                      <td class="total text-right amount">0.00</td>
                    </tr>
                  </tfoot>
                </table>
            </div>
            <div class="row">
              <div class="col-md-6">
                <button type="button" class="btn btn-default btn-calculate" style="display: none;">Calculate</button>
              </div>
              <div class="col-md-6">
                <table class="table no-border sales-details">
                  <tbody>
                    <tr class="re_bots" style="display: none;">
                        <td></td>
                        <td colspan="2"><h3>Return Bottle(s)</h3></td>
                    </tr>
                    <tr class="issued_bottle-row">
                      <td class="text-right f-label">Issued Bottle</td>
                      <td colspan="2" class="field-only">{!! Form::text('issued_bottle', null,['class' => 'form-control issued_bottle numbers-only', 'readonly']) !!}</td>
                    </tr>
                    <tr class="return_bottle-row">
                      <td class="text-right f-label">Return Bottle</td>
                      <td colspan="2" class="field-only">{!! Form::text('return_bottle', null, ['class' => 'form-control return_bottle numbers-only']) !!}</td>
                    </tr>
                    <tr class="refill_bottle-row">
                        <td class="text-right f-label">Refill Bottle</td>
                        <td colspan="2" class="field-only">{!! Form::text('refill_bottle', null, ['class' => 'form-control refill_bottle numbers-only', 'readonly']) !!}</td>
                    </tr>
                    <tr class="order_qty-row">
                      <td class="text-right f-label">Order QTY</td>
                      <td colspan="2" class="field-only">{!! Form::text('order_qty', null, ['class' => 'form-control order_qty numbers-only', 'readonly']) !!}</td>
                    </tr>
                    <tr class="container-row">
                        <td class="text-right f-label">Container QTY</td>
                        <td colspan="2" class="field-only">{!! Form::text('container_qty', null, ['class' => 'form-control container_qty numbers-only', 'readonly']) !!}</td>
                    </tr>
                    <tr class="dealer-row">
                            <td class="text-right f-label">Dealer QTY</td>
                            <td colspan="2" class="field-only">{!! Form::text('dealer_qty', null, ['class' => 'form-control dealer_qty numbers-only', 'readonly']) !!}</td>
                        </tr>
                    <tr class="others-row">
                        <td class="text-right f-label">Others QTY</td>
                        <td colspan="2" class="field-only">{!! Form::text('others_qty', null, ['class' => 'form-control others_qty numbers-only', 'readonly']) !!}</td>
                    </tr>
                    <tr class="amount_due-row">
                      <td class="text-right f-label">Amount Due</td>
                      <td colspan="2" class="field-only">{!! Form::text('amount_due', null, ['class' => 'form-control amount_due numbers-only','readonly']) !!}</td>
                    </tr>
                    <tr class="amount_pay-row">
                        <td class="text-right f-label">Amount Paid</td>
                        <td colspan="2" class="field-only">{!! Form::text('amount_pay', null, ['class' => 'form-control amount_pay numbers-only']) !!}</td>
                      </tr>
                      <tr class="balance-row">
                        <td class="text-right f-label">Balance</td>
                        <td colspan="2" class="field-only">{!! Form::text('amt_balance', null, ['class' => 'form-control amt_balance numbers-only','readonly']) !!}</td>
                      </tr>
                      <input type="hidden" name="type_filter" value="">
                      {!! Form::hidden('status', null, ['class' => 'form-control status']) !!}
                  </tbody>
                </table>
              </div>
            </div>
        </div>
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

@include('product.table')

@endsection


@section('footer_script_preload')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection


@section('footer_script')
@include('water-refilling-monitoring.form-script')  
<script>
    $('.datepicker').datepicker('update', new Date());

    $('[name=amt_balance]').val('0.00');
    $('[name=amount_pay]').val('0.00');

    $('[name=amt_balance]').val('0.00');
    $('[name=change]').val('0.00');

    $( document ).ready(function() {
    
        $('.amount_pay').click( function( event_details ) {
            $(this).select();
        });
        
        $( "#prod_ajax" ).focus();
    });

    $( ".btn-search" ).click(function() {
        $( "#prod_ajax" ).focus();
    });

    $( ".amount_pay" ).change(function() {
        $( "#prod_ajax" ).focus();
    });

    $( ".datepicker" ).change(function() {
        $( "#prod_ajax" ).focus();
    });
</script>
<style type="text/css">
div.typeofentry{
    float: left;
    margin-right: 10px;
}
</style>
@endsection