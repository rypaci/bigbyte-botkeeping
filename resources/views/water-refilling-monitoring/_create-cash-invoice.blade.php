@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('header_style_preload')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Create New Cash Invoice</h1>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('cash-invoice.index') }}"> Back</a>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
{!! Form::open(array('route' => 'cash-invoice.store','method'=>'POST')) !!}
    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"></button>
                </div>
            </div>
            <div class="col-sm-6 cash-box-left-right">
                
                    <div class="input-group input-group-sm" style="margin-bottom: 10px;">
                        <div class="input-group-btn"><button type="button" class="btn">Customer</button></div>
                        <input type="text" size="250" class="form-control customer_name" name="customer_name" id="cust_ajax" value="" placeholder="Customer Name" autocomplete="off">
                        <span class="input-group-btn"><button type="button" class="btn btn-info btn-flat btn-search">Go!</button></span>
                    </div>
                
                <div class="box box-info box-solid vendor-info-box">
                    <div class="box-header with-border">
                        <h3 class="box-title text-sm">Customer Info</h3>
                        <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool"></button></div>
                    </div>
                    
                    <div class="col-xs-12 col-sm-12 col-md-12 cash-invoice-input">
                        <input type="hidden" name="customer_id" class="id" value="">
                        <input type="hidden" name="full_name" class="full_name" value="">
                        <div class="box-body">
                          <div class="box-body table-responsive no-padding">
                            <table class="table table-hover table-customer-info">
                                <tbody>
                                    <tr class="individual_name">
                                        <th>Full Name/Company Name</th>
                                        <td class="full_name" style="display: block;width: 250px;"></td>
                                    </tr>
                                    <tr><th>Individual?</th><td class="individual"></td></tr>
                                    <tr><th>City</th><td class="city"></td></tr>
                                    <tr><th>Country</th><td class="country"></td></tr>
                                    <tr><th>TIN</th><td class="tin"></td></tr>
                                    <tr><th>Branch Code</th><td class="branch_code"></td></tr>
                                    <tr><th>Phone</th><td class="phone_no"></td></tr>
                                    <tr><th>Fax</th><td class="fax"></td></tr>
                                    <tr><th>Email</th><td class="email"></td></tr>
                                </tbody>
                            </table>
                          </div>
                        </div>
                    </div>
                        <div style="clear:both;"></div>
                </div>
            </div>
            <div class="col-sm-6 cash-box-left-right">
                <div class="input-group input-group-sm" style="margin-bottom: 10px;">
                    <div class="input-group-btn"><button type="button" class="btn">Inv. No.</button></div>
                    <input type="text" class="form-control invoice-no" name="invoice_number" value="">
                </div>
            </div>
            <div class="col-sm-6 cash-box-left-right">
                <div class="input-group input-group-sm" style="margin-bottom: 10px;">
                    <div class="input-group-btn"><button type="button" class="btn">Date</button></div>
                    <input type="text" class="form-control datepicker" name="invoice_date">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-left" style="margin-bottom: 20px;">
                <!--<button type="submit" class="btn btn-primary">Create</button>-->
            </div>
            <div style="clear: both;"></div>
        </div>
    </section>

        <section class="content">
        <div class="col-lg-12" style="background-color: #ddd; padding-top: 15px; padding-bottom: 15px;">
            <div class="searh-div" style="margin-bottom: 10px;">
                <input type="text" name="search_prod" class="search_prod form-control" id="prod_ajax" placeholder="Search Product Name" autocomplete="off"/>
                <datalist id="prod_ajaxlist">
                    <option value="Select"></option>
                </datalist>
                <input type="hidden" value="" name="prod_id">
                <input type="hidden" value="19" name="vat_id_hidden" class="vat_id_hidden">
                <input type="hidden" value="1" name="discount_id_hidden" class="discount_id_hidden">
                <input type="text" name="qty" class="prod-qty form-control" placeholder="Qty"/>
                <button class="btn btn-primary btn-enter" onclick="qtyFunction(); return false;">Enter</button>
            </div>
            <div style="clear: both;"></div>
            <div class="table-responsive">
                <table id="tbl-items" class="table table-bordered table-striped table-hover table-product-info">
                    <tr>
                        <th style="width: 60%; text-align: center;">Item</th>
                        <th style="text-align: center; width: 70px;">Qty</th>
                        <th style="text-align: center; width: 70px;">Price</th>
                        <th style="text-align: center; width: 70px;">Amount</th>
                    </tr>
                </table>
                <div class="pull-left">
                    <a href="javascript:" class="btn btn-primary enter-btn clear-rows" style="margin-top: 10px;">Clear Items</a>
                </div>
                <div class="pull-right sales-amount-details total-amount">
                    <table style="padding: 15px;display: block;">
                        <tr><th>Total</th><td class="not-editable-td"><input type="text" name="total" class="form-control total" value=""></td></tr>
                        <tr><th>VAT</th><td class="not-editable-td1"><input type="text" name="vat" value="" class="form-control vat" value=""></td></tr>
                        <tr><th>Net of VAT</th><td class="not-editable-td2"><input type="text" name="nov" class="form-control net-of-vat" value=""></td></tr>
                        <tr class="discount-yes-display" style="display: none;"><th>No. of Person</th><td><input type="text" name="no_of_person" class="form-control no_of_person" value="0"></td></tr>
                        <tr class="discount-yes-display" style="display: none;"><th>No. of SC/PWD</th><td><input type="text" name="no_of_scpwd" class="form-control no_of_scpwd" value="0"></td></tr>
                        <tr><th>Less: SC/PWD Discount</th><td class="not-editable-td3"><select class="form-control disc-yes-no"><option value="1">No</option><option value="2">Yes</option></select><input type="text" name="scpwd_discount" class="form-control discount" value=""><input type="hidden" name="discount" value="" class="form-control discount_hidden"></td></tr>
                        <tr><th>Net Sales</th><td class="not-editable-td4"><input type="text" name="net_sales" class="form-control net-sales" value=""></td></tr>
                        <tr><th>Add: VAT</th><td class="not-editable-td5"><input type="text" name="add_vat" class="form-control add-vat" value=""><input type="hidden" name="vat" value="" class="form-control vat_hidden"></td></tr>
                        <tr><th>Withholding Tax</th><td><select class="form-control whtaxtype"><option value="0">Select</option></select><input type="text" name="whtax_rate" class="form-control whtax_rate"></td></tr>
                        <tr><th>Amount Due</th><td class="not-editable-td6"><input type="text" name="invoice_amount_due" class="form-control amount-due" value=""></td></tr>
                    </table>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>  
        <div class="col-lg-12" style="background-color: #fff; padding-top: 15px; padding-bottom: 15px; margin-top: 20px;">
            <div class="pull-left">
                <h2>Journal Entries</h2>
            </div>
            <div style="clear: both;"></div>
            <div class="table-responsive">
                <input type="hidden" name="chartaccount" value="cash" class="form-control chartaccount_hidden">
                <table class="table table-bordered table-striped table-hover cash-inv-journal-entry-table">
                    <tr class="cash-inv-journal-entry-table-th">
                        <th style="text-align: center">Acct. No.</th>
                        <th style="width: 35%!important; text-align: center">Account Title</th>
                        <th style="text-align: center">Ref #</th>
                        <th style="text-align: center">Debit</th>
                        <th style="text-align: center">Credit</th>
                    </tr>
                    <tr class="cash-debit">
                        <td><input type="text" name="account_no" class="account_no form-control"></td>
                        <td>
                            <select class="chart-account form-control">
                                <option value="Select">Select</option>
                            </select>
                        </td>
                        <td><input type="text" name="ref_no" class="ref_no form-control"></td>
                        <td><input type="text" name="debit" class="debit-amount form-control cash-input-disable" style="text-align: right"></td>
                        <td></td>
                    </tr>
                    <tr class="sales-credit">
                        <td><input type="text" name="sales_account_no" class="sales_account_no form-control" value="4000"></td>
                        <td>
                            <input type="text" name="sales" class="sales form-control cash-input-disable" value="Sales" style="width: 250px; float: right;">
                        </td>
                        <td><input type="text" name="ref_no" class="ref_no form-control"></td>
                        <td></td>
                        <td><input type="text" name="credit" class="credit-sales form-control cash-input-disable" style="text-align: right"></td>
                    </tr>
                    <tr class="credit-ouput-tax">
                        <td><input type="text" name="output_tax_acount_no" class="output_tax_acount_no form-control" value="2003"></td>
                        <td>
                            <input type="text" name="output_tax" class="output_tax form-control cash-input-disable" value="Output Tax" style="width: 250px; float: right;">
                        </td>
                        <td><input type="text" name="ref_no" class="ref_no form-control"></td>
                        <td></td>
                        <td><input type="text" name="credit" class="credit-ouput_tax form-control cash-input-disable" style="text-align: right"></td>
                    </tr>
                    <tr class="credit-ouput-tax">
                        <td></td>
                        <td></td>
                        <td style="vertical-align: middle; text-align: right; font-weight: bold; font-size: 16px;">Total:</td>
                        <td><input type="text" name="total_debit" class="total_debit form-control cash-input-disable" style="text-align: right; font-weight: bold; font-size: 16px;"></td>
                        <td><input type="text" name="total_credit" class="total_credit form-control cash-input-disable" style="text-align: right; font-weight: bold; font-size: 16px;"></td>
                    </tr>
                </table>
            </div>
        </div>
            <button type="submit" class="btn btn-primary save-btn" style="margin-top: 10px; width: 100px;" onclick="return cust_idFunction();">Save</button>
        <div style="clear: both;"></div>
    </section>
    {!! Form::close() !!}
