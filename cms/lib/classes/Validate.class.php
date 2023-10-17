<?php

/* validation.php,
 *  functions for validation of different types of user input.
 *  for PHP version > 5.2.0
 * 
 * function list:
 *  phoneNumber($string_number [, $b_return])
 *  email($string [, $b_return])
 *  url($string [, $b_return])
 *  zipcode($string_number [, $b_return])
 *  keys($a_requiredFields, $a_suppliedFields [, $b_return])
 * 
 * Disclaimer:
 *  validation can not possibly know with 100% accuracy if
 *  the supplied data is in deed valid. It can only check
 *  to see if that data conforms with a set of predefined 
 *  definitions for a data-type.
 */

class Validate{


    public function phoneNumber($d_num, $return = false){
        if (!is_string($d_num) && !is_numeric($d_num))
            return false;
        $p_num = preg_replace('/[^0-9xX]/', '', $d_num);
        if(!preg_match('/^\d{10,15}([xX]\d{3,})?$/', $p_num))
            return false;
        else
            return ($return) ? $p_num : true;
    }

    public function email($s_email, $return = false){
        if (!is_string($s_email))
            return false;
        if (filter_var($s_email, FILTER_VALIDATE_EMAIL))
            return ($return) ? $s_email : true;
        else
            return false;
    }

    public function url($s_url, $return = false){
        if (!is_string($s_url))
            return false;
        if (filter_var($s_url, FILTER_VALIDATE_URL))
            return ($return) ? $s_url : true;
        else
            return false;
    }

    public function zipcode($ns_zip, $return = false){
        if (!is_string($ns_zip) && !is_int($ns_zip))
            return false;
        $zip = preg_replace('/[^0-9-]/', '', $ns_zip);
        if (preg_match('/^\d{5}(-\d{4})?$/', $zip))
            return ($return) ? $zip : true;
        else 
            return false;
    }

    public function keys ($a_required, $a_supplied, $return=false){
        if ($return) {
            $a_return = array();
            foreach ($a_required as $ark) {
                if(array_key_exists($ark, $a_supplied))
                    continue;
                $a_return[] = $ark;
            }
            return (count($a_return))?$a_return:true;
        } else {
            foreach ($a_required as $ark) {
                if(!array_key_exists($ark, $a_supplied))
                    return false;
            }
            return true;
        }
    }
}
?>
