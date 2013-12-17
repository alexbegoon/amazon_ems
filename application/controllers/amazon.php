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
    
    /**
     * Show MWS transactions LOG
     */
    public function log($page=0)
    {
        $data['title'] = humanize($this->router->method);
        
        // Load models
        $this->load->model('amazon/amazon_model');
        
        $data['logs'] = $this->amazon_model->get_all_info($page);
        
        $config['base_url'] = base_url().'index.php/amazon/log/';
        $config['total_rows'] = $this->amazon_model->get_total_count();
        $config['per_page'] = 50; 

        $this->pagination->initialize($config); 
        $data['pagination'] = $this->pagination->create_links();
        
        $this->load->template('amazon/'.$this->router->method, $data);
    }
    
    
}