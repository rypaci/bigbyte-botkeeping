@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection





@section('content')

<div class="content-wrapper">

    <section class="content-header">

    

        @include('_includes.message')

        

        <div class="pull-left">

            <h1>List of Services <a href="{{ route('service-list.create') }}" class="btn btn-primary btn-xs" title="Add New Service"><i class="fa fa-plus" aria-hidden="true"></i></a></h1>

        </div>

        <div style="clear: both;"></div>

        

    </section>



    <!-- Main content -->

    <section class="content">

        <div class="table-responsive">

        <table class="table table-bordered table-striped table-hover">

            <thead>

                <tr>

                    <th>No</th>

                    <th>Services</th>

                    <th>Rate</th>

                    <th class="actions">Action</th>

                </tr>

            </thead>

            <tbody>

            {{-- */$x=0;/* --}}

            @foreach ($services as $key => $service)

                {{-- */$x++;/* --}}

                <tr>

                    <td>{{ $x }}</td>

                    <td>{{ $service->name }}</td>

                    <td>{{ $service->rate }}</td>

                    <td>

                        <a class="btn btn-success btn-xs" href="{{ route('service-list.show',$service->id) }}"><i class="fa fa-eye"></i></a>

                        <a class="btn btn-primary btn-xs" href="{{ route('service-list.edit',$service->id) }}"><i class="fa fa-pencil"></i></a>

                        {!! Form::open(['method' => 'DELETE','route' => ['service-list.destroy', $service->id],'style'=>'display:inline']) !!}

                        {!! Form::button('<i class="fa fa-trash"></i>', array(

                                        'type' => 'submit',

                                        'class' => 'btn btn-danger btn-xs',

                                        'title' => 'Delete Service',

                                        'onclick'=>'return confirm("Confirm delete?")'

                                ));!!}

                        {!! Form::close() !!}

                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>
        <div class="pagination"> {!! $services->render() !!} </div>
        </div>

    </section>

</div>

    

@endsection