@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>List of Employees <a href="{{ route('list-employees.create') }}" class="btn btn-primary btn-xs" title="Add New Service"><i class="fa fa-plus" aria-hidden="true"></i></a></h1>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-employee">
            <p>{{ $message }}</p>
        </div>
    @endif

    <section class="content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <tr>
                    <th>No.</th>
                    <th>Name Of Employee</th>
                    <th>TIN</th>
                    <th>Address</th>
                    <th>Birthday</th>
                    <th>Sex</th>
                    <th>Status</th>
                    <th>Dependents</th>
                    <th>Daily Rate</th>
                    <th>Monthly Rate</th>
                    <th>Overtime Rate</th>
                    <th>Absent Rate</th>
                    <th>Late Rate</th>
                    <th>Salary Method</th>
                    <th>Min. Hrs/Day</th>
                    <th>Employee Status</th>
                    <th class="actions">Action</th>
                </tr>

            @foreach ($employees as $key => $employee)
            <tr>
                <td>{{ ++$i }}</td>
                <td style="display:none;">{{ $employee->id }}</td>
                <td>{{ $employee->employee_name }}</td>
                <td>{{ $employee->tin_no }}</td>
                <td>{{ $employee->address }}</td>
                <td>{{ $employee->birthday }}</td>
                <td>{{ $employee->sex }}</td>
                <td>{{ $employee->status }}</td>
                <td>{{ $employee->dependents }}</td>
                <td align="right">{{ $employee->daily_rate }}</td>
                <td align="right">{{ $employee->monthly_rate }}</td>
                <td align="right">{{ $employee->overtime_rate }}</td>
                <td align="right">{{ $employee->absent_rate }}</td>
                <td align="right">{{ $employee->late_rate }}</td>
                <td align="center">{{ $employee->salary_method }}</td>
                <td align="center">{{ $employee->min_hours_per_day }}</td>
                <td align="center">{{ $employee->employee_status }}</td>
                <td>
                    <a class="btn btn-success btn-xs" href="{{ route('list-employees.show',$employee->id) }}"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-primary btn-xs" href="{{ route('list-employees.edit',$employee->id) }}"><i class="fa fa-pencil"></i></a>
                    {{-- <a class="btn btn-danger btn-xs" href="{{ route('list-employees.get.destroy', $employee->id) }}" onclick = 'return confirm("Confirm delete?")'><i class="fa fa-trash"></i></a> --}}
                    {!! Form::open(['method' => 'DELETE','route' => ['list-employees.destroy', $employee->id],'style'=>'display:inline']) !!}
                    {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Employee',
                                    'onclick'=>'return confirm("Confirm delete?")'
                    )); !!}
                    {!! Form::close() !!}
                </td>
            </tr>
            @endforeach
            </table>
            <div class="pull-left">
                <a href="{{ url('/payroll') }}" class="btn btn-primary" title="View Payroll Details">Click here to create payroll</a>
            </div>
        </div>
    </section>
</div>
    {!! $employees->render() !!}
@endsection

@section('footer_script')

<script type="text/javascript">
$(document).ready(function(){

    var $form = $('.addtopayroll');
    var $checkbox = $('.select-emp');
        $form.on('submit', function(e) {
           if(!$checkbox.is(':checked')){
                alert("Select Employee to add to Payroll.");
                e.preventDefault();
            }
        });
});
</script>
@endsection