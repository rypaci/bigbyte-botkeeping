{{-- */ $expiry_notices = \App\ProductBatch::expiringNotices(); $out_of_stock_notices = \App\Product::outOfStockNotices(); /* --}}
@if (count($expiry_notices) > 0 || count($out_of_stock_notices) > 0)
<div class="alert alert-warning alert-product expiry-notice">
    <div class="row">
        <div class="col-md-7">
            @if (count($expiry_notices) == 0)
            <p><strong><i class="fa fa-exclamation-triangle"></i> Expiry warning:</strong> No expired or expiring batches.</p>
            @else
            <p><strong><i class="fa fa-exclamation-triangle"></i> Expiry warning:</strong>
                {{ count($expiry_notices) }} batch(es) are expired or will expire within {{ \App\ProductBatch::EXPIRY_WARNING_MONTHS }} months.</p>
            <table class="table table-condensed" style="background: #fff; color: #333; margin: 10px 0 0; width: 100%;">
                <tr>
                    <th>Product</th>
                    <th>Batch</th>
                    <th style="text-align: right;">Remaining Qty</th>
                    <th>Expiry</th>
                    <th>Status</th>
                </tr>
                @foreach ($expiry_notices as $notice)
                <tr>
                    <td>{{ $notice['product_name'] }}</td>
                    <td>{{ $notice['batch_code'] }}</td>
                    <td align="right">{{ $notice['qty'] }}</td>
                    <td>{{ $notice['expiry_date'] }}</td>
                    <td>
                        @if ($notice['status'] == 'Expired')
                            <span class="label label-danger">Expired</span>
                        @else
                            <span class="label label-warning">Expires in {{ $notice['days_left'] }} day(s)</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
            @endif
        </div>
        <div class="col-md-5">
            @if (count($out_of_stock_notices) == 0)
            <p><strong><i class="fa fa-check-circle"></i> Out of stock:</strong> No Out of Stock</p>
            @else
            <p><strong><i class="fa fa-exclamation-triangle"></i> Out of stock:</strong>
                {{ count($out_of_stock_notices) }} product(s) are out of stock or at/below their stock threshold.</p>
            <div style="max-height: 220px; overflow-y: auto; margin: 10px 0 0;">
                <table class="table table-condensed" style="background: #fff; color: #333; margin: 0; width: 100%;">
                    <tr>
                        <th>Product</th>
                        <th style="text-align: right;">Remaining Qty</th>
                        <th style="text-align: right;">Threshold</th>
                        <th>Status</th>
                    </tr>
                    @foreach ($out_of_stock_notices as $notice)
                    <tr>
                        <td>{{ $notice['product_name'] }}</td>
                        <td align="right">{{ $notice['qty'] }}</td>
                        <td align="right">{{ $notice['stock_threshold'] }}</td>
                        <td>
                            @if ($notice['qty'] <= 0)
                                <span class="label label-danger">Out of stock</span>
                            @else
                                <span class="label label-warning">Low stock</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endif
