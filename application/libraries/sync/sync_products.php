<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Abstract class of sync_products.
 * Global class for all providers that help to get products from provider automatically
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
abstract class Sync_products 
{
    protected $_CI, $_db, $_url_service, $_test_mode, $_products, $_provider_name, $_eans_to_exclude;

    public function __construct() 
    {
        $this->_CI =& get_instance(); // Instance of CodeIgniter
        
        // Load models
        $this->_CI->load->model('products/products_model');
        
        $this->_CI->load->helper('file');
        $this->_db = $this->_CI->db; // Instance of DB object
        $this->_test_mode = FALSE;  // Test mode boolean
        $this->_url_service = null; // URL string of Provider service. Products link
        $this->_eans_to_exclude = null; // Reset exclude list
        $this->_products    = null; // Products array. Format: $product['sku'],
                                    //                         $product['product_name'],
                                    //                         $product['provider_name'],
                                    //                         $product['price'],
                                    //                         $product['stock'],
                                    //                         $product['brand'],
                                    //                         $product['sex']
        $this->_provider_name = null; // String: Provider name. Example: ENGELSA .  Like in the Providers table.
        $this->check_products_exceptions();
    }

    /**
     *  Get products from URL or CSV or something...
     */
    abstract protected function extract_products();
    
    /**
     * Check products EANs in special lists, tables, taht help to ignore products
     */
    abstract protected function check_products_exceptions();

        /**
     *  Store products to products table of amazoni4
     */
    protected function store_products()
    {
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
    
    /**
     * Remove products from providers_products table
     * @param int $id
     */
    protected function delete_product($id)
    {
        $this->_CI->products_model->delete_product($id);
    }
    
    /**
     * Remove products from providers_products table using EAN and provider name
     * @param type $ean
     * @param type $provider_name
     */
    protected function delete_product_by_ean($ean, $provider_name)
    {
        $query = $this->_CI->db->get_where('providers_products', array('sku' => $ean, 'provider_name' => $provider_name));
        
        if($query->num_rows() === 1)
        {
            return $this->delete_product((int)$query->row()->id);
        }
        
        return FALSE;
    }
}