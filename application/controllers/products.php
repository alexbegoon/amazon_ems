<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Products
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Products extends CI_Controller
{
    function __construct()
    {
        parent::__construct();

        // Authorization check
        if (!$this->ion_auth->logged_in())
        {
           redirect('auth/login');
        }

        // Load model
        $this->load->model('products/products_model');
        $this->load->model('incomes/providers_model');
        
    }
    
    function index()
    {
        redirect('products/page');
    }
    
    function page($page = 0)
    {
        $post_data = $this->input->post();
        
        $data['title'] = humanize($this->router->class);
        
        if(isset($post_data['provider']))
        {
            $data['providers_list'] = $this->providers_model->get_providers_list($post_data['provider'],true,true);
        }
        else 
        {
            $data['providers_list'] = $this->providers_model->get_providers_list(null,true,true);
        }
        
        $data['products']              = $this->products_model->get_products($page);
        $data['providers_statistic']   = $this->products_model->get_providers_statistic();
        $data['total_products']        = $this->products_model->count_products();
        
        // Pagination
        
        $config['base_url'] = base_url().'index.php/products/page/';
        $config['total_rows'] = $this->products_model->count_products();
        $config['per_page'] = 50; 

        $this->pagination->initialize($config); 
        $data['pagination'] = $this->pagination->create_links();
        
        
        // Load view  
        $this->load->template('products/default', $data);
    }
    
    function show_provider_statistic($provider_name)
    {
        // Load view  
        $data = array();
        
        $data['provider_name']   = $provider_name;
        $data['provider_statistic_history']   = $this->products_model->get_provider_statistic_history($provider_name);
        
        $this->load->view('products/statistic', $data);
    }
}