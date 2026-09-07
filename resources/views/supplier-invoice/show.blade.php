@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif show
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		<h1>
			Supplier's Invoice {{ $supplierinvoice->id }}
		</h1>
		
	</section>

	<!-- Main content -->
	<section class="content">

	<div class="table-responsive">
		<table class="table table-bordered table-striped table-hover">
			<tbody>
				<tr>
					<th>ID.</th><td>{{ $supplierinvoice->id }}</td>
				</tr>
				<tr><th> Vendor </th><td> {{ $supplierinvoice->vendor_name }} </td></tr>
				<tr><th> Date </th><td> {{ $supplierinvoice->date }} </td></tr>
				<tr><th> Invoice Number </th><td> {{ $supplierinvoice->invoice_number }} </td></tr>
				<tr><th> Terms </th><td> {{ $supplierinvoice->terms }} </td></tr>
				{{-- {<tr><th> Amount Due </th><td> {{ $supplierinvoice->amount_due }} </td></tr> --}}
				<tr><th> Amount </th><td> {{ $supplierinvoice->amount }} </td></tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">
						<a href="{{ url('/supplier-invoice/' . $supplierinvoice->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Supplier's Invoice"><i class="fa fa-pencil"></i></a>
						{!! Form::open([
							'method'=>'DELETE',
							'url' => ['/supplier-invoice', $supplierinvoice->id],
							'style' => 'display:inline'
						]) !!}
							{!! Form::button('<i class="fa fa-trash"></i>', array(
									'type' => 'submit',
									'class' => 'btn btn-danger btn-xs',
									'title' => 'Delete Supplier\'s Invoice',
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
					<td>{{ $item->account_number }}</td>
					<td class="account_title">{{ $item->account_title }}</td>
					<td>{{ $supplierinvoice->invoice_number }}</td>
					<td>@if(floatval($item->debit)) {{ $item->debit }} @endif</td>
					<td>@if(floatval($item->credit)) {{ $item->credit }} @endif</td>
				</tr>
			@endforeach
				<tr class="total-row">
					<td colspan="3" class="text-right"> TOTAL </td>
					<td class="debit"> {{ $supplierinvoice->amount }} </td>
					<td class="credit"> {{ $supplierinvoice->amount }} </td>
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