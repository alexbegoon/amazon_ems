<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Abstract class of sync_products.
 * Global class for all providers that help to get products from provider automatically
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
abstract class Sync_products 
{
    protected $_CI, $_db, $_url_service, $_test_mode, $_products, $_provider_name;

    public function __construct() 
    {
        $this->_CI =& get_instance(); // Instance of CodeIgniter
        $this->_db = $this->_CI->db; // Instance of DB object
        $this->_test_mode = FALSE;  // Test mode boolean
        $this->_url_service = null; // URL string of Provider service. Products link
        $this->_products    = null; // Products array. Format: $product['sku'],
                                    //                         $product['product_name'],
                                    //                         $product['provider_name'],
                                    //                         $product['price'],
                                    //                         $product['stock'],
                                    //                         $product['brand'],
                                    //                         $product['sex']
        $this->_provider_name = null; // String: Provider name. Example: ENGELSA .  Like in the Providers table.
    }

    // Get products from URL or CSV or something...
    abstract protected function extract_products();
    
    // Store products to products table of amazoni4
    protected function store_products()
    {
        $this->_CI->load->model('products/products_model');
        
        if(is_array($this->_products) && !$this->_test_mode)
        {
            // Check format
            
            reset($this->_products);
            $first_key = key($this->_products);
            
            if(    isset($this->_products[$first_key]['sku']) &&
                   isset($this->_products[$first_key]['product_name']) &&
                   isset($this->_products[$first_key]['provider_name']) &&
                   isset($this->_products[$first_key]['price']) &&
                   isset($this->_products[$first_key]['stock'])     )
            {
                $this->_CI->products_model->update_products_table($this->_products);
            }
        }
    }
}