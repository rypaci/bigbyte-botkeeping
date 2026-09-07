<?php 
/* Initialize some unset variables. */
if( !isset($item) ) $item = (object)[];
foreach(['product_id', 'name', 'qty', 'price', 'amount'] as $field) {
    if( !isset($item->{ $field }) ) $item->{ $field } = '';
}
if( !isset($x) ) $x = '{num}';
?>
<tr class="prod-row">
  <td class="actions">
    <i class="fa fa-close text-red"></i>
  </td>
  <td class="name">
    <input type="hidden" name="products[{{$x}}][product_id]" class="product_id" value="{{ $item->product_id }}"/>
    <input type="text" name="products[{{$x}}][name]" class="name" readonly value="{{ $item->name }}"/>
  </td>
  <td class="qty"><input type="text" name="products[{{$x}}][qty]" class="qty" readonly value="{{ $item->qty }}"/></td>
  <td class="price"><input type="text" name="products[{{$x}}][price]" class="price" value="{{ $item->price }}"/></td>
  <td class="subtotal"><input type="text" name="products[{{$x}}][amount]" class="subtotal" readonly value="{{ $item->amount }}"/></td>
</tr>
