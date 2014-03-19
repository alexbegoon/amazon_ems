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
         $this->load->model('incomes/providers_model');
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
        $post_data = $this->input->post();
        
        $this->bsc_model->store_checkboxes();
        
        if(isset($post_data['to_excel']))
        {
            if($post_data['to_excel'] == 1)
            {
                $this->bsc_model->export_to_excel();
            }
        }
        
        // Load data 
        $data['title'] = ucfirst($this->router->method);
        $data['overview']  = $this->bsc_model->get_overview($page);
        $data['period_radios']  = $this->bsc_model->get_radio_inputs_periods();
        $data['unique_products_count']  = $this->bsc_model->get_unique_products_count();
        
        if(isset($post_data['provider']))
        {
            $data['providers_list'] = $this->providers_model->get_providers_list($post_data['provider'],true,true);
        }
        else 
        {
            $data['providers_list'] = $this->providers_model->get_providers_list(null,true,true);
        }
        
        // Pagination
        
        $config['base_url'] = base_url().'index.php/bsc/overview/';
        $config['total_rows'] = $this->bsc_model->get_total_rows();
        $config['per_page'] = 50; 

        $this->pagination->initialize($config); 
        $data['pagination'] = $this->pagination->create_links();
        
        // Load view 
        $this->load->template('bsc/overview', $data);
    }
    
    public function update_product($id)
    {
        if($this->bsc_model->update_product($id))
        {
            $this->output->set_output('success');
        }
    }
    
    public function ssa($page = 0)
    {
        // Load data 
        $data['title'] = ucfirst($this->router->method);
        
        // Load view 
        $this->load->template('bsc/ssa', $data);
    }
}