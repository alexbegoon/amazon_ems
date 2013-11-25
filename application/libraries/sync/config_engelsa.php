<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Configuration file for Engelsa sync process  
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Config_engelsa {
    
    public $test_mode = false;
    
    // DB connection for AMAZONI DB with table engelsa
    public $host      = '213.165.69.85';
    public $port      = '';
    public $user      = 'amazoni';
    public $pass      = 'amazoni1985';
    public $db        = 'amazoni';
    public $prefix    = 'amazoni4';
    
    
    // Data path
    public $data_url  = 'http://mayoristas.engelsa.com/servicios/dropshipping.ashx?&usuario=buying&password=Byng895&operacion=listadoarticulos';
    
    // Max execution time (secs)
    public $max_execution_time = 300;
    
    // Start parse from N row
    public $start_from_row     = 2; 
    
    // Send emails to
    public $mail_to            = 'info@buyin.es'; 
    
    // Start datetime 
    // public $start_time      = '2013-08-19 00:00:00'; //Format: YYYY-MM-DD HH:MM:SS
    
}