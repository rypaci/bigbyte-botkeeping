@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif show
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		<h1>
			Chart Account Type {{ $chartaccounttype->id }}
		</h1>
		
	</section>

	<!-- Main content -->
	<section class="content">

	<div class="table-responsive">
		<table class="table table-bordered table-striped table-hover">
			<tbody>
				<tr>
					<th>ID.</th><td>{{ $chartaccounttype->id }}</td>
				</tr>
				<tr><th> Min </th><td> {{ $chartaccounttype->min }} </td></tr><tr><th> Max </th><td> {{ $chartaccounttype->max }} </td></tr><tr><th> Name </th><td> {{ $chartaccounttype->name }} </td></tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">
						<a href="{{ url('/chart-account-type/' . $chartaccounttype->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Chart Account Type"><i class="fa fa-pencil"></i></a>
						{!! Form::open([
							'method'=>'DELETE',
							'url' => ['/chart-account-type', $chartaccounttype->id],
							'style' => 'display:inline'
						]) !!}
							{!! Form::button('<i class="fa fa-trash"></i>', array(
									'type' => 'submit',
									'class' => 'btn btn-danger btn-xs',
									'title' => 'Delete Chart Account Type',
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