@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('header_style_preload')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Point of sales report</h1>
            </div>
        </div>
        <div style="clear: both; margin-bottom: 20px;"></div>
        <div class="col-lg-4 payroll-details">
            <h4>View POS by Customer's Name</h4>
            {!! Form::open(array('url' => 'point-of-sale/reports','method'=>'GET')) !!}
            <input type="text" name="search_cust_name" class="search-emp-name form-control" placeholder="Search Customer Name"/>
            <button type="submit" class="btn btn-primary">Search</button>
            {!! Form::close() !!}
        </div>
        <div class="col-lg-4 payroll-details">
            <h4>View POS by Date Range</h4>
            {!! Form::open(array('url' => 'point-of-sale/reports','method'=>'GET')) !!}
            <input type="text" name="date_from" class="date-search form-control datepicker" placeholder="Date From:" autocomplete="off"/>
            <input type="text" name="date_to" class="date-search form-control datepicker" placeholder="Date To:" autocomplete="off"/>
            <button type="submit" class="btn btn-primary">Search</button>
            {!! Form::close() !!}
        </div>
        <div class="col-lg-4 payroll-details">
                <h4>View POS by All/Paid/Unpaid</h4>
            {!! Form::open(array('url' => 'point-of-sale/reports','method'=>'GET')) !!}
            <select class="form-control view-all-paid-unpaid" name="viewstatus">
                <option value="" selected>Select</option>
                <option value="">All</option>
                <option value="Paid">Paid</option>
                <option value="Balanced">Balanced</option>
                <option value="Unpaid">Unpaid</option>
            </select>
            <button type="submit" class="btn btn-primary">View</button>
            {!! Form::close() !!}
        </div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-employee warning-msg-true">
            <p>{{ $message }}</p>
        </div>
    @endif
    @if ($message = Session::get('warning'))
        <div class="alert alert-warning alert-employee warning-msg-true">
            <p>{{ $message }}</p>
        </div>
    @endif

    <section class="content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover payroll-table" style="width:100%;">
                <tr>
                    <th align="center">List No.</th>
                    <th align="center">Ref. No.</th>
                    <th align="center">Customer Name</th>
                    <th align="center">Amount Due</th>
                    <th align="center">Date</th>
                    <th align="center">Status</th>
                    <th align="center">Balanced</th>
                    <th align="center" colspan="2">Action</th>
                </tr>

            {{--*/
                $total_amount = 0;
                $total_balance = 0;
                $status = '';

                $overall_amount_due = 0;
                $overall_balanced = 0;

            /*--}}

            @foreach ($pos_reports_no_pagination as $key => $val)
             {{--*/
                $overall_amount_due = $overall_amount_due + $val->amount_due;
                $overall_balanced = $overall_balanced + $val->amt_balance;

            /*--}}
            @endforeach

            {!! Form::open(array('url' => 'point-of-sale/reports/bulk-pay','method'=>'GET')) !!}
            {{-- */$x=0;/* --}}
            @foreach ($pos_reports as $key => $value)

            {{--*/
                $total_amount = $total_amount + $value->amount_due;
                $total_balance = $total_balance + $value->amt_balance;
                $status = $value->status;
            /*--}}
            {{-- */$x++;/* --}}
            <tr>
            <td>{{ $x }}</td>
                <td>{{ $value->id }}</td>
                <td>{{ $value->first_name }} {{ $value->middle_name }} {{ $value->last_name }}{{ $value->company_name }}</td>
                <td align="right">{{ number_format($value->amount_due, 2) }}</td>
                <td>{{ $value->sales_date }}</td>
                <td align="center">{{ $value->status }}</td>
                <td align="right">{{ number_format($value->amt_balance, 2) }}</td>
                @if($status == 'Balanced' || $status == 'Unpaid')
                <td align="center" class="btn-pay">
                    <a class="btn btn-success btn-xs" href="{{ url('point-of-sale/pay-pos', $value->id) }}" onclick = 'return confirm("Are you sure to mark this record as paid?")'>Pay</a>
                    <input type="checkbox" name="pay[]" class="form-check-input" value="{{ $value->id }}">
                </td>
                @else
                <td align="center" class="btn-pay">-</td>
                @endif
                <td align="center">
                    <a href="{{ url('point-of-sale/' . $value->id) }}" class="btn btn-primary btn-xs" title="View Details"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-danger btn-xs" href="{{ url('point-of-sale/delete-pos', $value->id) }}" onclick = 'return confirm("Are you sure to delete this record?")'><i class="fa fa-trash"></i></a>
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" align = "center"><strong>Total</strong></td>
                <td align="right"><strong>{{ number_format($total_amount, 2) }}</strong></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"><strong>{{ number_format($total_balance, 2) }}</strong></td>
                <td align="right">
                    <button class="btn btn-success form-contro" onclick = 'return confirm("Are you sure to mark this records as paid?")'>Quick Pay</button>
                </td>
            </tr>
            {!! Form::close() !!}
            
            <tr class="overall-total">
                <td colspan="3" align = "center"><strong>Overall Total</strong></td>
                <td align="right"><strong>{{ number_format($overall_amount_due, 2) }}</strong></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"><strong>{{ number_format($overall_balanced, 2) }}</strong></td>
            </tr>
            </table>
        </div>
        <div class="row" style="margin-right: 0px; margin-left: 0px;">
            <div class="pull-left" style="margin-bottom: 20px; margin-top: 20px;">
             <a href="{{ route('point-of-sale.create') }}" class="btn btn-primary">Back to Sales Entry</a>
             <a href="{{ url('point-of-sale/sales-reports') }}" class="btn btn-primary">Sales Reports</a>
            </div>
            <div class="pull-right">
             {{-- {!! $pos_reports->render(); !!} --}}
             {!! $pos_reports->appends(['date_from' => $start, 'date_to' => $end, 'viewstatus' => $viewstatus, 'search_cust_name' => $search_cust_name])->render(); !!}
            </div>
        </div>
    </section>
</div>
@endsection

@section('footer_script_preload')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('footer_script')
<script type="text/javascript">
$('.datepicker').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
});

</script>
<style type="text/css">
    div.total-issued-bots ul{padding-left: 0;}
    div.total-issued-bots ul li{
        list-style: none;
        font-size: 22px;
    }
    li.total-bots-issued{border-top: 1px solid #000;}
    div.payroll-details{width: 30%;}
    input.date-search{width: 36.5%; margin-right: 5px;}
    input.search-emp-name{width: 75%; margin-right: 5px;}
    select.view-all-paid-unpaid{width: 75%; margin-right: 7px; float: left;}
    div.payroll-details button{width: 21%;}
    tr.overall-total{background-color: #f1f1f1!important;}
    td.btn-pay{width: 100px;}
    td.btn-pay .btn-success{display: block; width: 50px;}
    td.btn-pay .form-check-input{width: 20px; height: 20px;}
</style>
@endsection