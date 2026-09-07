<script>

    //auto suggest
    $('#prod_ajax').autocomplete({
        source: '{{ url("cash-invoice/find-product-autosuggest") }}',
        minLength: 2,
    });

    $('#cust_ajax').autocomplete({
        source: '{{ url("cash-invoice/find-customer") }}',
        minLength: 3,
    });

    $('.datepicker').datepicker({
        autoclose: true,
        todayBtn: "linked",
        todayHighlight: true,
        format: 'yyyy-mm-dd'
    });

    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
    
    // Get customers on go button event
    $('.btn-search, .customer-search_by_id').on('click', function(){
        var cust_name = ($('.customer_name').val()).trim();
        var request = { cust_name: cust_name };
        var is_edit = false;
        
        if( $(this).is('.customer-search_by_id') ){
            is_edit = true;
            
            if( $(this).data('customer_id') > 0 ){
                request.customer_id = $(this).data('customer_id')
            }
            else{
                return;
            }
        }
        else if( !cust_name ) {
            return;
        }
        else if( $(this).is('.btn-search') 
                 && $('#cust_ajax').data('ui-autocomplete') 
                 && $('#cust_ajax').data('ui-autocomplete').selectedItem ){
            request.customer_id = $('#cust_ajax').data('ui-autocomplete').selectedItem.id;
        }
        
        $('.box .overlay').removeClass('hidden');
        
        if(!is_edit) request.inc_open = 1;
        
        $.get('{{url("cash-invoice/find-customer-only")}}', request, function(datacust){
            if(datacust && Object.keys(datacust).length){
                for(var prop in datacust){
                    $('.credit-invoice-input input.' + prop).val( datacust[prop] );
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
                }
                
                if(datacust.open_invoices){
                    setup_open_invoice_dropdown(datacust.open_invoices);
                }
            }
            else{
                /* Cleanup Customer Info */
                $('input[name=customer_id], .credit-invoice-input input').val('');
                $('.table-customer-info td').html('');
            }
        }).always(function(){
            $('.box .overlay').addClass('hidden');
        });
    });

    /* Set up open invoice dropdown */
    function setup_open_invoice_dropdown( data ){
        var html = '<option value=""> Select Invoice </option>';
        
        /* open_invoices id and oi_number */
        for( var i in data ){
            html += '<option data-amount="'+ data[ i ].amount +'" value="'+ data[ i ].id +'">'+ data[ i ].oi_number +'</option>';
        }
        
        $('select[name=open_invoice_id]').html( html );
    }
    
    $('select[name=open_invoice_id]').on('change', function(){
        // auto fill-in Open Invoice amount to advance_payment
        var advance_payment = $(this).find(":selected").data('amount') || '';
        $('[name=advance_payment]').val(advance_payment).trigger('change');
    });
    
    /* Open Invoice fields */
    $('[name=for_open_invoice]').on('ifChanged', function(event){
        $('.open-invoice-child').hide();
        var $advance_payment_tr = $('[name=advance_payment]').closest('tr');
        if($(this).is(':checked')){
            $('.open-invoice-child').css("display", "table");
            $advance_payment_tr.removeClass('hidden');
            
            $('[name=advance_payment]')
                .val( $('select[name=open_invoice_id]').find(":selected").data('amount') || '' )
                .trigger('change');
        }
        else{
            $advance_payment_tr.addClass('hidden');
            $('[name=advance_payment]').val('').trigger('change');
        }
    }).trigger('ifChanged');

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

    function do_products_total(){
        var gross_sales = 0;
        $('.table-product-info tr.prod-row').each(function(){
            var subtotal = $(this).find('input.subtotal').val() || 0;
            subtotal = isNaN(subtotal) ? 0 : parseFloat(subtotal).toFixed(2)*1;
            
            gross_sales += subtotal;
        });
        
        /* Display Product total */
        $('[name=amount]').val( gross_sales.toFixed(2) );
        $('.table-product-info td.total.amount').text( gross_sales.toFixed(2) );
    }
    
    var disable_acc_total = false;
    
    function do_calculation2(){
        console.log( 'DOING CALCULATION!!' );
        
        var gross_sales = +($('[name=amount]').val() || 0);
        var discounted  = $('select.discounted').val() == 1;
        
        var vat_data = $('[name=vat_id]').data();
        var vat_rate = +(vat_data.rate*1 || 0).toFixed(2);
        
        var withholding_data = $('select.withholding_tax option:selected').data();
        var withholding_rate = +(withholding_data.rate*1 || 0).toFixed(2);
        
        var no_of_person = 0,
            no_of_scpwd = 0,
            vatable_sales = 0,
            vat_exempt_sales = 0,
            net_sales = 0,
            vat = 0,
            discount = 0,
            withholding = 0,
            amount_due = 0;
        
        if(discounted){
            no_of_person = parseInt($('[name=no_of_person]').val()*1 || 0);
            var non_scpwd_persons = no_of_person;
            
            if(no_of_person){
                no_of_scpwd = parseInt($('[name=no_of_scpwd]').val()*1 || 0);
                non_scpwd_persons = no_of_person - no_of_scpwd;
            }
            
            if(no_of_person){
                vatable_sales = gross_sales * non_scpwd_persons / no_of_person / (1+vat_rate/100);
                vatable_sales = vatable_sales.toFixed(2)*1;
                /* round to 2 decimals only */
                vat_exempt_sales = gross_sales * no_of_scpwd / no_of_person / (1+vat_rate/100);
                vat_exempt_sales = vat_exempt_sales.toFixed(2)*1;
            }
            
            net_sales = (vatable_sales + vat_exempt_sales).toFixed(2)*1;
            
            vat = (vatable_sales * vat_rate/100).toFixed(2)*1;
            
            var discount_data = $('[name=discount_id]').data();
            var discount_rate = +(discount_data.rate*1 || 0).toFixed(2);
            discount = (vat_exempt_sales * discount_rate/100).toFixed(2)*1;
            
            withholding = (vat_exempt_sales * withholding_rate/100).toFixed(2)*1;
        }
        else{
            vatable_sales = (gross_sales / (1+vat_rate/100)).toFixed(2)*1;
            vat = (vatable_sales * vat_rate/100).toFixed(2)*1;
            withholding = (vatable_sales * withholding_rate/100).toFixed(2)*1;
            
            net_sales = vatable_sales;
        }
        
        var advance_payment = 0;
        // if($('[name=for_open_invoice]').is(':checked')){
            // advance_payment = +($('[name=advance_payment]').val()*1 || 0).toFixed(2);
        // }
        
        amount_due = net_sales + vat - discount - withholding - advance_payment;
        
        console.log( 
        'Gross Sales: ' + gross_sales + "\n" + 
        'no_of_person: ' + no_of_person + "\n" + 
        'no_of_scpwd: ' + no_of_scpwd + "\n" + 
        'vatable_sales: ' + vatable_sales + "\n" + 
        'vat_exempt_sales: ' + vat_exempt_sales + "\n" + 
        'net_sales: ' + net_sales + "\n" + 
        'vat: ' + vat + "\n" + 
        'discount: ' + discount + "\n" + 
        'withholding: ' + withholding + "\n" + 
        'amount_due: ' + amount_due + "\n"
        );
        
        /* Display Sales details */
        $('input.no_of_person').val( no_of_person );
        $('input.no_of_scpwd').val( no_of_scpwd );
        $('input.discount_amount').val( discount.toFixed(2) );
        $('input.vatable_sales').val( vatable_sales.toFixed(2) );
        $('input.vat_exempt_sales').val( vat_exempt_sales.toFixed(2) );
        $('input.net_sales').val( net_sales.toFixed(2) );
        $('input.vat_amount').val( vat.toFixed(2) );
        $('input.whtax_amount').val( withholding.toFixed(2) );
        $('input.amount_due').val( amount_due.toFixed(2) );
        
        /* We don't want to process function account_details_total() multiple times on 'change' events */
        disable_acc_total = true;
        
        /* Copy info */
        $('[name=vatable_sales], [name=vat_exempt_sales], [name=discount_amount], [name=vat_amount], select.withholding_tax, [name=amount_due]').trigger('change');
        
        $('select.chart-account-dropdown').trigger('change');
        
        disable_acc_total = false;
        account_details_total();
    }

    /* Account total for both Debit and Credit. Fill-out ref_number */
    function account_details_total(){
        if( disable_acc_total )
            return false;
        
        // all ref number fields
        $('.table-account-details input.ref_number').val( $('[name=invoice_number]').val() );
        
        // account details debit and credit total
        var debit_total = 0;
        $('tr.account-row').each(function(){
            debit_total += +($(this).find('input.debit_field').val()*1 || 0).toFixed(2);
        });
        var credit_total = 0;
        $('tr.account-row').each(function(){
            credit_total += +($(this).find('input.credit_field').val()*1 || 0).toFixed(2);
        });
        $('.table-account-details .total-row td.debit').html( debit_total.toFixed(2) );
        $('.table-account-details .total-row td.credit').html( credit_total.toFixed(2) );
        $('[name=debit_total]').val( debit_total.toFixed(2) );
        $('[name=credit_total]').val( credit_total.toFixed(2) );
    }


    /* Search and product and add a product row. */
    $('.btn-enter').on('click', function(){
        var s = ($('.search_prod').val()).trim();

        if( !s ) return;
        
        var qty = $('[name=qty]').val() * 1;
        if( !qty ){
            alert("Please Enter a valid quantity");
            return;
        }

        var request = { s: s };
        
        var search_ui = $('.search_prod').data('ui-autocomplete');
        if( search_ui && search_ui.selectedItem ){
            request.prod_id = search_ui.selectedItem.id;
        }
        
        $.get('{{url("cash-invoice/find-product")}}', request, function(data){

            if(data && Object.keys(data).length){
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
                
                /* Calculation */
                do_products_total();
            }
        }, 'json');
    });
    
    /* Remove product row */
    $('#tbl-items').on('click', '.fa-close', function(){
        $(this).closest('tr').remove();
        rename_fields();
        do_products_total();
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
            // console.log(response); return;
            if(typeof callback == 'function'){
                callback(response);
            }
        }, 'json');
        // });
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
</script>