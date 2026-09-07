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

/* Products table styling */
#products-table { font-size: 12px; }
#products-table tbody tr td { padding: 8px; vertical-align: middle; }
#products-table input[type="text"], #products-table input[type="number"] { 
    width: 100%; 
    padding: 5px; 
    font-size: 12px;
}
#products-table .btn-remove-row {
    padding: 2px 8px;
    font-size: 11px;
}
#products-table .remove-product-row:hover { 
    cursor: pointer; 
    background-color: #f5f5f5;
}
.total-cost,.total-amount{
  text-align: right;
}
#total-amount{
  border: 1px solid #d2d6de;
  width: 100%;
  padding: 5px;
  font-size: 14px;
  display: block;
}
.total-cost .row-total{
  border: 1px solid #d2d6de;
  width: 100%;
  padding: 5px;
  font-size: 14px;
  display: block;
}
#products-table thead th{
  background-color: #00c0ef;
  color: #fff;
}
</style>
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
        @include('_includes.message')
        <h1>
            Create New Expenses
        </h1>
    </section>

    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-employee warning-msg-true">
        <p>{{ $message }}</p>
    </div>
    @endif
    
    <!-- Main content -->
    <section class="content">
    
    {!! Form::open(['url' => '/pos-expenses']) !!}

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><a href="{{ route('point-of-sale.index') }}" class="btn btn-primary" style="float: left; margin-right: 10px;">Back to POS Dashboard</a></h3>
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
                @if(empty($pos_expenses->invoice_no))
                  <input type="text" name="invoice_no" class="form-control" id="invoice_no" value="0" readonly>
                @else
                  <input type="text" name="invoice_no" class="form-control" id="invoice_no" value="{{ $pos_expenses->invoice_no }}" readonly>
                @endif
                <input type="hidden" class="remarks" name="remarks">
                <div class="input-group-btn btn-editable">
                  <a href="#" class="edit-invoice btn btn-primary"><i class="fa fa-pencil"></i> Edit Invoice No.</a>
                  <a href="#" class="cancel-invoice btn btn-primary"><i class="fa fa-times"></i> Cancel</a>
                </div>
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
                  <button type="button" name="" class="btn"></button>
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
            </div>
            
            </div>
            <!-- /.row -->
        </div>
        <!-- ./box-body -->
    </div>

    <!-- Products Section -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Products / Items</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Add Product</label>
                        <div class="input-group input-group-sm">
                            <input type="hidden" id="product_id" name="product_id">
                            <input type="text" id="product_search" class="form-control" placeholder="Search product by name...">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-info btn-flat" id="btn-add-product">Add Product</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="products-table">
                            <thead>
                                <tr>
                                    <th style="width: 30%">Product Name</th>
                                    <th style="width: 15%">Cost Price</th>
                                    <th style="width: 15%">Quantity</th>
                                    <th style="width: 15%">Total</th>
                                    <th style="width: 15%">Remarks</th>
                                    <th style="width: 10%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="products-tbody">
                                <!-- Products will be added here -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" style="text-align: right; font-size: 16px;">Total Amount:</th>
                                    <th class="total-amount"><span id="total-amount">0.00</span></th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">
                {!! Form::submit('Create', ['class' => 'btn btn-primary form-control', 'style'=>'width:100px;']) !!}
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
    var productsData = {}; // Store product details by ID

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
        
        //cpv_show_hide_account_details();
    });
    $('[name=terms]').trigger('change');
    
    // Vendor autocomplete search
    var selectedVendorId = null;
    $('.vendor_name').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '{{ url("pos-expenses/find-vendors") }}',
                data: {
                    term: request.term
                },
                success: function(data) {
                    response(data);
                },
                error: function(error) {
                    console.error('Vendor search error:', error);
                    response([]);
                }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            $('.vendor_name').val(ui.item.label);
            selectedVendorId = ui.item.id;
            // Automatically trigger the search/Go button to load vendor info
            setTimeout(function() {
                $('.btn-search').click();
            }, 100);
            return false;
        }
    });

    // Clear the stale selection whenever the vendor name is typed manually
    $('.vendor_name').on('input', function(){
        selectedVendorId = null;
    });

    /* Search for a vendor info. */
    $('.btn-search').on('click', function(){
        var s = ($('.vendor_name').val()).trim();
        var request = { s: s };
        
        if( selectedVendorId ){
            request.vendor_id = selectedVendorId;
        }
        else if( !s ){
            // alert('it\'s empty'); 
            return;
        }
        // console.log( request );
        
        $('.box .overlay').removeClass('hidden');
        
        $.get('{{url("pos-expenses/find-vendor-only")}}', request, function(data){
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
    
    function submit_for_validation(callback){
        $.post('{{url("supplier-invoice/add-form-validate")}}', $('form').serialize(), function(response){
            if(typeof callback == 'function'){
                callback(response);
            }
        }, 'json');
    }

  // Hide and show the cancel button and the Edit button
    $('.cancel-invoice').hide();
    $('.edit-invoice').click(function(){
      $(this).hide();
      $("#invoice_no").attr("readonly", false).focus();
      $(".remarks").val('1');
      $('.cancel-invoice').show();
    });

    $('.cancel-invoice').click(function(){
      $(this).hide();
      $("#invoice_no").attr("readonly", true).focus();
      $(".remarks").val('');
      $('.edit-invoice').show();
    });

    // This will create the auto generated entry no
    var n = ($('[name=invoice_no]').val() || 0);
    var num = Number(n) + Number(1);

    var entry_no = String('0000000000' + num).slice(-10);
    window.onload = function () {
        document.getElementById("invoice_no").value = entry_no; // HERE ;)
    }

    // ===== PRODUCTS SECTION JAVASCRIPT =====
    
    // Product autocomplete search
    $('#product_search').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '{{ url("pos-expenses/find-products") }}',
                data: {
                    term: request.term
                },
                success: function(data) {
                    response(data);
                },
                error: function(error) {
                    console.error('Product search error:', error);
                    response([]);
                }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            $('#product_id').val(ui.item.id);
            productsData[ui.item.id] = {
                name: ui.item.label,
                cost_price: ui.item.cost_price
            };
            return false;
        }
    });

    // Add product button click
    $('#btn-add-product').on('click', function(e) {
        e.preventDefault();
        
        var productId = $('#product_id').val();
        var productSearch = $('#product_search').val();
        
        if (!productId) {
            alert('Please select a product');
            return;
        }
        
        // Check if product already exists in table
        if ($('#products-tbody').find('[data-product-id="' + productId + '"]').length > 0) {
            alert('This product is already added');
            return;
        }
        
        var productName = productsData[productId].name;
        var costPrice = parseFloat(productsData[productId].cost_price) || 0;
        
        // Add row to table
        var rowHTML = '<tr class="remove-product-row" data-product-id="' + productId + '">' +
            '<td><input type="hidden" name="product_ids[]" value="' + productId + '">' + productName + '</td>' +
            '<td><input type="text" class="form-control cost-price" value="' + costPrice.toFixed(2) + '" data-product-id="' + productId + '"></td>' +
            '<td><input type="number" class="form-control quantity" value="1" min="1" data-product-id="' + productId + '"></td>' +
            '<td class="total-cost"><span class="row-total">' + (costPrice * 1).toFixed(2) + '</span></td>' +
            '<td><input type="text" class="form-control remarks" name="product_remarks[]" placeholder="Notes..."></td>' +
            '<td><button type="button" class="btn btn-danger btn-xs btn-remove-row"><i class="fa fa-trash"></i></button></td>' +
            '</tr>';
        
        $('#products-tbody').append(rowHTML);
        
        // Reset search
        $('#product_search').val('');
        $('#product_id').val('');
        
        // Calculate totals
        updateTotals();
    });

    // Calculate row total when quantity or price changes
    $(document).on('change', '.quantity, .cost-price', function() {
        var $row = $(this).closest('tr');
        var quantity = parseFloat($row.find('.quantity').val()) || 0;
        var costPrice = parseFloat($row.find('.cost-price').val()) || 0;
        var total = quantity * costPrice;
        $row.find('.row-total').text(total.toFixed(2));
        updateTotals();
    });

    // Remove product row
    $(document).on('click', '.btn-remove-row', function(e) {
        e.preventDefault();
        $(this).closest('tr').remove();
        updateTotals();
    });

    // Update total amount
    function updateTotals() {
        var totalAmount = 0;
        $('#products-tbody tr').each(function() {
            var total = parseFloat($(this).find('.row-total').text()) || 0;
            totalAmount += total;
        });
        $('#total-amount').text(totalAmount.toFixed(2));
    }

    // Form submission - prepare product data
    $('form').on('submit', function(e) {
        // Add product details as hidden fields
        var productsCount = $('#products-tbody tr').length;
        
        if (productsCount === 0) {
            alert('Please add at least one product to the expense');
            e.preventDefault();
            return false;
        }
        
        // Create array of product details
        var productDetails = [];
        $('#products-tbody tr').each(function(index) {
            var productId = $(this).data('product-id');
            var quantity = $(this).find('.quantity').val();
            var costPrice = $(this).find('.cost-price').val();
            var totalPrice = $(this).find('.row-total').text();
            var remarks = $(this).find('.remarks').val();
            
            productDetails.push({
                product_id: productId,
                quantity: quantity,
                cost_price: costPrice,
                total_price: totalPrice,
                remarks: remarks
            });
        });
        
        // Add as hidden input
        $('<input type="hidden" name="products_json">').val(JSON.stringify(productDetails)).appendTo('form');
    });

</script>
@endsection