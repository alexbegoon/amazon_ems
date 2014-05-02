<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dashboard controller
 *
 * @author SancheZZ
 */
class Dashboard extends CI_Controller {
    
    public function __construct()
    {
         parent::__construct();
         
         // Authorization check
         if (!$this->ion_auth->logged_in())
         {
            redirect('auth/login');
         }
         
        // Load model
        $this->load->model('dashboard/dashboard_model');
        $this->load->model('incomes/web_field_model');
    }
       
    public function index($page = null)
    {   
        {
            redirect('dashboard/page');
        }
    }
    
    
    public function page ($page = 0) 
    {
        $post_data = $this->input->post();
        
        $data = array();
        $data['title'] = 'Dashboard';
        
        $data['orders'] = $this->dashboard_model->getOrders($page);
        $data['total_orders'] = $this->dashboard_model->countOrders();
        $data['web_fields_list'] = $this->web_field_model->get_web_fields_list($post_data['filter']['web'], 'filter[web]', 'id="combobox"');
        $data['providers_list'] = $this->providers_model->get_providers_list($post_data['provider'], false, true, 'id="combobox4"');
        
        // Pagination
        
        $config['base_url'] = base_url().'index.php/dashboard/page/';
        $config['total_rows'] = $this->dashboard_model->countOrders();
        $config['per_page'] = 50; 

        $this->pagination->initialize($config); 
        $data['pagination'] = $this->pagination->create_links();
        
        // Load view  
        $this->load->template('dashboard/default', $data);
    }
    
    public function edit ($id) 
    {
        $data = array();
        
        $data['order']  = $this->dashboard_model->getOrder($id);
        $data['info']   = $this->dashboard_model->get_order_detail_info((int)$id);
        
        $this->load->view('dashboard/edit', $data);
    }
    
    public function save () 
    {
        $post_data = $this->input->post();
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('correo', 'Correo', 'valid_email|xss_clean');
        $this->form_validation->set_rules('comentarios', 'Comenatarios', 'xss_clean');
        $this->form_validation->set_rules('tracking', 'Tracking', 'xss_clean');
        $this->form_validation->set_rules('procesado', 'Processado', 'required|xss_clean');
        $this->form_validation->set_rules('id', 'Id', 'integer|xss_clean');
        $this->form_validation->set_rules('sku1', 'SKU', 'required|xss_clean');
        $this->form_validation->set_rules('precio1', 'Price', 'required|numeric|xss_clean');
        $this->form_validation->set_rules('cantidad1', 'Quantity', 'required|is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('fechaentrada', 'Fechaentrada', 'required|xss_clean');
        $this->form_validation->set_rules('telefono', 'Telefono', 'required|xss_clean');
        $this->form_validation->set_rules('codigopostal', 'Codigopostal', 'required|xss_clean');
        $this->form_validation->set_rules('pais', 'Pais', 'required|xss_clean');
        $this->form_validation->set_rules('precio2', 'Price2', 'numeric|xss_clean');
        $this->form_validation->set_rules('cantidad2', 'Quantity2', 'is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('precio3', 'Price3', 'numeric|xss_clean');
        $this->form_validation->set_rules('cantidad3', 'Quantity3', 'is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('precio4', 'Price4', 'numeric|xss_clean');
        $this->form_validation->set_rules('cantidad4', 'Quantity4', 'is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('precio5', 'Price5', 'numeric|xss_clean');
        $this->form_validation->set_rules('cantidad5', 'Quantity5', 'is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('precio6', 'Price6', 'numeric|xss_clean');
        $this->form_validation->set_rules('cantidad6', 'Quantity6', 'is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('precio7', 'Price7', 'numeric|xss_clean');
        $this->form_validation->set_rules('cantidad7', 'Quantity7', 'is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('precio8', 'Price8', 'numeric|xss_clean');
        $this->form_validation->set_rules('cantidad8', 'Quantity8', 'is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('precio9', 'Price9', 'numeric|xss_clean');
        $this->form_validation->set_rules('cantidad9', 'Quantity9', 'is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('precio10', 'Price10', 'numeric|xss_clean');
        $this->form_validation->set_rules('cantidad10', 'Quantity10', 'is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('ingresos', 'Ingresos', 'numeric|xss_clean');
        $this->form_validation->set_rules('gasto', 'Gasto', 'numeric|xss_clean');
        
        if ($this->form_validation->run() == FALSE)
        {
            if(isset($post_data['id']))
            {
                $this->edit($post_data['id']); // Try to fill form one more
            }
            else 
            {
                $this->create_order();
            }  
        }
        else
        {
                $data['response'] = $this->dashboard_model->save();
                $this->load->view('dashboard/ajax_response', $data);
        }
        
    }
    
    public function get_order($id)
    {
        $data = array();
        
        $data['order'] = $this->dashboard_model->getOrder($id);
        
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data['order']));
    }
    
    public function get_order_for_printer($id)
    {
        $data = array();
        
        $data['order'] = $this->dashboard_model->get_order_for_printer($id);
        $data['method'] = humanize($this->router->method);
        $data['html_of_order'] = $this->load->view('dashboard/print_order', $data, true);
                
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    public function create_order()
    {
        $post_data = $this->input->post();
               
        $data = array();
        
        $data['order'] = $post_data;
                
        if(isset($post_data['pais']))
        {
            $data['pais_list'] = $this->dashboard_model->get_pais_list($post_data['pais']);
            $data['web_fields_list'] = $this->web_field_model->get_web_fields_list($post_data['web'], 'web', 'id="select_web"');
        }
        else 
        {
            $data['pais_list'] = $this->dashboard_model->get_pais_list();
            $data['web_fields_list'] = $this->web_field_model->get_web_fields_list(null, 'web', 'id="select_web"');
        }
        
        $this->load->view('dashboard/create_order', $data);
    }
    
    public function update_country_list($web)
    {
        $data = array();
        
        $data['country_list'] = $this->dashboard_model->get_available_coutries_to_ship($web);
        
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }
    
    public function get_available_shipping()
    {
        
        $post_data = $this->input->post();
        
        $data['shipping'] = $this->dashboard_model->get_available_shipping($post_data['country_code'], $post_data['web']);
        
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }
    
    public function cache_info()
    {
        $this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file')); 
        
        var_dump($this->cache->memcached->cache_info());
    }
}

