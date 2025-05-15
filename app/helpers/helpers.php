<?php

if(!function_exists('myP')){
    function myP($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}

if(!function_exists('myDate')){
    function myDate($date, $format)
    {
        $myDate=date($format, strtotime($date));
        return $myDate;
    }
}

if(!function_exists('formatDecimal')){
    function formatDecimal($number) {
        if ($number < 10) {
            
            return number_format($number, 2);
        } elseif ($number < 100) {
            
            return number_format($number, 1);
        } else {
            
            return number_format($number, 0);
        }
    }
}
