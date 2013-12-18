<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Pinternacional provider sync
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
require_once dirname(__FILE__).'/sync_products.php';

class Sync_products_pinternacional extends Sync_products 
{
    public function __construct() 
    {
        parent::__construct();
        
        $this->_url_service = 'http://www.perfumeriainternacional.infor-tel.com/pinter/tarifas.txt';
        $this->_provider_name = 'PINTERNACIONAL';
        
        // Test mode toggle
//        $this->_test_mode = TRUE;
        
        // Extract products
        $this->extract_products();
        
        // Store products
        $this->store_products();
        
    }
    
    protected function extract_products() 
    {
        $data_file = file_get_contents($this->_url_service);
        
        $data_array = explode("\n",$data_file);
        
        $this->_products = array();
        
        $i = 0;
        
        foreach ($data_array as $row)
        {
            $product = explode("\t", $row);
            
            if($this->_test_mode)
            {
                echo '<pre>';
                var_dump($product);
                echo '<pre>';
            }
            
            if(isset($product[4]))
            {
                $this->_products[$i]['sku'] = trim($product[4]);
                $this->_products[$i]['product_name'] = trim($product[0]);
                $this->_products[$i]['provider_name'] = $this->_provider_name;
                $this->_products[$i]['price']   = (float)$product[2];
                $this->_products[$i]['stock']   = (int)$product[3];
                $this->_products[$i]['brand']   = trim($product[5]);
                $this->_products[$i]['sex']     = trim($product[7]);
            }
            
            if($this->_test_mode)
            {
                if(isset($this->_products[$i]))
                {
                    echo '<pre>';
                    var_dump($this->_products[$i]);
                    echo '<pre>';
                }
            }
            
            $i++;
        }
    }
}