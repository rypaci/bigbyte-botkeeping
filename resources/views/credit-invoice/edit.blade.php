@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('header_style_preload')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ url('/') }}/assets/plugins/iCheck/square/blue.css">
@endsection

@section('header_style_postload')
<style>
.input-group.open-invoice { margin-top: 20px; }
.open-invoice-child { margin-left: 15px; display: none; }

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
            <h1>Edit Credit Invoice ({{ $creditinvoice->id }})</h1>
        </div>
        
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('credit-invoice.index') }}"> Back</a>
        </div>
        <div style="clear: both;"></div>
        
    </section>

    <!-- Main content -->
    <section class="content">
    
    {!! Form::model($creditinvoice, [
        'method' => 'PATCH',
        'url' => ['/credit-invoice', $creditinvoice->id],
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
                    <input type="hidden" name="customer_id" class="id" value="{{ $creditinvoice->customer_id }}">
                    
                    <span class="input-group-btn"><button type="button" class="btn btn-info btn-flat btn-search" disabled="disabled">Go!</button></span>
                </div>
                <button type="button" class="btn btn-info btn-flat customer-search_by_id hidden" data-customer_id="{{ $creditinvoice->customer_id }}">Hidden customer search by id</button>
        
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
                    {!! Form::text('invoice_number', null, ['class' => 'form-control invoice-no']) !!}
                </div>
                <div class="input-group input-group-sm">
                    <div class="input-group-btn"><button type="button" class="btn">Date</button></div>
                    {!! Form::text('date', null, ['class' => 'form-control datepicker']) !!}
                </div>
                <div class="input-group input-group-sm">
                    <div class="input-group-btn"><button type="button" class="btn">Terms</button></div>
                    {!! Form::select('terms', $term_options, null, ['class' => 'form-control']) !!}
                </div>
                <div class="input-group input-group-sm open-invoice">
                  <div class="checkbox icheck">
                    <label>
                      {!! Form::checkbox('for_open_invoice', '1', $creditinvoice->for_open_invoice==1) !!} for Open Invoice?
                    </label>
                  </div>
                </div>
                <div class="input-group input-group-sm open-invoice-child">
                    <div class="input-group-btn"><button type="button" class="btn">Open Invoice</button></div>
                    <select class="form-control" name="open_invoice_id">
                      @foreach ($open_invoices as $oi)
                      <option value="{{ $oi['id'] }}" {!! selected($creditinvoice->open_invoice_id, $oi['id'], false) !!} data-amount="{{ $oi['amount'] }}">{{ $oi['oi_number'] }}</option>
                      @endforeach
                    </select>
                </div>
            </div>
            
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"> &nbsp; </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="searh-div" style="margin-bottom: 10px;">
                <input type="text" name="search_prod" class="search_prod form-control" id="prod_ajax" placeholder="Search Product Name" autocomplete="off"/>
                <input type="text" name="qty" class="prod-qty form-control" placeholder="Qty"/>
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
                  {{-- */$x=0;/* --}}
                  @foreach($orderitems as $item)
                    @include('product.table_row')
                    {{-- */$x++;/* --}}
                  @endforeach
                  {{-- */unset($x);/* --}}
                  </tbody>
                  <tfoot>
                    <tr class="total-row">
                      <td colspan="4" class="text-right"> {!! Form::hidden('amount', null, ['class' => 'form-control amount']) !!} Total: </td>
                      <td class="total text-right amount">{{ $creditinvoice->amount }}</td>
                    </tr>
                  </tfoot>
                </table>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <button type="button" class="btn btn-default btn-calculate">Calculate</button>
              </div>
              <div class="col-md-6">
                <table class="table no-border sales-details">
                  <tbody>
                    <tr class="no_of_person-row show-on-discount"@if($creditinvoice->discounted) style="display:table-row" @endif>
                      <td class="text-right f-label">No. of Person</td>
                      <td colspan="2" class="field-only">{!! Form::text('no_of_person', null, ['class' => 'form-control no_of_person numbers-only']) !!}</td>
                    </tr>
                    <tr class="no_of_scpwd-row show-on-discount"@if($creditinvoice->discounted) style="display:table-row" @endif>
                      <td class="text-right f-label">No. of SC/PWD</td>
                      <td colspan="2" class="field-only">{!! Form::text('no_of_scpwd', null, ['class' => 'form-control no_of_scpwd numbers-only']) !!}</td>
                    </tr>
                    <tr class="discount-row">
                      <td class="text-right f-label">Less: SC/PWD Discount</td>
                      <td class="field-only">
                        {!! Form::select('discounted', ['0' => 'No', '1' => 'Yes'], null, ['class' => 'form-control discounted']) !!}
                        {!! Form::hidden('discount_id', $discount->id, [
                          'class' => 'discount', 'data-rate' => $discount->rate, 'data-code' => $discount->ca_code, 'data-id' => $discount->id,
                          'data-chart_account_id' => $discount->ca_id, 'data-chart_account_name' => $discount->ca_name
                        ]) !!}
                      </td>
                      <td class="field-only">{!! Form::text('discount_amount', null, ['class' => 'form-control discount_amount numbers-only']) !!}</td>
                    </tr>
                    <tr class="vatable_sales-row">
                      <td class="text-right f-label">Vatable Sales</td>
                      <td colspan="2" class="field-only">{!! Form::text('vatable_sales', null, ['class' => 'form-control vatable_sales numbers-only']) !!}</td>
                    </tr>
                    <tr class="vat_exempt_sales-row show-on-discount"@if($creditinvoice->discounted) style="display:table-row" @endif>
                      <td class="text-right f-label">Vat-exempt Sales</td>
                      <td colspan="2" class="field-only">{!! Form::text('vat_exempt_sales', null, ['class' => 'form-control vat_exempt_sales numbers-only']) !!}</td>
                    </tr>
                    <tr class="net_sales-row show-on-discount"@if($creditinvoice->discounted) style="display:table-row" @endif>
                      <td class="text-right f-label">Net Sales</td>
                      <td colspan="2" class="field-only">{!! Form::text('net_sales', null, ['class' => 'form-control net_sales numbers-only']) !!}</td>
                    </tr>
                    <tr class="vat-row">
                      <td class="text-right f-label">VAT</td>
                      <td colspan="2" class="field-only">
                        {!! Form::text('vat_amount', null, ['class' => 'form-control vat_amount numbers-only']) !!}
                        {!! Form::hidden('vat_id', $vat->id, ['class' => 'vat', 'data-rate' => $vat->rate, 'data-chart_account_id' => $vat->ca_id, 'data-chart_account_name' => $vat->ca_name]) !!}
                      </td>
                    </tr>
                    <tr class="withholding_tax-row">
                      <td class="text-right f-label">Withholding Tax</td>
                      <td class="field-only">{!! custom_form_select('whtax_id', $withholding['value'], ['class' => 'form-control withholding_tax'], $withholding['options'], $withholding['data_fields'], false) !!}</td>
                      <td class="field-only">{!! Form::text('whtax_amount', null, ['class' => 'form-control whtax_amount numbers-only']) !!}</td>
                    </tr>
                    <tr class="advance-payment hidden">
                      <td class="text-right f-label">Advance Payment</td>
                      <td colspan="2" class="field-only">{!! Form::text('advance_payment', null, ['class' => 'form-control advance-payment numbers-only']) !!}</td>
                    </tr>
                    <tr class="amount_due-row">
                      <td class="text-right f-label">Amount Due</td>
                      <td colspan="2" class="field-only">{!! Form::text('amount_due', null, ['class' => 'form-control amount_due numbers-only']) !!}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
        </div>
    </div>
        
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Account Details</h3>
            <div class="box-tools pull-right">
                <a href="{{url('credit-invoice/account-details')}}" target="_blank" class="btn btn-box-tool" data-toggle="tooltip" title="" data-original-title="Manage Account Details"><i class="fa fa-wrench"></i></a>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover table-account-details">
                <tbody><tr class="head">
                  <th>Account #</th>
                  <th>Account Title</th>
                  <th>Ref #</th>
                  <th class="debit">Debit</th>
                  <th class="credit">Credit</th>
                </tr>
                {{-- */extract($account_rows[0]);/* Main Debit --}}
                {!! view('_includes.account-row-template', compact('parent_name', 'entry_type', 'account_row_class', 'select', 'key', 'voucher')) !!}
                
                {{-- */extract($account_rows[1]);/* Withholding Tax Debit optional --}}
                {!! view('_includes.account-row-template', compact('parent_name', 'entry_type', 'account_row_class', 'select', 'key', 'voucher')) !!}
                
                {{-- */extract($account_rows[2]);/* Discount Debit optional --}}
                {!! view('_includes.account-row-template', compact('parent_name', 'entry_type', 'account_row_class', 'select', 'key', 'voucher')) !!}
                
                {{-- */extract($account_rows[3]);/* Main Credit --}}
                {!! view('_includes.account-row-template', compact('parent_name', 'entry_type', 'account_row_class', 'select', 'key', 'voucher')) !!}
                
                {{-- */extract($account_rows[4]);/* Credit2 optional --}}
                {!! view('_includes.account-row-template', compact('parent_name', 'entry_type', 'account_row_class', 'select', 'key', 'voucher')) !!}
                
                {{-- */extract($account_rows[5]);/* Vat Tax Credit --}}
                {!! view('_includes.account-row-template', compact('parent_name', 'entry_type', 'account_row_class', 'select', 'key', 'voucher')) !!}
                <tr class="total-row">
                  <td>  </td>
                  <td colspan="2" class="text-right"> {!! Form::hidden('debit_total', null) !!}{!! Form::hidden('credit_total', null) !!} Total: </td>
                  <td class="debit"> {{ number_format($creditinvoice->debit_total, 2, '.', '') }} </td>
                  <td class="credit"> {{ number_format($creditinvoice->credit_total, 2, '.', '') }} </td>
                </tr>
              </tbody></table>
            </div>
            <!-- ./box-body -->
            <div class="box-footer clearfix">
              <div class="col-sm-6 pull-right">
                <!-- <table class="table lower-section"><tbody>
                  <tr><td class="text-right"><b>Total: </b></td><td><input type="text" name="total" class="text-right" readonly /></td></tr>
                </tbody></table> -->
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
    
    </section>
</div><!-- /.content-wrapper -->

@include('product.table')

@endsection


@section('footer_script_preload')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
<script src="{{ url('/') }}/assets/plugins/iCheck/icheck.min.js"></script>
@endsection

@section('footer_script')
@include('credit-invoice.form-script')
<script>
    var validation_url = '{{url("credit-invoice/edit-form-validate")}}';
    
    /* Search Customer by ID */
    $('.customer-search_by_id').trigger('click');
</script>
@endsection