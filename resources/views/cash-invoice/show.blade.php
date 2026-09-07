@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif show

@endsection





@section('content')

<div class="content-wrapper">



    <section class="content-header">

    

        <h1>

            Cash Invoice ({{ $cashinvoice->id }})

        </h1>

        

    </section>



    <!-- Main content -->

    <section class="content">



    <div class="table-responsive">

        <table class="table table-bordered table-striped table-hover">

            <tbody>

                <tr><th> ID. </th><td>{{ $cashinvoice->id }}</td></tr>

                <tr><th> Customer </th><td> {{ $cashinvoice->customer_name }} </td></tr>

                <tr><th> Date </th><td> {{ $cashinvoice->date }} </td></tr>

                <tr><th> Invoice Number </th><td> {{ $cashinvoice->invoice_number }} </td></tr>

                <tr><th> Amount </th><td> {{ $cashinvoice->amount }} </td></tr>

                <tr><th> Amount Due </th><td> {{ $cashinvoice->amount_due }} </td></tr>

            </tbody>

            <tfoot>

                <tr>

                    <td colspan="2">

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

                {{-- */$class = str_contains($item->key, 'debit') ? 'debit' : (str_contains($item->key, 'credit') ? 'credit' : '');/* --}}

                {{-- */$total->debit += floatval($item->debit)/* --}}

                {{-- */$total->credit += floatval($item->credit)/* --}}

                <tr class="{{ $class }}"@if($item->key=="tax_credit") style="display:none;" @endif>
                
                    <td>@if($item->account){{ $item->account->code }}@endif</td>

                    <td class="account_title">@if($item->account){{ $item->account->name }}@endif</td>

                    <td>{{ $cashinvoice->invoice_number }}</td>

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