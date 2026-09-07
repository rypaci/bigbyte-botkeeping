@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
	
		@include('_includes.message')
		
        <h1>
            Users <a href="{{ url('/user/create') }}" class="btn btn-primary btn-xs" title="Add New Hijack"><i class="fa fa-plus" aria-hidden="true"></i></a>
        </h1>

    </section>

    <!-- Main content -->
    <section class="content">

        <div class="table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>S.No</th><th> {{ trans('Name') }} </th><th> {{ trans('user.email') }} </th><th class="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                {{-- */$x=0;/* --}}
                @foreach($user as $item)
                    {{-- */$x++;/* --}}
                    <tr>
                        <td>{{ $x }}</td>
                        <td>{{ $item->name }}</td><td>{{ $item->email }}</td>
                        <td>
                            <a href="{{ url('/user/' . $item->id) }}" class="btn btn-success btn-xs" title="View user"><i class="fa fa-eye"></i></a>
                            <a href="{{ url('/user/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit user"><i class="fa fa-pencil"></i></a>
                            {!! Form::open([
                                'method'=>'DELETE',
                                'url' => ['/user', $item->id],
                                'style' => 'display:inline'
                            ]) !!}
                                {!! Form::button('<i class="fa fa-trash"></i>', array(
                                        'type' => 'submit',
                                        'class' => 'btn btn-danger btn-xs',
                                        'title' => 'Delete user',
                                        'onclick'=>'return confirm("Confirm delete?")'
                                ));!!}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination"> {!! $user->render() !!} </div>
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
