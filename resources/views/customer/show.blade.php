@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif show
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        <h1>
            Customer {{ $customer->id }}
        </h1>
        
    </section>

    <!-- Main content -->
    <section class="content">

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <tbody>
                <tr>
                    <th>ID.</th><td>{{ $customer->id }}</td>
                </tr>
                <tr><th> Individual? </th><td>@if($customer->individual=='1') <span class="badge bg-green">Yes</span> @else <span class="badge bg-orange">No</span> @endif </td></tr>
                @if($customer->individual=='1')
                <tr><th> Last Name </th><td> {{ $customer->last_name }} </td></tr>
                <tr><th> First Name </th><td> {{ $customer->first_name }} </td></tr>
                <tr><th> Middle Name </th><td> {{ $customer->middle_name }} </td></tr>
                @else
                <tr><th> Company Name </th><td> {{ $customer->company_name }} </td></tr>
                @endif
                <tr><th> Business Name </th><td> {{ $customer->business_name }} </td></tr><tr><th> Business Address </th><td> {{ $customer->business_address }} </td></tr>
                <tr><th> City </th><td> {{ $customer->city }} </td></tr><tr><th> Country </th><td> {{ $customer->country }} </td></tr>
                <tr><th> TIN </th><td> {{ $customer->tin }} </td></tr><tr><th> Branch Code </th><td> {{ $customer->branch_code }} </td></tr>
                <tr><th> Opening Bal. </th><td> {{ $customer->opening_balance }} </td></tr><tr><th> Phone # </th><td> {{ $customer->phone_number }} </td></tr>
                <tr><th> Fax </th><td> {{ $customer->fax }} </td></tr><tr><th> Email </th><td> {{ $customer->email }} </td></tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <a href="{{ url('/customer/' . $customer->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Customer"><i class="fa fa-pencil"></i></a>
                        {!! Form::open([
                            'method'=>'DELETE',
                            'url' => ['/customer', $customer->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Customer',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            )) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection


@section('footer_script_preload')
@endsection