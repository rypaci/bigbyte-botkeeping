@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-3 margin-tb">
            <div class="pull-left">
                <h1>Product List <a href="{{ route('product.create') }}" class="btn btn-primary btn-xs" title="Add New Product"><i class="fa fa-plus" aria-hidden="true"></i></a></h1>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="row">
                <div class="col-md-6  align-right">
                    {!! Form::open(array('url' => 'product/search-product','method'=>'GET')) !!}
                    <div class="col-md-10 col-sm-12" style="padding-right: 0px;">
                        <input type="text" name="search_products" class="search-products form-control" placeholder="Search Products"/>
                    </div>
                    <div class="col-md-2 col-sm-12">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="col-md-6">
                    {!! Form::open(array('url' => 'product/search-product','method'=>'GET')) !!}
                    <div class="col-md-10 col-sm-12" style="padding-right: 0px;">
                        {!! Form::select('inventory_status', ['All'=>'All', 'Active'=>'Active', 'Inactive'=>'Inactive'], null, ['class' => 'form-control view-by-status']) !!}
                    </div>
                    <div class="col-md-2 col-sm-12">
                        <button type="submit" class="btn btn-primary">View Status</button>
                    </div>
                    {!! Form::close() !!}
                </div>
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
                    <th>Barcode</th>
                    <th>Product Name</th>
                    <th>Product Images</th>
                    <th>Price</th>
                    <th>Vendors/Supplier</th>
                    <th>Inventory Status</th>
                    <th class="actions">Action</th>
                </tr>
            @foreach ($products as $item)

            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $item->barcode }}</td>
                <td>{{ $item->name }}</td>
                <td>
                    <div id="image_preview">
                        @foreach($prod_images as $image)
                        <div class="image-division">
                            @if($image->foreign_id == $item->id)
                            <a href="{{url('product/image-delete', $image->id)}}" class="btn btn-danger btn-xs image" onclick="return confirm('Confirm delete?')" title="Delete Image"><i class="fa fa-times"></i></a>
                            <a href="{{url('product/edit-image', $image->id)}}" class="btn btn-primary btn-xs image" title="Edit Image"><i class="fa fa-pencil"></i></a>
                                <a href="#" class="pop">
                                    <img class="imageresource" src="{{ asset('public/uploads/images/'.$image->image_name) }}"/>
                                </a>
                            @endif
                        </div>
                        @endforeach
                        <a href="{{url('product/add-image', $item->id)}}" class="btn btn-success btn" title="Add Images"><i class="fa fa-plus"></i></a>
                        <!-- Creates the bootstrap modal where the image will appear -->
                        <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <h4 class="modal-title" id="myModalLabel">Image Preview</h4>
                                    </div>
                                    <div class="modal-body">
                                        <img src="" id="imagepreview" style="width: 100%;">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td align="right">{{ $item->price }}</td>
                <td>{{ $item->first_name }} {{ $item->middle_name }} {{ $item->last_name }}{{ $item->company_name }}</td>
                <td>{{ $item->inventory_status }}</td>
                <td>
                    <a class="btn btn-success btn-xs" href="{{ route('product.show', $item->id) }}"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-primary btn-xs" href="{{ route('product.edit', $item->id) }}"><i class="fa fa-pencil"></i></a>
                    <a class="btn btn-primary btn-xs" href="{{ url('product/details', $item->id) }}" title="Stock Details"><i class="fa fa-th-list"></i></a>
                    {!! Form::open(['method' => 'DELETE','route' => ['product.destroy', $item->id],'style'=>'display:inline']) !!}
                    {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Product',
                                    'onclick'=>'return confirm("Confirm delete?")'
                    )); !!}
                    {!! Form::close() !!}
                </td>
            </tr>
            @endforeach
            </table>
            <div class="row" style="margin-right: 0px; margin-left: 0px;">
                <div class="col-md-6">
                    <div class="pagination" style="margin:0;">{!! $products->appends(['inventory_status' => $inventory_status, 'search_products' => $search_products])->render(); !!}</div>
                </div>
                <div class="col-md-6" style="text-align: right;">
                    <?php /*<a href="{{ url('/inventory') }}" class="btn btn-primary">Go to stocks inventory</a>*/ ?>
                    <a href="{{ url('point-of-sale/inventory') }}" class="btn btn-primary">Go to POS stocks inventory</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@section('footer_script')
<script>
    $(".pop").on("click", function(e){
        e.preventDefault();
        $('#imagepreview').attr('src', $('.imageresource', this).attr('src')); // here asign the image to the modal when the user click the enlarge link
        $('#imagemodal').modal('show'); // imagemodal is the id attribute assigned to the bootstrap modal, then i use the show function
    });
</script>
<style type="text/css">
    ul.pagination{
        margin: 0px;
    }
    .table-responsive table{
        font-size: 14px;
    }
    div.image-division{
        display: inline-block;
    }
    a.image{
        position: relative;
    }
    a.pop{
        display: block;
        margin-top: -22px;
    }
    img.imageresource{
        width: 75px;
        height: 75px;
    }
    th.actions{
        width: 120px!important;
    }
</style>
@endsection