<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Providers controller
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Providers extends CI_Controller 
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
        $this->load->model('incomes/providers_model');
        $this->load->model('export_csv/export_csv_model');
        
        // Load helpers
        $this->load->helper('download');
    }
    
    public function orders($page = 0)
    {
        $post_data = $this->input->post();
        
        $data['post_data'] = $post_data;
        $data['title'] = humanize($this->router->class . ' ' .$this->router->method);
        $data['orders'] = $this->providers_model->get_provider_orders($page);
        $data['total_orders'] = $this->providers_model->count_all_providers_orders();
        $data['providers_dropdown'] = $this->providers_model->get_providers_list($post_data['provider'], false, true);
                
        // Pagination
        
        $config['base_url'] = base_url().'index.php/providers/orders/';
        $config['total_rows'] = $this->providers_model->count_all_providers_orders();
        $config['per_page'] = 50; 

        $this->pagination->initialize($config); 
        $data['pagination'] = $this->pagination->create_links();
        
        // Load view  
        $this->load->template('providers/orders', $data);
    }
    
    public function get_order ($id, $return_url) 
    {
        $data['order'] = $this->providers_model->get_provider_order((int)$id);
        $data['extra_items'] = $this->providers_model->get_provider_order_extra_items((int)$id);
        $data['id'] = (int)$id;
        $data['return_url'] = $return_url;
        
        // Load view  
        $this->load->view('providers/order', $data);
    }
    
    public function download_order ($id)
    {
        $file = $this->export_csv_model->download_provider_order($id);
        
        if($file)
        {
            force_download($file->name, $file->data);
        }
    }
    
    public function download_order_csv ($id)
    {
        $file = $this->export_csv_model->download_provider_order_csv($id);
        
        if($file)
        {
            force_download($file->name, $file->data);
        }
    }
    
    public function send_order ($id, $return_url = null)
    {
        $this->providers_model->send_order($id);
        
        if(!empty($return_url))
        {
            redirect(base64_url_decode($return_url), 'refresh');
        }
        else
        {
            redirect('providers/orders');
        }
        
    }

    public function report_error($order_id)
    {
        $data['order'] = $this->providers_model->get_provider_order((int)$order_id);
        $data['id'] = (int)$order_id;

        // Load view
        $this->load->view('providers/report_error', $data);
    }
    
    public function process_error_products() 
    {
        $data['title'] = humanize($this->router->method);

        $data['products'] = $this->input->post('products');
        $data['available_quantity'] = $this->input->post('available_quantity');
        $data['reasons'] = $this->input->post('reasons');
        $data['provider_order_id'] = $this->input->post('order_id');
        $data['provider_name'] = $this->providers_model->get_provider_name_by_order_id((int)$this->input->post('order_id'));

        $data['process_rows'] = $this->providers_model->process_error_products($data);

        // Load view
        $this->load->template('providers/process_error_products', $data);
    }
    
    public function process_orders()
    {
        $data['title'] = humanize($this->router->method);
        
        $data['process_rows'] = $this->providers_model->process_customer_orders_after_provider_error($data);
        
        // Load view
        $this->load->template('providers/process_orders', $data);
    }
    
    public function compare()
    {
        $data['title'] = humanize($this->router->class . ' ' .$this->router->method);
        
        // Load view
        $this->load->template('providers/compare', $data);
    }
    
    public function add_products_to_order($order_id)
    {
        $data['order'] = $this->providers_model->get_provider_order((int)$order_id);
        $data['id'] = (int)$order_id;

        // Load view
        $this->load->view('providers/add_products_to_order', $data);
    }
    
    public function process_added_products()
    {
        $post_data = $this->input->post();
        
        $this->providers_model->save_extra_items_order($post_data);
        
        redirect('providers/orders/');
    }
}