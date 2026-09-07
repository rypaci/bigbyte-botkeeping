@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
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
        
        <div class="pull-left">
            <h1>Customer <a href="{{ route('customer.create') }}" class="btn btn-primary btn-xs" title="Add New Customer"><i class="fa fa-plus" aria-hidden="true"></i></a></h1>
        </div>
        <div style="clear: both;"></div>
        
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Individual?</th>
                        <th>Name</th>
                        <th>Business Name</th>
                        <th>Business Address</th>
                        <th class="actions">Action</th>
                    </tr>
                </thead>
                <tbody>
                {{-- */$x=0;/* --}}
                @foreach ($customers as $customer)
                    {{-- */$x++;/* --}}
                    <tr>
                        <td>{{ $x }}</td>
                        <td>@if($customer->individual=='1') <span class="badge bg-green">Yes</span> @else <span class="badge bg-orange">No</span> @endif </td>
                        <td>{{ $customer->fullname }}</td>
                        <td>{{ $customer->business_name }}</td>
                        <td>{{ $customer->business_address }}</td>
                        <td>
                            <a class="btn btn-success btn-xs" href="{{ route('customer.show',$customer->id) }}"><i class="fa fa-eye"></i></a>
                            <a class="btn btn-primary btn-xs" href="{{ route('customer.edit',$customer->id) }}"><i class="fa fa-pencil"></i></a>
                            {!! Form::open(['method' => 'DELETE','route' => ['customer.destroy', $customer->id],'style'=>'display:inline']) !!}
                            {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Customer',
                                    'onclick'=>'return confirm("Confirm delete?")'
                                ));!!}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination"> {!! $customers->render() !!} </div>
        </div>
    </section>
</div>

@endsection