</div>

@endsection

@section('footer_script_preload')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endsection

@section('footer_script')
<script>

$('td.not-editable-td input').attr('readonly', true);
$('td.not-editable-td1 input').attr('readonly', true);
$('td.not-editable-td2 input').attr('readonly', true);
$('td.not-editable-td3 input').attr('readonly', true);
$('td.not-editable-td4 input').attr('readonly', true);
$('td.not-editable-td5 input').attr('readonly', true);
$('td.not-editable-td6 input').attr('readonly', true);
$('input.cash-input-disable').attr('readonly', true);
$('input.account_no').attr('readonly', true);
$('input.sales_account_no').attr('readonly', true);
$('input.output_tax_acount_no').attr('readonly', true);
$('input.ref_no').attr('readonly', true);
$('input.whtax_rate').attr('readonly', true);

var prod_info_tr = '<tr class="tr-rows"> \
<td class="name"></td> \
<td class="td-qty"></td> \
<td><input type="text" name="n_price" value="" class="price"/></td> \
<td><input type="text" name="n_amount" value="" class="td-amount"/></td> \
</tr> \
';

var total_amt = 0;

$('.btn-enter').on('click', function(){

var s = ($('.search_prod').val()).trim();

if( !s ) {
return;
}
var request = { s: s };
$.get('{{url("cash-invoice/find-product")}}', request, function(data){

var qtyfilter = $('.prod-qty').val();

    if(qtyfilter==''){
    }
    else
    {
        if(data.length == ""){}
        else{
            var $qty = $('.prod-qty').val();

            $('.table-product-info > tbody'). append( prod_info_tr );
            var $tr = $('.table-product-info tr.tr-rows:last-child');

            for(var prop in data){
                $tr.find('td.td-qty').html($qty);
                $tr.find('td.' + prop).html( data[prop] );
                $tr.find('td input.' + prop).val(data[prop]);
            }

            var price = $('tr.tr-rows:last-child input.price').val();
            var amount = price * $('.prod-qty').val();
     
            $tr.find('td input.td-amount').val(commaSeparateNumber(amount.toFixed(2)));

            total_amt = total_amt + amount;

            $('input.total').val(commaSeparateNumber(total_amt.toFixed(2)));

            var total_amount = total_amt;

            var vat = total_amount * (3/28);
            $('input.vat').val(commaSeparateNumber(vat.toFixed(2)));

            var net_of_vat = total_amount - vat;
            $('input.net-of-vat').val(commaSeparateNumber(net_of_vat.toFixed(2)));

            $('input.vat').val(commaSeparateNumber(vat.toFixed(2)));

            var sc_pwd_discount = net_of_vat * 0;
            $('.discount').val(commaSeparateNumber(sc_pwd_discount.toFixed(2)));
            
            var net_sales = net_of_vat - sc_pwd_discount;
            $('.net-sales').val(commaSeparateNumber(net_sales.toFixed(2)));

            var addvat_hidden = $('input.vat_hidden').val();
            var addvat_conversion = addvat_hidden/100;
            var addvat = net_sales * addvat_conversion;
            $('.add-vat').val(commaSeparateNumber(addvat.toFixed(2)));

            var amountdue = addvat + net_sales;
            $('.amount-due').val(commaSeparateNumber(amountdue.toFixed(2)));

            $('.debit-amount').val(commaSeparateNumber(amountdue.toFixed(2)));
            $('.credit-sales').val(commaSeparateNumber(net_sales.toFixed(2)));
            $('.credit-ouput_tax').val(commaSeparateNumber(addvat.toFixed(2)));
            $('.total_debit').val(commaSeparateNumber(amountdue.toFixed(2)));
            $('.total_credit').val(commaSeparateNumber(amountdue.toFixed(2)));

            var inv_no = $('.invoice-no').val();            
            $( ".ref_no" ).val(inv_no);
            $( ".search_prod" ).focus();
        }
    }
}, 'json');
});

