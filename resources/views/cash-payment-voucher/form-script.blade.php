<script>
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
        
        /* supplier_invoices id and invoice_number */
        for( var i in data ){
            html += '<option data-amount="'+ data[ i ].coa_credit +'" data-description="'+ data[ i ].description +'" class="" value="'+ data[ i ].id +'">'+ data[ i ].invoice_number +'</option>';
        }
        
        $('select.invoice-dropdown').html( html );
        
    }
    
    function get_supplier_invoices( vendor_id ){
        
        var request = { vendor_id: vendor_id };
        $.get('{{url("vendors/get-supplier-invoices")}}', request, function(data){
            
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
        
        amount_payable = isNaN(amount_payable) ? 0 : parseFloat(amount_payable);
        current_payment = isNaN(current_payment) ? 0 : parseFloat(current_payment);
        
        var balance = (amount_payable - current_payment).toFixed(2);
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
    
    $('.vendor_name').autocomplete({
        source: '{{ url("supplier-invoice/find-vendors") }}',
        minLength: 3
    });

    /* Search for a vendor info. */
    $('.btn-search, .vendor-search_by_id').on('click', function(){
        var s = ($('.vendor_name').val()).trim();
        var request = { s: s };
        
        if( $(this).is('.vendor-search_by_id') ){
            if( $(this).data('vendor_id') > 0 ){
                request.vendor_id = $(this).data('vendor_id');
            }
            else{
                return;
            }
        }
        else if( !s ) 
            return;
        else if( $(this).is('.btn-search') 
                 && $('.vendor_name').data('ui-autocomplete') 
                 && $('.vendor_name').data('ui-autocomplete').selectedItem ){
            request.vendor_id = $('.vendor_name').data('ui-autocomplete').selectedItem.id;
        }
        
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
            
            var vendor_id = $('input[name=vendor_id]').val();
            /* only run this script on search by name */
            // if(vendor_id && !request.vendor_id){
                get_supplier_invoices( vendor_id );
            // }
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
        
        var description    = $(this).find(":selected").data('description');
        var amount_payable = $(this).find(":selected").data('amount');
        
        var this_tr = $(this).closest('tr');
        this_tr.find('td input.description').val( description );
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
        calculate_account_total();
        
        $('[name=supplier_invoice_id]').val( $(this).val() );
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
        calculate_account_total();
    });
    
    /* Account Title Dropdown on change */
    $('.table-account-details').on('change', 'select.chart-account-dropdown', function(){
        var this_tr = $(this).closest('tr');
        
        var code = $(this).find(":selected").data('code');
        this_tr.find('td.account-number input.code').val( code );
        
        /* Ref # */
        if($('[name=cpv_id]').length){
            this_tr.find('td.ref-number input').val( $('[name=invoice_number]').val() );
        }
        else{
            var invoice_number = $('select.invoice-dropdown:first').find(':selected').text();
            this_tr.find('td.ref-number input').val( invoice_number );
            $('[name=invoice_number]').val( invoice_number );
        }
        
        /* Set Debit or Credit value */
        if(this_tr.is('.debit')){
            this_tr.find('td .debit-field').val( $('input.current_payment:first').val() );
        }
        else{
            this_tr.find('td .credit-field').val( $('input.current_payment:first').val() );
        }
    });
    
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
            
            /* if the field is supplier_invoice_id */
            if(error.field == 'supplier_invoice_id'){
                $('tr.invoice-row').addClass('has-error');
            }
            /* if the field is balance */
            else if(error.field == 'balance'){
                $('.table-invoice-details tr.total-row').addClass('has-error');
            }
            /* if the field starts with voucher */
            else if((error.field).indexOf('voucher') === 0){
                if(error.field == 'voucher1') $('tr.account-row.debit').addClass('has-error');
                if(error.field == 'voucher2') $('tr.account-row.credit').addClass('has-error');
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