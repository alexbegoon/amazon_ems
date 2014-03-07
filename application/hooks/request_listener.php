<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Listen the requests and make some actions with the same
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Request_listener extends CI_Model{
    
    // Instance od Codeigniter
    private $CI;

    public function __construct()
    {
        parent::__construct();
        
        // Instance od Codeigniter
        $this->CI =& get_instance();
    }
    
    public function store_last_request()
    {
        $index = $this->CI->router->class.'_'.$this->CI->router->method;
        
        $request[$index]['order_by'] = $this->CI->input->post('order_by');
        $request[$index]['order_option'] = $this->CI->input->post('order_option');
        
        if($request[$index]['order_option'] && $request[$index]['order_option'])
        {
            $this->CI->session->set_userdata($request);
        }
    }
}