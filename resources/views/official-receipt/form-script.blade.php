<script>
    //auto suggest
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
    
    /* Set up invoice dropdown */
    function setup_invoice_dropdown( data ){
        
        var html = '<option value=""> Select Invoice </option>';
        
        /* billing_invoices id and invoice_number */
        for( var i in data ){
            html += '<option data-amount="'+ data[ i ].amount_due +'" value="'+ data[ i ].id +'">'+ data[ i ].invoice_number +'</option>';
        }
        
        $('select.invoice-dropdown').html( html );
        
    }
    
    function get_billing_invoices( customer_id ){
        
        var request = { customer_id: customer_id };
        $.get('{{url("customer/get-billing-invoices")}}', request, function(data){
            
                if(data){
                    setup_invoice_dropdown( data );
                }
            
        });
        
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
    
    /* calculate Invoice Balance */
    function calculate_invoice_balance(){
        var amount_payable = $('.table-invoice-details input.amount_payable:first').val() || 0;
        var current_payment = $('input[name=amount]').val() || 0;
        var sales_discount = $('input[name=sales_discount]').val() || 0;
        
        amount_payable = isNaN(amount_payable) ? 0 : +(parseFloat(amount_payable).toFixed(2));
        current_payment = isNaN(current_payment) ? 0 : +(parseFloat(current_payment).toFixed(2));
        sales_discount = isNaN(sales_discount) ? 0 : +(parseFloat(sales_discount).toFixed(2));
        
        var balance = (amount_payable - sales_discount - current_payment).toFixed(2);
        $('[name=balance]').val( balance );
        $('.table-invoice-details .total-row .balance').text( balance );
    }
    
    function calculate_account_total(){
        var debit_total = 0;
        $('.table-account-details td.debit .debit-field').each(function(){
            var deb = $(this).val() || 0;
            deb = isNaN(deb) ? 0 : parseFloat(deb);
            debit_total += deb;
        });
        
        var credit_total = 0;
        $('.table-account-details td.credit .credit-field').each(function(){
            var cred = $(this).val() || 0;
            cred = isNaN(cred) ? 0 : parseFloat(cred);
            credit_total += cred;
        });
        
        $('tr.total-row td.debit').text( debit_total.toFixed(2) );
        $('tr.total-row td.credit').text( credit_total.toFixed(2) );
    }
    
    /* Search for a customer info. */
    $('.btn-search, .customer-search_by_id').on('click', function(){
        var cust_name = ($('.customer_name').val()).trim();
        var request = { cust_name: cust_name };
        
        if( $(this).is('.customer-search_by_id') ){
            if( $(this).data('customer_id') > 0 ){
                request.customer_id = $(this).data('customer_id');
            }
            else{
                return;
            }
        }
        else if( !cust_name ) {
            return;
        }
        else if( $(this).is('.btn-search') 
                 && $('.customer_name').data('ui-autocomplete') 
                 && $('.customer_name').data('ui-autocomplete').selectedItem ){
            request.customer_id = $('.customer_name').data('ui-autocomplete').selectedItem.id;
        }
        
        $('.box .overlay').removeClass('hidden');
        
        $.get('{{url("cash-invoice/find-customer-only")}}', request, function(data){
            if(data && Object.keys(data).length){
                for(var prop in data){
                    if(prop == 'individual' && data[prop] == '1'){
                        $('.table-customer-info td.'+prop).html( '<span class="badge bg-green">Yes</span>' );
                    }
                    else if(prop == 'individual' && data[prop] == '0'){
                        $('.table-customer-info td.'+prop).html( '<span class="badge bg-yellow">No</span>' );
                    }
                    else if(prop == 'id'){
                        $('input[name=customer_id]').val( data[prop] );
                    }
                    else{
                        $('.table-customer-info td.'+prop).html( data[prop] );
                    }
                    
                    if(prop == 'full_name') $('.customer_name').val( data[prop] );
                }
            }
            else{
                /* Cleanup Customer Info */
                $('input[name=customer_id]').val('');
                $('.table-customer-info td').html('');
            }
        }, 'json').always(function(){
            $('.box .overlay').addClass('hidden');
            
            var customer_id = $('input[name=customer_id]').val();
            /* only run this script on search by name */
            if($('form').is('.create')){
                get_billing_invoices( customer_id );
            }
        });
    });
    
    /* Payment Method Dropdown */
    $('[name=payment_method]').change(function(){
        $('.paymethod').hide();
        if($(this).val() == 'cash'){
            $('.paymethod-cash').css("display", "table");
        }
        else if($(this).val() == 'check'){
            $('.paymethod-check').css("display", "table");
        }
    });
    $('[name=payment_method]').trigger('change');
    
    /* Invoice Dropdown on change */
    $('.table-invoice-details').on('change', 'select.invoice-dropdown', function(){
        
        var amount_payable = $(this).find(":selected").data('amount');
        
        var this_tr = $(this).closest('tr');
        this_tr.find('td input.amount_payable').val( amount_payable );
        
        var amount = $('input[name=amount]').val() || 0;
        amount = isNaN(amount) ? '0.00' : parseFloat(amount).toFixed(2);
        this_tr.find('td input.current_payment').val( amount );
        
        /* set empty amount if selected dropdown has no value */
        if(!$(this).val()){
            this_tr.find('td input.current_payment').val( '' ); 
        }
        
        calculate_invoice_balance();
        $('select.chart-account-dropdown').trigger('change');
        $('select.tax-dropdown').trigger('change');
        calculate_account_total();
        
        $('[name=billing_invoice_id]').val( $(this).val() );
    });
    
    /* update the current payment everytime Amount field is changed. */
    $('[name=amount]').change(function(){
        var amount = $(this).val() || 0;
        amount = isNaN(amount) ? '0.00' : parseFloat(amount).toFixed(2);
        
        $('select.invoice-dropdown').each(function(){
            /* set the amount only if this dropdown has a value */
            if($(this).val()){ 
                $(this).closest('tr').find('input.current_payment').val( amount );
            }
        });
        
        calculate_invoice_balance();
        $('select.chart-account-dropdown').trigger('change');
        $('select.tax-dropdown').trigger('change');
        calculate_account_total();
    });
    
    /* update everytime Sales discount field is changed. */
    $('[name=sales_discount]').change(function(){
        calculate_invoice_balance();
        $('select.chart-account-dropdown').trigger('change');
        $('select.tax-dropdown').trigger('change');
        calculate_account_total();
    });
    
    /* Account Title Dropdown on change */
    $('.table-account-details').on('change', 'select.chart-account-dropdown', function(){
        var this_tr = $(this).closest('tr');
        
        var code = $(this).find(":selected").data('code');
        this_tr.find('td.account-number input.code').val( code );
    });
    
    /* Account Title Dropdown on change  -- for Tax dropdown */
    $('.table-account-details').on('change', 'select.tax-dropdown', function(){
        var this_tr = $(this).closest('tr');
        
        var data = $(this).find(":selected").data();
        
        if(data && data.code && data.tax_id){
            this_tr.find('td.account-number input.code').val( data.code );
            this_tr.find('td.tax .tax_id').val( data.tax_id );
        }
        else {
            this_tr.find('td.account-number input.code').val( '' );
            this_tr.find('td.tax .tax_id').val( '' );
        }
        
        do_account_details_calc();
    });
    
    function do_account_details_calc(){
        var amount = $('input.current_payment:first').val() || 0;
        amount = isNaN(amount) ? 0 : parseFloat(amount).toFixed(2)*1; // *1 purpose is just to convert the type from string to number.
        
        var sales_discount = $('input[name=sales_discount]').val() || 0;
        sales_discount = isNaN(sales_discount) ? 0 : parseFloat(sales_discount).toFixed(2)*1;
        
        // amount = amount - sales_discount;
        
        var debit_tax_rate = $('.table-account-details tr.tax-row.debit .tax-dropdown').find(":selected").data('rate') || 0;
        
        /* Calc for Net of VAT for Debit */
        var debit_amount = amount / (1 + debit_tax_rate/100);
        var debit_tax    = debit_amount * (debit_tax_rate/100);
        
        /* Fill-in rates */
        $('.tax-row.debit .rate').val( debit_tax_rate );
        
        /* Show Ref # */
        var invoice_number = $('select.invoice-dropdown:first').find(':selected').text();
        if(!invoice_number) invoice_number = $('input[name=invoice_number]').val();
        $('td.ref-number input, input[name=invoice_number]').val( invoice_number );
        
        /* Show calculated amounts to table. */
        $('tr.account-row.debit td.debit input').val( debit_amount.toFixed(2) );
        $('tr.tax-row.debit td.debit input').val( debit_tax.toFixed(2) );
        $('tr.discount-row.debit td.debit input').val( sales_discount.toFixed(2) );
        $('tr.account-row.credit td.credit input').val( (amount + sales_discount).toFixed(2) );
    }
    
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
            
            /* if the field is billing_invoice_id */
            if(error.field == 'billing_invoice_id'){
                $('tr.invoice-row').addClass('has-error');
            }
            /* if the field is balance */
            else if(error.field == 'balance'){
                $('.table-invoice-details tr.total-row').addClass('has-error');
            }
            /* if the field starts with voucher */
            else if((error.field).indexOf('voucher') === 0){
                if(error.field == 'voucher1') $('tr.account-row.debit').addClass('has-error');
                if(error.field == 'voucher2') $('tr.tax-row.debit').addClass('has-error');
                if(error.field == 'voucher3') $('tr.discount-row.debit').addClass('has-error');
                if(error.field == 'voucher4') $('tr.account-row.credit').addClass('has-error');
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
    
</script>