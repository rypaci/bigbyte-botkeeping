@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
        
        <div class="pull-left">
            <h1>Daily Time Record</h1>
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
        <div class="row dtr-login">
            <div class="col-md-3">
                <h3>Time In</h3>
                {!! Form::open(array('route' => 'daily-time-record.store','method'=>'POST')) !!}
                {{-- {!! Form::open(array('url' => 'daily-time-record/submit','method'=>'POST')) !!} --}}
                <div class="row">
                    <div class="col-sm-12">
                        <label>Name/Username:</label>
                        <select name="e_id" class="form-control">
                            <option value="">Select Name/Username</option>
                            @foreach($employees as $employee)
                                @if($employee->employee_status == 'Active')
                                <option value="{{ $employee->e_id }}">{{ $employee->employee_name }} >>> {{ $employee->username }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <label>Password:</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter password" required/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <label>Select Option:</label>
                        <select name="status" class="form-control select-option">
                            <option value="">Select Option</option>
                            <option value="In">Time In</option>
                            <option value="Break">Break</option>
                            <option value="Lunch">Lunch</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <label>Note:</label>
                        <input type="text" name="notes" class="form-control" placeholder="Optional only" data-lpignore="true"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="daily-time-record/create-password" class="btn btn-primary no-password">Create username and password? Click here!</a>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
            <div class="col-md-9">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Time In</th>
                                <th>Date In</th>
                                <th>Time Out</th>
                                <th>Date Out</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th class="actions">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        {{-- */$x=0;/* --}}
                        @foreach ($current_dtrs as $dtr)
                            {{-- */$x++;/* --}}
                            <tr>
                                <td>{{ $x }}</td>
                                <td>{{ $dtr->employee_name }}</td>
                                <td align="right">{{ date('g:i A', strtotime($dtr->time_in)) }}</td>
                                <td align="right">{{ date('m-d-Y', strtotime($dtr->time_in)) }}</td>
                                <td align="right">@if($dtr->time_out == '0000-00-00 00:00:00') @else {{ date('g:i A', strtotime($dtr->time_out)) }}@endif</td>
                                <td align="right">@if($dtr->time_out == '0000-00-00 00:00:00') @else {{ date('m-d-Y', strtotime($dtr->time_out)) }}@endif</td>
                                <td>@if($dtr->status == 'Out') <span class="red-text">{{ $dtr->status }}</span> @else {{ $dtr->status }}@endif</td>
                                <td>{{ $dtr->notes }}</td>
                                <td align="right">
                                    @if($dtr->status == 'In')
                                        <a class="btn btn-success btn-xs" href="{{ url('daily-time-record/logout', $dtr->id) }}" onclick = 'return confirm("Are you sure to Logout")'> Time Out</a>
                                    @elseif($dtr->status == 'Lunch' || $dtr->status == 'Break')
                                        <a class="btn btn-success btn-xs" href="{{ url('daily-time-record/finish-break', $dtr->id) }}" onclick = 'return confirm("Are you sure to finish Lunch/Quick break?")'> Finish Lunch/Quick break</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{-- <div class="pagination"> {!! $customers->render() !!} </div> --}}
                </div>
            </div>
        </div>
    </section>
</div>

@endsection

<style type="text/css">
.dtr-login{
    margin-right: 0!important;
    margin-left: 0!important;
}
.dtr-login .col-md-3{
    background-color: #ffffff;
}
.dtr-login .col-sm-12{
    margin-bottom: 10px;
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
select.select-option{
    width: 150px;
}
.no-password{
    white-space: unset!important;
}
.btn-primary{
    margin-bottom: 10px!important;
}
</style>