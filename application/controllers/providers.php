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
}