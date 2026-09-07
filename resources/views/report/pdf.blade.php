@extends('layouts.pdf_tpl')


@section('content')
    <section class="">
        
        <h1>
            Report
        </h1>
        
    </section>
    
    <!-- Main content -->
    <section class="content">
    
    @if(empty($vouchers) || count($vouchers) == 0)
        <br>
        <p class="lead text-center">Result not found</p>
    @else
        @include('report.table')
    @endif

    </section><!-- /.content -->
@endsection
