@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('header_style_preload')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
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

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"></button>
                </div>
            </div>
            <div class="col-sm-6 cash-box-left-right">
                {!! Form::open(array('url' => 'cash-invoice/create-cash-invoice','method'=>'POST')) !!}
                    <div class="input-group input-group-sm" style="margin-bottom: 10px;">
                        <div class="input-group-btn"><button type="button" class="btn">Customer</button></div>
                        <input type="text" class="form-control customer_name" name="customer_name" value="">
                        <input type="hidden" value="" name="customer_id">
                        <span class="input-group-btn"><button type="submit" class="btn btn-info btn-flat btn-search">Go!</button></span>
                    </div>
                {!! Form::close() !!}
                <div class="box box-info box-solid vendor-info-box">
                <div class="box-header with-border">
                    <h3 class="box-title text-sm">Customer Info</h3>
                    <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool"></button></div>
                </div>
                {!! Form::open(array('route' => 'cash-invoice.store','method'=>'POST')) !!}
                <div class="col-xs-12 col-sm-12 col-md-12 cash-invoice-input">
                    <div class="box-body">
                      <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-vendor-info">
                            <tbody>
                            @foreach ($customers as $customer)
                            {{-- */
                                $ids = $customer->id;
                                $company_name = $customer->company_name;
                                $first_name = $customer->first_name;
                                $middle_name = $customer->middle_name;
                                $last_name = $customer->last_name;
                                $individual = $customer->individual;
                                $city = $customer->city;
                                $country = $customer->country;
                                $tin = $customer->tin;
                                $branch_code = $customer->branch_code;
                                $phone_no = $customer->phone_no;
                                $fax = $customer->fax;
                                $email = $customer->email;
                            /* --}}
                                <tr class="individual_name"><th>Full Name/Company Name</th>
                                <td class="name" style="display: block;width: 250px;">
                                <input type="hidden" name="customer_id" value="{{$ids}}">
                                <input type="hidden" name="pay_to" value="{{$company_name.''.$first_name.' '.$middle_name.' '.$last_name}}" class="payto">{{$company_name.$first_name.' '.$middle_name.' '.$last_name}}
                                </td></tr>
                                <tr><th>Individual?</th><td class="individual">@if($individual=='1') <span class="badge bg-green">Yes</span> @else <span class="badge bg-orange">No</span> @endif</td></tr>
                                <tr><th>City</th><td class="city">{{$city}}</td></tr>
                                <tr><th>Country</th><td class="country">{{$country}}</td></tr>
                                <tr><th>TIN</th><td class="tin">{{$tin}}</td></tr>
                                <tr><th>Branch Code</th><td class="branch_code">{{$branch_code}}</td></tr>
                                <tr><th>Phone</th><td class="phone">{{$phone_no}}</td></tr>
                                <tr><th>Fax</th><td class="fax">{{$fax}}</td></tr>
                                <tr><th>Email</th><td class="email">{{$email}}</td></tr>
                            @endforeach
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
                <input type="text" name="search_prod" class="search_prod form-control" id="prod_ajax" list="prod_ajaxlist" placeholder="Search Product Name" autocomplete="off"/>
                <datalist id="prod_ajaxlist">
                    <option value="Select"></option>
                </datalist>
                <input type="hidden" value="" name="prod_id">
                <input type="hidden" value="2" name="vat_id_hidden" class="vat_id_hidden">
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
                <div class="pull-right total-amount">
                    <table style="padding: 15px;display: block;">
                        <tr><th>Total</th><td class="not-editable-td"><input type="text" name="total" class="form-control total" value=""></td></tr>
                        <tr><th>VAT</th><td class="not-editable-td1"><input type="text" name="vat" value="" class="form-control vat" value=""></td></tr>
                        <tr><th>Net of VAT</th><td class="not-editable-td2"><input type="text" name="nov" class="form-control net-of-vat" value=""></td></tr>
                        <tr><th>Less: SC/PWD Discount <select class="disc-yes-no"><option value="1">No</option><option value="2">Yes</option></select></th><td class="not-editable-td3"><input type="text" name="scpwd_discount" class="form-control discount" value=""><input type="hidden" name="discount" value="" class="form-control discount_hidden"></td></tr>
                        <tr><th>Net Sales</th><td class="not-editable-td4"><input type="text" name="net_sales" class="form-control net-sales" value=""></td></tr>
                        <tr><th>Add: VAT</th><td class="not-editable-td5"><input type="text" name="add_vat" class="form-control add-vat" value=""><input type="hidden" name="vat" value="" class="form-control vat_hidden"></td></tr>
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
                <input type="hidden" name="chartaccount" value="1" class="form-control chartaccount_hidden">
                <table class="table table-bordered table-striped table-hover cash-inv-journal-entry-table">
                    <tr class="cash-inv-journal-entry-table-th">
                        {{-- */
                        $th_date="";
                        $th_date = Carbon\Carbon::now()->subDay()->format('m/Y');
                        $td_day = Carbon\Carbon::now()->format('d');
                        /* --}}
                        <th style="text-align: center">{{$th_date}}</th>
                        <th style="text-align: center">Acct. No.</th>
                        <th style="width: 35%!important; text-align: center">Account Title</th>
                        <th style="text-align: center">Ref #</th>
                        <th style="text-align: center">Debit</th>
                        <th style="text-align: center">Credit</th>
                    </tr>
                    <tr class="cash-debit">
                        <td align="center">{{$td_day}}</td>
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
                        <td></td>
                        <td><input type="text" name="sales_account_no" class="sales_account_no form-control" value="4000"></td>
                        <td>
                            <input type="text" name="sales" class="sales form-control cash-input-disable" value="Sales" style="width: 250px; float: right;">
                        </td>
                        <td><input type="text" name="ref_no" class="ref_no form-control"></td>
                        <td></td>
                        <td><input type="text" name="credit" class="credit-sales form-control cash-input-disable" style="text-align: right"></td>
                    </tr>
                    <tr class="credit-ouput-tax">
                        <td></td>
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
                        <td></td>
                        <td style="vertical-align: middle; text-align: right; font-weight: bold; font-size: 16px;">Total:</td>
                        <td><input type="text" name="total_debit" class="total_debit form-control cash-input-disable" style="text-align: right; font-weight: bold; font-size: 16px;"></td>
                        <td><input type="text" name="total_credit" class="total_credit form-control cash-input-disable" style="text-align: right; font-weight: bold; font-size: 16px;"></td>
                    </tr>
                </table>
            </div>
        </div>
            <button type="submit" class="btn btn-primary save-btn" style="margin-top: 10px; width: 100px;" onclick="cust_idFunction(); return false;">Save</button>
        {!! Form::close() !!}
        <div style="clear: both;"></div>
    </section>
</div>

@endsection

@section('footer_script_preload')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
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
    total_amt = 0;
    discount_rate = 0;
});

$( document ).ready(function() {
    $(".disc-yes-no").change(function() {
        var target = $('.disc-yes-no option:selected').val();
        if(target == 2){
            var discount_rate = $('input.discount_hidden').val();
            var discount_conversion = discount_rate/100;
            console.log(discount_conversion);
            var net_of_vat = $('input.net-of-vat').val();
            net_of_vat = parseFloat(net_of_vat.replace(/,/g,''))
            //console.log(net_of_vat);
            var sc_pwd_discount = parseFloat(net_of_vat) * discount_conversion;
            //console.log(sc_pwd_discount);
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

        }else if(target == 1){
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

//auto suggest
$('#prod_ajax').keyup(function(){
    var ss = $('.search_prod').val();
    if( !ss ) {
    return;
    }
    if($('#prod_ajax').data('timeout')) {
        clearTimeout($('#prod_ajax').data('timeout'));
    }
    var timesearch = setTimeout(function(){
      var request = { ss: ss };
            $.get('{{url("cash-invoice/find-product-autosuggest")}}', request, function(datasuggest){
                $('#prod_ajaxlist').empty();
                if(datasuggest){
                    for(var prop in datasuggest){
                    var opt = $('<option value="" class="product_name"></option>').attr("value", datasuggest[prop].product_name);
                    $('#prod_ajaxlist').append(opt);
                    }
                }
            }, 'json');
    }, 400);
    $('#prod_ajax').data('timeout', timesearch);
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
    var cust_ids = $('.payto').val();
    if(cust_ids==''){
    alert("Please select customer");
    $( ".payto" ).focus();
    }
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