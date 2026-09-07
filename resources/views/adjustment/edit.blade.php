@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif <?php echo implode(' ', Request::segments()); ?>
@endsection


@section('header_style_preload')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ url('/') }}/assets/plugins/iCheck/square/blue.css">
@endsection


@section('header_style_postload')
<style>
body.adjusting.edit .table-account-details th.actions { width: 24px; }
body.adjusting.edit .table-account-details th.account-number { width: 18%; }
.table-account-details th.account-title { width: 40%; }
.table-account-details tr.account-row > td { vertical-align: middle; }
tr.account-row span.account_name { display: inline; }
</style>
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Edit Adjusting Entry ({{ $adjustment->id }})
        </h1>
    
    </section>

    <!-- Main content -->
    <section class="content">
    
    {!! Form::model($adjustment, [
        'method' => 'PATCH',
        'url' => ['/adjusting', $adjustment->id],
        'class' => 'main_form'
    ]) !!}

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"></h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">

            <div class="col-sm-6">
                <div class="input-group input-group-sm">
                    <div class="input-group-btn">{!! Form::select('entity_type', ['customer' => 'Customer', 'vendor' => 'Vendor'], null, ['class' => 'btn btn-default entity_type']) !!}</div>

                    <input type="text" size="250" class="form-control entity_name" name="entity_name" value="" placeholder="Name" autocomplete="off">
                    {!! Form::hidden('entity_id', null, ['class' => 'entity_id']) !!}

                    <span class="input-group-btn"><button type="button" class="btn btn-info btn-flat btn-search">Go!</button></span>
                </div>
                <button type="button" class="btn btn-info btn-flat hidden-search hidden">Hidden Search by Entity ID</button>

                <div class="box box-info box-solid entity-info-box">
                    <div class="box-header with-border">
                        <h3 class="box-title text-sm"><span class="entity-name">Customer</span> Info</h3>
                        <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button></div>
                    </div>

                    <div class="box-body">
                        <div class="box-body table-responsive no-padding">
                            <input type="hidden" name="full_name" class="full_name" value="">
                            <table class="table table-hover table-entity-info"><tbody>
                                <tr class="individual_name"><th>Name</th><td class="name full_name"></td></tr>
                                <tr class="company_name hidden"><th>Company Name</th><td class="name company_name"></td></tr>
                                <tr><th>Individual?</th><td class="individual"></td></tr>
                                <tr><th>City</th><td class="city"></td></tr>
                                <tr><th>Country</th><td class="country"></td></tr>
                                <tr><th>TIN</th><td class="tin"></td></tr>
                                <tr><th>Branch Code</th><td class="branch_code"></td></tr>
                                <tr><th>Phone</th><td class="phone_no"></td></tr>
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
                    <div class="input-group-btn"><button type="button" class="btn">AE No.</button></div>
                    {!! Form::text('adj_number', null, ['class' => 'form-control adj-number', 'readonly']) !!}
                </div>
                <div class="input-group input-group-sm" style="margin-bottom: 10px;">
                    <div class="input-group-btn"><button type="button" class="btn">Date</button></div>
                    {!! Form::text('date', null, ['class' => 'form-control datepicker']) !!}
                </div>
                <div class="input-group input-group-sm">
                    <div class="input-group-btn"><button type="button" class="btn">Particulars</button></div>
                    {!! Form::text('description', null, ['class' => 'form-control']) !!}
                </div>
                {!! Form::hidden('amount') !!}
            </div>

            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Account Details</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover table-account-details">
                <tbody><tr class="head">
                  <th class="text-right actions"></th>
                  <th class="account-number">Account #</th>
                  <th class="account-title">Account Title</th>
                  <th>Ref #</th>
                  <th class="debit">Debit</th>
                  <th class="credit">Credit</th>
                </tr>
                
            <?php $totals = (object) ['debit' => 0, 'credit' => 0,];?>
            @foreach($vouchers as $v)
                <?php 
                $entry_type = (floatval($v->debit) > 0) ? 'debit' : 'credit';
                $account_row_class = 'main';
                $parent_name = "vouchers[{$v->order}]";

                if($v->tax_id) $account_row_class = 'tax';
                elseif($v->discount_id) $account_row_class = 'discount';

                $key = $v->key;
                $voucher = $v;
                
                $totals->debit += floatval($v->debit);
                $totals->credit += floatval($v->credit);
                ?>
                @include('adjustment.account-row-template')
            @endforeach
            <?php 
            $entry_type = $account_row_class = $parent_name = $key = $voucher = null; 
            $debit_total = number_format($totals->debit, 2, '.', '');
            $credit_total = number_format($totals->credit, 2, '.', '');
            ?>

                <tr class="total-row">
                  <td colspan="2"> <button type="button" class="btn btn-sm btn-default btn-add-row">Add</button> </td>
                  <td colspan="2" class="text-right"> {!! Form::hidden('debit_total', $debit_total) !!}{!! Form::hidden('credit_total', $credit_total) !!} Total: </td>
                  <td class="debit"> {{ $debit_total }} </td>
                  <td class="credit"> {{ $credit_total }} </td>
                </tr>
              </tbody></table>
            </div>
            <!-- ./box-body -->
            <div class="box-footer clearfix">
              <div class="col-sm-6 pull-right">
              </div>
            </div>
    </div>

    <div class="row">
      <div class="col-sm-3">
        <div class="form-group">
            {!! Form::submit('Update', ['class' => 'btn btn-primary form-control']) !!}
        </div>
      </div>
    </div>
    {!! Form::close() !!}

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->


@include('adjustment.account-details-template')
@include('adjustment.account-row-options-modal')

@endsection


@section('footer_script_preload')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
<script src="{{ url('/') }}/assets/plugins/iCheck/icheck.min.js"></script>
@endsection


@section('footer_script')
@include('adjustment.form-script')
<script>
    var validation_url = '{{url("adjusting/edit-form-validate")}}';

    /* Search Customer/Vendor by ID */
    $('.hidden-search').trigger('click');
</script>
@endsection