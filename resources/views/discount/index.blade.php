@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif discount
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		@include('_includes.message')
		
		<h1>
			Discount <a href="{{ url('/discount/create') }}" class="btn btn-primary btn-xs" title="Add New Discount"><i class="fa fa-plus" aria-hidden="true"></i></a>
		</h1>
		
	</section>
	
    <!-- Main content -->
	<section class="content">
	
	<div class="table">
		<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th>S.No</th><th> Name </th><th> Rate </th><th> Chart Account </th><th class="actions">Actions</th>
				</tr>
			</thead>
			<tbody>
			{{-- */$x=0;/* --}}
			@foreach($discount as $item)
				{{-- */$x++;/* --}}
				<tr>
					<td>{{ $x }}</td>
					<td>{{ $item->name }}</td><td>{{ $item->rate }}</td><td>@if($item->chart_account){{ $item->chart_account->name }}@endif</td>
					<td>
						<a href="{{ url('/discount/' . $item->id) }}" class="btn btn-success btn-xs" title="View Discount"><i class="fa fa-eye"></i></a>
						<a href="{{ url('/discount/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Discount"><i class="fa fa-pencil"></i></a>
						{!! Form::open([
							'method'=>'DELETE',
							'url' => ['/discount', $item->id],
							'style' => 'display:inline'
						]) !!}
							{!! Form::button('<i class="fa fa-trash"></i>', array(
									'type' => 'submit',
									'class' => 'btn btn-danger btn-xs',
									'title' => 'Delete Discount',
									'onclick'=>'return confirm("Confirm delete?")'
							));!!}
						{!! Form::close() !!}
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<div class="pagination"> {!! $discount->render() !!} </div>
	</div>

	</section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
