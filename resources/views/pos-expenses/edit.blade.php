@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection

@section('header_style_postload')
<link rel="stylesheet" href="{{ url('/') }}/assets/plugins/iCheck/square/blue.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		@include('_includes.message')
		
		<h1>
			Edit POS Expenses ({{ $vendor_expenses->id }})
		</h1>
	
	</section>

	<!-- Main content -->
	<section class="content">
	
    {!! Form::model($vendor_expenses,[
		'method' => 'PATCH',
		'url' => ['/pos-expenses', $vendor_expenses->id],
		'class' => ''
	]) !!}

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
                <input type="hidden" value="{{ $vendor_info->vendor_id}}" name="vendor_id">
                {!! Form::hidden('vendor_id', null) !!}
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
                    <tr class="individual_name"><th>Full Name</th><td class="name"><span>{{ $vendor_info->first_name." ".$vendor_info->last_name." ".$vendor_info->company_name }}</span></td></tr>
                        <tr class="company_name hidden"><th>Company Name</th><td class="name"></td></tr>
                        @if($vendor_info->individual == 0)
                        <tr><th>Individual?</th><td class="individual"><span class="badge bg-yellow">No</span></td></tr>
                        @else
                        <tr><th>Individual?</th><td class="individual"><span class="badge bg-green">Yes</span></td></tr>
                        @endif
                        <tr><th>City</th><td class="city">{{ $vendor_info->city}}</td></tr>
                        <tr><th>Country</th><td class="country">{{ $vendor_info->country}}</td></tr>
                        <tr><th>TIN</th><td class="tin">{{ $vendor_info->tin}}</td></tr>
                        <tr><th>Branch Code</th><td class="branch_code">{{ $vendor_info->branch_code}}</td></tr>
                        <tr><th>Opening Balance</th><td class="opening_balance">{{ $vendor_info->opening_balance}}</td></tr>
                        <tr><th>Phone</th><td class="phone">{{ $vendor_info->phone_number}}</td></tr>
                        <tr><th>Fax</th><td class="fax">{{ $vendor_info->fax}}</td></tr>
                        <tr><th>Email</th><td class="email">{{ $vendor_info->email}}</td></tr>
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
                {!! Form::text('date', null, array('placeholder' => 'Date','class' => 'form-control datepicker')) !!}
              </div>
              <div class="input-group input-group-sm">
                <div class="input-group-btn">
                  <button type="button" class="btn">Invoice No.</button>
                </div>
                <!-- /btn-group -->
                {!! Form::text('invoice_no', null, array('placeholder' => 'Invoice No','class' => 'form-control')) !!}
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
                {!! Form::select('period', [''=>'Select', '30'=>'30 days', '45'=>'45 days', '180'=>'180 days'], null, ['class' => 'form-control']) !!}
              </div>
              <div class="input-group input-group-sm description">
                <div class="input-group-btn">
                  <button type="button" class="btn">Desc / Memo</button>
                </div>
                <!-- /btn-group -->
                {!! Form::text('description', null, array('placeholder' => 'Description','class' => 'form-control')) !!}
              </div>
              <div class="input-group input-group-sm">
                <div class="input-group-btn">
                  <button type="button" class="btn">Amount</button>
                </div>
                <!-- /btn-group -->
                {!! Form::text('amount', null, array('placeholder' => 'Amount','class' => 'form-control')) !!}
              </div>
            </div>
            
            </div>
            <!-- /.row -->
        </div>
        <!-- ./box-body -->
    </div>

    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">
                <a href="{{ route('point-of-sale.index') }}" class="btn btn-primary" style="float: left; margin-right: 10px;">Back to POS Dashboard</a>
                {!! Form::submit('Update', ['class' => 'btn btn-primary form-control', 'style'=>'width:100px;']) !!}
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
        
        //cpv_show_hide_account_details();
    });
    $('[name=terms]').trigger('change');
    
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
    
    function submit_for_validation(callback){
        $.post('{{url("supplier-invoice/add-form-validate")}}', $('form').serialize(), function(response){
            if(typeof callback == 'function'){
                callback(response);
            }
        }, 'json');
    }
  
</script>
@endsection