@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Official Receipt <a href="{{ url('/official-receipt/create') }}" class="btn btn-primary btn-xs" title="Add New Official Receipt"><i class="fa fa-plus" aria-hidden="true"></i></a>
        </h1>
        
    </section>
    
    <!-- Main content -->
    <section class="content">
    
    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>S.No</th><th> Customer </th><th> Date </th><th> CV No. </th><th> Invoice Number </th><th> Payment Method </th><th> Amount </th><th> Sales Discount </th><th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            {{-- */$x=0;/* --}}
            @foreach($officialreceipt as $item)
                {{-- */$x++;/* --}}
                <tr>
                    <td>{{ $x }}</td>
                    <td>{{ $item->customer_name }}</td><td>{{ $item->date }}</td><td>{{ $item->or_number }}</td>
                    <td>{{ $item->invoice_number }}</td>
                    <td>{{ $item->payment_method }}</td><td>{{ $item->amount }}</td><td>{{ $item->sales_discount }}</td>
                    <td>
                        <a href="{{ url('/official-receipt/' . $item->id) }}" class="btn btn-success btn-xs" title="View Official Receipt"><i class="fa fa-eye"></i></a>
                        <a href="{{ url('/official-receipt/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Official Receipt"><i class="fa fa-pencil"></i></a>
                        {!! Form::open([
                            'method'=>'DELETE',
                            'url' => ['/official-receipt', $item->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Official Receipt',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ));!!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pagination"> {!! $officialreceipt->render() !!} </div>
    </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
