@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif show discount
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		<h1>
			Discount {{ $discount->id }}
		</h1>
		
	</section>

	<!-- Main content -->
	<section class="content">

	<div class="table-responsive">
		<table class="table table-bordered table-striped table-hover">
			<tbody>
				<tr>
					<th>ID.</th><td>{{ $discount->id }}</td>
				</tr>
				<tr><th> Name </th><td> {{ $discount->name }} </td></tr>
				<tr><th> Rate </th><td> {{ $discount->rate }} </td></tr>
				<tr><th> Chart Account </th><td> @if($discount->chart_account){{ $discount->chart_account->name }}@endif </td></tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">
						<a href="{{ url('/discount/' . $discount->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Discount"><i class="fa fa-pencil"></i></a>
						{!! Form::open([
							'method'=>'DELETE',
							'url' => ['/discount', $discount->id],
							'style' => 'display:inline'
						]) !!}
							{!! Form::button('<i class="fa fa-trash"></i>', array(
									'type' => 'submit',
									'class' => 'btn btn-danger btn-xs',
									'title' => 'Delete Discount',
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