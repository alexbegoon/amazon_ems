<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Amazon controller
 *
 * @author      Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

class Amazon extends CI_Controller {
    
    public function __construct()
    {
         parent::__construct();
         
         // Authorization check
         if (!$this->ion_auth->logged_in())
         {
            redirect('auth/login');
         }
    }
    
    public function index()
    {   
        // Load view  
        $data['title'] = ucfirst($this->router->class);
        $this->load->template('amazon/'.$this->router->method, $data);
    }
}