@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection

@section('header_style_preload')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-6 margin-tb">
            <div class="pull-left">
            <h1>Cash Advance Details for <strong>{{ $emp_data->employee_name }}</strong></h1>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-md-12  align-right">
                    {!! Form::open(array('url' => 'cash-advance/details/date-result','method'=>'GET')) !!}
                    <div class="col-md-12 col-sm-12" style="padding-right: 0px;">
                        <input type="hidden" name="e_id" value="{{ $emp_data->id }}">
                        <input type="text" name="date_from" class="form-control datepicker ca-date_from" placeholder="Date From:" autocomplete="off" required>
                        <input type="text" name="date_to" class="form-control datepicker ca-date_to" placeholder="Date To:" autocomplete="off" required>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                    <div class="col-md-2 col-sm-12">
                        
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-product">
            <p>{{ $message }}</p>
        </div>
    @endif

    <section class="content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <tr>
                    <th>No</th>
                    <th>Date</th>
                    <th>Cash Advance Amount</th>
                    <th>Cash Amount Deducted</th>
                    <th>CA Running Total</th>
                    <th>CA Description</th>
                    <th>Action</th>
                </tr>
                {{-- */
                    $ca_current_total = 0;
                    $ca_amount_total = 0;
                    $ca_deduction_total = 0;
                /* --}}
                @foreach ($ca_data as $cd)
                {{-- */
                    $ca_amount_total = $ca_amount_total + $cd->ca_amount;
                    $ca_deduction_total = $ca_deduction_total + $cd->ca_deduction;
                    $ca_current_total = $ca_current_total + ($cd->ca_amount - $cd->ca_deduction);
                /* --}}
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $cd->date }}</td>
                    <td align="right">{{ number_format(floatval($cd->ca_amount), 2) }}</td>
                    <td align="right">{{ number_format(floatval($cd->ca_deduction), 2) }}</td>
                    <td align="right">{{ number_format(floatval($ca_current_total), 2) }}</td>
                    <td align="left">{{ $cd->ca_description }}</td>
                    <td>
                        @if($cd->keys == 'ca')
                            <a class="btn btn-primary btn-xs" href="{{ route('cash-advance.edit', $cd->id) }}" title="Cash Advance Details"><i class="fa fa fa-pencil"></i></a>
                            {!! Form::open(['method' => 'DELETE','route' => ['cash-advance.destroy', $cd->id],'style'=>'display:inline']) !!}
                            {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Cash Advance?',
                                    'onclick'=>'return confirm("Confirm delete?")'
                                ));!!}
                            {!! Form::close() !!}
                        @endif
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="2" align="right"><strong>Total:</strong></td>
                    <td align="right"><strong>{{ number_format(floatval($ca_amount_total), 2) }}</strong></td>
                    <td align="right"><strong>{{ number_format(floatval($ca_deduction_total), 2) }}</strong></td>
                    <td align="right"><strong>{{ number_format(floatval($ca_amount_total - $ca_deduction_total), 2) }}</strong></td>
                </tr>
            </table>
            <div class="row" style="margin-right: 0px; margin-left: 0px;">
                <div class="col-md-6" style="padding-left: 0;">
                    <div class="pagination" style="margin:0;">
                        {{-- {!! $products->appends(['inventory_status' => $inventory_status, 'search_products' => $search_products])->render(); !!} --}}
                        {{-- {!! $ca_data->render(); !!} --}}
                        <a href="{{ url('cash-advance') }}" class="btn btn-primary">Back to Cash Advance</a>
                    </div>
                </div>
                <div class="col-md-6" style="text-align: right;">
                    {{-- <a href="{{ url('cash-advance') }}" class="btn btn-primary">Back to Cash Advance</a> --}}
                </div>
            </div>
        </div>
    </section>
</div>
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
    }).datepicker();

    /* Trigger change to fill in Account # */
    // $('select.chart-account-dropdown, select.tax-dropdown').trigger('change');

    /*var validation_url = '{{url("water-refilling-monitoring/add-form-validate")}}';

    $(".withholding_tax").prop('selectedIndex', 1);*/
</script>
@endsection

<style type="text/css">
    ul.pagination{
        margin: 0px;
    }
    .table-responsive table{
        font-size: 14px;
    }
    .ca-date_from,
    .ca-date_to{
        width: 100%!important;
        max-width: 150px!important;
        float: left;
        margin-right: 10px;
    }
</style>