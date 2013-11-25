<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Engelsa controller
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

class Engelsa extends CI_Controller {
    
    public function __construct()
    {
         parent::__construct();
         
         // Authorization check
         if (!$this->ion_auth->logged_in())
         {
            redirect('auth/login');
         }
         
        // Load model
        $this->load->model('engelsa/engelsa_model');
    }
    
    public function index($page = null)
    {   
        {
            redirect('engelsa/page');
        }
    }
    
    public function page ($page = 0) 
    {
        
        $data = array();
        $data['title'] = 'Engelsa';
        
        $data['products']       = $this->engelsa_model->getProducts($page);
        $data['total_products'] = $this->engelsa_model->countProducts();
        $data['brand_options']  = $this->engelsa_model->getBrandOptions();
        
        // Pagination
        
        $config['base_url'] = base_url().'index.php/engelsa/page/';
        $config['total_rows'] = $this->engelsa_model->countProducts();
        $config['per_page'] = 50; 

        $this->pagination->initialize($config); 
        $data['pagination'] = $this->pagination->create_links();
        
        // Load view  
        $this->load->template('engelsa/default', $data);
    }
}