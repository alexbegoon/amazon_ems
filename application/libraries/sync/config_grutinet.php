<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Configuration file for Grutinet sync process  
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Config_grutinet
{
    public $test_mode = false;
    
    // Data path
    public $data_url  = 'http://media.grutinet.com/ficheros/productos_xml_sin_dvd.xml';
    
    // Max execution time (secs)
    public $max_execution_time = 300;
        
    // Send emails to
    public $mail_to            = 'info@buyin.es'; 
    
    
    
    public function get_config_array()
    {
        return (array)$this;
    }
}