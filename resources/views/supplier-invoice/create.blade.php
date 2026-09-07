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
.right-info .input-group, .left-info .input-group { margin-bottom: 4px; }
.vendor-info-box { margin-top: 12px; }
.box-header .box-title.text-sm { font-size: 12px; }

table.table.table-hover.table-vendor-info { font-size: 12px; }
.table-vendor-info th { width: 30% }
.table.table-vendor-info > tbody > tr > td, .table.table-vendor-info > tbody > tr > th { padding:4px; }
.delete-row:hover { cursor:pointer; }

.input-group.terms, .input-group.description { margin-top: 20px; }
.terms-ch { margin-left: 15px; display: none; }
</style>
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Create New Supplier's Invoice
        </h1>
        
    </section>

    <!-- Main content -->
    <section class="content">
    
    {!! Form::open(['url' => '/supplier-invoice']) !!}

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
                <input type="text" class="form-control vendor_name">
                <input type="hidden" value="" name="vendor_id">
                
                <span class="input-group-btn"><button type="button" class="btn btn-info btn-flat btn-search">Go!</button></span>
              </div>
              
              
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
                  <button type="button" class="btn">Date</button>
                </div>
                <!-- /btn-group -->
                <input type="text" name="date" class="form-control datepicker" required>
              </div>
              <div class="input-group input-group-sm">
                <div class="input-group-btn">
                  <button type="button" class="btn">Invoice No.</button>
                </div>
                <!-- /btn-group -->
                <input type="text" name="invoice_number" class="form-control">
              </div>
              <div class="input-group input-group-sm terms">
                <div class="input-group-btn">
                  <button type="button" name="" class="btn">Terms</button>
                </div>
                <!-- /btn-group -->
                {!! Form::select('terms', ['cod'=>'COD', 'On account'=>'On account'], null, ['class' => 'form-control']) !!}
              </div>
              <div class="input-group input-group-sm period terms-ch terms-on-account">
                <div class="input-group-btn">
                  <button type="button" name="" class="btn"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </button>
                </div>
                <!-- /btn-group -->
                {!! Form::select('period', [''=>'Select', '30'=>'30 days', '45'=>'45 days', '180'=>'180 days'], '', ['class' => 'form-control']) !!}
              </div>
              <div class="input-group input-group-sm description">
                <div class="input-group-btn">
                  <button type="button" class="btn">Desc / Memo</button>
                </div>
                <!-- /btn-group -->
                <input type="text" name="description" class="form-control">
              </div>
              <div class="input-group input-group-sm">
                <div class="input-group-btn">
                  <button type="button" class="btn">Amount</button>
                </div>
                <!-- /btn-group -->
                <input type="text" name="amount" class="form-control">
              </div>
              <div class="input-group input-group-sm">
                <div class="input-group-btn">
                  <button type="button" class="btn">Amount Subject to VAT</button>
                </div>
                <!-- /btn-group -->
                <input type="text" name="amount_subj_to_vat" class="form-control" placeholder="Leave this blank if not applicable">
              </div>
            </div>
            
            </div>
            <!-- /.row -->
            
            <div class="row">
                <div class="col-sm-6">
                    <div class="journal-entry-section">
                        {!! Form::select('journal_entry', [''=>'Select', 'asset'=>'Asset', 'purchases'=>'Purchases', 'expenses'=>'Expenses'], '', ['class' => 'form-control input-sm']) !!}
                    </div>
                </div>
            </div>
            <!-- /.row -->
            
        </div>
        <!-- ./box-body -->
    </div>
    
    <div class="box hidden account-details-section">
        <div class="box-header with-border">
            <h3 class="box-title">Account Details</h3>
            <div class="box-tools pull-right">
                <a href="{{url('supplier-invoice/account-details')}}" target="_blank" class="btn btn-box-tool" data-toggle="tooltip" title="" data-original-title="Manage Account Details"><i class="fa fa-wrench"></i></a>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover table-account-details">
                <tbody><tr class="head">
                  <th>Account #</th>
                  <th>Account Title</th>
                  <th>Invoice #</th>
                  <th>Debit</th>
                  <th>Credit</th>
                </tr>
                <tr class="account-row debit">
                  <td class="account-number"> 
                    <input type="hidden" value="0" name="vouchers[0][rate]" /> 
                    <input type="text" name="vouchers[0][code]" value="" class="form-control input-sm debit code" readonly /> 
                  </td>
                  <td class="account-title"> 
                    <input type="hidden" value="0" name="vouchers[0][tax_id]" /> 
                    <select class="form-control input-sm chart-account-dropdown" name="vouchers[0][chart_account_id]"></select> 
                  </td>
                  <td class="ref-number"> <input type="text" name="vouchers[0][ref_number]" value="" class="form-control input-sm debit" readonly /> </td>
                  <td class="debit"> <input type="text" name="vouchers[0][debit]" value="" class="form-control input-sm debit" readonly /> </td>
                  <td class="credit"> <input type="hidden" value="0" name="vouchers[0][credit]" /> </td>
                  <input type="hidden" value="coa_debit" name="vouchers[0][key]" />
                </tr>
                <tr class="account-row debit other hidden">
                  <td class="account-number"> 
                    <input type="hidden" value="0" name="vouchers[1][rate]" /> 
                    <input type="text" name="vouchers[1][code]" value="" class="form-control input-sm debit code" readonly /> 
                  </td>
                  <td class="account-title"> 
                    <input type="hidden" value="0" name="vouchers[1][tax_id]" /> 
                    <select class="form-control input-sm chart-account-dropdown" name="vouchers[1][chart_account_id]"></select> 
                  </td>
                  <td class="ref-number"> <input type="text" name="vouchers[1][ref_number]" value="" class="form-control input-sm debit" readonly /> </td>
                  <td class="debit"> <input type="text" name="vouchers[1][debit]" value="" class="form-control input-sm debit" readonly /> </td>
                  <td class="credit"> <input type="hidden" value="0" name="vouchers[1][credit]" /> </td>
                  <input type="hidden" value="coa_debit_other" name="vouchers[1][key]" />
                </tr>
                <tr class="tax-row debit">
                  <td class="account-number"> 
                    <input class="rate" type="hidden" value="0" name="vouchers[2][rate]" /> 
                    <input type="text" name="vouchers[2][code]" value="" class="form-control input-sm debit code" readonly />
                  </td>
                  <td class="tax"> 
                    <input type="hidden" value="0" name="vouchers[2][tax_id]" class="debit tax_id" /> 
                    <select class="form-control input-sm tax-dropdown" name="vouchers[2][chart_account_id]"></select> 
                  </td>
                  <td class="ref-number"> <input type="text" name="vouchers[2][ref_number]" value="" class="form-control input-sm debit" readonly /> </td>
                  <td class="debit"> <input type="text" name="vouchers[2][debit]" value="" class="form-control input-sm debit" readonly /> </td>
                  <td class="credit"> <input type="hidden" value="0" name="vouchers[2][credit]" /> </td>
                  <input type="hidden" value="tax_debit" name="vouchers[2][key]" />
                </tr>
                <tr class="account-row credit">
                  <td class="account-number"> 
                    <input type="hidden" value="0" name="vouchers[3][rate]" /> 
                    <input type="text" name="vouchers[3][code]" value="" class="form-control input-sm credit code" readonly /> 
                  </td>
                  <td class="account-title"> 
                    <input type="hidden" value="0" name="vouchers[3][tax_id]" /> 
                    <select class="form-control input-sm chart-account-dropdown" name="vouchers[3][chart_account_id]"></select> 
                  </td>
                  <td class="ref-number"> <input type="text" name="vouchers[3][ref_number]" value="" class="form-control input-sm credit" readonly /> </td>
                  <td class="debit"> <input type="hidden" value="0" name="vouchers[3][debit]" /> </td>
                  <td class="credit"> <input type="text" name="vouchers[3][credit]" value="" class="form-control input-sm credit" readonly /> </td>
                  <input type="hidden" value="coa_credit" name="vouchers[3][key]" />
                </tr>
                <tr class="tax-row credit">
                  <td class="account-number"> 
                    <input class="rate" type="hidden" value="0" name="vouchers[4][rate]" /> 
                    <input type="text" name="vouchers[4][code]" value="" class="form-control input-sm credit code" readonly />
                  </td>
                  <td class="tax"> 
                    <input type="hidden" value="0" name="vouchers[4][tax_id]" class="credit tax_id" /> 
                    <select class="form-control input-sm tax-dropdown" name="vouchers[4][chart_account_id]"></select> 
                  </td>
                  <td class="ref-number"> <input type="text" name="vouchers[4][ref_number]" value="" class="form-control input-sm credit" readonly /> </td>
                  <td class="debit"> <input type="hidden" value="0" name="vouchers[4][debit]" /> </td>
                  <td class="credit"> <input type="text" name="vouchers[4][credit]" value="" class="form-control input-sm credit" readonly /> </td>
                  <input type="hidden" value="tax_credit" name="vouchers[4][key]" />
                </tr>
                <tr class="total-row">
                  <td> {{--<button type="button" class="btn">Other</button>--}} </td>
                  <td colspan="2" class="text-right"> Total: </td>
                  <td class="debit"> 0.00 </td>
                  <td class="credit"> 0.00 </td>
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

    <div class="box hidden cpv-account-details-section">
        <div class="box-header with-border">
            <h3 class="box-title">Cash Payment Voucher Account Details</h3>
            <div class="box-tools pull-right">
                <a href="{{url('cash-payment-voucher/account-details')}}" target="_blank" class="btn btn-box-tool" data-toggle="tooltip" title="" data-original-title="Manage Account Details"><i class="fa fa-wrench"></i></a>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover cpv-table-account-details">
                <tbody><tr class="head">
                  <th>Account #</th>
                  <th>Account Title</th>
                  <th>Ref #</th>
                  <th class="debit">Debit</th>
                  <th class="credit">Credit</th>
                </tr>
                <tr class="cpv-account-row debit">
                  <td class="account-number"> <input type="text" name="cpvvouchers[0][code]" value="" class="form-control input-sm debit code" readonly /> </td>
                  <td class="account-title"> 
                    <input type="hidden" value="0" name="cpvvouchers[0][tax_id]" /> 
                    <select class="form-control input-sm cpv-chart-account-dropdown coa-accounts_payable" name="cpvvouchers[0][chart_account_id]">
                        @if (count($cpvcoas['debit'])>1)<option value=""> Select Chart of Account </option>@endif
                        @foreach ($cpvcoas['debit'] as $c)
                        <option data-code="{{ $c['code'] }}" value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                        @endforeach
                    </select> 
                  </td>
                  <td class="ref-number"> <input type="text" name="cpvvouchers[0][ref_number]" value="" class="form-control input-sm" readonly /> </td>
                  <td class="debit"> <input type="text" name="cpvvouchers[0][debit]" value="" class="form-control input-sm debit debit-field" readonly /> </td>
                  <td class="credit"> <input type="hidden" value="0" name="cpvvouchers[0][credit]" /> </td>
                  <input type="hidden" value="coa_debit" name="cpvvouchers[0][key]" />
                </tr>
                <tr class="cpv-account-row credit">
                  <td class="account-number"> <input type="text" name="cpvvouchers[1][code]" value="" class="form-control input-sm credit code" readonly /> </td>
                  <td class="account-title"> 
                    <input type="hidden" value="0" name="cpvvouchers[1][tax_id]" /> 
                    <select class="form-control input-sm cpv-chart-account-dropdown coa-cash" name="cpvvouchers[1][chart_account_id]">
                        @if (count($cpvcoas['credit'])>1)<option value=""> Select Chart of Account </option>@endif
                        @foreach ($cpvcoas['credit'] as $c)
                        <option data-code="{{ $c['code'] }}" value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                        @endforeach
                    </select> 
                  </td>
                  <td class="ref-number"> <input type="text" name="cpvvouchers[1][ref_number]" value="" class="form-control input-sm" readonly /> </td>
                  <td class="debit"> <input type="hidden" value="0" name="cpvvouchers[1][debit]" /> </td>
                  <td class="credit"> <input type="text" name="cpvvouchers[1][credit]" value="" class="form-control input-sm credit credit-field" readonly /> </td>
                  <input type="hidden" value="coa_credit" name="cpvvouchers[1][key]" />
                </tr>
                <tr class="cpv-total-row">
                  <td>  </td>
                  <td colspan="2" class="text-right"> Total: </td>
                  <td class="debit">0.00</td>
                  <td class="credit">0.00</td>
                </tr>
              </tbody></table>
            </div>
            <!-- ./box-body -->
            <div class="box-footer clearfix">
              <div class="col-sm-6 pull-right"></div>
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

