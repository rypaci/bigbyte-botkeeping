@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
        
        <div class="pull-left">
            <h1>List of DTR Accounts</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row search-dtr">
                    {!! Form::open(array('url' => 'daily-time-record/dtr-account-search','method'=>'post')) !!}
                    <div class="col-md-6 col-sm-12" style="padding-right: 0px;">
                        <input type="text" name="employee_name" class="form-control" placeholder="Search by names"/>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div style="clear: both;"></div>
        
        @if ($message = Session::get('success'))
        <div class="alert alert-success alert-customer alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <p>{{ $message }}</p>
        </div>
        @endif

        @if ($message = Session::get('warning'))
        <div class="alert alert-warning alert-customer alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <p>{{ $message }}</p>
        </div>
        @endif

    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row dtr-list">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email Address</th>
                                <th>Time Schedule</th>
                                <th>Employee Status</th>
                                <th class="actions">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        {{-- */$x=0;/* --}}
                        @foreach ($list_of_dtr_accounts as $dtr_account)
                            {{-- */$x++;/* --}}
                            <tr>
                                <td>{{ $x }}</td>
                                <td>{{ $dtr_account->employee_name }}</td>
                                <td>{{ $dtr_account->username }}</td>
                                <td>{{ $dtr_account->email }}</td>
                                <td>{{ date('g:i A', strtotime($dtr_account->start_time)) }} - {{ date('g:i A', strtotime($dtr_account->end_time)) }}</td>
                                <td>@if($dtr_account->employee_status == 'Inactive') <span class="red">{{ $dtr_account->employee_status }}</span> @else {{ $dtr_account->employee_status }} @endif</td>
                                <td align="right">
                                    <a class="btn btn-success btn-xs" href="{{ url('daily-time-record/dtr-details', $dtr_account->e_id) }}"> DTR Details Report</a>
                                    <a class="btn btn-success btn-xs" href="{{ url('daily-time-record/hours-shifting', $dtr_account->e_id) }}"> Time Schedule</a>
                                    <a class="btn btn-success btn-xs" href="{{ url('daily-time-record/absent-list', $dtr_account->e_id) }}"> Check Absent</a>
                                    <a class="btn btn-success btn-xs" href="{{ url('daily-time-record/reset-password', $dtr_account->e_id) }}"> Reset Password</a>
                                    <a class="btn btn-success btn-xs" href="{{ url('daily-time-record/send-password', $dtr_account->e_id) }}"> Send Password</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination"> {!! $list_of_dtr_accounts->render() !!} </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection

<style type="text/css">
.dtr-list{
    margin-right: 0!important;
    margin-left: 0!important;
}
.dtr-list .col-md-12{
    padding: 0px;
}
.red-text{
    color: #FD0017;
}
.no-password{
    float: right;
}
.table th{
    text-align: center;
}
.pagination{
    margin: 0px!important;
}
.search-dtr{
    margin-top: 20px;
}
span.red{
    color: #FF0C09
}
</style>