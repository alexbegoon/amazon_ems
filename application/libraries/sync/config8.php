<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Configuration file for http://www.tufarmaciaonline.net/
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Config8 {
    
    public $test_mode = false;
        
        
    // Start datetime 
    public $start_time      = '2013-08-13 00:00:00'; //Format: YYYY-MM-DD HH:MM:SS
    
    // Web field
    public $web             = 'TUFARMACIAONLINE'; 
    
    // Installed languages, this is may used in the cases of JOIN language tables
    public $languages       = array(
        
                        'es_es' => 'Spanish'
    );
    
}