$(".invoice-no").change(function() {
    var inv_no = $('.invoice-no').val(); 
    $( ".ref_no" ).val(inv_no);
});

$('.clear-rows').on("click", function(){
    $('.table-product-info tr.tr-rows').empty();
    $('.search_prod').val('');
    $('.prod-qty').val('');
    $('input.total').val('');
    $('input.net-of-vat').val('');
    $('input.vat').val('');
    $('input.discount').val('');
    $('input.net-sales').val('');
    $('input.add-vat').val('');
    $('input.amount-due').val('');
    $('input.whtax_rate').val('');
    total_amt = 0;
    discount_rate = 0;
});

$( document ).ready(function() {
    $(".disc-yes-no").change(function() {
        var target = $('.disc-yes-no option:selected').val();
        if(target == 2){

            $('.discount-yes-display').show('slow');

            var discount_rate = $('input.discount_hidden').val();
            var discount_conversion = discount_rate/100;
            //console.log(discount_conversion);
            var net_of_vat = $('input.net-of-vat').val();
            net_of_vat = parseFloat(net_of_vat.replace(/,/g,''))

            var no_of_person = $('.no_of_person').val();
            var no_of_scpwd = $('.no_of_scpwd').val();

            var temp_total_1 = net_of_vat/no_of_person;
            var temp_total_2 = temp_total_1*no_of_scpwd;
            var discount_value = temp_total_2*discount_conversion;

            $('.discount').val(commaSeparateNumber(discount_value.toFixed(2)));
            
            var net_sales = net_of_vat - discount_value;
            $('.net-sales').val(commaSeparateNumber(net_sales.toFixed(2)));
            
            var addvat_hidden = $('input.vat_hidden').val();
            var addvat_conversion = addvat_hidden/100;
            var addvat = net_sales * addvat_conversion;
            $('.add-vat').val(commaSeparateNumber(addvat.toFixed(2)));

            var amountdue = addvat + net_sales;
            $('.amount-due').val(commaSeparateNumber(amountdue.toFixed(2)));
            
            $('.debit-amount').val(commaSeparateNumber(amountdue.toFixed(2)));
            $('.credit-sales').val(commaSeparateNumber(net_sales.toFixed(2)));
            $('.credit-ouput_tax').val(commaSeparateNumber(addvat.toFixed(2)));
            $('.total_debit').val(commaSeparateNumber(amountdue.toFixed(2)));
            $('.total_credit').val(commaSeparateNumber(amountdue.toFixed(2)));

        }else if(target == 1){

            $('.discount-yes-display').hide('slow');

            var discount_conversion = 0/100;
            var net_of_vat = $('input.net-of-vat').val();
            net_of_vat = parseFloat(net_of_vat.replace(/,/g,''))
            var sc_pwd_discount = net_of_vat * discount_conversion;
            $('.discount').val(commaSeparateNumber(sc_pwd_discount.toFixed(2)));
            
            var net_sales = net_of_vat - sc_pwd_discount;
            $('.net-sales').val(commaSeparateNumber(net_sales.toFixed(2)));

            var addvat_hidden = $('input.vat_hidden').val();
            var addvat_conversion = addvat_hidden/100;
            var addvat = net_sales * addvat_conversion;
            $('.add-vat').val(commaSeparateNumber(addvat.toFixed(2)));

            var amountdue = addvat + net_sales;
            $('.amount-due').val(commaSeparateNumber(amountdue.toFixed(2)));

            $('.debit-amount').val(commaSeparateNumber(amountdue.toFixed(2)));
            $('.credit-sales').val(commaSeparateNumber(net_sales.toFixed(2)));
            $('.credit-ouput_tax').val(commaSeparateNumber(addvat.toFixed(2)));
            $('.total_debit').val(commaSeparateNumber(amountdue.toFixed(2)));
            $('.total_credit').val(commaSeparateNumber(amountdue.toFixed(2)));
        }
    });
});

