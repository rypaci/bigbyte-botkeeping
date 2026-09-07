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
                    <h1>Show Employee</h1>
                </div>
                <div class="pull-right">
                    <a class="btn btn-primary" href="{{ route('list-employees.index') }}"> Back</a>
                </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>
    <section class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 employee-show">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>Name Of Employee:</strong>
            </div>
            <div class="col-sm-6">
                {{ $employee->employee_name }}
            </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-show">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>TIN:</strong>
            </div>
            <div class="col-sm-6">
                {{ $employee->tin_no }}
            </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-show">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>Address:</strong>
            </div>
            <div class="col-sm-6">
                {{ $employee->address }}
            </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-show">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>Birthday:</strong>
            </div>
            <div class="col-sm-6">
                {{ $employee->birthday }}
            </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-show">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>Sex:</strong>
            </div>
            <div class="col-sm-6">
                {{ $employee->sex }}
            </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-show">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>Status:</strong>
            </div>
            <div class="col-sm-6">
                {{ $employee->status }}
            </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-show">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>Dependents:</strong>
            </div>
            <div class="col-sm-6">
                {{ $employee->dependents }}
            </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-show">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>Daily Rate:</strong>
            </div>
            <div class="col-sm-6">
                {{ $employee->daily_rate }}
            </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-show">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>Monthly Rate:</strong>
            </div>
            <div class="col-sm-6">
                {{ $employee->monthly_rate }}
            </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-show">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>Overtime Rate:</strong>
            </div>
            <div class="col-sm-6">
                {{ $employee->overtime_rate }}
            </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 employee-show">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>Late Rate:</strong>
            </div>
            <div class="col-sm-6">
                {{ $employee->late_rate }}
            </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
        <div class="col-sm-2">
            <a class="btn btn-primary btn-xs" href="{{ route('list-employees.edit',$employee->id) }}"><i class="fa fa-pencil"></i></a>
                    {!! Form::open(['method' => 'DELETE','route' => ['list-employees.destroy', $employee->id],'style'=>'display:inline']) !!}
                    {!! Form::button('<i class="fa fa-trash"></i>', array(

                                    'type' => 'submit',

                                    'class' => 'btn btn-danger btn-xs',

                                    'title' => 'Delete Employee',

                                    'onclick'=>'return confirm("Confirm delete?")'
                            ));!!}
                    {!! Form::close() !!}
        </div>
        </div>
        </div>
    </div>
    </section>
</div>
@endsection