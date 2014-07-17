<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Sync process controller
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

class Sync_process extends CI_Controller
{
    
    private $_path_to_sync_library = 'application/libraries/sync/';
    private $_path_to_library = 'sync/';
    
    public function __construct()
    {
         parent::__construct();
         
//         $this->output->enable_profiler(TRUE);
    }
    
    public function index()
    {
        return true;
    }
    
    public function sync_orders()
    {
        require_once FCPATH . $this->_path_to_sync_library . 'sync.php';
        require_once FCPATH . $this->_path_to_sync_library . 'syncold.php';
                
        $this->load->model('incomes/web_field_model');
        
        $web_sites = $this->web_field_model->get_all_web_fields();
        
        foreach ($web_sites as $web_site)
        {
            
            if($web_site->sync_enabled == '1')
            {
                switch ($web_site->virtuemart_version)
                {
                    case '1.0.0.0' : 
                        new SyncOld($web_site->web);
                    break;
                    case '2.0.0.0' : 
                        new Sync($web_site->web);
                    break;
                }
            }
        }
        
        $this->output->set_output('Done');
    }
    
    public function sync_engelsa()
    {
        $this->sync_engelsa_products();
    }
    
    public function sync_grutinet()
    {
        // Load config
        $this->load->library($this->_path_to_library.'config_grutinet');
        
        $config = $this->config_grutinet->get_config_array();
        
        // Load sync process class
        $this->load->library($this->_path_to_library.'sync_grutinet',$config);
        
        $this->output->set_output('Done');
    }
    
    /**
     * Clear the cache in the path ./application/cache/$folder
     * @param string $folder cache target folder
     */
    public function clear_cache($folder = null)
    {
        if(!empty($folder) && is_string($folder))
        {
            $this->load->helper('file');
            
            delete_files(FCPATH.'application/cache/'.$folder.'/', TRUE);
        }
        
        // Remove old files and logs
        
        $this->load->helper('file');
        $files_uploaded = get_dir_file_info(FCPATH.'upload/', FALSE);
        $files_logs     = get_dir_file_info(FCPATH.'application/logs/', FALSE);
        
        foreach ($files_uploaded as $file) 
        {
            if( $file['date'] < (time() - SECONDS_PER_DAY*31) )
            {
                unlink($file['server_path']);
            }
        }
        
        foreach ($files_logs as $file) 
        {
            if( $file['date'] < (time() - SECONDS_PER_DAY*31) )
            {
                unlink($file['server_path']);
            }
        }
        
        $this->load->model('amazon/amazon_model');
        
        $this->amazon_model->clear_logs();
        
        $this->output->set_output('Done');
    }
    
    public function sync_top_sales()
    {
        $this->load->model('incomes/top_sales_model');
        
//        $this->output->enable_profiler(TRUE);
        
        $this->top_sales_model->sync_with_pedidos();
        
        $this->output->set_output('Done');
    }
    
    public function sync_product_list_with_engelsa_and_grutinet()
    {
        $this->sync_engelsa_products();
        $this->sync_grutinet();
        
        // Load model
        $this->load->model('products/products_model');
        
//        $this->products_model->sync_with_engelsa();
        $this->products_model->sync_with_grutinet();
        
        $this->output->set_output('Done');
    }
    
    public function send_magnet_emails()
    {
        $this->load->model('magnet/magnet_model');
        
        $this->magnet_model->send_email_messages();
        
        $this->output->set_output('Done');
    }
    
    public function sync_reviews()
    {
        $this->load->model('reviews/reviews_model');
        
        $this->reviews_model->sync_reviews();
        
        
        // Update logs of Amazon 
        $this->load->model('stokoni/stokoni_model');
        $this->load->model('amazon/amazon_model');
        
        $this->amazon_model->update_log();
        
        $this->output->set_output('Done');
    }
    
    /**
     * Be very carefull! Dangerous method!
     */
    public function fix_top_sales()
    {
//        $this->load->model('incomes/top_sales_model');
//        $this->top_sales_model->fix_top_sales_table();
    }
    
