@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Credit Invoice <a href="{{ url('/credit-invoice/create') }}" class="btn btn-primary btn-xs" title="Add New Credit Invoice"><i class="fa fa-plus" aria-hidden="true"></i></a>
        </h1>
        
    </section>
    
    <!-- Main content -->
    <section class="content">
    
    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Pay To</th>
                    <th>Date</th>
                    <th>Invoice Number</th>
                    <th>Amount Due</th>
                    <th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            {{-- */$x=0;/* --}}
            @foreach($creditinvoices as $item)
                {{-- */$x++;/* --}}
                <tr>
                    <td style="text-align: center;">{{ $x }}</td>
                    <td>{{ $item->customer_name }}</td>
                    <td>{{ $item->date }}</td>
                    <td>{{ $item->invoice_number }}</td>
                    <td style="text-align: right;">{{ $item->amount_due }}</td>
                    <td style="text-align: center;">
                        <a href="{{ url('/credit-invoice/' . $item->id) }}" class="btn btn-success btn-xs" title="View Credit Invoice"><i class="fa fa-eye"></i></a>
                        <a href="{{ url('/credit-invoice/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Credit Invoice"><i class="fa fa-pencil"></i></a>
                        {!! Form::open([
                            'method'=>'DELETE',
                            'url' => ['/credit-invoice', $item->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Credit Invoice',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ));!!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pagination"> {!! $creditinvoices->render() !!} </div>
    </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
