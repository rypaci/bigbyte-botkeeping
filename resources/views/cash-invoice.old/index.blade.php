@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Cash Invoice <a href="{{ url('/cash-invoice/create-cash-invoice') }}" class="btn btn-primary btn-xs" title="Add New Cash Invoice"><i class="fa fa-plus" aria-hidden="true"></i></a>
        </h1>
        
    </section>
    
    <!-- Main content -->
    <section class="content">
    
    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th style="text-align: center;">S.No</th>
                    <th style="text-align: center;">Pay To</th>
                    <th style="text-align: center;">Address</th>
                    <th style="text-align: center;">Tin</th>
                    <th style="text-align: center;">Amount</th>
                    <th class="actions" style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
            {{-- */$x=0;/* --}}
            @foreach($cashinvoices as $cashinvoice)
                {{-- */$x++;/* --}}
                <tr>
                    <td style="text-align: center;">{{ $x }}</td>
                    <td>{{ $cashinvoice->pay_to }}</td>
                    <td>{{ $cashinvoice->business_address }}</td>
                    <td>{{ $cashinvoice->tin }}</td>
                    <td style="text-align: right;">{{ $cashinvoice->invoice_amount }}</td>
                    <td>
                        <a href="{{ url('/cash-invoice/' . $cashinvoice->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Cash Invoice"><i class="fa fa-pencil"></i></a>
                        {!! Form::open([
                            'method'=>'DELETE',
                            'url' => ['/cash-invoice', $cashinvoice->id],
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