$('.no_of_person').keyup(function(){
    $('.discount-yes-display').show('slow');

    var discount_rate = $('input.discount_hidden').val();
    var discount_conversion = discount_rate/100;
    //console.log(discount_conversion);
    var net_of_vat = $('input.net-of-vat').val();
    net_of_vat = parseFloat(net_of_vat.replace(/,/g,''))

    var no_of_person = $('.no_of_person').val();
    var no_of_scpwd = $('.no_of_scpwd').val();

    var temp_total_1 = net_of_vat/no_of_person;
    var temp_total_2 = temp_total_1*no_of_scpwd;
    var discount_value = temp_total_2*discount_conversion;

    $('.discount').val(commaSeparateNumber(discount_value.toFixed(2)));

    var net_sales = net_of_vat - discount_value;
    $('.net-sales').val(commaSeparateNumber(net_sales.toFixed(2)));

    var addvat_hidden = $('input.vat_hidden').val();
    var addvat_conversion = addvat_hidden/100;
    var addvat = net_sales * addvat_conversion;
    $('.add-vat').val(commaSeparateNumber(addvat.toFixed(2)));

    var amountdue = addvat + net_sales;
    $('.amount-due').val(commaSeparateNumber(amountdue.toFixed(2)));

    $('.debit-amount').val(commaSeparateNumber(amountdue.toFixed(2)));
    $('.credit-sales').val(commaSeparateNumber(net_sales.toFixed(2)));
    $('.credit-ouput_tax').val(commaSeparateNumber(addvat.toFixed(2)));
    $('.total_debit').val(commaSeparateNumber(amountdue.toFixed(2)));
    $('.total_credit').val(commaSeparateNumber(amountdue.toFixed(2)));
});

