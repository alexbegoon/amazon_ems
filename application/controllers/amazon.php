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
         
         // Load models
        $this->load->model('amazon/amazon_model');
        $this->load->model('incomes/providers_model');
        $this->load->model('incomes/web_field_model');
        $this->load->model('incomes/exchange_rates_model');
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
                
        $data['logs'] = $this->amazon_model->get_all_info($page);
        
        $config['base_url'] = base_url().'index.php/amazon/log/';
        $config['total_rows'] = $this->amazon_model->get_total_count();
        $config['per_page'] = 50; 

        $this->pagination->initialize($config); 
        $data['pagination'] = $this->pagination->create_links();
            
        $this->load->template('amazon/'.$this->router->method, $data);
    }
    
    public function price_rules($page = 0)
    {
        // Prepare data
        $data['title'] = humanize($this->router->method);
        
        $data['price_rules'] = $this->amazon_model->get_all_price_rules($page);
        
        $config['base_url'] = base_url().'index.php/amazon/price_rules/';
        $config['total_rows'] = $this->amazon_model->get_total_count_of_rules();
        $config['per_page'] = 50; 

        $this->pagination->initialize($config); 
        $data['pagination'] = $this->pagination->create_links();
        
        
        // Load view  
        $this->load->template('amazon/'.$this->router->method, $data);
        
    }
    
    public function add_price_rule()
    {
        // Prepare data
        $data['title']          = humanize($this->router->method);
        $data['providers_list'] = $this->providers_model->get_providers_list(null);
        $data['web_list']       = $this->web_field_model->get_web_fields_list();
        $data['currency_list']  = $this->exchange_rates_model->getCurrenciesList();
        $data['errors']         = null;
                
        $this->load->view('amazon/'.$this->router->method, $data);
    }
    
    public function edit_price_rule()
    {
        // Prepare data
        $post_data = $this->input->post();
        
        $rule = $this->amazon_model->get_price_rule($post_data['id']);
        
        $data['title']          = humanize($this->router->method);
        $data['providers_list'] = $this->providers_model->get_providers_list($rule->provider_id);
        $data['web_list']       = $this->web_field_model->get_web_fields_list($rule->web);
        $data['currency_list']  = $this->exchange_rates_model->getCurrenciesList($rule->currency_id);
        $data['errors']         = null;
        $data['post_data']      = (array)$rule;
                
        $this->load->view('amazon/'.$this->router->method, $data);
    }
    
    public function save_rule()
    {
        $this->load->library('form_validation');
        
        $data = array();
        $post_data = $this->input->post();
        
        $data['post_data'] = $post_data;
        $data['errors'] = null;
        $data['action'] = base_url().'index.php/amazon/'.$this->router->method.'/';
        
        $config = array(
               array(
                     'field'   => 'provider', 
                     'label'   => 'Provider', 
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'web', 
                     'label'   => 'Website', 
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'currency_id', 
                     'label'   => 'Currency', 
                     'rules'   => 'required|numeric'
                  ),   
               array(
                     'field'   => 'multiply', 
                     'label'   => 'Profit Margin', 
                     'rules'   => 'required|numeric'
                  ),   
               array(
                     'field'   => 'sum', 
                     'label'   => 'Extra Margin', 
                     'rules'   => 'required|numeric'
                  ),   
               array(
                     'field'   => 'transport', 
                     'label'   => 'Transport Margin', 
                     'rules'   => 'required|numeric'
                  ),   
               array(
                     'field'   => 'marketplace', 
                     'label'   => 'Marketplace Margin', 
                     'rules'   => 'required|numeric'
                  ),   
               array(
                     'field'   => 'tax', 
                     'label'   => 'Taxes', 
                     'rules'   => 'required|numeric'
                  )   
            );
        
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() == FALSE)
        {
                $data['providers_list'] = $this->providers_model->get_providers_list($post_data['provider']);
                $data['web_list']       = $this->web_field_model->get_web_fields_list($post_data['web']);
                $data['currency_list']  = $this->exchange_rates_model->getCurrenciesList($post_data['currency_id']);
                $this->load->view('amazon/edit_price_rule' ,$data);  
        }
        else
        {
                $data['response'] = $this->amazon_model->save_rule($post_data);
                $this->load->view('amazon/ajax_response', $data);
        }
    }
    
    public function delete_price_rule()
    {
        $post_data = $this->input->post();
        
        $data['response'] = $this->amazon_model->delete_rule($post_data['id']);
        
        $this->load->view('amazon/ajax_response', $data);
    }
    
    public function get_price_rule($id)
    {
        $data['price_rule'] = $this->amazon_model->get_price_rule($id);
        
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data['price_rule']));
    }
    
    public function sales_rank($page = 0)
    {
        // Prepare data
        $data['title'] = humanize($this->router->method);
        
        $data['sales_rank'] = $this->amazon_model->get_sales_rank($page);
        
        // Load view  
        $this->load->template('amazon/'.$this->router->method, $data);
    }
}