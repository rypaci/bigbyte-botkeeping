@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		@include('_includes.message')
		
		<h1>
			SubAccountType <a href="{{ url('/sub-account-type/create') }}" class="btn btn-primary btn-xs" title="Add New SubAccountType"><i class="fa fa-plus" aria-hidden="true"></i></a>
		</h1>
		
	</section>
	
    <!-- Main content -->
	<section class="content">
	
	<div class="table">
		<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th>S.No</th><th> Main Account Type </th><th> Name / Sub Account Type </th><th> Description </th><th class="actions">Actions</th>
				</tr>
			</thead>
			<tbody>
			{{-- */$x=0;/* --}}
			@foreach($subaccounttype as $item)
				{{-- */$x++;/* --}}
				<tr>
					<td>{{ $x }}</td>
					<td>{{ $item->account_type->name }}</td><td>{{ $item->name }}</td><td>{{ $item->description }}</td>
					<td>
						<a href="{{ url('/sub-account-type/' . $item->id) }}" class="btn btn-success btn-xs" title="View SubAccountType"><i class="fa fa-eye"></i></a>
						<a href="{{ url('/sub-account-type/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit SubAccountType"><i class="fa fa-pencil"></i></a>
						{!! Form::open([
							'method'=>'DELETE',
							'url' => ['/sub-account-type', $item->id],
							'style' => 'display:inline'
						]) !!}
							{!! Form::button('<i class="fa fa-trash"></i>', array(
									'type' => 'submit',
									'class' => 'btn btn-danger btn-xs',
									'title' => 'Delete SubAccountType',
									'onclick'=>'return confirm("Confirm delete?")'
							));!!}
						{!! Form::close() !!}
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<div class="pagination"> {!! $subaccounttype->render() !!} </div>
	</div>

	</section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
