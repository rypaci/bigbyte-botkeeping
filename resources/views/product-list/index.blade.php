@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Product List <a href="{{ route('product-list.create') }}" class="btn btn-primary btn-xs" title="Add New Product"><i class="fa fa-plus" aria-hidden="true"></i></a></h1>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-product">
            <p>{{ $message }}</p>
        </div>
    @endif

    <section class="content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <tr>
                    <th>No</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th class="actions">Action</th>
                </tr>
            @foreach ($items as $key => $item)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->price }}</td>
                <td>
                    <a class="btn btn-success btn-xs" href="{{ route('product-list.show',$item->id) }}"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-primary btn-xs" href="{{ route('product-list.edit',$item->id) }}"><i class="fa fa-pencil"></i></a>
                    {!! Form::open(['method' => 'DELETE','route' => ['product-list.destroy', $item->id],'style'=>'display:inline']) !!}
                    {!! Form::button('<i class="fa fa-trash"></i>', array(

                                    'type' => 'submit',

                                    'class' => 'btn btn-danger btn-xs',

                                    'title' => 'Delete Product',

                                    'onclick'=>'return confirm("Confirm delete?")'
                            ));!!}
                    {!! Form::close() !!}
                </td>
            </tr>
            @endforeach
            </table>
        </div>
    </section>
</div>
    {!! $items->render() !!}

@endsection