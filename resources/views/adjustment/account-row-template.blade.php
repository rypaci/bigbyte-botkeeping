<?php 
    if(!isset($entry_type)) $entry_type = 'debit';
    if(!isset($account_row_class)) $account_row_class = 'main';
    if(!isset($parent_name)) $parent_name = 'vouchers[{num}]';
    if(!isset($key)) $key = 'v_{num}';

    $value = (object)[];
    $value->rate = 0;
    $value->code = '';
    $value->coa_id = '';
    $value->account_name = '';
    $value->tax_id = 0;
    $value->discount_id = 0;
    $value->ref_number = '';
    $value->debit = '0.00';
    $value->credit = '0.00';

    $value->id = '0';

    if(!empty($voucher)) {
        if( isset($voucher->ref_number) ) $value->ref_number = $voucher->ref_number;
        if( isset($voucher->debit) ) $value->debit = $voucher->debit;
        if( isset($voucher->credit) ) $value->credit = $voucher->credit;
        if( isset($voucher->tax_id) ) $value->tax_id = $voucher->tax_id;
        if( isset($voucher->discount_id) ) $value->discount_id = $voucher->discount_id;
        if( isset($voucher->rate) ) $value->rate = $voucher->rate;
        if( isset($voucher->account) ) {
            $value->code = $voucher->account->code;
            $value->coa_id = $voucher->chart_account_id;
            $value->account_name = $voucher->account->name;
        }
        if( isset($voucher->id) ) $value->id = $voucher->id;
    }

    $force_show = false;
    if(strpos($account_row_class, 'optional') !== false) {
        if(floatval($value->debit) || floatval($value->credit))
            $force_show = true;
    }
    // print_r( $value ); echo '<br>';
?>
<tr class="account-row {{ $entry_type }} {{ $account_row_class }} {{ $key }}"@if($force_show) style="display:table-row" @endif data-key="{{ $key }}">
  <td class="actions">
    <i class="fa fa-close text-red"></i>
  </td>
  <td class="account-number"> 
    <input type="hidden" class="rate" value="{{ $value->rate }}" name="{{ $parent_name }}[rate]" />
    <input type="text" name="{{ $parent_name }}[code]" value="{{ $value->code }}" class="form-control input-sm {{ $entry_type }} code" readonly /> 
  </td>
  <td class="account-title"> 
    <i class="fa fa-cog"></i><input type="hidden" name="{{ $parent_name }}[chart_account_id]" class="chart_account_id" value="{{ $value->coa_id }}"><span class="account_name">{{ $value->account_name }}</span>
  </td>
  <td class="ref-number"> 
    <input type="hidden" class="tax_id" value="{{ $value->tax_id }}" name="{{ $parent_name }}[tax_id]" /> 
    <input type="hidden" class="discount_id" value="{{ $value->discount_id }}" name="{{ $parent_name }}[discount_id]" /> 
    <input type="text" name="{{ $parent_name }}[ref_number]" value="{{ $value->ref_number }}" class="form-control input-sm {{ $entry_type }} ref_number" readonly />
  </td>
@if(empty($voucher))
  <td class="debit">  <input type="text" name="{{ $parent_name }}[debit]" value="{{ number_format($value->debit, 2, '.', '') }}" class="form-control input-sm numbers-only debit_field" /> </td>
  <td class="credit"> <input type="text" name="{{ $parent_name }}[credit]" value="{{ number_format($value->credit, 2, '.', '') }}" class="form-control input-sm numbers-only credit_field" /> </td>
@elseif($entry_type == 'debit')
  <td class="debit">  <input type="text" name="{{ $parent_name }}[debit]" value="{{ number_format($value->debit, 2, '.', '') }}" class="form-control input-sm numbers-only debit_field" /> </td>
  <td class="credit"> <input type="hidden" name="{{ $parent_name }}[credit]" value="0" class="form-control input-sm numbers-only credit_field" /> </td>
@else($entry_type == 'credit')
  <td class="debit">  <input type="hidden" name="{{ $parent_name }}[debit]" value="0" class="form-control input-sm numbers-only debit_field" /> </td>
  <td class="credit"> <input type="text" name="{{ $parent_name }}[credit]" value="{{ number_format($value->credit, 2, '.', '') }}" class="form-control input-sm numbers-only credit_field" /> </td>
@endif
  <input type="hidden" value="{{ $key }}" name="{{ $parent_name }}[key]" class="key" />
  <input type="hidden" value="{{ $value->id }}" name="{{ $parent_name }}[id]" class="v_id" />
</tr>
