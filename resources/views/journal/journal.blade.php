@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('header_style_postload')
<style>
table.journal tfoot tr { font-weight: bold; }
</style>
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Journal
        </h1>
        
    </section>
    
    <!-- Main content -->
    <section class="content">
    
    @if(empty($vouchers) || count($vouchers) == 0)
    <br>
    <p class="lead text-center">Result not found</p>
    @else
    <div class="table">
        <table class="table table-bordered table-striped table-hover journal">
            <thead>
                <tr>
                    <th>Date</th><th> Payee/Customer/Vendor </th><th> Ref </th>
                    <th> Chart of Account </th><th> Debit </th><th> Credit </th>
                </tr>
            </thead>
            <tbody>
            @foreach($vouchers as $v)
                <tr>
                    <td>{{ $v->date }}</td><td>{{ $v->entity_name }}</td><td>{{ $v->ref_number }}</td>
                    <td>{{ $v->coa_code . ' -- ' . $v->coa_name }}</td>
                    <td class="text-right">{{ $v->debit_formatted }}</td>
                    <td class="text-right">{{ $v->credit_formatted }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td></td><td></td><td></td>
                    <td class="text-right">Total:</td>
                    <td class="text-right">{{ $total->debit_formatted }}</td>
                    <td class="text-right">{{ $total->credit_formatted }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
