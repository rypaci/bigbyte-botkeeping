@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif show
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		<h1>
			Account {{ $chartaccount->id }}
		</h1>
		
	</section>

	<!-- Main content -->
	<section class="content">

	<div class="table-responsive">
		<table class="table table-bordered table-striped table-hover">
			<tbody>
				<tr>
					<th>ID.</th><td>{{ $chartaccount->id }}</td>
				</tr>
				<tr><th> Name </th><td> {{ $chartaccount->name }} </td></tr><tr><th> Code </th><td> {{ $chartaccount->code }} </td></tr>
				<tr><th> Level </th><td> {{ $chartaccount->level }} </td></tr><tr><th> Account Type </th><td> {{ $chartaccount->account_type->name }} </td></tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">
						<a href="{{ url('/chart-account/' . $chartaccount->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Account"><i class="fa fa-pencil"></i></a>
						{!! Form::open([
							'method'=>'DELETE',
							'url' => ['/chart-account', $chartaccount->id],
							'style' => 'display:inline'
						]) !!}
							{!! Form::button('<i class="fa fa-trash"></i>', array(
									'type' => 'submit',
									'class' => 'btn btn-danger btn-xs',
									'title' => 'Delete Account',
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