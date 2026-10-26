@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('header_style_preload')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">

        @include('_includes.message')

        <div class="pull-left">
            <h1>Edit Sales Entry #{{ $pos_record->id }}</h1>
        </div>

        <div class="pull-right">
            <a class="btn btn-primary" href="{{ url('point-of-sale/reports') }}"> Back</a>
        </div>
        <div style="clear: both;"></div>

    </section>

    <section class="content">

    {!! Form::open(array('url' => 'point-of-sale/' . $pos_record->id, 'method' => 'PUT', 'id' => 'edit-pos-form')) !!}

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="input-group input-group-sm">
                        <div class="input-group-btn"><button type="button" class="btn">Customer</button></div>
                        <input type="text" class="form-control customer_name" id="cust_ajax" value="{{ old('customer_name', $customer_name) }}" name="customer_name" placeholder="Customer Name" autocomplete="off">
                        <input type="hidden" name="customer_id" class="id" value="{{ old('customer_id', $pos_record->customer_id) }}">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="input-group input-group-sm">
                        <div class="input-group-btn"><button type="button" class="btn">Date</button></div>
                        <input type="text" class="form-control datepicker" name="date" value="{{ old('date', $pos_record->sales_date) }}" autocomplete="off">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title err-title"> &nbsp; </h3>
        </div>
        <div class="box-body">
            <div class="searh-div" style="margin-bottom: 10px;">
                <input type="text" class="search_prod form-control" id="prod_ajax" placeholder="Enter barcode or product name" autocomplete="off"/>
                <input type="text" class="prod-qty form-control" placeholder="Qty"/>
                <button type="button" class="btn btn-primary btn-enter">Enter</button>
            </div>
            <div style="clear: both;"></div>
            <div class="row">
                <div class="col-md-9 col-sm-12">
                    <div class="table-responsive">
                        <table id="tbl-items" class="table table-bordered table-striped table-hover table-product-info">
                        <thead>
                            <tr>
                                <th class="actions" style="width: 24px;"></th>
                                <th style="width: 60%; text-align: center;">Item</th>
                                <th style="text-align: center; width: 70px;">Qty</th>
                                <th style="text-align: center; width: 70px;">Price</th>
                                <th style="text-align: center; width: 70px;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pos_sold_products as $n => $item)
                            <tr class="prod-row">
                                <td class="text-center"><i class="fa fa-close text-red remove-row" style="cursor: pointer;" title="Remove"></i></td>
                                <td class="name">
                                    <input type="hidden" name="products[{{ $n }}][product_id]" value="{{ $item->product_id }}" />
                                    <input type="text" class="name" value="{{ trim($item->name . ($item->sr_priority ? ' - ' . $item->sr_priority : '')) }}" readonly />
                                </td>
                                <td class="qty"><input type="text" name="products[{{ $n }}][qty]" class="qty" value="{{ $item->qty + 0 }}" /></td>
                                <td class="price"><input type="text" name="products[{{ $n }}][price]" class="price" value="{{ number_format($item->price, 2, '.', '') }}" /></td>
                                <td class="subtotal"><input type="text" class="subtotal" value="{{ number_format($item->amount, 2, '.', '') }}" readonly /></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="4" class="text-right">Total:</td>
                                <td class="total text-right amount">{{ number_format($pos_sold_products->sum('amount'), 2, '.', '') }}</td>
                            </tr>
                        </tfoot>
                        </table>
                    </div>
                </div>
                <div class="col-md-3 col-sm-12">
                    <table class="table no-border sales-details">
                    <tbody>
                        <tr>
                            <td class="text-right f-label">Amount Due</td>
                            <td class="field-only"><input type="text" class="form-control amount_due" name="amount_due" value="{{ number_format($pos_sold_products->sum('amount'), 2, '.', '') }}" readonly></td>
                        </tr>
                        <tr>
                            <td class="text-right f-label">Amount Paid</td>
                            <td class="field-only"><input type="text" class="form-control amount_pay" name="amount_pay" value="{{ old('amount_pay', number_format($amount_paid, 2, '.', '')) }}" autocomplete="off"></td>
                        </tr>
                        <tr>
                            <td class="text-right f-label">Balance</td>
                            <td class="field-only"><input type="text" class="form-control amt_balance" name="amt_balance" value="0.00" readonly></td>
                        </tr>
                        <tr>
                            <td class="text-right f-label">Status</td>
                            <td class="field-only"><input type="text" class="form-control status" value="" readonly></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 0">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary form-control save-sales-entry">Update</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}

    </section>

</div>
@endsection


@section('footer_script_preload')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection


