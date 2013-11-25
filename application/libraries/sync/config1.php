<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Configuration file for BUYIN.ES
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Config1 {
    
    public $test_mode = false;
    
    
    // Start datetime 
    public $start_time      = '2013-07-20 00:00:00'; //Format: YYYY-MM-DD HH:MM:SS
    
    // Web field
    public $web             = 'BUYIN';
    
    // Installed languages, this is may used in the cases of JOIN language tables
    public $languages       = array(
        
                        'de_de' => 'German',
                        'en_gb' => 'English',
                        'es_es' => 'Spanish',
                        'fr_fr' => 'French'
    );
}