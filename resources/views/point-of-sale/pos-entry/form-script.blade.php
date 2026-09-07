<script>
    //auto suggest
    $('#prod_ajax').autocomplete({
        source: '{{ url("pos-product-autosuggest") }}',
        minLength: 2,
    });

    $('#cust_ajax').autocomplete({
        source: '{{ url("find-customer-autosuggest") }}',
        minLength: 3
    });

    $('.datepicker').datepicker({
        autoclose: true,
        todayBtn: "linked",
        todayHighlight: true,
        format: 'yyyy-mm-dd'
    });

    // Get customers on go button event
    $('.btn-search, .customer-search_by_id').on('click', function(){

        var cust_name = ($('.customer_name').val()).trim();
        var request = { cust_name: cust_name };

        if( $(this).is('.customer-search_by_id') ){
            if( $(this).data('customer_id') > 0 ){
                request.customer_id = $(this).data('customer_id')
            }
            else{
                return;
            }
        }
        else if( !cust_name ){
            return;
        }
        else if( $(this).is('.btn-search') 
                 && $('#cust_ajax').data('ui-autocomplete') 
                 && $('#cust_ajax').data('ui-autocomplete').selectedItem ){
            request.customer_id = $('#cust_ajax').data('ui-autocomplete').selectedItem.id;
        }

        $('.box .overlay').removeClass('hidden');

        $.get('{{url("pos-find-customer")}}', request, function(datacust){

            if(datacust && Object.keys(datacust).length){

                //console.log(datacust);

                        // var issued_bottle = 0,
                        // return_bottle = 0,
                        // order_qty = 0,
                        // total_issued_bottle =0;

                for(var prop in datacust){
                    $('.cash-invoice-input input.' + prop).val( datacust[prop] );

                    if(prop == 'individual' && datacust[prop] == '1'){
                        $('.table-customer-info td.'+prop).html( '<span class="badge bg-green">Yes</span>' );
                    }
                    else if(prop == 'individual' && datacust[prop] == '0'){
                        $('.table-customer-info td.'+prop).html( '<span class="badge bg-yellow">No</span>' );
                    }
                    else if(prop == 'id'){
                        $('input[name=customer_id]').val( datacust[prop] );
                    }
                    else{
                        $('.table-customer-info td.'+prop).html( datacust[prop] );
                    }

                    if(prop == 'full_name') $('.customer_name').val( datacust[prop] );
                
                    if(prop == 'return_bottle') return_bottle = datacust[prop];
                    if(prop == 'order_qty') order_qty = datacust[prop];    
                }

                // total_issued_bottle = Number(order_qty) - Number(return_bottle);
                // //console.log({total_issued_bottle,order_qty,return_bottle});
                // $('input[name=issued_bottle]').val(total_issued_bottle);
                // $('input[name=return_bottle]').val(0);
            }
            else{
                /* Cleanup Customer Info */
                $('input[name=customer_id], .cash-invoice-input input').val('');
                $('.table-customer-info td').html('');
            }

        }).always(function(){
            $('.box .overlay').addClass('hidden');
        });
    });

    function rename_fields(){

        var cntr = 0;

        $('#tbl-items tr.prod-row').each(function(){
            $('input', this).each(function(){
                var field_name = $(this).attr('name');
                if( field_name.indexOf("products[") == 0 ){
                    var new_field_name = field_name.replaceAt(9, ''+cntr);
                    $(this).attr('name', new_field_name);
                }
            });
            cntr++;
        });
    }

    function commaSeparateNumber(val){
        while (/(\d+)(\d{3})/.test(val.toString())){
            val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
        }
        return val;
    }

    // Calculating the balance amount and put a status which is paid, unpaid, balance
    function calculateBalance(){

        var amount_due = 0,
        amount_pay = 0,
        balance = 0,
        change = 0;
        
        amount_due = $('[name=amount_due]').val() || 0;
        amount_pay = $('[name=amount_pay]').val() || 0;

        amount_due = amount_due.replace(/\,/g,'');

        balance = Number(amount_due) - Number(amount_pay);
        change = Number(amount_pay) - Number(amount_due);

        $('[name=amt_balance]').val(balance.toFixed(2));

        if(balance == 0){
            $('[name=status]').val('Paid');
        }else if(balance == amount_due){
            $('[name=status]').val('Unpaid');
        }else{
            $('[name=status]').val('Balanced');
        }

        if(balance < 0){
            $('[name=status]').val('Paid');
            $('[name=amt_balance]').val('0.00');
        }
        $("input.change").val(commaSeparateNumber(change.toFixed(2)));
        if(change < 0){
            $('[name=change]').val('0.00');
        }
    }

    function do_products_total(){

        var amount_total = 0;
        var order_qty = 0;

        order_qty = ($('[name=qty]').val() || 0);

        var type_entry = $("select.type-entry").val();
        if(type_entry == 2){
            $('[name=refill_bottle]').val(order_qty); 
        }else if(type_entry == 4){
            $('[name=others_qty]').val(order_qty); 
        }else if(type_entry == 5){
            $('[name=container_qty]').val(order_qty);
        }else if(type_entry == 6){
            $('[name=dealer_qty]').val(order_qty);
        }else{
            $('[name=order_qty]').val(order_qty); 
        }

        $('.table-product-info tr.prod-row').each(function(){
            var subtotal = $(this).find('input.subtotal').val() || 0;
            subtotal = isNaN(subtotal) ? 0 : parseFloat(subtotal).toFixed(2)*1;
            amount_total += subtotal;

        });

        /* Display Product total */

        $('[name=amount_due]').val(amount_total.toFixed(2));
        $('[name=amt_balance]').val(amount_total.toFixed(2));
        $('.table-product-info td.total.amount').text( amount_total.toFixed(2) );
    }

    //var disable_acc_total = false;

    function do_calculation2(){

        $('input.order_qty').val(order_qty);

        /* Copy info */
        $('[name=order_qty], [name=amount_due]').trigger('change');
    }

    /* Search and product and add a product row. */
    $('.btn-enter').on('click', function(){

        var s = ($('.search_prod').val()).trim();
        if( !s ) return;

        var qty = $('[name=qty]').val() * 1;

        if( !qty ){

            // alert("Please Enter a valid quantity");
            $("h3.err-title").html("<strong style='color: red;'>Please Enter a valid quantity. Thank you.</strong>");
            $( ".prod-qty" ).focus();
            return;
        }else{
            $("h3.err-title").html(" ");
            $( "#prod_ajax" ).focus();
        }

        var request = { s: s };
        var search_ui = $('.search_prod').data('ui-autocomplete');

        if( search_ui && search_ui.selectedItem ){

            request.prod_id = search_ui.selectedItem.id;
        }

        $.get('{{url("pos-find-product")}}', request, function(data){

            console.log(data);

            if(data == ''){
                $("h3.err-title").html("<strong style='color: red;'>Sorry!!! Barcode not found. Thank you.</strong>");
                return false;
            }

            if(data && Object.keys(data).length){
                
                // console.log(data);
                var request_qty = $(".prod-qty").val();
                var remaining_stocks = 0;
                var sold_qty = data.sold_qty;
                var total_stock = data.total_added_qty;
                var inventory_status = data.inventory_status;

                remaining_stocks = total_stock - sold_qty;

                if(remaining_stocks < request_qty && inventory_status == 'Active'){
                    $("h3.err-title").html("<strong style='color: red;'>Sorry!!! Stock quantity left is "+remaining_stocks+" only. Thank you.</strong>");
                    return false;
                }else{
                    $("h3.err-title").html(" ");
                }

                /* Add tr */
                var html = $('.prod-row-template tbody').html();
                var num  = $('.table-product-info tr.prod-row').length;

                html = html.replace(new RegExp('{num}', 'g'), num);

                $('.table-product-info > tbody').append( html );

                /* Fill in values to new tr */
                var $tr = $('.table-product-info tr.prod-row:last-child');

                $tr.find('input.product_id').val( data.id );
                $tr.find('input.name').val( data.name );
                $tr.find('input.qty').val( qty );
                $tr.find('input.price').val( parseFloat(data.price).toFixed(2) );
                $tr.find('input.subtotal').val( parseFloat(data.price * qty).toFixed(2) );                

                $('[name=pro_id]').val(data.id)

                /* Calculation */
                do_products_total();
                calculateBalance();
                $('.search_prod').val("");
                $('.prod-qty').val("");
            }
        }, 'json');
    });

    

    /* Remove product row */

    $('#tbl-items').on('click', '.fa-close', function(){

        $(this).closest('tr').remove();

        rename_fields();

        do_products_total();

        $('input.order_qty').val(0);
        $('input.container_qty').val(0);
        $('input.dealer_qty').val(0);
        $('input.change').val('0.00');
        $('input.amount_pay').val('');
        $('[name=type_filter]').val('');
        $("h3.err-title").html('');

    });

    

    /* When product Price is changed inside a TR, do calculation */

    $('.table-product-info').on('change', 'input.price', function(){

        var price = $(this).val() || 0;

        price = isNaN(price) ? 0 : parseFloat(price).toFixed(2)*1;

        

        var $tr = $(this).closest('tr');

        var qty = $tr.find('input.qty').val() || 0;

        $tr.find('input.subtotal').val( parseFloat(price * qty).toFixed(2) );

        do_products_total();

    });



    /* Calculate Button */

    $('.btn-calculate').on('click', function(){

        do_calculation2();

    });



    /* # of persons with condition */

    $('[name=no_of_person], [name=no_of_scpwd]').on('change', function(){

        var no_of_person = parseInt($('[name=no_of_person]').val()*1 || 0);
        var no_of_scpwd  = parseInt($('[name=no_of_scpwd]').val()*1 || 0);

        if( $(this).is('[name=no_of_person]') && no_of_person < no_of_scpwd) $('[name=no_of_scpwd]').val( no_of_person );
        if( $(this).is('[name=no_of_scpwd]') && no_of_scpwd > no_of_person) $('[name=no_of_person]').val( no_of_scpwd );
    });



    /* Show on discount */

    $("select.discounted").change(function() {
        var discounted = $(this).val() == 1;

        if(discounted){
            $('.show-on-discount').show(200);
            $('.show-on-nodiscount').hide(200);
        }
        else{
            $('.show-on-discount').hide(200);
            $('.show-on-nodiscount').show(200);
        }
    });

    /* Fill out Account row for Vatable Sales (coa_credit) */
    $('[name=vatable_sales]').change(function(){

        var vatable_sales = $(this).val()*1 || 0;
        var $acc_row = $('tr.account-row.credit.main');

        $acc_row.find('input.tax_id').val( $('[name=vat_id]').val() );
        $acc_row.find('input.rate').val( $('[name=vat_id]').data('rate') );
        $acc_row.find('input.debit_field').val( '' );
        $acc_row.find('input.credit_field').val( vatable_sales.toFixed(2) );

        account_details_total();
    });



    /* Fill out Account row for Vat-exempt Sales (coa_credit) optional */
    $('[name=vat_exempt_sales]').change(function(){

        var vat_exempt_sales = $(this).val()*1 || 0;
        var $acc_row = $('tr.account-row.credit.credit2');

        if($('select.discounted').val() == 0){
            $acc_row.hide(200);
			vat_exempt_sales = 0;
		}
		else if(vat_exempt_sales == 0)
            $acc_row.hide(200); // hide because it's optional row
        else
            $acc_row.show(200);

        $acc_row.find('input.tax_id').val( $('[name=vat_id]').val() );
        $acc_row.find('input.rate').val( $('[name=vat_id]').data('rate') );
        $acc_row.find('input.debit_field').val( '' );
		$acc_row.find('input.credit_field').val( vat_exempt_sales.toFixed(2) );

        account_details_total();
    });



    /* Fill out Account row for Discount SC/PWD (discount_debit) optional */

    $('[name=discount_amount]').change(function(){

        var discount_amount = $(this).val()*1 || 0;
        var $acc_row = $('tr.account-row.debit.discount');
        var discount_data = $('input.discount').data();
		var $select = $acc_row.find('select.chart-account-dropdown');

		$select.val(discount_data.chart_account_id);

		disable_acc_total = true;

		$select.trigger('change');

		disable_acc_total = false;

		

        if($('select.discounted').val() == 0){

            $acc_row.hide(200);

			discount_amount = 0;

		}

		else if(discount_amount == 0)

            $acc_row.hide(200); // hide because it's optional row

        else

            $acc_row.show(200);

		

        $acc_row.find('input.discount_id').val( $('[name=discount_id]').val() );

        $acc_row.find('input.rate').val( $('[name=discount_id]').data('rate') );

		

        $acc_row.find('input.credit_field').val( '' );

        $acc_row.find('input.debit_field').val( discount_amount.toFixed(2) );

        

        account_details_total();

    });



    /* Fill out Account row for VAT tax (tax_credit) */

    $('[name=vat_amount]').change(function(){

        var vat_amount = $(this).val()*1 || 0;

        var $acc_row = $('tr.account-row.tax.vat');

        

        $acc_row.find('input.tax_id').val( $('[name=vat_id]').val() );

        $acc_row.find('input.rate').val( $('[name=vat_id]').data('rate') );

		

        $acc_row.find('input.debit_field').val( '' );

        $acc_row.find('input.credit_field').val( vat_amount.toFixed(2) );

		

        account_details_total();

    });



    /* Fill out Account row for withholding tax (tax_debit) optional */

    $('select.withholding_tax, input.whtax_amount').change(function(){

        var whtax_amount = $('input.whtax_amount').val()*1 || 0;

        var $acc_row = $('tr.account-row.withholding');

        var withholding_data = $('select.withholding_tax option:selected').data();

		

        var $select = $acc_row.find('select.chart-account-dropdown');

		$select.val(withholding_data.chart_account_id)

		

		disable_acc_total = true;

		$select.trigger('change');

		disable_acc_total = false;

		

        if($('select.withholding_tax').val() == 0){

            $acc_row.hide(200);

			whtax_amount = 0;

		}

		else if(whtax_amount == 0)

            $acc_row.hide(200); // hide because it's optional row

        else

            $acc_row.show(200);

        

        $acc_row.find('input.tax_id').val( $('select.withholding_tax').val() );

        $acc_row.find('input.rate').val( withholding_data.rate );

		

        $acc_row.find('input.debit_field').val( whtax_amount.toFixed(2) );

        $acc_row.find('input.credit_field').val( '' );

		

        account_details_total();

    });



    /* Fill out Account row for VAT tax (coa_debit) */

    $('[name=amount_due]').change(function(){

        var amount_due = $(this).val()*1 || 0;

        var $acc_row = $('tr.account-row.debit.main');

        $acc_row.find('input.debit_field').val( amount_due.toFixed(2) );

        $acc_row.find('input.credit_field').val( '' );

        

        account_details_total();

    });

    

    /* Account Title Dropdown on change */

    $('.table-account-details').on('change', 'tr.account-row select.chart-account-dropdown', function(){

        var this_tr = $(this).closest('tr');

        

        var data = $(this).find(":selected").data();

		if(data){

			if(data.code) this_tr.find('td.account-number input.code').val( data.code );

			if(data.tax_id) this_tr.find('input.tax_id').val( data.tax_id );

			if(data.discount_id) this_tr.find('input.discount_id').val( data.discount_id );

		}

        

        account_details_total();

    });

    

    /* start --- Form validation */

    function submit_for_validation(url, callback){

        $.post(url, $('form').serialize(), function(response){

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

            

            /* if the field starts with voucher */

            if((error.field).indexOf('voucher') === 0){

                if(error.field == 'voucher1') $('tr.account-row.debit.main').addClass('has-error');

                if(error.field == 'voucher2') $('tr.account-row.withholding').addClass('has-error');

                if(error.field == 'voucher3') $('tr.account-row.debit.discount').addClass('has-error');

                if(error.field == 'voucher4') $('tr.account-row.credit.main').addClass('has-error');

                if(error.field == 'voucher5') $('tr.account-row.credit.credit2').addClass('has-error');

                if(error.field == 'voucher6') $('tr.account-row.credit.tax').addClass('has-error');

                if(error.field == 'vouchertotal') $('.table-account-details tr.total-row').addClass('has-error');

            }

            else

                $(selector).closest('.input-group').addClass('has-error');

            

            if(error.message){

                error_msgs.push( error.message );

            }

        }

        

        $('.content-header').prepend( create_alert2('warning', false, error_msgs) );

    }

  

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

        submit_for_validation(validation_url, function(response){
            
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



    /* end --- Form validation */

    // Put default value to 0
    $('[name=order_qty]').val(0);
    $('[name=amount_due]').val('0.00');

    $(".amount_pay").bind("change paste keyup", function() {
        calculateBalance();
    });
    
    // // This will create the auto generated entry no
    // var n = ($('[name=entry_no]').val() || 0);
    // var num = Number(n) + Number(1);

    // var entry_no = String('0000000000' + num).slice(-10);
    // window.onload = function () {
    //     document.getElementById("entry_no").value = entry_no; // HERE ;)
    // }
</script>