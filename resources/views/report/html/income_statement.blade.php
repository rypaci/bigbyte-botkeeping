<style>
h3.report-title { margin-bottom: 20px; }
th.balance { width: 140px; }
.table td.account-name { padding-left: 20px; }
@if(Request::input('action') == 'pdf')
table thead th { background-color: #f5f5f5; }
.text-bold { font-weight: bold; }
.text-right { text-align: right; }
table.no-border tr th, table.no-border tr td { border-top: 1px solid #fff; }
@endif
</style>

@if(Request::input('action') == 'html')
<div class="text-right" style="padding-top: 20px;"><a href="{{ $pdf_link }}" class="btn btn-default btn-pdf"><i class="fa fa-file-pdf-o text-danger"></i> PDF</a></div>
@endif

<h3 class="report-title text-center">{{ $company }}<br>
Income Statement<br>
From {{ $dates['from'] }} to {{ $dates['to'] }}</h3>

<table class="table no-border table-hover">
    <tbody>
    @foreach($is as $at_name => $at)
      <?php if($at_name == '') $at_name = '&nbsp;'; ?>
      <tr class="account-type text-bold"><td colspan="3">{{ $at_name }}</td></tr>
        @foreach($at as $sat_name => $sat)
          {{-- Do not display sub account type Name for all COA revenues --}}
          @if(strtolower($at_name) != 'revenue')
          <tr class="sub-account-type"><td colspan="3">{{ $sat_name }}</td></tr>
          @endif
          @foreach($sat as $r)
            <tr>
              <td class="account-name"> {{ $r->level1_coa }} </td>
              <td class="text-right"> {{ number_format($r->balance, 2, '.', ',') }} </td>
            </tr>
          @endforeach
        @endforeach
      <tr class="total-row account-type text-bold">
        <td> Total {{ ucwords( strtolower($at_name) ) }} </td>
        <td class="text-right" style="border-top:1px solid #999"> {{ number_format($totals[$at_name]['--total--']['balance'], 2, '.', ',') }} </td>
      </tr>
      <tr><td colspan="3"> &nbsp; </td></tr>
    @endforeach
      <?php
      // $rev_exp['balance'] is Net Income Before Tax
      // Net Income Before Tax = revenue - expenses
      $rev_exp['balance']  = isset($totals[ $rev_exp['rev_label'] ]) ? $totals[ $rev_exp['rev_label'] ]['--total--']['balance'] : 0;
      $rev_exp['balance'] -= isset($totals[ $rev_exp['exp_label'] ]) ? $totals[ $rev_exp['exp_label'] ]['--total--']['balance'] : 0;
      ?>
      <tr class="total-row text-bold">
        <td style="border-top:1px solid"> Net Income Before Tax </td>
        <td class="text-right" style="border-top:1px solid"> {{ number_format($rev_exp['balance'], 2, '.', ',') }} </td>
      </tr>
    </tbody>
</table>
