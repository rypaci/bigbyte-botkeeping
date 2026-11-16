{{-- */ $expiry_notices = \App\ProductBatch::expiringNotices(); /* --}}
@if (count($expiry_notices) > 0)
<div class="alert alert-warning alert-product expiry-notice">
    <p><strong><i class="fa fa-exclamation-triangle"></i> Expiry warning:</strong>
        {{ count($expiry_notices) }} batch(es) are expired or will expire within {{ \App\ProductBatch::EXPIRY_WARNING_MONTHS }} months.</p>
    <table class="table table-condensed" style="background: #fff; color: #333; margin: 10px 0 0; width: auto; min-width: 60%;">
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
@endif
