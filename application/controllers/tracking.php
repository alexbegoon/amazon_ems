<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tracking controller
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Tracking extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        
        // Authorization check
        if (!$this->ion_auth->logged_in())
        {
           redirect('auth/login');
        }
        
        // Load model
        $this->load->model('tracking/tracking_model');
    }
    
    public function index()
    {
        // Prepare data
        $data['title'] = humanize($this->router->class);
        
        // Load view 
        $this->load->template('tracking/index', $data);
    }       
    
    /**
     * Return single order from Pedidos table
     * @param int $id ID of order
     * @return string order form
     */
    public function get_order($pedido)
    {
        // Load model
        $this->load->model('dashboard/dashboard_model');
        $this->load->model('incomes/shipping_costs_model');
        
        // Model task
        $data['orders'] = $this->dashboard_model->get_order_by_pedido($pedido, true);
        $data['shipping_companies_list'] = $this->shipping_costs_model->getShippingCompanies();
        
        // Load view 
        $this->load->view('tracking/order', $data);
    }
    
    public function save_tracking()
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('correo', 'Email del cliente', 'required|valid_email|xss_clean');
        $this->form_validation->set_rules('nombre', 'Nombre del Cliente', 'required|xss_clean');
        $this->form_validation->set_rules('tracking', 'Tracking del Cliente', 'required|xss_clean');
        $this->form_validation->set_rules('id_shipping_company', 'Compañía de Transporte', 'required|xss_clean');
        $this->form_validation->set_rules('id', 'Número de pedido', 'required|xss_clean|integer');
        $this->form_validation->set_rules('web', 'Web del Cliente', 'required|xss_clean');
        $this->form_validation->set_rules('pedido', 'Pedido', 'required|xss_clean');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->get_order($this->input->post('id')); // Try to fill form one more
        }
        else
        {
            $data['response'] = $this->tracking_model->save_tracking();
            $this->load->view('dashboard/ajax_response', $data);
        }
    }
    
    public function edit_template($lang)
    {
        $data['title']      = humanize($this->router->method);
        $data['template']   = $this->tracking_model->get_email_template($lang);
        $data['subject']    = $this->tracking_model->get_subject_template($lang);
        $data['lang']       = $lang;
        
        $this->load->template('tracking/edit_template', $data);
    }
    
    public function save_template()
    {
        $post_data = $this->input->post();
        
        $this->tracking_model->save_template($post_data);
        
        $this->edit_template($post_data['lang']);
    }
    
    public function get_amazon_tracking_file()
    {
        $this->tracking_model->get_file_amazon_tracking();
        
        $this->index();
    }
    
}

