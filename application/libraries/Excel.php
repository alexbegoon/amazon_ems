<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of Excel
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

require_once APPPATH."/third_party/PHPExcel.php"; 

class Excel extends PHPExcel 
{ 
    public function __construct() 
    { 
        parent::__construct(); 
    } 
}