@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif show
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        <h1>
            Credit Invoice ({{ $creditinvoice->id }})
        </h1>
        
    </section>

    <!-- Main content -->
    <section class="content">

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <tbody>
                <tr><th> ID. </th><td>{{ $creditinvoice->id }}</td></tr>
                <tr><th> Customer </th><td> {{ $creditinvoice->customer_name }} </td></tr>
                <tr><th> Date </th><td> {{ $creditinvoice->date }} </td></tr>
                <tr><th> Invoice Number </th><td> {{ $creditinvoice->invoice_number }} </td></tr>
                <tr><th> Amount Due </th><td> {{ $creditinvoice->amount_due }} </td></tr>
                <tr><th> Terms </th><td> {{ $term_options[ $creditinvoice->terms ] }} </td></tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <a href="{{ url('/credit-invoice/' . $creditinvoice->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Credit Invoice"><i class="fa fa-pencil"></i></a>
                        {!! Form::open([
                            'method'=>'DELETE',
                            'url' => ['/credit-invoice', $creditinvoice->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Credit Invoice',
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
            {{-- */$x=0;/* --}}
            @foreach($vouchers as $item)
                {{-- */$x++;/* --}}
                {{-- */$class = floatval($item->debit) ? 'debit' : (floatval($item->credit) ? 'credit' : '');/* --}}
                <tr class="{{ $class }}">
                    <td>@if($item->account){{ $item->account->code }}@endif</td>
                    <td class="account_title">@if($item->account){{ $item->account->name }}@endif</td>
                    <td>{{ $creditinvoice->invoice_number }}</td>
                    <td>@if(floatval($item->debit)) {{ $item->debit }} @endif</td>
                    <td>@if(floatval($item->credit)) {{ $item->credit }} @endif</td>
                </tr>
            @endforeach
                <tr class="total-row">
                    <td colspan="3" class="text-right"> TOTAL </td>
                    <td class="debit"> {{ $creditinvoice->amount_due }} </td>
                    <td class="credit"> {{ $creditinvoice->amount_due }} </td>
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