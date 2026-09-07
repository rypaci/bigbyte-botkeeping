@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif show
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        <h1>
            Billing Invoice {{ $billinginvoice->id }}
        </h1>
        
    </section>

    <!-- Main content -->
    <section class="content">

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <tbody>
                <tr><th> ID. </th><td>{{ $billinginvoice->id }}</td></tr>
                <tr><th> Customer </th><td> {{ $billinginvoice->customer_name }} </td></tr>
                <tr><th> Date </th><td> {{ $billinginvoice->date }} </td></tr>
                <tr><th> BI No. </th><td> {{ $billinginvoice->invoice_number }} </td></tr>
                <tr><th> Amount </th><td> {{ $billinginvoice->amount }} </td></tr>
                <tr><th> Amount Due </th><td> {{ $billinginvoice->amount_due }} </td></tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <a href="{{ url('/billing-invoice/' . $billinginvoice->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Billing Invoice"><i class="fa fa-pencil"></i></a>
                        {!! Form::open([
                            'method'=>'DELETE',
                            'url' => ['/billing-invoice', $billinginvoice->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Billing Invoice',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            )) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Account Details</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="row">
                  <div class="col-md-12">
                  
    <div class="table">
        <table class="table table-bordered table-hover table-account-details">
            <thead>
                <tr>
                    <th> Account # </th><th> Account Title </th><th> Ref # </th><th> Debit </th><th> Credit </th>
                </tr>
            </thead>
            <tbody>
            {{-- */$x=0; $total=(object)['debit'=>0, 'credit'=>0];/* --}}
            @foreach($vouchers as $item)
                {{-- */$x++;/* --}}
                {{-- */$class = floatval($item->debit) ? 'debit' : (floatval($item->credit) ? 'credit' : '');/* --}}
                {{-- */$total->debit += floatval($item->debit)/* --}}
                {{-- */$total->credit += floatval($item->credit)/* --}}
                <tr class="{{ $class }}">
                    <td>@if($item->account){{ $item->account->code }}@endif</td>
                    <td class="account_title">@if($item->account){{ $item->account->name }}@endif</td>
                    <td>{{ $billinginvoice->invoice_number }}</td>
                    <td>@if($class == 'debit') {{ $item->debit }} @endif</td>
                    <td>@if($class == 'credit') {{ $item->credit }} @endif</td>
                </tr>
            @endforeach
                <tr class="total-row">
                    <td colspan="3" class="text-right"> TOTAL </td>
                    <td class="debit"> {{ number_format($total->debit, 2, '.', '') }} </td>
                    <td class="credit"> {{ number_format($total->credit, 2, '.', '') }} </td>
                </tr>
            </tbody>
        </table>
    </div>
    
                  </div>
              </div>
              <!-- /.row -->
            </div>
            <!-- ./box-body -->
          </div>
          
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection


@section('footer_script_preload')
@endsection