@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('header_style_postload')
<style>
/* .content-header > h1 { margin-bottom: 20px; } */
</style>
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Ledger
            @if(is_array($vouchers) && count($vouchers)) 
            <div><small>{{ $vouchers[0]->coa_code . ' -- ' . $vouchers[0]->coa_name }}</small> </div> 
            @endif
        </h1>
        
    </section>
    
    <!-- Main content -->
    <section class="content">
    
    @if(empty($vouchers) || count($vouchers) == 0)
    <br>
    <p class="lead text-center">Result not found</p>
    @else
    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Date</th><th> Customer/Payee/Supplier </th><th> Ref </th>
                    <th> Memo </th><th> Debit </th><th> Credit </th><th> Balance </th>
                </tr>
            </thead>
            <tbody>
            @foreach($vouchers as $v)
                <tr>
                    <td>{{ $v->date }}</td><td>{{ $v->entity_name }}</td><td>{{ $v->ref_number }}</td>
                    <td>{{ $v->description }}</td>
                    <td class="text-right">{{ $v->debit_formatted }}</td>
                    <td class="text-right">{{ $v->credit_formatted }}</td>
                    <td class="text-right @if(0>$v->balance){{'text-red'}}@endif">{{ $v->balance_formatted }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
