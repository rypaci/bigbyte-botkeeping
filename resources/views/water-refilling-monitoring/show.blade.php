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
            <h1>Showing Record for ({{ $wrm_records->id }})</h1>
        </div>
        
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ url('water-refilling-monitoring/reports') }}"> Back</a>
        </div>
        <div style="clear: both;"></div>
        
    </section>

    <!-- Main content -->
    <section class="content">
    <div class="box">
            <div class="box-header with-border">
                    <h3 class="box-title">Customer Information</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                      <div class="col-md-12">
                            <table class="table no-border">
                                <tbody>
                                    <tr class="entry_no-row">
                                        <td class="text-left f-label">Name</td>
                                    <td colspan="2" class="field-only form-control">{{$customer_info->first_name}} {{$customer_info->last_name}} {{$customer_info->middle_name}}{{$customer_info->company_name}}</td>
                                    </tr>
                                    <tr class="individual-row">
                                        <td class="text-left f-label">Individual</td>
                                        <td colspan="2" class="field-only form-control">@if($customer_info->individual=='1') <span class="badge bg-green">Yes</span> @else <span class="badge bg-orange">No</span> @endif</td> 
                                    </tr>
                                    <tr class="city-row">
                                        <td class="text-left f-label">City</td>
                                        <td colspan="2" class="field-only form-control">{{$customer_info->city}}</td>
                                    </tr>
                                    <tr class="country-row">
                                        <td class="text-left f-label">Country</td>
                                        <td colspan="2" class="field-only form-control">{{$customer_info->country}}</td>
                                    </tr>
                                    <tr class="tin-row">
                                        <td class="text-left f-label">TIN</td>
                                        <td colspan="2" class="field-only form-control">{{$customer_info->tin}}</td>
                                    </tr>
                                    <tr class="branch_code-row">
                                        <td class="text-left f-label">Branch code</td>
                                        <td colspan="2" class="field-only form-control">{{$customer_info->branch_code}}</td>
                                    </tr>
                                    <tr class="phone-row">
                                        <td class="text-left f-label">Phone</td>
                                        <td colspan="2" class="field-only form-control">{{$customer_info->phone_no}}</td>
                                    </tr>
                                    <tr class="fax-row">
                                        <td class="text-left f-label">Fax</td>
                                        <td colspan="2" class="field-only form-control">{{$customer_info->fax}}</td>
                                    </tr>
                                    <tr class="email-row">
                                        <td class="text-left f-label">Email</td>
                                        <td colspan="2" class="field-only form-control">{{$customer_info->email}}</td>
                                    </tr>
                                </tbody>
                            </table>
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
                            <tr class="entry_no-row">
                                <td class="text-left f-label">Entry No.</td>
                                <td colspan="2" class="field-only form-control">{{$wrm_records->entry_no}}</td>
                            </tr>
                            <tr class="date-row">
                                <td class="text-left f-label">Date</td>
                                <td colspan="2" class="field-only form-control">{{$wrm_records->date}}</td>
                            </tr>
                            <tr class="return_bottle-row">
                                <td class="text-left f-label">Returned Bottles</td>
                                <td colspan="2" class="field-only form-control">{{$wrm_records->return_bottle}}</td>
                            </tr>
                            <tr class="refill_bottle-row">
                                <td class="text-left f-label">Refilled Bottles</td>
                                <td colspan="2" class="field-only form-control">{{$wrm_records->refill_bottle}}</td>
                            </tr>
                            <tr class="order_qty-row">
                                <td class="text-left f-label">Delivered Bottles</td>
                                <td colspan="2" class="field-only form-control">{{$wrm_records->order_qty}}</td>
                            </tr>
                            <tr class="container-row">
                                <td class="text-left f-label">Container Sold</td>
                                <td colspan="2" class="field-only form-control">{{$wrm_records->container_qty}}</td>
                            </tr>
                            <tr class="dealer-row">
                                <td class="text-left f-label">Dealed Bottles</td>
                                <td colspan="2" class="field-only form-control">{{$wrm_records->dealer_qty}}</td>
                            </tr>
                            <tr class="others-row">
                                <td class="text-left f-label">Others bottles</td>
                                <td colspan="2" class="field-only form-control">{{$wrm_records->others_qty}}</td>
                            </tr>
                            <tr class="amount_due-row">
                                <td class="text-left f-label">Amount Due</td>
                                <td colspan="2" class="field-only form-control">{{number_format($wrm_records->amount_due,2)}}</td>
                            </tr>
                            <tr class="status-row">
                                <td class="text-left f-label">Status</td>
                                <td colspan="2" class="field-only form-control">{{$wrm_records->status}}</td>
                            </tr>
                        </tbody>
                    </table>
              </div>
            </div>
        </div>
    </div>

    <div class="row">
      <div class="col-sm-3">
        <div class="form-group">
                <a href="{{ url('water-refilling-monitoring/reports') }}" class="btn btn-primary">Back</a>
				<a href="{{ url('water-refilling-monitoring/' . $wrm_records->id . '/edit') }}" class="btn btn-primary" title="Edit Records"><i class="fa fa-pencil"></i> Edit Records</a>
        </div>
      </div>
    </div>
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
td.field-only{background-color: #eeeeee;}
</style>
@endsection