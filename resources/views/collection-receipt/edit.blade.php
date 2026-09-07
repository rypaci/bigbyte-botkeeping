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
table .form-control[readonly] { background-color: transparent; }

.right-info .input-group, .left-info .input-group { margin-bottom: 4px; }
.customer-info-box { margin-top: 12px; }
.box-header .box-title.text-sm { font-size: 12px; }
table.table.table-hover { font-size: 14px; }
.table-account-details input, .table-account-details select, .table-account-details textarea { border: 0; }
.table-invoice-details input, .table-invoice-details select, .table-invoice-details textarea { border: 0; }
.table-invoice-details th.description { width: 50%; }
.table-invoice-details th.current_payment { width: 15%; }
.table-invoice-details th.amount_payable { width: 15%; }
.table-account-details th:nth-child(1) { width:20%; }
.table-account-details th:nth-child(2) { width:30%; }
.table-account-details th:nth-child(3) { width:20%; }
.table-account-details th:nth-child(4) { width:15%; }
.table-account-details th:nth-child(5) { width:15%; }
tr.debit > td.account-title, tr.debit > td.tax { padding-right: 30px; }
tr.credit > td.account-title, tr.credit > td.tax { padding-left: 30px; }
.table-account-details td>span { padding: 6px; display: block; font-size: 12px; }
.table-account-details td input { width: 100%; }
table.table.table-hover.table-customer-info { font-size: 12px; }
.table-customer-info th { width: 30% }
.table.table-customer-info > tbody > tr > td, .table.table-customer-info > tbody > tr > th { padding:4px; }
.delete-row:hover { cursor:pointer; }
select.chart-account-dropdown { width: 100%; }
.table-account-details .form-control[readonly] { background-color: transparent; }
.table > tbody > tr.total-row > td { padding-left: 18px; padding-right: 18px; font-weight: bold; }
.total-row td:first-child { text-align: right; }
.paymethod .checkbox.icheck { display: inline; margin-right: 30px; }
.paymethod { margin-left: 15px; display: none; }
.input-group.payment_method { margin-top: 20px; }
.sales-discount-row>td.field-label { padding-right:18px }
input[name=sales_discount] { padding:1px 10px }
</style>
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Edit Collection Receipt ({{ $collectionreceipt->id }})
        </h1>
    
    </section>

    <!-- Main content -->
    <section class="content">
    
    {!! Form::model($collectionreceipt, [
        'method' => 'PATCH',
        'url' => ['/collection-receipt', $collectionreceipt->id],
        'class' => ''
    ]) !!}
    {!! Form::hidden('id', null, ['class' => 'form-control']) !!}

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"> &nbsp; </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
            
            <div class="col-sm-6">
              <div class="input-group input-group-sm">
                <div class="input-group-btn"><button type="button" class="btn">Customer</button></div>
                <!-- /btn-group -->
                {!! Form::text('customer_name', null, ['class' => 'form-control customer_name', 'id' => 'cust_ajax']) !!}
                {!! Form::hidden('customer_id', null, ['class' => 'form-control']) !!}
                
                <span class="input-group-btn"><button type="button" class="btn btn-info btn-flat btn-search">Go!</button></span>
              </div>
              <button type="button" class="btn btn-info btn-flat customer-search_by_id hidden" data-customer_id="{{ $collectionreceipt->customer_id }}">Hidden vendor search by id</button>
              
              <div class="box box-info box-solid customer-info-box">
                <div class="box-header with-border">
                  <h3 class="box-title text-sm">Customer Info</h3>
                  <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button></div>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="box-body table-responsive no-padding">
                    <input type="hidden" name="full_name" class="full_name" value="">
                    <table class="table table-hover table-customer-info"><tbody>
                        <tr class="entity_name"><th>Name</th><td class="full_name"></td></tr>
                        <tr><th>Individual?</th><td class="individual"></td></tr>
                        <tr><th>City</th><td class="city"></td></tr>
                        <tr><th>Country</th><td class="country"></td></tr>
                        <tr><th>TIN</th><td class="tin"></td></tr>
                        <tr><th>Branch Code</th><td class="branch_code"></td></tr>
                        <tr><th>Opening Balance</th><td class="opening_balance"></td></tr>
                        <tr><th>Phone</th><td class="phone"></td></tr>
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
                <div class="input-group-btn">
                  {!! Form::hidden('credit_invoice_id', null, ['class' => 'form-control']) !!}
                  <button type="button" class="btn">CR No.</button>
                </div>
                <!-- /btn-group -->
                {!! Form::text('cr_number', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
              </div>
              <div class="input-group input-group-sm">
                <div class="input-group-btn">
                  <button type="button" class="btn">Date</button>
                </div>
                <!-- /btn-group -->
                {!! Form::text('date', null, ['class' => 'form-control datepicker', 'required' => 'required']) !!}
              </div>
              <div class="input-group input-group-sm">
                <div class="input-group-btn">
                  <button type="button" class="btn">Amount</button>
                </div>
                <!-- /btn-group -->
                {!! Form::text('amount', null, ['class' => 'form-control']) !!}
              </div>
              <div class="input-group input-group-sm payment_method">
                <div class="input-group-btn">
                  <button type="button" name="" class="btn">Payment Method</button>
                </div>
                <!-- /btn-group -->
                {!! Form::select('payment_method', ['cash'=>'Cash', 'check'=>'Check'], '', ['class' => 'form-control']) !!}
              </div>
              <div class="input-group input-group-sm paymethod paymethod-cash">
                <div class="checkbox icheck">
                  <label>
                    {!! Form::checkbox('on_hand', '1', $collectionreceipt->on_hand==1) !!} On Hand
                  </label>
                </div>
                <div class="checkbox icheck">
                  <label>
                    {!! Form::checkbox('bank', '1', $collectionreceipt->bank==1) !!} Bank
                  </label>
                </div>
              </div>
              <div class="input-group input-group-sm paymethod paymethod-check">
                <div class="input-group-btn">
                  <button type="button" class="btn">Bank</button>
                </div>
                <!-- /btn-group -->
                {!! Form::text('bank_code', null, ['class' => 'form-control']) !!}
              </div>
              <div class="input-group input-group-sm paymethod paymethod-check">
                <div class="input-group-btn">
                  <button type="button" class="btn">Check No.</button>
                </div>
                <!-- /btn-group -->
                {!! Form::text('check_number', null, ['class' => 'form-control']) !!}
              </div>
            </div>
            
            </div>
            <!-- /.row -->
            
        </div>
        <!-- ./box-body -->
    </div>
    
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"> Invoice Details </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover table-invoice-details">
            <tbody><tr class="head">
              <th>Invoice #</th>
              <th class="description"></th>
              <th class="amount_payable">Amount Payable</th>
              <th class="current_payment">Current Payment</th>
            </tr>
            <tr class="invoice-row">
              <td> <input type="text" name="invoice_number" value="{{ $invoice->invoice_number }}" class="form-control input-sm invoice_number" readonly> </td>
              <td> <input type="text" name="description" value="{{ $invoice->description }}" class="form-control input-sm description" readonly> </td>
              <td> <input type="text" name="amount_payable" value="{{ $invoice->amount_due }}" class="form-control input-sm amount_payable" readonly> </td>
              <td> <input type="text" name="current_payment" value="{{ $collectionreceipt->amount }}" class="form-control input-sm current_payment" readonly> </td>
            </tr>
            <tr class="sales-discount-row">
              <td colspan="3" class="text-right field-label"> Sales Discount: </td>
              <td class="sales-discount"> {!! Form::text('sales_discount', null, ['class' => '']) !!} </td>
            </tr>
            <tr class="total-row">
              <td colspan="3"> <input type="hidden" value="{{ $collectionreceipt['balance'] }}" name="balance"> Balance: </td>
              <td class="balance"> {{ number_format($collectionreceipt['balance'], 2, '.', '') }} </td>
            </tr>
          </tbody></table>
        </div>
        <!-- ./box-body -->
    </div>
    
    <div class="box account-details-section">
        <div class="box-header with-border">
            <h3 class="box-title">Account Details</h3>
            <div class="box-tools pull-right">
                <a href="{{url('collection-receipt/account-details')}}" target="_blank" class="btn btn-box-tool" data-toggle="tooltip" title="" data-original-title="Manage Account Details"><i class="fa fa-wrench"></i></a>
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
                <tr class="account-row debit">
                  <td class="account-number"> <input type="text" name="vouchers[0][code]" value="{{ $vouchers_by_key['coa_debit']->account->code }}" class="form-control input-sm debit code" readonly /> </td>
                  <td class="account-title"> 
                    <input type="hidden" value="0" name="vouchers[0][tax_id]" /> 
                    <select class="form-control input-sm chart-account-dropdown" name="vouchers[0][chart_account_id]">
                        @if (count($coas['debit'])>1)<option value=""> Select Chart of Account </option>@endif
                        @foreach ($coas['debit'] as $c)
                        <option data-code="{{ $c['code'] }}" value="{{ $c['id'] }}" {!! selected($debit_coa_id, $c['id'], false) !!}>{{ $c['name'] }}</option>
                        @endforeach
                    </select> 
                  </td>
                  <td class="ref-number"> <input type="text" name="vouchers[0][ref_number]" value="{{ $vouchers_by_key['coa_debit']->ref_number }}" class="form-control input-sm" readonly /> </td>
                  <td class="debit"> <input type="text" name="vouchers[0][debit]" value="{{ $vouchers_by_key['coa_debit']->debit }}" class="form-control input-sm debit debit-field" readonly /> </td>
                  <td class="credit"> <input type="hidden" value="0" name="vouchers[0][credit]" /> </td>
                  <input type="hidden" value="coa_debit" name="vouchers[0][key]" />
                  <input type="hidden" value="{{ $vouchers_by_key['coa_credit']->id }}" name="vouchers[0][id]" />
                </tr>
                <tr class="tax-row debit">
                  <td class="account-number"> 
                    <input class="rate" type="hidden" @if($debit_tax_id)value="{{ $vouchers_by_key['tax_debit']->tax->rate }}"@endif name="vouchers[1][rate]" /> 
                    <input type="text" name="vouchers[1][code]" @if($debit_tax_id)value="{{ $vouchers_by_key['tax_debit']->account->code }}"@endif class="form-control input-sm debit code" readonly />
                  </td>
                  <td class="tax"> 
                    <input type="hidden" value="{{ $debit_tax_id }}" name="vouchers[1][tax_id]" class="debit tax_id" /> 
                    <select class="form-control input-sm tax-dropdown" name="vouchers[1][chart_account_id]">
                        <option value=""> Select Withholding Tax </option>
                        @foreach ($taxes['debit'] as $t)
                        <option data-code="{{ $t['code'] }}" data-rate="{{ $t['rate'] }}" data-tax_id="{{ $t['tax_id'] }}" value="{{ $t['id'] }}" {!! selected($debit_tax_coa_id, $t['id'], false) !!}>{{ $t['name'] }}</option>
                        @endforeach
                    </select> 
                  </td>
                  <td class="ref-number"> <input type="text" name="vouchers[1][ref_number]" @if($debit_tax_id)value="{{ $vouchers_by_key['tax_debit']->ref_number }}"@endif class="form-control input-sm" readonly /> </td>
                  <td class="debit"> <input type="text" name="vouchers[1][debit]" @if($debit_tax_id)value="{{ $vouchers_by_key['tax_debit']->debit }}"@endif class="form-control input-sm debit debit-field" readonly /> </td>
                  <td class="credit"> <input type="hidden" value="0" name="vouchers[1][credit]" /> </td>
                  <input type="hidden" value="tax_debit" name="vouchers[1][key]" />
                  <input type="hidden" @if($debit_tax_id)value="{{ $vouchers_by_key['tax_debit']->id }}"@endif name="vouchers[1][id]" />
                </tr>
                <tr class="discount-row debit">
                  <td class="account-number"> <input type="text" name="vouchers[2][code]" value="{{ $vouchers_by_key['discount_debit']->account->code }}" class="form-control input-sm debit code" readonly /> </td>
                  <td class="account-title"> 
                    <input type="hidden" value="0" name="vouchers[2][tax_id]" /> 
                    <select class="form-control input-sm chart-account-dropdown" name="vouchers[2][chart_account_id]">
                        @if (count($discounts['debit'])>1)<option value=""> Select Chart of Account </option>@endif
                        @foreach ($discounts['debit'] as $c)
                        <option data-code="{{ $c['code'] }}" value="{{ $c['id'] }}" {!! selected($debit_disc_coa_id, $c['id'], false) !!}>{{ $c['name'] }}</option>
                        @endforeach
                    </select> 
                  </td>
                  <td class="ref-number"> <input type="text" name="vouchers[2][ref_number]" value="{{ $vouchers_by_key['discount_debit']->ref_number }}" class="form-control input-sm" readonly /> </td>
                  <td class="debit"> <input type="text" name="vouchers[2][debit]" value="{{ $vouchers_by_key['discount_debit']->debit }}" class="form-control input-sm debit debit-field" readonly /> </td>
                  <td class="credit"> <input type="hidden" value="0" name="vouchers[2][credit]" /> </td>
                  <input type="hidden" value="discount_debit" name="vouchers[2][key]" />
                  <input type="hidden" value="{{ $vouchers_by_key['discount_debit']->id }}" name="vouchers[2][id]" />
                </tr>
                <tr class="account-row credit">
                  <td class="account-number"> <input type="text" name="vouchers[3][code]" value="{{ $vouchers_by_key['coa_credit']->account->code }}" class="form-control input-sm credit code" readonly /> </td>
                  <td class="account-title"> 
                    <input type="hidden" value="0" name="vouchers[3][tax_id]" /> 
                    <select class="form-control input-sm chart-account-dropdown coa-cash" name="vouchers[3][chart_account_id]">
                        @if (count($coas['credit'])>1)<option value=""> Select Chart of Account </option>@endif
                        @foreach ($coas['credit'] as $c)
                        <option data-code="{{ $c['code'] }}" value="{{ $c['id'] }}" {!! selected($credit_coa_id, $c['id'], false) !!}>{{ $c['name'] }}</option>
                        @endforeach
                    </select>
                  </td>
                  <td class="ref-number"> <input type="text" name="vouchers[3][ref_number]" value="{{ $vouchers_by_key['coa_credit']->ref_number }}" class="form-control input-sm" readonly /> </td>
                  <td class="debit"> <input type="hidden" value="0" name="vouchers[3][debit]" /> </td>
                  <td class="credit"> <input type="text" name="vouchers[3][credit]" value="{{ $vouchers_by_key['coa_credit']->credit }}" class="form-control input-sm credit credit-field" readonly /> </td>
                  <input type="hidden" value="coa_credit" name="vouchers[3][key]" />
                  <input type="hidden" value="{{ $vouchers_by_key['coa_credit']->id }}" name="vouchers[3][id]" />
                </tr>
                <tr class="total-row">
                  <td>  </td>
                  <td colspan="2" class="text-right"> Total: </td>
                  <td class="debit">{{ $collectionreceipt->amount }}</td>
                  <td class="credit">{{ $collectionreceipt->amount }}</td>
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

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection


@section('footer_script_preload')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
<script src="{{ url('/') }}/assets/plugins/iCheck/icheck.min.js"></script>
@endsection


@section('footer_script')
@include('collection-receipt.form-script')  
<script>
    var validation_url = '{{url("collection-receipt/edit-form-validate")}}';
    
    /* Search Customer by ID */
    $('.customer-search_by_id').trigger('click');
</script>
@endsection