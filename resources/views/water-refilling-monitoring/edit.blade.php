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
            <h1>Edit Record ({{ $wrm_records->id }})</h1>
        </div>
        
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ url('water-refilling-monitoring/reports') }}"> Back</a>
        </div>
        <div style="clear: both;"></div>
        
    </section>

    <!-- Main content -->
    <section class="content">
    
    {!! Form::model($wrm_records, [
        'method' => 'PATCH',
        'url' => ['/water-refilling-monitoring', $wrm_records->id],
        'class' => ''
    ]) !!}

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
                    <input type="hidden" name="customer_id" class="id" value="{{ $customer_info->id }}">
                    
                    <span class="input-group-btn"><button type="button" class="btn btn-info btn-flat btn-search">Go!</button></span>
                </div>
                <button type="button" class="btn btn-info btn-flat customer-search_by_id hidden" data-customer_id="{{ $customer_info->id }}">Hidden customer search by id</button>
        
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
                    <div class="input-group-btn"><button type="button" class="btn">Inv. No.</button></div>
                    {!! Form::text('entry_no', null, ['class' => 'form-control entry_no','readonly']) !!}
                </div>
                <div class="input-group input-group-sm" style="margin-bottom: 10px;">
                    <div class="input-group-btn"><button type="button" class="btn">Date</button></div>
                    {!! Form::text('date', null, ['class' => 'form-control datepicker']) !!}
                </div>
            </div>
            
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Transaction Details</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
              <div class="col-md-12">
                    <table class="table no-border">
                        <tbody>
                            <tr class="return_bottle-row">
                                <td class="text-left f-label">Returned Bottles</td>
                                <td colspan="2" class="field-only">{!! Form::text('return_bottle', null, ['class' => 'form-control return_bottle numbers-only']) !!}</td>
                            </tr>
                            <tr class="refill_bottle-row">
                                <td class="text-left f-label">Refilled Bottles</td>
                                <td colspan="2" class="field-only">{!! Form::text('refill_bottle', null, ['class' => 'form-control refill_bottle numbers-only']) !!}</td>
                            </tr>
                            <tr class="order_qty-row">
                                <td class="text-left f-label">Delivered Bottles</td>
                                <td colspan="2" class="field-only">{!! Form::text('order_qty', null, ['class' => 'form-control order_qty numbers-only']) !!}</td>
                            </tr>
                            <tr class="container-row">
                                <td class="text-left f-label">Container Sold</td>
                                <td colspan="2" class="field-only">{!! Form::text('container_qty', null, ['class' => 'form-control container_qty numbers-only']) !!}</td>
                            </tr>
                            <tr class="dealer-row">
                                    <td class="text-left f-label">Dealed Bottles</td>
                                    <td colspan="2" class="field-only">{!! Form::text('dealer_qty', null, ['class' => 'form-control dealer_qty numbers-only']) !!}</td>
                                </tr>
                            <tr class="others-row">
                                <td class="text-left f-label">Others bottles</td>
                                <td colspan="2" class="field-only">{!! Form::text('others_qty', null, ['class' => 'form-control others_qty numbers-only']) !!}</td>
                            </tr>
                            <tr class="amount_due-row">
                                <td class="text-left f-label">Amount Due</td>
                                <td colspan="2" class="field-only">{!! Form::text('amount_due', null, ['class' => 'form-control amount_due numbers-only']) !!}</td>
                            </tr>
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
            {!! Form::submit('Update', ['class' => 'btn btn-primary form-control']) !!}
        </div>
      </div>
    </div>
    
    {!! Form::close() !!}
    
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@include('cash-invoice.prod-row-template')

@endsection


@section('footer_script_preload')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection


@section('footer_script')
@include('cash-invoice.form-script')
<script>
    //var validation_url = '{{url("cash-invoice/edit-form-validate")}}';
    
    /* Search Customer by ID */
    $('.customer-search_by_id').trigger('click');
</script>
<style>
td.text-left{width: 150px;}
</style>
@endsection