@section('footer_script')
<script>
    $('.datepicker').datepicker({
        autoclose: true,
        todayBtn: "linked",
        todayHighlight: true,
        format: 'yyyy-mm-dd'
    });

    $('#cust_ajax').autocomplete({
        source: '{{ url("cash-invoice/find-customer") }}',
        minLength: 3,
        select: function(event, ui){
            $('input[name=customer_id]').val(ui.item.id);
        }
    });

    // Clearing the name keeps the existing customer; typing a new name without picking from the list is ignored
    $('#cust_ajax').on('input', function(){
        if(!$.trim($(this).val())){
            $('input[name=customer_id]').val('{{ $pos_record->customer_id }}');
        }
    });

    $('#prod_ajax').autocomplete({
        source: '{{ url("point-of-sale/find-product-autosuggest") }}',
        minLength: 2
    });

    function toNumber(val){
        val = parseFloat(String(val).replace(/,/g, ''));
        return isNaN(val) ? 0 : val;
    }

    function rename_fields(){
        $('#tbl-items tr.prod-row').each(function(i){
            $('input[name^="products["]', this).each(function(){
                var name = $(this).attr('name').replace(/^products\[\d+\]/, 'products[' + i + ']');
                $(this).attr('name', name);
            });
        });
    }

    function recalculate(){
        var total = 0;

        $('#tbl-items tr.prod-row').each(function(){
            var subtotal = Math.round(toNumber($(this).find('input.qty').val()) * toNumber($(this).find('input.price').val()) * 100) / 100;
            $(this).find('input.subtotal').val(subtotal.toFixed(2));
            total += subtotal;
        });

        total = Math.round(total * 100) / 100;
        $('#tbl-items td.total.amount').text(total.toFixed(2));
        $('input.amount_due').val(total.toFixed(2));

        var paid = toNumber($('input.amount_pay').val());
        var balance = Math.max(Math.round((total - paid) * 100) / 100, 0);

        $('input.amt_balance').val(balance.toFixed(2));

        if(balance == 0){
            $('input.status').val('Paid');
        }else if(paid == 0){
            $('input.status').val('Unpaid');
        }else{
            $('input.status').val('Balanced');
        }
    }

    $('#tbl-items').on('input change', 'input.qty, input.price', recalculate);
    $('input.amount_pay').on('input change paste keyup', recalculate);

    $('#tbl-items').on('click', '.remove-row', function(){
        $(this).closest('tr').remove();
        rename_fields();
        recalculate();
    });

    $('.btn-enter').on('click', function(){

        var s = $.trim($('#prod_ajax').val());
        if(!s) return;

        var qty = toNumber($('.prod-qty').val());
        if(!qty){
            alert('Please Enter a valid quantity');
            return;
        }

        var request = { s: s };
        var search_ui = $('#prod_ajax').data('ui-autocomplete');
        if(search_ui && search_ui.selectedItem){
            request.prod_id = search_ui.selectedItem.id;
        }

        $.get('{{ url("point-of-sale/find-product") }}', request, function(data){

            if(!data || !Object.keys(data).length){
                alert('Product not found.');
                return;
            }

            var num = $('#tbl-items tr.prod-row').length;
            var label = $('<div>').text(data.name + (data.sr_priority ? ' - ' + data.sr_priority : '')).html();

            var html = '<tr class="prod-row">' +
                '<td class="text-center"><i class="fa fa-close text-red remove-row" style="cursor: pointer;" title="Remove"></i></td>' +
                '<td class="name"><input type="hidden" name="products[' + num + '][product_id]" value="' + data.id + '" />' +
                '<input type="text" class="name" value="' + label + '" readonly /></td>' +
                '<td class="qty"><input type="text" name="products[' + num + '][qty]" class="qty" value="' + qty + '" /></td>' +
                '<td class="price"><input type="text" name="products[' + num + '][price]" class="price" value="' + parseFloat(data.price).toFixed(2) + '" /></td>' +
                '<td class="subtotal"><input type="text" class="subtotal" readonly /></td>' +
                '</tr>';

            $('#tbl-items > tbody').append(html);

            $('#prod_ajax').val('');
            $('.prod-qty').val('');
            if(search_ui) search_ui.selectedItem = null;
            recalculate();
        }, 'json');
    });

    // Enter in the quantity/search boxes adds the product instead of submitting the form
    $('#prod_ajax, .prod-qty').on('keydown', function(e){
        if(e.which == 13){
            e.preventDefault();
            $('.btn-enter').click();
        }
    });

    $('#edit-pos-form').on('submit', function(e){
        if(!$('#tbl-items tr.prod-row').length){
            alert('Please add at least one product.');
            e.preventDefault();
        }
    });

    recalculate();
</script>
<style type="text/css">
input.search_prod{
    max-width: 475px;
    width: 100%;
}
.searh-div input{
    display: inline-block;
}
.save-sales-entry{
    max-width: 180px;
    text-align: center!important;
    float: right;
    margin-top: 5px;
    margin-bottom: 5px;
}
.datepicker{
    font-size: 18px!important;
}
</style>
@endsection