    /**
     * Upload stock level to Amazon
     */
    public function sync_data_with_amazon()
    {
        $this->load->model('stokoni/stokoni_model');
        $this->load->model('amazon/amazon_model');
        
        $this->stokoni_model->upload_stock_to_amazon();
        $this->stokoni_model->upload_prices_to_amazon();
        
        $this->output->set_output('Done');
    }
    
//    public function sync_providers_products()
//    {
//        $this->sync_product_list_with_engelsa_and_grutinet();
//        
//        require_once FCPATH . $this->_path_to_sync_library . 'sync_products_pinternacional.php';
//        
//        new Sync_products_pinternacional();
//        
//        require_once FCPATH . $this->_path_to_sync_library . 'sync_products_coqueteo.php';
//        
//        new Sync_products_coqueteo();
//        
//        $this->output->set_output('Done');
//    }
    
    public function sync_engelsa_products()
    {
        require_once FCPATH . $this->_path_to_sync_library . 'sync_products_engelsa.php';
        
        new Sync_products_engelsa();
        
        $this->session->unset_userdata('verify_products_accepted');
        
        $this->output->set_output('Done');
    }
    
    public function sync_coqueteo_products()
    {
        require_once FCPATH . $this->_path_to_sync_library . 'sync_products_coqueteo.php';
        
        new Sync_products_coqueteo();
        
        $this->session->unset_userdata('verify_products_accepted');
        
        $this->output->set_output('Done');
    }
    
    public function sync_psellectiva_products()
    {
        require_once FCPATH . $this->_path_to_sync_library . 'sync_products_psellectiva.php';
        
        new Sync_products_psellectiva();
        
        $this->session->unset_userdata('verify_products_accepted');
        
        $this->output->set_output('Done');
    }
    
    public function sync_pinternacional_products()
    {
        require_once FCPATH . $this->_path_to_sync_library . 'sync_products_pinternacional.php';
        
        new Sync_products_pinternacional();
        
        $this->session->unset_userdata('verify_products_accepted');
        
        $this->output->set_output('Done');
    }
    
    public function update_stock()
    {
        // Authorization check
        if (!$this->ion_auth->logged_in())
        {
           redirect('auth/login');
        }

        // Only admin have access
        if (!$this->ion_auth->is_admin()) 
        {
            show_404();
            die;
        }
        
        $data = array();
        $data['title'] = humanize($this->router->method);
        
        $data['providers'] = array(
            
            'COQUETEO' => array(
                'url' => base_url().'index.php/sync_process/sync_coqueteo_products'
            ),
            'PINTERNACIONAL' => array(
                'url' => base_url().'index.php/sync_process/sync_pinternacional_products'
            ),
            'ENGELSA' => array(
                'url' => base_url().'index.php/sync_process/sync_engelsa_products'
            ),
            'PSELLECTIVA' => array(
                'url' => base_url().'index.php/sync_process/sync_psellectiva_products'
            ),
            
        );
        
        // Load view 
        $this->load->template('sync_process/update_stock', $data);
    }
    
    public function verify_products()
    {
        // Authorization check
        if (!$this->ion_auth->logged_in())
        {
           redirect('auth/login');
        }

        // Only admin have access
        if (!$this->ion_auth->is_admin()) 
        {
            show_404();
            die;
        }
        
        // Load models
        $this->load->model('export_csv/export_csv_model');
        
        $data = array();
        $data['title'] = humanize($this->router->method);
        
        $data['orders'] = $this->export_csv_model->get_summary('fedex_gls_summary');
        $data['verified_orders'] = $verified_orders = $this->session->userdata('verified_orders');
        
        
        
        
        // Load view 
        $this->load->template('sync_process/verify_products', $data);
    }
     
    public function export_product_translations_to($web)
    {
        // Load models
        $this->load->model('products/products_model');
        
        $this->products_model->export_all_translations_to_website($web);
    }
}
