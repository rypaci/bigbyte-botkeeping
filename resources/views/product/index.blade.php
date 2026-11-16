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

    @if (count($errors) > 0)
        <div class="alert alert-danger alert-product">
            <ul style="margin-bottom: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('product._expiry-popup')

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
                <td class="actions-cell">
                    <a class="btn btn-success btn-xs" href="{{ route('product.show', $item->id) }}"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-primary btn-xs" href="{{ route('product.edit', $item->id) }}"><i class="fa fa-pencil"></i></a>
                    <a class="btn btn-primary btn-xs" href="{{ url('product/details', $item->id) }}" title="Stock Details"><i class="fa fa-th-list"></i></a>
                    <button type="button" class="btn btn-success btn-xs btn-restock" data-id="{{ $item->id }}" title="Restock"><i class="fa fa-plus-square"></i></button>
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

<div class="modal fade" id="restockmodal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['url' => 'product/restock', 'method' => 'POST']) !!}
            <input type="hidden" name="pro_id" id="restock-pro-id" value=""/>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Restock Product</h4>
            </div>
            <div class="modal-body">
                <h4 id="restock-name"></h4>
                <p><strong>SKU:</strong> <span id="restock-sku"></span></p>
                <p><strong>Current Quantity:</strong> <span id="restock-current" style="font-size: 24px; font-weight: bold; margin-left: 5px;"></span></p>
                <div class="form-group">
                    <label for="restock-qty">Add Quantity</label>
                    <input type="number" min="1" step="1" name="added_qty" id="restock-qty" class="form-control" placeholder="0" required/>
                </div>
                <div class="form-group">
                    <label for="restock-expiry">Expiry Date</label>
                    <input type="date" name="expiry_date" id="restock-expiry" class="form-control"/>
                    <small class="text-muted">Leave blank if product has no expiry</small>
                </div>
                <div class="form-group">
                    <label for="restock-batch">Batch Code (optional)</label>
                    <input type="text" name="batch_code" id="restock-batch" maxlength="50" class="form-control" placeholder="Enter unique batch code or leave blank to auto-generate"/>
                </div>
                <div class="form-group">
                    <label for="restock-note">Batch Note (optional)</label>
                    <input type="text" name="batch_note" id="restock-note" maxlength="255" class="form-control" placeholder="e.g. Supplier lot #, delivery ref"/>
                </div>
                <h5><strong>Existing Batches (FEFO Order)</strong></h5>
                <table class="table table-condensed" id="restock-batches">
                    <thead>
                        <tr><th>Batch</th><th>Qty</th><th>Original</th><th>Expiry</th><th>Mfg Date</th><th>Status</th><th>Received</th></tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Restock</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
@section('footer_script')
<script>
    $('#expiryModal').modal('show');

    $(".btn-restock").on("click", function(){
        var id = $(this).data('id');
        $('#restock-pro-id').val(id);
        $('#restock-qty, #restock-expiry, #restock-batch, #restock-note').val('');
        $('#restock-batches tbody').html('<tr><td colspan="7">Loading...</td></tr>');
        $('#restockmodal').modal('show');

        $.getJSON('{{ url("product/restock") }}/' + id, function(data){
            $('#restock-name').text(data.name);
            $('#restock-sku').text(data.sku);
            $('#restock-current').text(data.current_qty);

            var labels = {'Good': 'label-success', 'Expiring Soon': 'label-warning', 'Expired': 'label-danger'};
            var $tbody = $('#restock-batches tbody').empty();
            if (!data.batches.length) {
                $tbody.html('<tr><td colspan="7" class="text-muted">No batches yet</td></tr>');
            }
            $.each(data.batches, function(i, b){
                var $tr = $('<tr/>');
                $.each([b.batch_code, b.qty, b.original_qty, b.expiry_date || '—', b.mfg_date || '—'], function(j, v){
                    $tr.append($('<td/>').text(v));
                });
                $tr.append($('<td/>').append($('<span class="label"/>').addClass(labels[b.status]).text(b.status)));
                $tr.append($('<td/>').text(b.received_date));
                $tbody.append($tr);
            });
        }).fail(function(){
            $('#restock-batches tbody').html('<tr><td colspan="7" class="text-danger">Unable to load product details.</td></tr>');
        });
    });

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
        width: 170px!important;
        white-space: nowrap;
    }
    td.actions-cell{
        white-space: nowrap;
    }
</style>
@endsection