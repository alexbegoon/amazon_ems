<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * The BuyIn Shopping Center (BSC). 
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Bsc extends CI_Controller{
    
    public function __construct()
    {
         parent::__construct();
         
         // Authorization check
         if (!$this->ion_auth->logged_in())
         {
            redirect('auth/login');
         }
         
         // Only admin have access
         if (!$this->ion_auth->is_admin()) 
         {
             show_404();
             die;
         }
         
         // Load model
         $this->load->model('incomes/bsc_model');
    }
    
    public function index()
    {
        // Load data 
        $data['title'] = ucfirst($this->router->class);
        
        // Load view 
        $this->load->template('bsc/index', $data);
    }
    
    public function overview($page = 0)
    {
        // Load data 
        $data['title'] = ucfirst($this->router->method);
        $data['overview']  = $this->bsc_model->get_overview($page);
        $data['period_radios']  = $this->bsc_model->get_radio_inputs_periods();
        
        
        // Pagination
        
        $config['base_url'] = base_url().'index.php/bsc/overview/';
        $config['total_rows'] = $this->bsc_model->get_total_rows();
        $config['per_page'] = 50; 

        $this->pagination->initialize($config); 
        $data['pagination'] = $this->pagination->create_links();
        
        // Load view 
        $this->load->template('bsc/overview', $data);
    }
}