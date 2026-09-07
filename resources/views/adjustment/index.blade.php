@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif <?php echo implode(' ', Request::segments()); ?>
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Adjusting Entries <a href="{{ url('/adjusting/create') }}" class="btn btn-primary btn-xs" title="Add New Adjusting Entry"><i class="fa fa-plus" aria-hidden="true"></i></a>
        </h1>
        
    </section>
    
    <!-- Main content -->
    <section class="content">
    
    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>S.No</th><th> Customer/Vendor </th><th> Adj Number </th><th> Date </th><th> Amount </th><th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            {{-- */$x=0;/* --}}
            @foreach($adjustment as $item)
                {{-- */$x++;/* --}}
                <tr>
                    <td>{{ $x }}</td>
                    <td>{{ $item->entity_name }}</td><td>{{ $item->adj_number }}</td><td>{{ $item->date }}</td><td>{{ $item->amount }}</td>
                    <td>
                        <a href="{{ url('/adjusting/' . $item->id) }}" class="btn btn-success btn-xs" title="View Adjusting Entry"><i class="fa fa-eye"></i></a>
                        <a href="{{ url('/adjusting/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Adjusting Entry"><i class="fa fa-pencil"></i></a>
                        {!! Form::open([
                            'method'=>'DELETE',
                            'url' => ['/adjusting', $item->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Adjusting Entry',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ));!!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pagination"> {!! $adjustment->render() !!} </div>
    </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
