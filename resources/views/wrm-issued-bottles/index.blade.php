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
                <h1>Summary of Issued Bottles Records</h1>
            </div>
        </div>
        <div style="clear: both; margin-bottom: 20px;"></div>
        <div class="col-lg-4 payroll-details">
            <h4>Search summary by customer</h4>
            {!! Form::open(array('url' => 'wrm-issued-bottles/search-result','method'=>'GET')) !!}
                <input type="text" name="cust_name" class="search-emp-name form-control" placeholder="Search Customer Name"/>
                <button type="submit" class="btn btn-primary">Search</button>
            {!! Form::close() !!}
        </div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-employee warning-msg-true">
            <p>{{ $message }}</p>
        </div>
    @endif

    <section class="content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover payroll-table">
                <tr>
                    <th align="center">List No.</th>
                    <th align="center">Customers</th>
                    <th align="center">Delivered/Issued Bottles</th>
                    <th align="center">Returned Bottles</th>
					<th align="center">Total Issued Bottles</th>
                    <th align="center">Details</th>
                </tr>
            {{--*/
                $x = 0;
                $total_issued_bottles = 0;
                $total_returned_bottles = 0;

                $overall_issued_bots = 0;
                $overall_returned_bottles = 0;
            /*--}}

            @foreach ($overall_issued_bottles as $key => $val)
            {{--*/
                $overall_issued_bots = $overall_issued_bots + $val->delivered_bots;
                $overall_returned_bottles = $overall_returned_bottles + $val->returned_bots;
            /*--}}
            @endforeach

            @foreach ($issued_bottles as $key => $value)
            {{--*/
                $total_issued_bottles = $total_issued_bottles + $value->delivered_bots;
                $total_returned_bottles = $total_returned_bottles + $value->returned_bots;
            /*--}}
            <tr>
                <td>{{ ($issued_bottles->currentpage()-1) * $issued_bottles->perpage() + $key + 1 }}</td>
                <td>{{ $value->first_name }} {{ $value->middle_name }} {{ $value->last_name }}{{ $value->company_name }}</td>
                <td align="right">{{ number_format($value->delivered_bots) }}</td>
                <td align="right">{{ number_format($value->returned_bots) }}</td>
                <td align="right">{{ number_format($value->delivered_bots - $value->returned_bots) }}</td>
                @if(empty($value->cust_id))
                <td align="center">
                    <a href="{{ url('/wrm-issued-bottles/issued-bots-details', $value->cust_id) }}" class="btn btn-primary btn-xs" title="Edit Bottles" onclick = 'return false'><i class="fa fa-eye"></i> Can't View Details</a>
                </td>
                @else
                <td align="center">
                    <a href="{{ url('/wrm-issued-bottles/issued-bots-details', $value->cust_id) }}" class="btn btn-primary btn-xs" title="Edit Bottles"><i class="fa fa-eye"></i> View Details</a>
                </td>
                @endif
            </tr>
            @endforeach
            <tr>
                <td align="right" colspan="2"><strong>Total:</strong></td>
                <td align="right"><strong>{{ number_format($total_issued_bottles) }}</strong></td>
                <td align="right"><strong>{{ number_format($total_returned_bottles) }}</strong></td>
                <td align="right"><strong>{{ number_format($total_issued_bottles-$total_returned_bottles) }}</strong></td>
            </tr>
            <tr>
                <td align="right" colspan="2"><strong>Overall Total:</strong></td>
                <td align="right"><strong>{{ number_format($overall_issued_bots) }}</strong></td>
                <td align="right"><strong>{{ number_format($overall_returned_bottles) }}</strong></td>
                <td align="right"><strong>{{ number_format($overall_issued_bots - $overall_returned_bottles) }}</strong></td>
            </tr>
            </table>
        </div>
        <div class="row" style="margin-right: 0px; margin-left: 0px;">
            <div class="pull-left" style="margin-bottom: 20px; margin-top: 20px;">
                <a href="{{ route('water-refilling-monitoring.index') }}" class="btn btn-primary">Back to Dashboard</a>
                <a href="{{ url('water-refilling-monitoring/reports') }}" class="btn btn-primary">Go to Reports</a>
            </div>
            <div class="pull-right">
             {!! $issued_bottles->render() !!}
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