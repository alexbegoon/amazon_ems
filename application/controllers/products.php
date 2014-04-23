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
    
    function edit()
    {
        $id = $this->input->post('id');
        
        $language_code = $this->input->post('translation_language_code');
        
        $product = $this->products_model->get_product_by_id($id);
        
        $translation_locked_by = $this->products_model->is_translation_locked($product->sku, $language_code);
        
        if($translation_locked_by !== false)
        {
            $data['errors'][] = 'Translation locked by ' . 
                    
            $this->ion_auth->user($translation_locked_by)->row()->first_name .
            ' '.
            $this->ion_auth->user($translation_locked_by)->row()->last_name .
            ' ';
            
            $data['errors'][] = 'Email: ' . $this->ion_auth->user($translation_locked_by)->row()->email;
            
            $this->load->view('products/error', $data);
        }
        else
        {
            $data['product_name']   = '';
            $data['product_desc']   = '';
            $data['product_s_desc'] = '';
            $data['meta_desc']      = '';
            $data['meta_keywords']  = '';
            $data['custom_title']   = '';
            $data['slug']           = '';
            $data['provider_product_name'] = $product->product_name;
            $data['sku'] = $product->sku;
            $data['translation_languages_dropdown'] = $this->products_model->get_translation_languages_dropdown();

            $data = array_merge($data, $this->products_model->get_product_translation($product->sku, $language_code));

            $this->load->view('products/edit_translation', $data);
        }
    }
    
    function save()
    {
        $data = $this->input->post();
        // Single hack. CI XSS filter removed styles.
        $data['product_desc'] = $_REQUEST['product_desc'];
        
        unset($data['provider_product_name']);
        
        $this->products_model->save_translation($data);
    }
    
    function get_translation()
    {
        $sku = $this->input->post('sku');
        $language_code = $this->input->post('language_code');
        
        $data = $this->products_model->get_product_translation($sku, $language_code);
        
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }
    
    function import_products_meta($iteration = 0)
    {
        $this->products_model->import_products_meta($iteration);
    }
    
    function export_products_meta()
    {
        $sku = $this->input->post('sku');
        $this->products_model->export_product_meta($sku);
    }
}