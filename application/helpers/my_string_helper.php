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

if ( ! function_exists('base64_url_encode'))
{
    function base64_url_encode($input) 
    {
        return rtrim(strtr(base64_encode($input), '+/', '-_'), '='); 
    }
}

if ( ! function_exists('base64_url_decode'))
{
    function base64_url_decode($input) 
    {
        return base64_decode(str_pad(strtr($input, '-_', '+/'), strlen($input) % 4, '=', STR_PAD_RIGHT));
    }
}