$('.no_of_scpwd').keyup(function(){
    $('.discount-yes-display').show('slow');

    var discount_rate = $('input.discount_hidden').val();
    var discount_conversion = discount_rate/100;
    //console.log(discount_conversion);
    var net_of_vat = $('input.net-of-vat').val();
    net_of_vat = parseFloat(net_of_vat.replace(/,/g,''))

    var no_of_person = $('.no_of_person').val();
    var no_of_scpwd = $('.no_of_scpwd').val();

    var temp_total_1 = net_of_vat/no_of_person;
    var temp_total_2 = temp_total_1*no_of_scpwd;
    var discount_value = temp_total_2*discount_conversion;

    $('.discount').val(commaSeparateNumber(discount_value.toFixed(2)));

    var net_sales = net_of_vat - discount_value;
    $('.net-sales').val(commaSeparateNumber(net_sales.toFixed(2)));

    var addvat_hidden = $('input.vat_hidden').val();
    var addvat_conversion = addvat_hidden/100;
    var addvat = net_sales * addvat_conversion;
    $('.add-vat').val(commaSeparateNumber(addvat.toFixed(2)));

    var amountdue = addvat + net_sales;
    $('.amount-due').val(commaSeparateNumber(amountdue.toFixed(2)));

    $('.debit-amount').val(commaSeparateNumber(amountdue.toFixed(2)));
    $('.credit-sales').val(commaSeparateNumber(net_sales.toFixed(2)));
    $('.credit-ouput_tax').val(commaSeparateNumber(addvat.toFixed(2)));
    $('.total_debit').val(commaSeparateNumber(amountdue.toFixed(2)));
    $('.total_credit').val(commaSeparateNumber(amountdue.toFixed(2)));
});

