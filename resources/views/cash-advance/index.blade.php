@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-3 margin-tb">
            <div class="pull-left">
                <h1>Cash Advance Summary <a href="{{ route('cash-advance.create') }}" class="btn btn-primary btn-xs" title="Add new cash advance"><i class="fa fa-plus" aria-hidden="true"></i></a></h1>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="row">
                <div class="col-md-12  align-right">
                    {!! Form::open(array('url' => 'cash-advance/results','method'=>'GET')) !!}
                    <div class="col-md-10 col-sm-12" style="padding-right: 0px;">
                        <input type="text" name="employee_name" class="form-control" placeholder="Search by Employees"/>
                    </div>
                    <div class="col-md-2 col-sm-12">
                        <button type="submit" class="btn btn-primary">Search</button>
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
                    <th>Employee Name</th>
                    <th style="width: 250px;">CA Current Amount</th>
                    <th class="actions">Action</th>
                </tr>
            {{-- @foreach ($cash_advances as $key => $item) --}}
            {{-- */
                $ca_total_amount = 0;
            /* --}}
            @foreach ($cash_advances as $ca)
            {{-- */
                $ca_total_amount = $ca_total_amount + $ca->total_current_amount;
            /* --}}
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $ca->employee_name }}</td>
                <td align="right">{{ $ca->total_current_amount }}</td>
                <td>
                    <a class="btn btn-primary btn-xs" href="{{ url('cash-advance/details', $ca->e_id) }}" title="Cash Advance Details"><i class="fa fa-th-list"></i></a>
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2" align="right"><strong>Total:</strong></td>
                <td align="right"><strong>{{ number_format(floatval($ca_total_amount), 2) }}</strong></td>
            </tr>
            </table>
            <div class="row" style="margin-right: 0px; margin-left: 0px;">
                <div class="col-md-6">
                    <div class="pagination" style="margin:0;">
                        {{-- {!! $products->appends(['inventory_status' => $inventory_status, 'search_products' => $search_products])->render(); !!} --}}
                        {!! $cash_advances->appends(['employee_name' => $emp_name])->render(); !!}
                        {{-- {!! $cash_advances->render(); !!} --}}
                    </div>
                </div>
                <div class="col-md-6" style="text-align: right;">
                    <a href="{{ route('cash-advance.create') }}" class="btn btn-primary">Add new cash advance</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

<style type="text/css">
    ul.pagination{
        margin: 0px;
    }
    .table-responsive table{
        font-size: 14px;
    }
</style>