@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif show
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		<h1>
			Vendor {{ $vendor_info->id }}
		</h1>
		
	</section>

	<!-- Main content -->
	<section class="content">

	<div class="table-responsive">
		<table class="table table-bordered table-striped table-hover">
			<tbody>
				<tr style="background: #3984B3; color: #fff;"><td colspan="2" align="center"><strong>Vendor Information</strong></td></tr>
				<tr>
					<th>ID.</th><td>{{ $vendor_info->id }}</td>
				</tr>
				<tr><th> Individual? </th><td>@if($vendor_info->individual=='1') <span class="badge bg-green">Yes</span> @else <span class="badge bg-orange">No</span> @endif </td></tr>
				@if($vendor_info->individual=='1')
				<tr><th> Last Name </th><td> {{ $vendor_info->last_name }} </td></tr>
				<tr><th> First Name </th><td> {{ $vendor_info->first_name }} </td></tr>
				<tr><th> Middle Name </th><td> {{ $vendor_info->middle_name }} </td></tr>
				@else
				<tr><th> Company Name </th><td> {{ $vendor_info->company_name }} </td></tr>
				@endif
				<tr><th> Business Name </th><td> {{ $vendor_info->business_name }} </td></tr><tr><th> Business Address </th><td> {{ $vendor_info->business_address }} </td></tr>
				<tr><th> City </th><td> {{ $vendor_info->city }} </td></tr><tr><th> Country </th><td> {{ $vendor_info->country }} </td></tr>
				<tr><th> TIN </th><td> {{ $vendor_info->tin }} </td></tr><tr><th> Branch Code </th><td> {{ $vendor_info->branch_code }} </td></tr>
				<tr><th> Opening Bal. </th><td> {{ number_format($vendor_info->opening_balance, 2) }} </td></tr><tr><th> Phone # </th><td> {{ $vendor_info->phone_number }} </td></tr>
				<tr><th> Fax </th><td> {{ $vendor_info->fax }} </td></tr><tr><th> Email </th><td> {{ $vendor_info->email }} </td></tr>
			</tbody>
			<tfoot>
				<tr style="background: #3984B3; color: #fff;"><td colspan="2" align="center"><strong>Expenses Details</strong></td></tr>
				<tr><th> Date </th><td> {{ $vendor_expenses->date }}</td></tr>
				<tr><th> Invoice No. </th><td> {{ $vendor_expenses->invoice_no }}</td></tr>
				<tr><th> Terms </th><td> {{ $vendor_expenses->terms }}</td></tr>
				<tr><th> Period </th><td> {{ $vendor_expenses->period }}</td></tr>
				<tr><th> Amount </th><td> {{ number_format($vendor_expenses->amount, 2) }}</td></tr>
				<tr><th> Description </th><td> {{ $vendor_expenses->description }}</td></tr>
			</tfoot>
		</table>
	</div>
		<div class="row">
			<div class="col-sm-3">
				<a href="{{ url('wrmexpenses/tracks-expenses') }}" class="btn btn-primary">Back</a>
				<a href="{{ url('wrmexpenses/' . $vendor_expenses->id . '/edit') }}" class="btn btn-primary" title="Edit Expenses"><i class="fa fa-pencil"></i> Edit Expenses</a>
			</div>
		</div>
	</section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
@section('footer_script_preload')
@endsection
<style type="text/css">
th{
	width: 250px;
}
</style>