//auto suggest
$('#prod_ajax').keyup(function(){
    var ss = $('.search_prod').val();
    if( !ss ) {
    return;
    }
    /*if($('#prod_ajax').data('timeout')) {
        clearTimeout($('#prod_ajax').data('timeout'));
    }*/
    var timesearch = setTimeout(function(){
      var request = { ss: ss };
            $.get('{{url("cash-invoice/find-product-autosuggest")}}', request, function(datasuggest){
                //$('#prod_ajaxlist').empty();
                if(datasuggest){
                    /*for(var prop in datasuggest){
                    var opt = $('<option value="" class="product_name"></option>').attr("value", datasuggest[prop].product_name);
                    $('#prod_ajaxlist').append(opt);*/

                    var products = [];
                    for(var prop in datasuggest){           
                    products.push( datasuggest[prop].product_name );
                    }
                    //console.log(full_names);
                    $( "#prod_ajax" ).autocomplete({
                    source: products
                    });
                }
            }, 'json');
    } , 400);
    //$('#prod_ajax').data('timeout', timesearch);
});

//get vat with id = 2
$( document ).ready(function() {
    var vat_id = $('.vat_id_hidden').val();
    var request = { vat_id: vat_id };
    $.get('{{url("cash-invoice/find-vat")}}', request, function(datavat){
        if(datavat){
            for(var prop in datavat){
            var opt = $('input.vat_hidden').attr("value", datavat[prop]);
            }
        }
    }, 'json');
});

// get discount id = 1
$( document ).ready(function() {
    var discount_id = $('.discount_id_hidden').val();
    var request = { discount_id: discount_id };
    $.get('{{url("cash-invoice/find-discount")}}', request, function(datadiscount){
        if(datadiscount){
            for(var prop in datadiscount){
            //console.log(datadiscount[prop]);
            var opt = $('input.discount_hidden').attr("value", datadiscount[prop]);
            }
        }
    }, 'json');
});

// get characcount levels
$( document ).ready(function() {
    var levels_id = $('.chartaccount_hidden').val();
    var request = { levels_id: levels_id };
    $.get('{{url("cash-invoice/find-chart-level")}}', request, function(data){
        if(data){
            for(var prop in data){
                //console.log(data[prop].name);
                var opt = $('<option value="">'+ data[prop].name +'</option>').attr("value", data[prop].code);
                $('.chart-account').append(opt);
            }
        }
    }, 'json');
});

// get withholdingtax by type
$( document ).ready(function() {
    //var whtax = $('.discount_id_hidden').val();
    var whtaxtype = 'Withholding';
    var timesearch = setTimeout(function(){
    var request = { whtaxtype: whtaxtype };
        $.get('{{url("cash-invoice/find-withholdingtax")}}', request, function(data_withholdingtax){
            if(data_withholdingtax){
                for(var prop in data_withholdingtax){
                //console.log(datadiscount[prop]);
                var opt = $('<option value="">'+ data_withholdingtax[prop].name +'</option>').attr("value", data_withholdingtax[prop].rate);
                $('.whtaxtype').append(opt);
                //var opt = $('input.discount_hidden').attr("value", data_withholdingtax[prop]);
                }
            }
        }, 'json');
    }, 3000); 
});

