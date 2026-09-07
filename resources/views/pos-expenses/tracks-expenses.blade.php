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
                <h1>POS Expenses Records</h1>
            </div>
        </div>
        <div style="clear: both; margin-bottom: 20px;"></div>
        <div class="col-lg-6 payroll-details">
            <h4>Track Expenses by Vendor's Name</h4>
            {!! Form::open(array('url' => 'pos-expenses/tracks-expenses','method'=>'GET')) !!}
            <input type="text" name="search_vendor_name" class="search-emp-name form-control" placeholder="Search Vendor Name"/>
            <button type="submit" class="btn btn-primary">Search</button>
            {!! Form::close() !!}
        </div>
        <div class="col-lg-6 payroll-details">
            <h4>Track Expenses by Date Range</h4>
            {!! Form::open(array('url' => 'pos-expenses/tracks-expenses','method'=>'GET')) !!}
            <input type="text" name="date_from" class="date-search form-control datepicker" placeholder="Date From:" autocomplete="off"/>
            <input type="text" name="date_to" class="date-search form-control datepicker" placeholder="Date To:" autocomplete="off"/>
            <button type="submit" class="btn btn-primary">Search</button>
            {!! Form::close() !!}
        </div>
        </section>
    </div>
        
        <div class="alert alert-warning alert-employee warning-msg-false" style="display: none;">
            <p>No Vendor's selected</p>
        </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-employee warning-msg-true">
            <p>{{ $message }}</p>
        </div>
    @endif

    <section class="content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover payroll-table" style="width:100%;">
                <tr>
                    <th align="center">List No.</th>
                    <th align="center">Invoice No.</th>
                    <th align="center">Vendor Name</th>
                    <th align="center">Terms</th>
                    <th align="center">Period/days</th>
                    <th align="center">Amount</th>
                    <th align="center">Description</th>
                    <th align="center">Date</th>
                    <th align="center">Action</th>
                </tr>

            {{--*/
                $total_expenses = 0;
            /*--}}

            @foreach ($expenses_reports as $key => $value)

            {{--*/
                $total_expenses = $total_expenses + $value->amount;
            /*--}}

            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $value->invoice_no }}</td>
                <td><span>{{ $value->first_name." ".$value->last_name." ".$value->company_name }}</span></td>
                <td>{{ $value->terms }}</td>
                <td align="right">{{ $value->period }}</td>
                <td align="right">{{ number_format($value->amount, 2) }}</td>
                <td>{{ $value->description }}</td>
                <td align="center">{{ $value->date }}</td>
                <td align="center">
                    <a href="{{ url('pos-expenses/' . $value->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Expenses"><i class="fa fa-pencil"></i></a>
                    <a href="{{ url('pos-expenses/' . $value->id) }}" class="btn btn-primary btn-xs" title="View Expenses"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-danger btn-xs" href="{{ url('pos-expenses/tracks-expenses', $value->id) }}" onclick = 'return confirm("Are you sure to delete this record?")'><i class="fa fa-trash"></i></a>
                </td>
            </tr>
            @endforeach
            </table>
            <h3>Total Amount Expenses: <strong>{{ number_format($total_expenses, 2) }}</strong></h3>
        </div>
        <div class="row" style="margin-right: 0px; margin-left: 0px;">
            <div class="pull-left" style="margin-bottom: 20px; margin-top: 20px;">
                <a href="{{ route('point-of-sale.index') }}" class="btn btn-primary">Back to Dashboard</a>
                <a href="{{ route('pos-expenses.create') }}" class="btn btn-primary">Add Expenses</a>
            </div>
            <div class="pull-right">
             {!! $expenses_reports->appends(['date_from' => $start, 'date_to' => $end, 'search_vendor_name' => $search_vendor_name])->render() !!}
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
</style>
@endsection