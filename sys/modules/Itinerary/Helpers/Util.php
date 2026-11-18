<?php

if (!function_exists('t')) {
    function t($namespace, $key = null, $return = true) {
        if ($key == '') {
            $lang =  __('itinerary::common.undefined');
        } else {
            $index = "{$namespace}.{$key}";
            if (Lang::has($index)) {
                $lang =  __($index);
            } else {
                $lang = ucwords(str_replace("_", " ", $key));
            }
        }
        if ($return) {
            return $lang;
        } else {
            echo $lang;
            return $lang;
        }
    }
}

if (!function_exists('is_valid_json')) {
    function is_valid_json($string) {
        $obj = json_decode($string);
        return !json_last_error();
    }
}
if (!function_exists('str_to_dashed')) {
    function str_to_dashed($str) {
        $str = strtolower($str);
        $str = preg_replace('!\s+!', ' ', $str);
        $str = preg_replace('!\s!', '-', $str);
        return $str;
    }
}