// Calculate WithHoldingTax
$( document ).ready(function() {
        $(".whtaxtype").change(function() {
            var targettax = $('.whtaxtype option:selected').val();
            ///var target = $('.chart-account option:selected').val();
            //console.log(targettax);
            $( ".whtax_rate" ).val(targettax);
            var taxholding = targettax/100;
            //console.log(taxholding);
            var totalamount_temp = $('.total').val();
            totalamount = parseFloat(totalamount_temp.replace(/,/g,''));
            //console.log(totalamount);
            var total_tax_holding = 0;
            total_tax_holding = totalamount * taxholding;
            //console.log(total_tax_holding);
            $('.whtax_rate').val(commaSeparateNumber(total_tax_holding.toFixed(2)));
    });
});

//get customers on keyup event
$( document ).ready(function() {
    //get customers on keyup event
    $("#cust_ajax").keyup(function() {
        var cust_name = $('.customer_name').val();
        if( !cust_name ){
        return;
        }
        /*if($('#cust_ajax').data('timeout')) {
            clearTimeout($('#cust_ajax').data('timeout'));
        }*/
        var timesearch = setTimeout(function(){
            var request = { cust_name: cust_name };
            $.get('{{url("cash-invoice/find-customer")}}', request, function(datacustomer){
                //$('#customer_ajaxlist').empty();
                /*if(datacustomer){
                    for(var prop in datacustomer){           
                        var opt = $('<option class="cname"></option>').attr("value", datacustomer[prop].full_name);
                        var source_data = [datacustomer[prop].full_name];
                        console.log(source_data);
                        $('#customer_ajaxlist').append(opt);
                    }*/
                if(datacustomer){
                    var full_names = [];
                    for(var prop in datacustomer){           
                    full_names.push( datacustomer[prop].full_name );
                    }
                    //console.log(full_names);
                    $( "#cust_ajax" ).autocomplete({
                    source: full_names
                    });
                }
            }, 'json');
        }, 400);   
        //$('#cust_ajax').data('timeout', timesearch);
    });
});
//http://projects.sergiodinislopes.pt/flexdatalist/

//get customers on go button event
$('.btn-search').on('click', function(){
    var cust_name = ($('.customer_name').val()).trim();
    if( !cust_name ) {
    return;
    }
    var request = { cust_name: cust_name };
    $.get('{{url("cash-invoice/find-customer-only")}}', request, function(datacust){
    if(datacust){
            var $tr = $('.table-customer-info');
            for(var prop in datacust){
                $('.cash-invoice-input input.' + prop).val( datacust[prop] );
                $('.cash-invoice-input input.' + prop).val( datacust[prop] );
                if(prop == 'individual' && datacust[prop] == '1'){
                    $('.table-customer-info td.'+prop).html( '<span class="badge bg-green">Yes</span>' );
                }
                else if(prop == 'individual' && datacust[prop] == '0'){
                    $('.table-customer-info td.'+prop).html( '<span class="badge bg-yellow">No</span>' );
                }
                else{
                    $('.table-customer-info td.'+prop).html( datacust[prop] );
                }
            }
        }
    });
});

$( document ).ready(function() {
    $(".chart-account").change(function() {
        var target = $('.chart-account option:selected').val();
        //if(target){
            console.log(target);
            $( ".account_no" ).val(target);
        //}
    });
});

function qtyFunction(){
    var qtyfilter = $('.prod-qty').val();
    if(qtyfilter==''){
    alert("Please Enter Quantity");
    $( ".prod-qty" ).focus();
    }
}
function cust_idFunction(){
    var cust_ids = $('input.id').val();
    if(cust_ids==''){
    alert("Please select customer");
    $( "input.customer_name" ).focus();
        return false;
    }/*else{
        return true;
    }*/
}

function commaSeparateNumber(val){
    while (/(\d+)(\d{3})/.test(val.toString())){
      val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
    }
    return val;
}

$('.datepicker').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
});

</script>
@endsection