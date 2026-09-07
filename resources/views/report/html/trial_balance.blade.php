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
Trial Balance<br>
From {{ $dates['from'] }} to {{ $dates['to'] }}</h3>

<table class="table no-border table-hover">
    <thead style="border-bottom: 1px solid">
        <tr>
            <th> </th>
            <th class="debit text-right"> Dr </th>
            <th class="credit text-right"> Cr </th>
        </tr>
    </thead>
    <tbody>
    @foreach($rows as $r)
        <tr>
            <td>{{ $r->coa_name }}</td>
            <td class="text-right">{{ $r->debit_formatted }}</td>
            <td class="text-right">{{ $r->credit_formatted }}</td>
        </tr>
    @endforeach
        <tr class="total-row text-bold">
          <td> TOTAL </td>
          <td class="text-right" style="border-top:1px solid"> {{ $totals->debit_formatted }} </td>
          <td class="text-right" style="border-top:1px solid"> {{ $totals->credit_formatted }} </td>
        </tr>
    </tbody>
</table>
