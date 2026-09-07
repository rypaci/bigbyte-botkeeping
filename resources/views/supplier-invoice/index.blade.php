@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		@include('_includes.message')
		
		<h1>
			Supplier's Invoice <a href="{{ url('/supplier-invoice/create') }}" class="btn btn-primary btn-xs" title="Add New Supplier's Invoice">CREATE</a>
		</h1>
		
	</section>
	
    <!-- Main content -->
	<section class="content">
	
	<div class="table">
		<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th>S.No</th><th> Vendor </th><th> Date </th><th> Invoice Number </th><th> Terms </th>{{-- <th> Amount Due </th> --}}<th> Amount </th><th class="actions">Actions</th>
				</tr>
			</thead>
			<tbody>
			{{-- */$x=0;/* --}}
			@foreach($supplierinvoice as $item)
				{{-- */$x++;/* --}}
				<tr>
					<td>{{ $x }}</td>
					<td>{{ $item->vendor_name }}</td><td>{{ $item->date }}</td><td>{{ $item->invoice_number }}</td><td>{{ $item->terms }}</td>{{-- <td>{{ $item->amount_due }}</td> --}}<td>{{ $item->amount }}</td>
					<td>
						<a href="{{ url('/supplier-invoice/' . $item->id) }}" class="btn btn-success btn-xs" title="View Supplier's Invoice"><i class="fa fa-eye"></i></a>
						<a href="{{ url('/supplier-invoice/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Supplier's Invoice"><i class="fa fa-pencil"></i></a>
						{!! Form::open([
							'method'=>'DELETE',
							'url' => ['/supplier-invoice', $item->id],
							'style' => 'display:inline'
						]) !!}
							{!! Form::button('<i class="fa fa-trash"></i>', array(
									'type' => 'submit',
									'class' => 'btn btn-danger btn-xs',
									'title' => 'Delete Supplier\'s Invoice',
									'onclick'=>'return confirm("Confirm delete?")'
							));!!}
						{!! Form::close() !!}
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<div class="pagination"> {!! $supplierinvoice->render() !!} </div>
	</div>

	</section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
