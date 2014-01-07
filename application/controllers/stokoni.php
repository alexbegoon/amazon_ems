<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Stokoni controller
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

class Stokoni extends CI_Controller {
    
    public function __construct()
    {
         parent::__construct();
         
         // Authorization check
         if (!$this->ion_auth->logged_in())
         {
            redirect('auth/login');
         }
         
        // Load model
        $this->load->model('stokoni/stokoni_model');
        $this->load->model('incomes/providers_model');
        
        //Load classes
        $this->load->library('table');
    }
    
    public function index($page = null)
    {   
        {
            redirect('stokoni/page');
        }
    }
    
    public function page ($page = 0) 
    {
        $data = array();
        $data['title'] = humanize($this->router->class);
        
        $post_data = $this->input->post();
        
        $data['products']       = $this->stokoni_model->getProducts($page);
        $data['total_products'] = $this->stokoni_model->countProducts();
        $data['summary']        = $this->stokoni_model->getSummary();
        
        if(isset($post_data['provider']))
        {
            $provider = $post_data['provider'];
        }
        else
        { 
            $provider = null;
        }
        
        $data['providers_list'] = $this->providers_model->get_providers_list($provider, true, true);
        
        // Pagination
        
        $config['base_url'] = base_url().'index.php/stokoni/page/';
        $config['total_rows'] = $this->stokoni_model->countProducts();
        $config['per_page'] = 50; 

        $this->pagination->initialize($config); 
        $data['pagination'] = $this->pagination->create_links();
        
        // Load view  
        $this->load->template('stokoni/default', $data);
    }
    
    public function add_product()
    {
        //Load models
        $this->load->model('incomes/providers_model');
        
        $post_data  = $this->input->post();
        $data       = array();
        
        $data['action'] = base_url().'index.php/stokoni/'.$this->router->method.'/';
        
        $data['errors'] = null;
        
        if(empty($post_data['ean']))
        {
            $data['providers_list'] = $this->providers_model->get_providers_list(null, true, true, 'id="providers_list_2"');
            $this->load->view('stokoni/add' ,$data);                    
        }
        else
        {
            $data['product']        = $post_data;
            $data['providers_list'] = $this->providers_model->get_providers_list($post_data['provider'], true);
            
            $this->load->library('form_validation');
            
            $config = array(
               array(
                     'field'   => 'ean', 
                     'label'   => 'EAN', 
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'nombre', 
                     'label'   => 'Nombre', 
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'coste', 
                     'label'   => 'Coste', 
                     'rules'   => 'required|numeric'
                  ),   
               array(
                     'field'   => 'stock', 
                     'label'   => 'Stock', 
                     'rules'   => 'required|is_natural'
                  ),   
               array(
                     'field'   => 'provider', 
                     'label'   => 'Proveedor', 
                     'rules'   => 'required'
                  ),   
               array(
                     'field'   => 'vendidas', 
                     'label'   => 'Vendidas', 
                     'rules'   => 'required|is_natural'
                  ),   
               array(
                     'field'   => 'fechaDeCompra', 
                     'label'   => 'Fecha De Compra', 
                     'rules'   => 'required'
                  )
            );

            $this->form_validation->set_rules($config);

            if ($this->form_validation->run() == FALSE)
            {
                    $this->load->view('stokoni/edit' ,$data);  
            }
            else
            {
                $is_exists = $this->stokoni_model->find_product_by_ean($post_data['ean']);

                if($is_exists)
                {
                    $data['errors'] = 'EAN ' . $post_data['ean'] . ' is already exists';
                    $this->load->view('stokoni/edit' ,$data);  
                }
                else
                {   
                    $data['response'] = $this->stokoni_model->add_product($post_data);
                    $this->load->view('stokoni/ajax_response', $data);                    
                }
            }
        }
    }
    
    public function edit()
    {
        //Load models
        $this->load->model('incomes/providers_model');
        
        $post_data  = $this->input->post();
                        
        $data       = array();
        
        $data['action'] = base_url().'index.php/stokoni/save/';
        
        $data['errors'] = null;
        
        $data['product'] = (array)$this->stokoni_model->getProduct($post_data['id']);
        
        if($data['product'])
        {
            $data['providers_list'] = $this->providers_model->get_providers_list($data['product']['proveedor'], true, true, 'id="providers_list_2"');
            $this->load->view('stokoni/edit' ,$data);
        }
    }
    
    public function save()
    {
        //Load models
        $this->load->model('incomes/providers_model');
        
        $data['product']  = $this->input->post();
        
        $data['errors'] = null;
        
        $data['action'] = base_url().'index.php/stokoni/save/';
        
        $data['providers_list'] = $this->providers_model->get_providers_list($data['product']['provider'], true);
        
        $this->load->library('form_validation');
            
            $config = array(
               array(
                     'field'   => 'ean', 
                     'label'   => 'EAN', 
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'nombre', 
                     'label'   => 'Nombre', 
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'coste', 
                     'label'   => 'Coste', 
                     'rules'   => 'required|numeric'
                  ),   
               array(
                     'field'   => 'stock', 
                     'label'   => 'Stock', 
                     'rules'   => 'required|is_natural'
                  ),   
               array(
                     'field'   => 'provider', 
                     'label'   => 'Proveedor', 
                     'rules'   => 'required'
                  ),   
               array(
                     'field'   => 'vendidas', 
                     'label'   => 'Vendidas', 
                     'rules'   => 'required|is_natural'
                  ),   
               array(
                     'field'   => 'fechaDeCompra', 
                     'label'   => 'Fecha De Compra', 
                     'rules'   => 'required'
                  )
            );

            $this->form_validation->set_rules($config);

            if ($this->form_validation->run() == FALSE)
            {
                $this->load->view('stokoni/edit' ,$data);
            }
            else
            {
                $data['response'] = $this->stokoni_model->save($data['product']);
                $this->load->view('stokoni/ajax_response', $data);
            }
    }
    
    public function delete()
    {
        $post_data = $this->input->post();
                
        $data['response'] = $this->stokoni_model->remove((int)$post_data['id']);
        $this->load->view('incomes/ajax_response', $data);
    }

    public function get_product($id)
    {
        $data = array();
        
        $data['order'] = $this->stokoni_model->getProduct($id);
        
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data['order']));
    }
}