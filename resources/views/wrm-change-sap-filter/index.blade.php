@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('header_style_postload')
<style>
	.table th.individual { width: 90px; }
</style>
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		@include('_includes.message')
		
		<h1>
			Vendor <a href="{{ url('/vendor/create') }}" class="btn btn-primary btn-xs" title="Add New Vendor"><i class="fa fa-plus" aria-hidden="true"></i></a>
		</h1>
		
	</section>
	
    <!-- Main content -->
	<section class="content">
	
	<div class="table">
		<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<!-- <th>S.No</th><th> Last Name </th><th> First Name </th><th> Middle Name </th><th>Actions</th> -->
					<th>S.No</th><th> Name / Company </th><th class="individual"> Individual? </th><th class="actions">Actions</th>
				</tr>
			</thead>
			<tbody>
			{{-- */$x=0;/* --}}
			@foreach($vendor as $item)
				{{-- */$x++;/* --}}
				<tr>
					<td>{{ $x }}</td>
					<!-- <td>{{ $item->last_name }}</td><td>{{ $item->first_name }}</td><td>{{ $item->middle_name }}</td> --> 
					<td>{{ $item->fullname }}</td><td>@if($item->individual=='1') <span class="badge bg-green">Yes</span> @else <span class="badge bg-orange">No</span> @endif </td>
					<td>
						<a href="{{ url('/vendor/' . $item->id) }}" class="btn btn-success btn-xs" title="View Vendor"><i class="fa fa-eye"></i></a>
						<a href="{{ url('/vendor/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Vendor"><i class="fa fa-pencil"></i></a>
						{!! Form::open([
							'method'=>'DELETE',
							'url' => ['/vendor', $item->id],
							'style' => 'display:inline'
						]) !!}
							{!! Form::button('<i class="fa fa-trash"></i>', array(
									'type' => 'submit',
									'class' => 'btn btn-danger btn-xs',
									'title' => 'Delete Vendor',
									'onclick'=>'return confirm("Confirm delete?")'
							));!!}
						{!! Form::close() !!}
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<div class="pagination"> {!! $vendor->render() !!} </div>
	</div>

	</section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
