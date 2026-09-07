<style>
h3.report-title { margin-bottom: 20px; }
th.balance { width: 140px; }
@if(Request::input('action') == 'pdf')
table thead th { background-color: #f5f5f5; }
.text-bold { font-weight: bold; }
@endif
</style>

@if(Request::input('action') == 'html')
<div class="text-right" style="padding-top: 20px;"><a href="{{ $pdf_link }}" class="btn btn-default btn-pdf"><i class="fa fa-file-pdf-o text-danger"></i> PDF</a></div>
@endif

<h3 class="report-title text-center">{{ $company }}<br>
Accounts Payable Summary<br>
{{ $date }}</h3>

<table class="table no-border table-hover">
    <thead style="border-bottom: 1px solid">
        <tr>
            <th> Vendor </th><th class="balance text-right"> Balance </th>
        </tr>
    </thead>
    <tbody>
    @foreach($rows as $r)
        <?php $link = url('/report?vid='.$r->vendor_id.'&form=vendor-accounts-payable-detail'
                .'&from='.Request::input('from').
                '&to='.Request::input('to').
                '&action='.Request::input('action')); ?>
        <tr>
            <td>{{ $r->vendor }}</td>
            <td class="text-right">
        @if(Request::input('action') == 'pdf') 
            {{ $r->balance_formatted }}
        @else 
            <a href="{{ $link }}">{{ $r->balance_formatted }}</a>
        @endif
            </td>
        </tr>
    @endforeach
        <tr class="total-row text-bold">
          <td> TOTAL </td>
          <td class="text-right" @if(Request::input('action') == 'html')style="border-top:1px solid"@endif> {{ $total_balance }} </td>
        </tr>
    </tbody>
</table>
