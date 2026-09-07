<?php

/**  
* to be used in menu to generate 'active' class
*
* @param $search is a string
* @param $subject can either be a string or an array
*/
function add_active_class( $search, $subject ) {
    
    if( !$subject || !$search ) return '';
    
    if( is_array($subject) && in_array($search, $subject) )
        return 'active';
    else if( $search == $subject ) 
        return 'active';
    
    return '';
    
}

function __checked_selected_helper( $helper, $current, $echo, $type ) {
    if ( (string) $helper === (string) $current )
        $result = " $type='$type'";
    else
        $result = '';

    if ( $echo )
        echo $result;

    return $result;
}

function disabled( $disabled, $current = true, $echo = true ) {
    return __checked_selected_helper( $disabled, $current, $echo, 'disabled' );
}

function selected( $selected, $current = true, $echo = true ) {
    return __checked_selected_helper( $selected, $current, $echo, 'selected' );
}

function checked( $checked, $current = true, $echo = true ) {
    return __checked_selected_helper( $checked, $current, $echo, 'checked' );
}

function number_between($number, $min, $max) {
    return ($min <= $number && $number <= $max );
}

/* 
 * Example:
 * $options = [
 *   '' => ' SELECT ',
 *   1 => (object) ['name' => 'Chart Account 1', 'tax_id' => '1234', 'code' => 's0000'],
 *   9 => (object) ['name' => 'Account Receivable', 'tax_id' => '2345', 'code' => 's0003'],
 *   12 => (object) ['name' => 'Account Payable', 'tax_id' => '3456', 'code' => 's000w'],
 * ];
 * $html = custom_form_select('coa_debit', 12, ['class' => 'form-control another-class', 'id' => 'customed-input'], $options, ['tax_id', 'code'], false);
 * echo $html; die;
 */
function custom_form_select( $name, $value = '', $attr = [], $options = [], $data_fields = [], $echo = true ) {
    $attr = (is_array($attr) && count($attr)) ? $attr : false;
    $options = (is_array($options) && count($options)) ? $options : false;
    $data_fields = (is_array($data_fields) && count($data_fields)) ? $data_fields : false;
    
    $attribs = '';
    if($attr) {
        foreach($attr as $k => $v) {
            $attribs .= " $k=\"$v\"";
        }
    }
    
    $select_options = '';
    if($options) {
        foreach($options as $k => $obj) {
            
            $label = $option_data_fields = '';
            
            if(is_object($obj)) {
                $label = (isset($obj->name)) ? $obj->name : '';
                
                if($data_fields) {
                    foreach($data_fields as $dfield) {
                        $dvalue = (isset($obj->{$dfield})) ? $obj->{$dfield} : '';
                        if(!$dvalue) continue;
                        
                        $option_data_fields .= " data-$dfield=\"$dvalue\"";
                    }
                }
            }
            else {
                $label = $obj;
            }
            
            $selected = selected( $value, $k, false );
            $select_options .= "<option value=\"$k\"$option_data_fields$selected>$label</option>";
        }
    }
    
    $html = '<select name="' . $name . '"' . $attribs . '>';
    $html .= $select_options;
    $html .= '</select>';
    
    if ( $echo )
        echo $html;

    return $html;
}

