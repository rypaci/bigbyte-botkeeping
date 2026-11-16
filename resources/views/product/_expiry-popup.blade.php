{{-- */ $expiry_notices = \App\ProductBatch::expiringNotices(); /* --}}
@if (count($expiry_notices) > 0)
<div class="modal fade" id="expiryModal" tabindex="-1" role="dialog" aria-labelledby="expiryModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #f39c12; color: #fff;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="expiryModalLabel"><i class="fa fa-exclamation-triangle"></i> Expiry Warning</h4>
            </div>
            <div class="modal-body">
                <p>{{ count($expiry_notices) }} batch(es) are expired or will expire within {{ \App\ProductBatch::EXPIRY_WARNING_MONTHS }} months.</p>
                <div class="table-responsive">
                    <table class="table table-hover table-condensed">
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
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif
