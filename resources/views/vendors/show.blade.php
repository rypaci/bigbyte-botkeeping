@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif show
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		<h1>
			Vendor {{ $vendor->id }}
		</h1>
		
	</section>

	<!-- Main content -->
	<section class="content">

	<div class="table-responsive">
		<table class="table table-bordered table-striped table-hover">
			<tbody>
				<tr>
					<th>ID.</th><td>{{ $vendor->id }}</td>
				</tr>
				<tr><th> Individual? </th><td>@if($vendor->individual=='1') <span class="badge bg-green">Yes</span> @else <span class="badge bg-orange">No</span> @endif </td></tr>
				@if($vendor->individual=='1')
				<tr><th> Last Name </th><td> {{ $vendor->last_name }} </td></tr>
				<tr><th> First Name </th><td> {{ $vendor->first_name }} </td></tr>
				<tr><th> Middle Name </th><td> {{ $vendor->middle_name }} </td></tr>
				@else
				<tr><th> Company Name </th><td> {{ $vendor->company_name }} </td></tr>
				@endif
				<tr><th> Business Name </th><td> {{ $vendor->business_name }} </td></tr><tr><th> Business Address </th><td> {{ $vendor->business_address }} </td></tr>
				<tr><th> City </th><td> {{ $vendor->city }} </td></tr><tr><th> Country </th><td> {{ $vendor->country }} </td></tr>
				<tr><th> TIN </th><td> {{ $vendor->tin }} </td></tr><tr><th> Branch Code </th><td> {{ $vendor->branch_code }} </td></tr>
				<tr><th> Opening Bal. </th><td> {{ $vendor->opening_balance }} </td></tr><tr><th> Phone # </th><td> {{ $vendor->phone_number }} </td></tr>
				<tr><th> Fax </th><td> {{ $vendor->fax }} </td></tr><tr><th> Email </th><td> {{ $vendor->email }} </td></tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">
						<a href="{{ url('/vendor/' . $vendor->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Vendor"><i class="fa fa-pencil"></i></a>
						{!! Form::open([
							'method'=>'DELETE',
							'url' => ['/vendors', $vendor->id],
							'style' => 'display:inline'
						]) !!}
							{!! Form::button('<i class="fa fa-trash"></i>', array(
									'type' => 'submit',
									'class' => 'btn btn-danger btn-xs',
									'title' => 'Delete Vendor',
									'onclick'=>'return confirm("Confirm delete?")'
							)) !!}
						{!! Form::close() !!}
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	</section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection


@section('footer_script_preload')
@endsection