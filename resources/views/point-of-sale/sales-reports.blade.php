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
                <h1>Sales report</h1>
            </div>
        </div>
        <div style="clear: both; margin-bottom: 20px;"></div>
        {!! Form::open(array('url' => 'point-of-sale/sales-reports/search','method'=>'GET')) !!}
        <div class="col-lg-12">
            <h4>Search by product's</h4>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <input type="text" name="search" class="search-prod-name form-control" placeholder="Search by product's"/>
                </div>
                <div class="col-md-6 col-sm-12">
                    <input type="text" name="date_from" class="date-search form-control datepicker" placeholder="Date From:" autocomplete="off"/>
                    <input type="text" name="date_to" class="date-search form-control datepicker" placeholder="Date To:" autocomplete="off"/>
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
        </section>
    </div>
        
        {{-- <div class="alert alert-warning alert-employee warning-msg-false" style="display: none;">
            <p>No Customer selected successfully</p>
        </div> --}}

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-employee warning-msg-true">
            <p>{{ $message }}</p>
        </div>
    @endif

    <section class="content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover payroll-table" style="width:100%;">
                <tr>
                    <th style="text-align: left;">Product ID</th>
                    <th style="text-align: left;">Product Name</th>
                    <th style="text-align: right;">Qty Sold</th>
                    <th style="text-align: right;">Amount</th>
                    <th style="text-align: left;">Action</th>
                </tr>
                @foreach($salesReports as $data)
                <tr>
                    <td align="left">{{ $data->prod_id }}</td>
                    <td align="left">{{ $data->name }}</td>
                    <td align="right">{{ $data->total_solds }}</td>
                    <td align="right">{{ number_format($data->total_amount, 2) }}</td>
                    <td align="left">
                    <a class="btn btn-primary btn-xs" href="/point-of-sale/sales-reports/product-detail-sales/{{ $data->prod_id }}/{{ $date_from }}/{{ $date_to }}" title="Sales Details"><i class="fa fa-th-list"></i> Details</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        <input type="hidden" name="datefrom" value="{{ $date_from }}"/>
        <input type="hidden" name="dateto" value="{{ $date_to }}"/>
        <div class="row" style="margin-right: 0px; margin-left: 0px;">
            <div class="pull-left" style="margin-bottom: 20px; margin-top: 20px;">
             <a href="{{ url('point-of-sale/reports') }}" class="btn btn-primary">Back to Sales Summary Report</a>
            </div>
            <div class="pull-right">
             {{-- {!! $salesReports->render(); !!} --}}
             {{-- {!! $pos_reports->appends(['date_from' => $start, 'date_to' => $end, 'viewstatus' => $viewstatus, 'search_cust_name' => $search_cust_name])->render(); !!} --}}
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
</style>
@endsection