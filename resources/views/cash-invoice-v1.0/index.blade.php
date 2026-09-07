@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Cash Invoice <a href="{{ url('/cash-invoice/create') }}" class="btn btn-primary btn-xs" title="Add New Cash Invoice"><i class="fa fa-plus" aria-hidden="true"></i></a>
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
                    <th>Amount</th>
                    <th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            {{-- */$x=0;/* --}}
            @foreach($cashinvoices as $item)
                {{-- */$x++;/* --}}
                <tr>
                    <td style="text-align: center;">{{ $x }}</td>
                    <td>{{ $item->customer_name }}</td>
                    <td>{{ $item->date }}</td>
                    <td>{{ $item->invoice_number }}</td>
                    <td style="text-align: right;">{{ $item->amount }}</td>
                    <td style="text-align: center;">
                        <a href="{{ url('/cash-invoice/' . $item->id) }}" class="btn btn-success btn-xs" title="View Cash Invoice"><i class="fa fa-eye"></i></a>
                        <a href="{{ url('/cash-invoice/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Cash Invoice"><i class="fa fa-pencil"></i></a>
                        {!! Form::open([
                            'method'=>'DELETE',
                            'url' => ['/cash-invoice', $item->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Cash Invoice',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ));!!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pagination"> {!! $cashinvoices->render() !!} </div>
    </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
