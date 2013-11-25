<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Grutinet controller
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Grutinet extends CI_Controller
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
        $this->load->model('grutinet/grutinet_model');
    }
    
    public function index($page = null)
    {   
        {
            redirect('grutinet/page');
        }
    }
    
    public function page ($page = 0) 
    {
        
        $data = array();
        $data['title'] = humanize($this->router->class);
        
        $data['products']       = $this->grutinet_model->get_products($page);
        $data['total_products'] = $this->grutinet_model->count_products();
        $data['brand_options']  = $this->grutinet_model->get_brand_options_list();
        
        // Pagination
        
        $config['base_url'] = base_url().'index.php/grutinet/page/';
        $config['total_rows'] = $this->grutinet_model->count_products();
        $config['per_page'] = 50; 

        $this->pagination->initialize($config); 
        $data['pagination'] = $this->pagination->create_links();
        
        // Load view  
        $this->load->template('grutinet/default', $data);
    }
}