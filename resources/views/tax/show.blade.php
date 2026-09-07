@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif show tax
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		<h1>
			Tax {{ $tax->id }}
		</h1>
		
	</section>

	<!-- Main content -->
	<section class="content">

	<div class="table-responsive">
		<table class="table table-bordered table-striped table-hover">
			<tbody>
				<tr>
					<th>ID.</th><td>{{ $tax->id }}</td>
				</tr>
				<tr><th> Name </th><td> {{ $tax->name }} </td></tr>
				<tr><th> Rate </th><td> {{ $tax->rate }} </td></tr>
				<tr><th> Chart Account </th><td> @if($tax->chart_account){{ $tax->chart_account->name }}@endif </td></tr>
				<tr><th> Type </th><td> {{ $tax->type }} </td></tr>
				<tr><th> Description </th><td> {{ $tax->description }} </td></tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">
						<a href="{{ url('/tax/' . $tax->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Tax"><i class="fa fa-pencil"></i></a>
						{!! Form::open([
							'method'=>'DELETE',
							'url' => ['/tax', $tax->id],
							'style' => 'display:inline'
						]) !!}
							{!! Form::button('<i class="fa fa-trash"></i>', array(
									'type' => 'submit',
									'class' => 'btn btn-danger btn-xs',
									'title' => 'Delete Tax',
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