<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of reviews
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Reviews extends CI_Controller
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
         $this->load->model('reviews/reviews_model');
         $this->load->model('incomes/web_field_model');
         
         $this->load->helper('html');
         
    }
    
    public function index($page = null)
    {   
        
        redirect('reviews/page/');
        
    }
    
    public function page($page = null)
    {
        $post_data = $this->input->post();
        
        // Load data 
        $data['title'] = ucfirst($this->router->class);
        $data['reviews'] = $this->reviews_model->get_all_reviews($page);
        $data['total_rows'] = $this->reviews_model->count_reviews();
        $data['rating_filter'] = $this->reviews_model->get_rating_filter();
        $data['web_field_filter'] = $this->web_field_model->get_web_fields_list($post_data['web']);
        
        // Pagination
        $config['base_url'] = base_url().'index.php/reviews/page/';
        $config['total_rows'] = $this->reviews_model->count_reviews();
        $config['per_page'] = 50; 

        $this->pagination->initialize($config); 
        $data['pagination'] = $this->pagination->create_links();
        
        $this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));
        var_dump($this->cache->memcached->is_supported());
        die;
        $this->cache->memcached->save('foo', 'bar', 600);
        
        for($i = 1; $i <= 500; $i++)
        {
            $data['from_cache'][] = $this->cache->memcached->get('foo');
        }
        
        // Load view
//        $this->load->template('reviews/index', $data);
                
    }
   
    
}