@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		@include('_includes.message')
		
		<h1>
			Company <a href="{{ url('/company/create') }}" class="btn btn-primary btn-xs" title="Add New Company"><i class="fa fa-plus" aria-hidden="true"></i></a>
		</h1>
		
	</section>
	
    <!-- Main content -->
	<section class="content">
	
	<div class="table">
		<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th>S.No</th><th> Last Name </th><th> First Name </th><th> Middle Name </th><th class="actions">Actions</th>
				</tr>
			</thead>
			<tbody>
			{{-- */$x=0;/* --}}
			@foreach($company as $item)
				{{-- */$x++;/* --}}
				<tr>
					<td>{{ $x }}</td>
					<td>{{ $item->last_name }}</td><td>{{ $item->first_name }}</td><td>{{ $item->middle_name }}</td>
					<td>
						<a href="{{ url('/company/' . $item->id) }}" class="btn btn-success btn-xs" title="View Company"><i class="fa fa-eye"></i></a>
						<a href="{{ url('/company/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Company"><i class="fa fa-pencil"></i></a>
						{!! Form::open([
							'method'=>'DELETE',
							'url' => ['/company', $item->id],
							'style' => 'display:inline'
						]) !!}
							{!! Form::button('<i class="fa fa-trash"></i>', array(
									'type' => 'submit',
									'class' => 'btn btn-danger btn-xs',
									'title' => 'Delete Company',
									'onclick'=>'return confirm("Confirm delete?")'
							));!!}
						{!! Form::close() !!}
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<div class="pagination"> {!! $company->render() !!} </div>
	</div>

	</section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
