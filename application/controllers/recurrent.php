<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Recurrent controller
 *
 * @author Alexander Begoon
 */
class Recurrent extends CI_Controller {
    
    public function __construct()
    {
         parent::__construct();
         
         // Authorization check
         if (!$this->ion_auth->logged_in())
         {
            redirect('auth/login');
         }
         
        // Load model
        $this->load->model('recurrent/recurrent_model');
    }
    
    public function index($page = null)
    {   
        {
            redirect('recurrent/page');
        }
    }
    
    public function page ($page = 0) 
    {
        
        $data = array();
        $data['title'] = 'Recurrent buyers';
        
        $data['recurrent_buyers'] = $this->recurrent_model->getRecurrentBuyers($page);
        
        $data['total_rows'] = $this->recurrent_model->countBuyers();
        
        $data['filter'] = $this->input->post("filter");
        
        // Pagination
        
        $config['base_url'] = base_url().'index.php/recurrent/page/';
        $config['total_rows'] = $this->recurrent_model->countBuyers();
        $config['per_page'] = 50; 

        $this->pagination->initialize($config); 
        $data['pagination'] = $this->pagination->create_links();
        
        // Load view  
        $this->load->template('recurrent/default', $data);
    }
    
    public function orders() 
    {
        $data = array();
        
        $data['email'] = $this->input->post("email");
        
        $data['orders'] = $this->recurrent_model->getOrders($data['email']);
        
        $this->load->view('recurrent/orders', $data);
    }
}

?>
