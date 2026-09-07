@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Account <a href="{{ url('/chart-account/create') }}" class="btn btn-primary btn-xs" title="Add New Account"><i class="fa fa-plus" aria-hidden="true"></i></a>
        </h1>
        
    </section>
    
    <!-- Main content -->
    <section class="content">
    
    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <!-- <th>S.No</th><th> Name </th><th> Code </th><th> Level </th><th> Account Type </th><th class="actions">Actions</th> -->
                    <th>S.No</th><th> Main Account Type </th><th> Sub Account Type </th><th> Level 1 </th><th> Level 2 </th><th> Level 3 </th><th> Level 4 </th>
                    <th> Code </th><th> Current Balance </th><th> No. of Entries </th><th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            {{-- */$x=0;/* --}}
            @foreach($accounts as $item)
                @if($item->level > 0) {{-- */$x++;/* --}} @endif
                <tr>
                    <td>@if($item->level > 0) {{ $x }} @endif</td>
                    <td>@if($item->level == 0) {{ $item->name }} @endif</td>
                    <td>@if($item->level == -1) <b>{{ $item->name }}</b> @endif</td>
                    <td>@if($item->level == 1) {{ $item->name }} @endif</td>
                    <td>@if($item->level == 2) {{ $item->name }} @endif</td>
                    <td>@if($item->level == 3) {{ $item->name }} @endif</td>
                    <td>@if($item->level == 4) {{ $item->name }} @endif</td>
                    <td>@if($item->level == 0) <em>( {{ $item->code }} )</em> @else {{ $item->code }} @endif</td>
                    <td class="text-right">@if(number_between($item->level, 1, 4)) {{ number_format(floatval($item->current_balance), 2) }} @endif </td>
                    <td class="text-right">@if(number_between($item->level, 1, 4) && $item->num_entries > 0) {{ $item->num_entries }} @endif</td>
                    
                    <td>
                        @if($item->level > 0)
                        <a href="{{ url('/chart-account/' . $item->id) }}" class="btn btn-success btn-xs" title="View Account"><i class="fa fa-eye"></i></a>
                        <a href="{{ url('/chart-account/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Account"><i class="fa fa-pencil"></i></a>
                        {!! Form::open([
                            'method'=>'DELETE',
                            'url' => ['/chart-account', $item->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Account',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ));!!}
                        {!! Form::close() !!}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pagination"> {!! $chartaccount->render() !!} </div>
    </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
