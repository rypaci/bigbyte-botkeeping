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
                    <th>No</th>
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
                    <th>Late Rate</th>
                    <th width="280px">Action</th>
                </tr>
            @foreach ($employees as $key => $employee)
            {!! Form::open(array('route' => 'list-employees.store','method'=>'POST')) !!}
            <tr>
                <td>{{ ++$i }}</td>
                <td><a href="{{Route('name', ['id' => $employee->id ])}}">{{ $employee->employee_name }}</a></td>
                <td>{{ $employee->tin_no }}</td>
                <td>{{ $employee->address }}</td>
                <td>{{ $employee->birthday }}</td>
                <td>{{ $employee->sex }}</td>
                <td>{{ $employee->status }}</td>
                <td>{{ $employee->dependents }}</td>
                <td>{{ $employee->daily_rate }}</td>
                <td>{{ $employee->monthly_rate }}</td>
                <td>{{ $employee->overtime_rate }}</td>
                <td>{{ $employee->late_rate }}</td>
                <td>
                    <a class="btn btn-success btn-xs" href="{{ route('list-employees.show',$employee->id) }}"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-primary btn-xs" href="{{ route('list-employees.edit',$employee->id) }}"><i class="fa fa-pencil"></i></a>
                    {!! Form::open(['method' => 'DELETE','route' => ['list-employees.destroy', $employee->id],'style'=>'display:inline']) !!}
                    {!! Form::button('<i class="fa fa-trash"></i>', array(

                                    'type' => 'submit',

                                    'class' => 'btn btn-danger btn-xs',

                                    'title' => 'Delete Employee',

                                    'onclick'=>'return confirm("Confirm delete?")'
                            ));!!}
                    {!! Form::close() !!}
                </td>
            </tr>
            {!! Form::close() !!}
            @endforeach
            </table>
        </div>
    </section>
</div>
    {!! $employees->render() !!}
@endsection