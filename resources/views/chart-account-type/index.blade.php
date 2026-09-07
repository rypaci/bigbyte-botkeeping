@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		@include('_includes.message')
		
		<h1>
			Chart Account Type <a href="{{ url('/chart-account-type/create') }}" class="btn btn-primary btn-xs" title="Add New Chart Account Type"><i class="fa fa-plus" aria-hidden="true"></i></a>
		</h1>
		
	</section>
	
    <!-- Main content -->
	<section class="content">
	
	<div class="table">
		<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th>S.No</th><th> Min </th><th> Max </th><th> Main Account Type </th><th> Sub Account Types </th><th class="actions">Actions</th>
				</tr>
			</thead>
			<tbody>
			{{-- */$x=0;/* --}}
			@foreach($chartaccounttype as $item)
				{{-- */$x++;/* --}}
				<tr>
					<td>{{ $x }}</td>
					<td>{{ $item->min }}</td><td>{{ $item->max }}</td><td>{{ $item->name }}</td>
					<td>@foreach($item->sub_account_types as $sat) {{ $sat->name }} <br> @endforeach </td>
					<td>
						<a href="{{ url('/chart-account-type/' . $item->id) }}" class="btn btn-success btn-xs" title="View Chart Account Type"><i class="fa fa-eye"></i></a>
						<a href="{{ url('/chart-account-type/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Chart Account Type"><i class="fa fa-pencil"></i></a>
						{!! Form::open([
							'method'=>'DELETE',
							'url' => ['/chart-account-type', $item->id],
							'style' => 'display:inline'
						]) !!}
							{!! Form::button('<i class="fa fa-trash"></i>', array(
									'type' => 'submit',
									'class' => 'btn btn-danger btn-xs',
									'title' => 'Delete Chart Account Type',
									'onclick'=>'return confirm("Confirm delete?")'
							));!!}
						{!! Form::close() !!}
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<div class="pagination"> {!! $chartaccounttype->render() !!} </div>
	</div>

	</section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
