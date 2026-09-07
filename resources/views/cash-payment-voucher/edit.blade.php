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
.vendor-info-box { margin-top: 12px; }
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
table.table.table-hover.table-vendor-info { font-size: 12px; }
.table-vendor-info th { width: 30% }
.table.table-vendor-info > tbody > tr > td, .table.table-vendor-info > tbody > tr > th { padding:4px; }
.delete-row:hover { cursor:pointer; }
select.chart-account-dropdown { width: 100%; }
.table-account-details .form-control[readonly] { background-color: transparent; }
.table > tbody > tr.total-row > td { padding-left: 18px; padding-right: 18px; font-weight: bold; }
.total-row td:first-child { text-align: right; }
.paymethod .checkbox.icheck { display: inline; margin-right: 30px; }
.paymethod { margin-left: 15px; display: none; }
.input-group.payment_method { margin-top: 20px; }
</style>
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Edit CashPaymentVoucher ({{ $cashpaymentvoucher->id }})
        </h1>
        
    </section>

    <!-- Main content -->
    <section class="content">
    
    {!! Form::model($cashpaymentvoucher, [
        'method' => 'PATCH',
        'url' => ['/cash-payment-voucher', $cashpaymentvoucher->id],
        'class' => ''
    ]) !!}
    {!! Form::hidden('id', null, ['class' => 'form-control', 'name' => 'cpv_id']) !!}

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
                <div class="input-group-btn"><button type="button" class="btn">Vendor</button></div>
                <!-- /btn-group -->
                {!! Form::text('vendor_name', null, ['class' => 'form-control vendor_name']) !!}
                {!! Form::hidden('vendor_id', null, ['class' => 'form-control']) !!}
                
                <span class="input-group-btn"><button type="button" class="btn btn-info btn-flat btn-search">Go!</button></span>
              </div>
              <button type="button" class="btn btn-info btn-flat vendor-search_by_id hidden" data-vendor_id="{{ $cashpaymentvoucher->vendor_id }}">Hidden vendor search by id</button>
              
              <div class="box box-info box-solid vendor-info-box">
                <div class="box-header with-border">
                  <h3 class="box-title text-sm">Vendor Info</h3>
                  <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button></div>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="box-body table-responsive no-padding">
                    <table class="table table-hover table-vendor-info"><tbody>
                        <tr class="individual_name"><th>Full Name</th><td class="name"></td></tr>
                        <tr class="company_name hidden"><th>Company Name</th><td class="name"></td></tr>
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
                  {!! Form::hidden('supplier_invoice_id', null, ['class' => 'form-control']) !!}
                  <button type="button" class="btn">CV No.</button>
                </div>
                <!-- /btn-group -->
                {!! Form::text('cv_number', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
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
                {!! Form::select('payment_method', ['cash'=>'Cash', 'check'=>'Check'], null, ['class' => 'form-control']) !!}
              </div>
              <div class="input-group input-group-sm paymethod paymethod-cash">
                <div class="checkbox icheck">
                  <label>
                    {!! Form::checkbox('on_hand', '1', $cashpaymentvoucher->on_hand==1) !!} On Hand
                  </label>
                </div>
                <div class="checkbox icheck">
                  <label>
                    {!! Form::checkbox('bank', '1', $cashpaymentvoucher->bank==1) !!} Bank
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
              <th class="description">Description</th>
              <th class="amount_payable">Amount Payable</th>
              <th class="current_payment">Current Payment</th>
            </tr>
            <tr class="invoice-row">
              <td> <input type="text" name="invoice_number" value="{{ $cashpaymentvoucher->invoice_number }}" class="form-control input-sm invoice_number" readonly> </td>
              <td> <input type="text" name="description" value="{{ $cashpaymentvoucher->description }}" class="form-control input-sm description" readonly> </td>
              <td> <input type="text" name="amount_payable" value="{{ $cashpaymentvoucher->invoice_amount }}" class="form-control input-sm amount_payable" readonly> </td>
              <td> <input type="text" name="current_payment" value="{{ $cashpaymentvoucher->amount }}" class="form-control input-sm current_payment" readonly> </td>
            </tr>
            <tr class="total-row">
              <td colspan="3"> <input type="hidden" value="{{ $cashpaymentvoucher['balance'] }}" name="balance"> Balance: </td>
              <td class="balance"> {{ number_format($cashpaymentvoucher['balance'], 2, '.', '') }} </td>
            </tr>
          </tbody></table>
        </div>
        <!-- ./box-body -->
    </div>
    
    <div class="box account-details-section">
        <div class="box-header with-border">
            <h3 class="box-title">Account Details</h3>
            <div class="box-tools pull-right">
                <a href="{{url('cash-payment-voucher/account-details')}}" target="_blank" class="btn btn-box-tool" data-toggle="tooltip" title="" data-original-title="Manage Account Details"><i class="fa fa-wrench"></i></a>
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
                  <td class="account-number"> <input type="text" name="vouchers[0][code]" value="{{ $coa_debit->account->code }}" class="form-control input-sm debit code" readonly /> </td>
                  <td class="account-title"> <input type="hidden" value="0" name="vouchers[0][tax_id]" /> 
                    <select class="form-control input-sm chart-account-dropdown coa-accounts_payable" name="vouchers[0][chart_account_id]">
                      @if (count($coas['debit'])>1)<option value=""> Select Chart of Account </option>@endif
                      @foreach ($coas['debit'] as $c)
                      <option data-code="{{ $c['code'] }}" value="{{ $c['id'] }}" {!! selected($coa_debit_id, $c['id'], false) !!}>{{ $c['name'] }}</option>
                      @endforeach
                    </select> 
                  </td>
                  <td class="ref-number"> <input type="text" name="vouchers[0][ref_number]" value="{{ $coa_debit->ref_number }}" class="form-control input-sm" readonly /> </td>
                  <td class="debit"> <input type="text" name="vouchers[0][debit]" value="{{ $coa_debit->debit }}" class="form-control input-sm debit debit-field" readonly /> </td>
                  <td class="credit"> <input type="hidden" value="0" name="vouchers[0][credit]" /> </td>
                  <input type="hidden" value="coa_debit" name="vouchers[0][key]" />
                </tr>
                <tr class="account-row credit">
                  <td class="account-number"> <input type="text" name="vouchers[1][code]" value="{{ $coa_credit->account->code }}" class="form-control input-sm credit code" readonly /> </td>
                  <td class="account-title"> <input type="hidden" value="0" name="vouchers[1][tax_id]" /> 
                    <select class="form-control input-sm chart-account-dropdown coa-cash" name="vouchers[1][chart_account_id]">
                      <option value=""> Select Chart of Account </option>
                      @foreach ($coas['credit'] as $c)
                      <option data-code="{{ $c['code'] }}" value="{{ $c['id'] }}" {!! selected($coa_credit_id, $c['id'], false) !!}>{{ $c['name'] }}</option>
                      @endforeach
                    </select> 
                  </td>
                  <td class="ref-number"> <input type="text" name="vouchers[1][ref_number]" value="{{ $coa_credit->ref_number }}" class="form-control input-sm" readonly /> </td>
                  <td class="debit"> <input type="hidden" value="0" name="vouchers[1][debit]" /> </td>
                  <td class="credit"> <input type="text" name="vouchers[1][credit]" value="{{ $coa_credit->credit }}" class="form-control input-sm credit credit-field" readonly /> </td>
                  <input type="hidden" value="coa_credit" name="vouchers[1][key]" />
                </tr>
                <tr class="total-row">
                  <td>  </td>
                  <td colspan="2" class="text-right"> Total: </td>
                  <td class="debit">0.00</td>
                  <td class="credit">0.00</td>
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
@include('cash-payment-voucher.form-script')  
<script>
    var validation_url = '{{url("cash-payment-voucher/edit-form-validate")}}';
    
    /* Trigger change to fill in Account # */
    $('select.chart-account-dropdown, select.tax-dropdown').trigger('change');

    /* Search Vendor by ID */
    $('.vendor-search_by_id').trigger('click');
    
    calculate_account_total();
</script>
@endsection