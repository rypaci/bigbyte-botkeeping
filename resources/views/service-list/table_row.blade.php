<?php 

/* Initialize some unset variables. */

if( !isset($item) ) $item = (object)[];

foreach(['service_id', 'name', 'amount'] as $field) {

    if( !isset($item->{ $field }) ) $item->{ $field } = '';

}

if( !isset($x) ) $x = '{num}';

?>

<tr class="serv-row">

  <td class="actions">

    <i class="fa fa-close text-red"></i>

  </td>

  <td class="name">

    <input type="hidden" name="services[{{$x}}][service_id]" class="service_id" value="{{ $item->service_id }}">

    <input type="text" name="services[{{$x}}][name]" class="name" readonly="" value="{{ $item->name }}">

  </td>

  <td class="subtotal"><input type="text" name="services[{{$x}}][amount]" class="price" value="{{ $item->amount }}"></td>

</tr>

