<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * String helper
 * 
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */


if ( ! function_exists('getInnerSubstring'))
{
    function getInnerSubstring($string,$delim)
    {
        if(strpos($string, $delim) === false)
        return $string;
        // "foo a foo" becomes: array(""," a ","")
        $result_string = explode($delim, $string, 3); // also, we only need 2 items at most
        // we check whether the 2nd is set and return it, otherwise we return an empty string
        if(!isset($result_string[2]))
        return $string;
        
        return isset($result_string[1]) ? $result_string[1] : '';
    }
}