<style>
h3.report-title { margin-bottom: 20px; }
th.balance { width: 140px; }
@if(Request::input('action') == 'pdf')
table thead th { background-color: #f5f5f5; }
.text-bold { font-weight: bold; }
.text-right { text-align: right; }
@endif
</style>

@if(Request::input('action') == 'html')
<div class="text-right" style="padding-top: 20px;"><a href="{{ $pdf_link }}" class="btn btn-default btn-pdf"><i class="fa fa-file-pdf-o text-danger"></i> PDF</a></div>
@endif

<h3 class="report-title text-center">{{ $company }}<br>
Customer Accounts Receivable Detail</h3>

<h4 class="text-bold">Customer: {{ $customer }}</h4>
<table class="table no-border table-hover">
    <thead style="border-bottom: 1px solid">
        <tr>
            <th> Date </th>
            <th> Invoice </th>
            <th> Reference </th>
            <th class="amount text-right"> Amount </th>
            <th class="balance text-right"> Balance </th>
        </tr>
    </thead>
    <tbody>
    @foreach($rows as $r)
        <?php $link = ($r->invoice == 'Credit Invoice') ? url('/credit-invoice/'.$r->ref_id) : url('/collection-receipt/'.$r->ref_id); ?>
        <tr>
            <td>{{ $r->date }}</td>
            <td>{{ $r->invoice }}</td>
            <td>{{ $r->reference }}</td>
            <td class="amount text-right">
        @if(Request::input('action') == 'pdf') 
            {{ $r->amount_formatted }}
        @else 
            <a href="{{ $link }}">{{ $r->amount_formatted }}</a>
        @endif
            </td>
            <td class="balance text-right">{{ $r->balance_formatted }}</td>
        </tr>
    @endforeach
        <tr class="total-row text-bold">
          <td colspan="3"> TOTAL </td>
          <td class="text-right" style="border-top:1px solid"> {{ $balance_formatted }} </td>
          <td class="text-right" style="border-top:1px solid"> {{ $balance_formatted }} </td>
        </tr>
    </tbody>
</table>
