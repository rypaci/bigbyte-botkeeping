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
                <h1>Customer <a href="{{ route('customer.create') }}" class="btn btn-primary btn-xs" title="Add New Customer"><i class="fa fa-plus" aria-hidden="true"></i></a></h1>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('customer.index') }}"> Back</a>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-customer">
            <p>{{ $message }}</p>
        </div>
    @endif

    <section class="content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <tr>
                    <th>Individual?</th>
                    <th>Name</th>
                    <th>Business Name</th>
                    <th>Business Address</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>Tin</th>
                    <th>Phone No</th>
                    <th>Fax</th>
                    <th>Email</th>
                </tr>
            <tr>
                <td>{{ $customer->individual }}</td>
                <td>{{ $customer->fullname }}</td>
                <td>{{ $customer->business_name }}</td>
                <td>{{ $customer->business_address }}</td>
                <td>{{ $customer->city }}</td>
                <td>{{ $customer->country }}</td>
                <td>{{ $customer->tin }}</td>
                <td>{{ $customer->phone_no }}</td>
                <td>{{ $customer->fax }}</td>
                <td>{{ $customer->email }}</td>
            </tr>
            </table>
            <div class="col-xs-12 col-sm-12 col-md-12">
                    <a class="btn btn-primary btn-xs" href="{{ route('customer.edit',$customer->id) }}"><i class="fa fa-pencil"></i></a>
                    {!! Form::open(['method' => 'DELETE','route' => ['customer.destroy', $customer->id],'style'=>'display:inline']) !!}
                    {!! Form::button('<i class="fa fa-trash"></i>', array(

                                    'type' => 'submit',

                                    'class' => 'btn btn-danger btn-xs',

                                    'title' => 'Delete Customer',

                                    'onclick'=>'return confirm("Confirm delete?")'
                            ));!!}
                    {!! Form::close() !!}

            </div>
        </div>
    </section>
</div>
@endsection