@endsection


@section('footer_script_preload')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection


@section('footer_script')
<script>
    $('.datepicker').datepicker({
        autoclose: true,
        todayBtn: "linked",
        todayHighlight: true,
        format: 'yyyy-mm-dd'
    }).datepicker('update', new Date());
    
    /* Terms Dropdown */
    $('[name=terms]').change(function(){
        $('.terms-ch').hide();
        if($(this).val() == 'cod'){
            $('.terms-cod').css("display", "table");
        }
        else if($(this).val() == 'On account'){
            $('.terms-on-account').css("display", "table");
        }
        
        cpv_show_hide_account_details();
    });
    $('[name=terms]').trigger('change');
    
    function do_calculation(){
        
        var amount = $('input[name=amount]').val() || 0;
        amount = isNaN(amount) ? 0 : parseFloat(amount).toFixed(2)*1; // *1 purpose is just to convert the type from string to number.
        
        var amount_stvat = $('input[name=amount_subj_to_vat]').val() || 0;
        amount_stvat = isNaN(amount_stvat) ? 0 : parseFloat(amount_stvat).toFixed(2)*1; // *1 purpose is just to convert the type from string to number.
        
        var debit_tax_rate = $('.table-account-details tr.tax-row.debit .tax-dropdown').find(":selected").data('rate') || 0;
        var credit_tax_rate = $('.table-account-details tr.tax-row.credit .tax-dropdown').find(":selected").data('rate') || 0;
        
        /* Calc for Net of VAT */
        // var debit_amount       = amount_stvat / (1 + debit_tax_rate/100);
        // var debit_tax          = debit_amount * (debit_tax_rate/100);
        // var other_debit_amount = amount - amount_stvat;
        // console.log(debit_amount, debit_tax, other_debit_amount);
        
        /* Calc for Net of VAT for Debit */
        var debit_amount       = amount / (1 + debit_tax_rate/100);
        var debit_tax          = debit_amount * (debit_tax_rate/100);
        var other_debit_amount = 0;
        // console.log(debit_amount, debit_tax, other_debit_amount);
        
        /* if the user set an amount in amount_subj_to_vat field, calc vouchers with OTHERS Row */
        if(amount_stvat > 0){
            // console.log(amount_stvat, debit_amount);
            debit_amount       = (amount_stvat <= debit_amount)? amount_stvat : debit_amount;
            debit_tax          = debit_amount * (debit_tax_rate/100);
            other_debit_amount = amount - debit_amount - debit_tax;
            $('.account-row.debit.other').removeClass('hidden');
        }
        else{
            $('.account-row.debit.other').addClass('hidden');
        }
        // console.log(debit_amount, debit_tax, other_debit_amount);
        
        /* Calc for Net of VAT for Credit */
        var credit_tax    = debit_amount * (credit_tax_rate/100);
        var credit_amount = amount - credit_tax;
        
        /* Fill-in rates */
        $('.tax-row.debit .rate').val( debit_tax_rate );
        $('.tax-row.credit .rate').val( credit_tax_rate );
        
        /* Show Ref # */
        $('td.ref-number input').val( $('input[name=invoice_number]').val() );
        
        /* Reset Amout subj to VAT */
        if(amount_stvat > 0){
            $('input[name=amount_subj_to_vat]').val( debit_amount.toFixed(2) );
        }
        
        /* Show calculated amounts to table. */
        $('tr.account-row.debit:not(.other) td.debit input').val( debit_amount.toFixed(2) );
        $('tr.account-row.debit.other td.debit input').val( other_debit_amount.toFixed(2) );
        $('tr.tax-row.debit td.debit input').val( debit_tax.toFixed(2) );
        $('tr.account-row.credit td.credit input').val( credit_amount.toFixed(2) );
        $('tr.tax-row.credit td.credit input').val( credit_tax.toFixed(2) );
        
        /* Total Debit and Total Credit */
        $('tr.total-row td.debit').text( (debit_amount + other_debit_amount + debit_tax).toFixed(2) );
        $('tr.total-row td.credit').text( (credit_amount + credit_tax).toFixed(2) );
        
        cpv_calculate_account_details();
    }
  
    /* Set up chart account dropdown */
    function setup_chart_account_dropdown( data ){
        
        var html = '<option value=""> Select Chart of Account </option>';
        for( var i in data.debit ){
            var c = data.debit[ i ];
            html += '<option data-code="'+ c.code +'" class="" value="'+ c.id +'">'+ c.name +'</option>';
        }
        $('.account-row.debit select.chart-account-dropdown').html( html );
        
        var html = '<option value=""> Select Chart of Account </option>';
        for( var i in data.credit ){
            var c = data.credit[ i ];
            html += '<option data-code="'+ c.code +'" class="" value="'+ c.id +'">'+ c.name +'</option>';
        }
        $('.account-row.credit select.chart-account-dropdown').html( html );
        
    }
    
    /* Set up tax account dropdown */
    function setup_tax_account_dropdown( data ){
        
        var html = '<option value=""> Select Tax </option>';
        for( var i in data.debit ){
            var t = data.debit[ i ];
            html += '<option data-code="'+ t.code +'" data-rate="'+ t.rate +'" data-tax_id="'+ t.tax_id +'" value="'+ t.id +'">'+ t.name +'</option>';
        }
        $('.tax-row.debit select.tax-dropdown').html( html );
        
        var html = '<option value=""> Select Withholding Tax </option>';
        for( var i in data.credit ){
            var t = data.credit[ i ];
            html += '<option data-code="'+ t.code +'" data-rate="'+ t.rate +'" data-tax_id="'+ t.tax_id +'" value="'+ t.id +'">'+ t.name +'</option>';
        }
        $('.tax-row.credit select.tax-dropdown').html( html );
        
    }
    
    /* journal entry dropdown */
    $('select[name=journal_entry]').on('change', function(){
        
        /* Add a tr.account-row */
        if( $(this).val() != '' ){
            
            var request = {journal: $(this).val()};
            $.get('{{url("supplier-invoice/get-accounts-taxes")}}', request, function(data){
                
                if(data){
                    setup_chart_account_dropdown( data.coas );
                    setup_tax_account_dropdown( data.taxes );
                    $('.account-details-section').removeClass('hidden');
                    
                    cpv_show_hide_account_details();
                }
                
            }, 'json').always(function(){
                // $('.box .overlay').addClass('hidden');
            });
            
        }
        else{
            
            // setup_chart_account_dropdown();
            $('.account-details-section, .cpv-account-details-section').addClass('hidden');
            cpv_show_hide_account_details();
            
        }
        
    });
    
    /* Account Title Dropdown on change -- for normal account titles */
    $('.table-account-details').on('change', 'select.chart-account-dropdown', function(){
        var this_tr = $(this).closest('tr');
        
        var code = $(this).find(":selected").data('code');
        this_tr.find('td.account-number input.code').val( code );
        
        do_calculation();
    });
    
    /* Account Title Dropdown on change -- for Tax dropdown */
    $('.table-account-details').on('change', 'select.tax-dropdown', function(){
        var this_tr = $(this).closest('tr');
        
        var data = $(this).find(":selected").data();
        if(!data) data = {};
        if(!data.code) data.code = '';
        if(!data.tax_id) data.tax_id = 0;
        
        this_tr.find('td.account-number input.code').val( data.code );
        this_tr.find('td.tax .tax_id').val( data.tax_id );
        
        do_calculation();
    });
    
    $('.vendor_name').autocomplete({
        source: '{{ url("supplier-invoice/find-vendors") }}',
        minLength: 3
    });

    /* Search for a vendor info. */
    $('.btn-search').on('click', function(){
        var s = ($('.vendor_name').val()).trim();
        var request = { s: s };
        
        if( s && $('.vendor_name').data('ui-autocomplete') && $('.vendor_name').data('ui-autocomplete').selectedItem ){
            request.vendor_id = $('.vendor_name').data('ui-autocomplete').selectedItem.id;
        }
        else if( !s ){
            // alert('it\'s empty'); 
            return;
        }
        console.log( request );
        
        $('.box .overlay').removeClass('hidden');
        
        $.get('{{url("supplier-invoice/find-vendor")}}', request, function(data){
            if(data && Object.keys(data).length){
                for(var prop in data){
                    if(prop == 'individual' && data[prop] == '1'){
                        $('.table-vendor-info td.'+prop).html( '<span class="badge bg-green">Yes</span>' );
                        $('.table-vendor-info tr.individual_name').removeClass( 'hidden' );
                        $('.table-vendor-info tr.company_name').addClass( 'hidden' );
                    }
                    else if(prop == 'individual' && data[prop] == '0'){
                        $('.table-vendor-info td.'+prop).html( '<span class="badge bg-yellow">No</span>' );
                        $('.table-vendor-info tr.company_name').removeClass( 'hidden' );
                        $('.table-vendor-info tr.individual_name').addClass( 'hidden' );
                    }
                    else if(prop == 'id'){
                        $('input[name=vendor_id]').val( data[prop] );
                    }
                    else{
                        $('.table-vendor-info td.'+prop).html( data[prop] );
                    }
                }
            }
            else{
                $('.table-vendor-info td').html('');
                $('input[name=vendor_id]').val('');
            }
        }, 'json').always(function(){
            $('.box .overlay').addClass('hidden');
        });
    });
    
    $('input[name=amount], input[name=amount_subj_to_vat]').on('change', function(){
        
        /* Do calculation only if journal entry DD has a value */
        if($('select[name=journal_entry]').val() != ''){
            do_calculation();
        }
        
    });
    
    $('input[name=invoice_number]').on('change', function(){
        
        /* Do calculation only if journal entry DD has a value */
        if($('select[name=journal_entry]').val() != ''){
            do_calculation();
        }
        
    });
    
    
    function cpv_show_hide_account_details(){
        if($('select[name=journal_entry]').val() != '' && $('select[name=terms]').val() == 'cod'){
            $('.cpv-account-details-section').removeClass('hidden');
        }
        else{
            $('.cpv-account-details-section').addClass('hidden');
        }
    }
    
    function cpv_calculate_account_details(){
        // var amount = $('input[name=amount]').val() || 0;
        var amount = $('tr.account-row.credit td.credit input').val() || 0;
        amount = isNaN(amount) ? 0 : parseFloat(amount).toFixed(2)*1; // *1 purpose is just to convert the type from string to number.
        
        $('.cpv-table-account-details td.debit .debit-field').val( amount.toFixed(2) );
        $('.cpv-table-account-details td.credit .credit-field').val( amount.toFixed(2) );
        
        $('tr.cpv-total-row td.debit').text( amount.toFixed(2) );
        $('tr.cpv-total-row td.credit').text( amount.toFixed(2) );
    }
    
    /* CPV Account Title Dropdown on change */
    $('.cpv-table-account-details').on('change', 'select.cpv-chart-account-dropdown', function(){
        var this_tr = $(this).closest('tr');
        
        var code = $(this).find(":selected").data('code');
        this_tr.find('td.account-number input.code').val( code );
    });
    
    /* Trigger change to fill in Account # */
    $('select.cpv-chart-account-dropdown').trigger('change');
    
    
    var validated = false;
    $('form').on('submit', function(e){
        // only allow the form submission if the current focus is in the submit button. 
        // This will ensure that the user is really attempting to submit the form.
        if($('input[type=submit]').is(':focus') == false){
            return false;
        }
        
        // to prevent from revalidating after the form is validated already.
        if(validated) return;
        
        // remove class has-error
        $('form').find('.has-error').removeClass('has-error');
        
        // remove error messages
        $('[data-dismiss="alert"]').click();
        
        // submit to server to validate
        // if validated then submit to save.  if not then show errors
        submit_for_validation(function(response){
            if(response){
                console.log( response );
                if(response.valid){
                    validated = true;
                    // submit the form to save to DB.
                    $('form').submit();
                }
                else{
                    show_form_errors( response.errors );
                }
            }
        });
        
        return false;
    });
    
    function submit_for_validation(callback){
        $.post('{{url("supplier-invoice/add-form-validate")}}', $('form').serialize(), function(response){
            if(typeof callback == 'function'){
                callback(response);
            }
        }, 'json');
    }
    
    function show_form_errors( errors ){
        var error_msgs = [];
        
        for(var i in errors){
            var error = errors[i],
                selector = '[name="'+ error.field +'"]';
            
            /* if the field is one of the following inside this array */
            if(['journal_entry'].includes(error.field)){
                $(selector).parent().addClass('has-error');
            }
            /* if the field starts with voucher */
            else if((error.field).indexOf('voucher') === 0){
                if(error.field == 'voucher1') $('tr.account-row.debit:not(.other)').addClass('has-error');
                if(error.field == 'voucher2') $('tr.account-row.debit.other').addClass('has-error');
                if(error.field == 'voucher3') $('tr.tax-row.debit').addClass('has-error');
                if(error.field == 'voucher4') $('tr.account-row.credit').addClass('has-error');
                if(error.field == 'voucher5') $('tr.tax-row.credit').addClass('has-error');
            }
            /* if the field starts with voucher */
            else if((error.field).indexOf('cpvvoucher') === 0){
                if(error.field == 'cpvvoucher1') $('tr.cpv-account-row.debit').addClass('has-error');
                if(error.field == 'cpvvoucher2') $('tr.cpv-account-row.credit').addClass('has-error');
            }
            else
                $(selector).closest('.input-group').addClass('has-error');
            
            if(error.message){
                error_msgs.push( error.message );
            }
        }
        
        $('.content-header').prepend( create_alert2('warning', false, error_msgs) );
    }
  
</script>
@endsection