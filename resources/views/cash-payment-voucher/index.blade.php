@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            CashPaymentVoucher <a href="{{ url('/cash-payment-voucher/create') }}" class="btn btn-primary btn-xs" title="Add New CashPaymentVoucher"><i class="fa fa-plus" aria-hidden="true"></i></a>
        </h1>
        
    </section>
    
    <!-- Main content -->
    <section class="content">
    
    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>S.No</th><th> Vendor </th><th> Date </th><th> CV No. </th><th> Invoice Number </th><th> Payment Method </th><th> Amount </th><th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            {{-- */$x=0;/* --}}
            @foreach($cashpaymentvoucher as $item)
                {{-- */$x++;/* --}}
                <tr>
                    <td>{{ $x }}</td>
                    <td>{{ $item->vendor_name }}</td><td>{{ $item->date }}</td><td>{{ $item->cv_number }}</td>
                    <td>@if($item->supplier_invoice){{ $item->supplier_invoice->invoice_number }}@endif</td>
                    <td>{{ $item->payment_method }}</td><td>{{ $item->amount }}</td>
                    <td>
                        <a href="{{ url('/cash-payment-voucher/' . $item->id) }}" class="btn btn-success btn-xs" title="View CashPaymentVoucher"><i class="fa fa-eye"></i></a>
                        <a href="{{ url('/cash-payment-voucher/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit CashPaymentVoucher"><i class="fa fa-pencil"></i></a>
                        {!! Form::open([
                            'method'=>'DELETE',
                            'url' => ['/cash-payment-voucher', $item->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete CashPaymentVoucher',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ));!!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pagination"> {!! $cashpaymentvoucher->render() !!} </div>
    </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
