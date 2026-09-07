<script>
    function cleanup_entity_info(){
        $('.table-entity-info td').text( '' );
        $('[name=entity_id], [name=entity_name]').val('');
    }

    $('select[name=entity_type]').on('change', function(){
        var etype_name = $(this).find('option:selected').html();
        $('.entity-name').text( etype_name );
        cleanup_entity_info();
    });

    $('input.entity_name').autocomplete({
        source: function( request, response ){
            var etype = $('select[name=entity_type]').val();
            var url = (etype == 'customer') ? 
                '{{ url("cash-invoice/find-customer") }}' : 
                '{{ url("supplier-invoice/find-vendors") }}';
            $.get(url, request, function( data ){
                response( data );
            });
        },
        minLength: 3
    });

    $('input.adj-number').on('change', function(){
        fill_reference_number();
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
    $('input[name=taxordiscount]').on('ifUnchecked', function (e) {
        // hide all dropdowns;
        var formGroup = $(this).closest('.form-group');
        formGroup.find('select').addClass('hidden');
    });
    $('input[name=taxordiscount]').on('ifChecked', function (e) {
        // uncheck other checkbox if one is checked
        var value = $(this).attr('value');
        $('input[name=taxordiscount]:not([value='+value+'])').iCheck('uncheck');
        
        // show one dropdown; the rest, hide it
        var formGroup = $(this).closest('.form-group');
        formGroup.find('select').addClass('hidden');
        formGroup.find('select.'+value).removeClass('hidden');
    });
    
    // Get customers on go button event
    $('.btn-search, .hidden-search').on('click', function(){
        var entity_id   = 0;
        var entity_name = ($('.entity_name').val()).trim();

        var auto_c = $('.entity_name').data('ui-autocomplete');
        if( $(this).is('.hidden-search') ){ // this should be called only when loading the edit page
            entity_id = $('[name=entity_id]').val();
            if( !entity_id ) return;
        }
        else if( auto_c && auto_c.selectedItem ){ // this should be prioritized when the user selects an option from autocomplete dropdown
            entity_id = auto_c.selectedItem.id;
            if( !entity_id ) return;
        }
        else if( !entity_name ){ // lastly, we have no more option but to search by name and will return only the first row in search results
            return;
        }

        var request = {};
        var etype = $('select[name=entity_type]').val();
        if(etype == 'customer'){
            if(entity_id) request.customer_id = entity_id;
            else request.cust_name = entity_name;
        }
        else{
            if(entity_id) request.vendor_id = entity_id;
            else request.s = entity_name;
        }

        $('.box .overlay').removeClass('hidden');
        var url = (etype == 'customer') ? 
                '{{ url("cash-invoice/find-customer-only") }}' : 
                '{{ url("supplier-invoice/find-vendor") }}';

        $.get(url, request, function(data){
            if(data && Object.keys(data).length){
                for(var prop in data){
                    if(prop == 'individual' && data[prop] == '1'){
                        $('.table-entity-info td.'+prop).html( '<span class="badge bg-green">Yes</span>' );
                        $('.table-entity-info tr.individual_name').removeClass( 'hidden' );
                        $('.table-entity-info tr.company_name').addClass( 'hidden' );
                    }
                    else if(prop == 'individual' && data[prop] == '0'){
                        $('.table-entity-info td.'+prop).html( '<span class="badge bg-yellow">No</span>' );
                        $('.table-entity-info tr.company_name').removeClass( 'hidden' );
                        $('.table-entity-info tr.individual_name').addClass( 'hidden' );
                    }
                    else if(prop == 'id'){
                        $('input[name=entity_id]').val( data[prop] );
                    }
                    else{
                        $('.table-entity-info td.'+prop).html( data[prop] );
                    }

                    // do only on edit form
                    if(entity_id > 0){
                        if($.inArray(prop, ['name', 'full_name']) !== -1){
                            if((data[prop]).length > 0)
                                $('[name=entity_name]').val( data[prop] );
                        }
                    }
                    // console.log(prop, data[prop]);
                }
            }
            else{
                $('.table-entity-info td').html('');
                $('input[name=entity_id]').val('');
            }
        }, 'json').always(function(){
            $('.box .overlay').addClass('hidden');
        });
    });


    function add_account_row(){
        remove_on_edits();

        var html = $('.account-row-template tbody').html();
        var num  = $('.table-account-details tr.account-row').length;
        html = html.replace(new RegExp('{num}', 'g'), num);

        var row = $( html );
        row.addClass('on-edit incomplete');

        $('.table-account-details tr.total-row').before( row );

        fill_reference_number();

        return row;
    }
    function remove_on_edits(){
        $('.table-account-details tr.account-row').removeClass('on-edit incomplete');
    }
    function fill_reference_number(){
        $('.table-account-details tr.account-row input.ref_number').val( $('input.adj-number').val() );
    }
    function rename_account_fields(){
        var cntr = 0;
        $('.table-account-details tr.account-row').each(function(){
            $('input', this).each(function(){
                var field_name = $(this).attr('name');
                if( field_name.indexOf("vouchers[") == 0 ){
                    var new_field_name = field_name.replaceAt(9, ''+cntr);
                    $(this).attr('name', new_field_name);
                }
            });
            
            // Change the classnames
            var classes = $.grep(this.className.split(" "), function(v, i){
                return v.indexOf('v_') === 0;
            }).join();

            var key = 'v_' + cntr;
            $(this).removeClass(classes).addClass(key);
            $(this).attr('data-key', key).data('key', key);
            
            // Change key value
            $(this).find('input.key').val('v_' + cntr);
            
            cntr++;
        });
    }
    function generate_account_key( classes, entry_type ){
        // identify if a row is either tax, discount OR main
        if($.inArray('tax', classes) !== -1) key = 'tax_';
        else if($.inArray('discount', classes) !== -1) key = 'discount_';
        else if($.inArray('main', classes) !== -1) key = 'coa_';

        key = key+entry_type;

        // check how many keys exist the same as this key
        var count = 0;
        $('.table-account-details tr.account-row:not(.on-edit) input.key').each(function(){
            // if the row key begins with the key we just generated
            if( !!($(this).val()||'').match('^'+key) ) 
                count++;
        });

        // if we found 1 or more then add a suffix for increment
        if(count > 0) key = key + (count+1);

        return key;
    }
    /* Account total for both Debit and Credit */
    function account_details_total(){
        // account details debit and credit total
        var debit_total = 0;
        $('.table-account-details tr.account-row').each(function(){
            debit_total += +($(this).find('input.debit_field').val()*1 || 0).toFixed(2);
        });
        var credit_total = 0;
        $('.table-account-details tr.account-row').each(function(){
            credit_total += +($(this).find('input.credit_field').val()*1 || 0).toFixed(2);
        });
        $('.table-account-details .total-row td.debit').html( debit_total.toFixed(2) );
        $('.table-account-details .total-row td.credit').html( credit_total.toFixed(2) );
        $('[name=debit_total]').val( debit_total.toFixed(2) );
        $('[name=credit_total]').val( credit_total.toFixed(2) );

        $('[name=amount]').val( debit_total.toFixed(2) );
    }

    $('.table-account-details').on('click', '.btn-add-row', function(){
        // Add an account row to Account Details table.
        var current_row = add_account_row();

        // Show modal
        $("#modal-account-row-options").modal()
            .data('current_row', current_row); // store the current accout-row
    });

    /* Remove account row */
    $('.table-account-details').on('click', '.fa-close', function(){
        $(this).closest('tr').remove();
        rename_account_fields();

        account_details_total();
    });

    /* Show account settings in modal */
    $('.table-account-details').on('click', '.fa-cog', function(){
        remove_on_edits();
        var current_row = $(this).closest('tr');

        // add class to currently editing tr options
        current_row.addClass('on-edit');

        $('#modal-account-row-options').modal()
            .data('current_row', current_row); // store the current accout-row
    });

    /* Update readonly field account_number value */
    $('#modal-account-row-options').on('change', 'select#coa_id', function(){
        var code = $(':selected', this).data('code');
        $('#modal-account-row-options').find('[name=account_number]').val( code );
    })
    /* Copy from account row data to modal form data */
    .on('shown.bs.modal', function(){
        var current_row = $(this).data('current_row'),
            modal = $(this);

        // if not a new row
        if(!current_row.is('.incomplete')){
            // chart_account_id, account_number
            $('select#coa_id', modal).val(current_row.find('input.chart_account_id').val()).trigger('change');

            // Tax or Discount
            var tax_id = current_row.find('input.tax_id').val();
            var discount_id = current_row.find('input.discount_id').val();
            if(parseInt(tax_id)){
                $('select#tax_id', modal).val( tax_id );
                $('input[value=tax]', modal).iCheck('check');
                console.log('tax');
            }
            else if(parseInt(discount_id)){
                $('select#discount_id', modal).val( discount_id );
                $('input[value=discount]', modal).iCheck('check');
                console.log('dis');
            }
            else{
                $('input[name=taxordiscount]', modal).iCheck('uncheck');
            }

            // Debit or Credit
            var entry_type = (current_row.is('.debit')) ? 'debit' : 'credit';
            $('input[name=type][value='+entry_type+']', modal).iCheck('check');
        }
    })
    /* Copy from modal form data to current row. Finish setting up our account-row. */ 
    .on('click', '.btn-update-account-row', function(){
        var modal = $('#modal-account-row-options'),
            current_row = modal.data('current_row'),
            classes = ['account-row'];

        // update account number OR code
        current_row.find('input.code').val( $('[name=account_number]', modal).val() );

        // update chart_account_id
        current_row.find('input.chart_account_id').val( $('select[name=coa_id]', modal).val() );
        current_row.find('.account_name').text( $('select[name=coa_id] :selected', modal).text() );

        // update debit/credit fields
        var entry_type = $('[name=type]:checked', modal).val();
        if(entry_type == 'debit'){
            classes.push( 'debit' );
            current_row.find('td.debit input.debit_field').attr('type', 'text');
            current_row.find('td.credit input.credit_field').attr('type', 'hidden').val('0.00');
            current_row.find('input.code').removeClass('credit').addClass('debit');
            current_row.find('input.ref_number').removeClass('credit').addClass('debit');
        }
        else{
            classes.push( 'credit' );
            current_row.find('td.credit input.credit_field').attr('type', 'text');
            current_row.find('td.debit input.debit_field').attr('type', 'hidden').val('0.00');
            current_row.find('input.code').removeClass('debit').addClass('credit');
            current_row.find('input.ref_number').removeClass('debit').addClass('credit');
        }

        // set discount_id and tax_id to zero
        current_row.find('input.tax_id, input.discount_id').val(0);
        // reset rate to zero
        current_row.find('input.rate').val(0);
        
        // if selected set discount_id OR tax_id
        if($('[name=taxordiscount]:checked', modal).val() == 'tax'){
            classes.push( 'tax' );
            current_row.find('input.tax_id').val( $('[name=tax_id]', modal).val() );
            current_row.removeClass('main discount').addClass('tax');
            current_row.find('input.rate').val( $('[name=tax_id] :selected', modal).data('rate') );
        }
        else if($('[name=taxordiscount]:checked', modal).val() == 'discount'){
            classes.push( 'discount' );
            current_row.find('input.discount_id').val( $('[name=discount_id]', modal).val() );
            current_row.find('input.rate').val( $('[name=discount_id] :selected', modal).data('rate') );
        }
        else 
            classes.push( 'main' );

        classes.push( current_row.data('key') );
        current_row[0].className = classes.join(' '); // set current row with new classes

        account_details_total();
    })
    /* Set code here when the modal form is done closing */
    .on('hidden.bs.modal', function(){
        var current_row = $(this).data('current_row');

        // remove the current row if it is not completely set with options
        if(current_row.is('.incomplete'))
            current_row.find('.fa-close').trigger('click');

        remove_on_edits(); // removes classes

        $(this).data('current_row', null);
    })
    /* Inside modal form, set a default value for account number */
    .find('[name=account_number]').val(
        $('#modal-account-row-options option:first').data('code')
    );

    /* Everytime debit or credit changes its value */
    $('.table-account-details').on('change', 'input.debit_field, input.credit_field', function(){
        // recalculate total
        account_details_total();
    });


    /* start --- Form validation */
    function submit_for_validation(url, callback){
        $.post(url, $('form.main_form').serialize(), function(response){
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
            if((error.field).indexOf('v_') === 0){
                $('tr.account-row.'+error.field).addClass('has-error');
                if(error.field == 'v_total') $('.table-account-details tr.total-row').addClass('has-error');
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
    $('form.main_form').on('submit', function(e){
        // only allow the form submission if the current focus is in the submit button. 
        // This will ensure that the user is really attempting to submit the form.
        if($('input[type=submit]').is(':focus') == false){
            return false;
        }
        
        // to prevent from revalidating after the form is validated already.
        if(validated) return;
        
        // remove class has-error
        $('form.main_form').find('.has-error').removeClass('has-error');
        
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
                    $('form.main_form').submit();
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