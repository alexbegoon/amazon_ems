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
    }
    
    public function sync_engelsa()
    {
        
        $this->load->library($this->_path_to_library . 'Sync_engelsa', array('config' => 'Config_engelsa'));
        
    }
    
    public function sync_grutinet()
    {
        // Load config
        $this->load->library($this->_path_to_library.'config_grutinet');
        
        $config = $this->config_grutinet->get_config_array();
        
        // Load sync process class
        $this->load->library($this->_path_to_library.'sync_grutinet',$config);
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
    }
    
    public function sync_top_sales()
    {
        $this->load->model('incomes/top_sales_model');
        
//        $this->output->enable_profiler(TRUE);
        
        $this->top_sales_model->sync_with_pedidos();
    }
    
    public function sync_product_list_with_engelsa_and_grutinet()
    {
        $this->sync_engelsa();
        $this->sync_grutinet();
        
        // Load model
        $this->load->model('products/products_model');
        
        $this->products_model->sync_with_engelsa();
        $this->products_model->sync_with_grutinet();
    }
    
    public function send_magnet_emails()
    {
        $this->load->model('magnet/magnet_model');
        
        $this->magnet_model->send_email_messages();
    }
    
    public function sync_reviews()
    {
        $this->load->model('reviews/reviews_model');
        
        $this->reviews_model->sync_reviews();
        
        
        // Update logs of Amazon 
        $this->load->model('stokoni/stokoni_model');
        $this->load->model('amazon/amazon_model');
        
        $this->amazon_model->update_log();
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
    }
    
    public function sync_providers_products()
    {
        $this->sync_product_list_with_engelsa_and_grutinet();
        
        require_once FCPATH . $this->_path_to_sync_library . 'sync_products_pinternacional.php';
        
        new Sync_products_pinternacional();
        
        require_once FCPATH . $this->_path_to_sync_library . 'sync_products_coqueteo.php';
        
        new Sync_products_coqueteo();
    }
}
