<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * AGHATA provider sync
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
require_once dirname(__FILE__).'/sync_products.php';

class Sync_products_aghata extends Sync_products 
{
    public function __construct() 
    {
        parent::__construct();
        
        $this->_url_service = FCPATH .'upload/cano/articulos.csv';
        
        $this->_provider_name = 'AGHATA';
        
        // Test mode toggle
//        $this->_test_mode = TRUE;
        
        // Extract products
        $this->extract_products();
        
        // Store products
        $this->store_products();
    }
    
    protected function extract_products() 
    {
        $data_file = read_file($this->_url_service);
        
        if(!$data_file)
        {
            echo "Can't open a file";
            log_message('ERROR', "Can't open a file");
            
            return FALSE;
        }
        $data_file = utf8_encode($data_file);
        $data_array = explode("\n",$data_file);
        
        $this->_products = array();
        
        $i = 0;
        
        foreach ($data_array as $row)
        {
            $product = str_getcsv($row,";");
            
            if($this->_test_mode)
            {
                echo '<pre>';
                var_dump($product);
                echo '<pre>';
            }
            
            $ean = null;
            $price = 0;
            
            if(isset($product[7]))
            {
                $price = (float)preg_replace('/,/', '.', $product[7]);
            }
            
            if(isset($product[0]))
            {
                $ean = (string)preg_replace('/^#/', '', $product[0]);
            }
            
            if( isset($ean) && preg_match('/^\d{6,13}/', $ean) )
            {
                
                $this->_products[$i]['sku'] = $ean;
                $this->_products[$i]['inner_sku'] = $product[1];
                $this->_products[$i]['product_name'] = trim($product[2]);
                $this->_products[$i]['provider_name'] = $this->_provider_name;
                $this->_products[$i]['price']   = $price * 1.04;
                if( $price <= floatval(1) || in_array((string)$ean, $this->_eans_to_exclude) || $product[10]<=1)
                {
                    $this->_products[$i]['stock']   = 0;
                }
                else 
                {
                    $this->_products[$i]['stock']   = (integer)$product[10];
                }
                $this->_products[$i]['brand']   = trim($product[6]);
                $this->_products[$i]['provider_image_url']   = trim($product[11]);
            }
            else
            {
                if($this->_test_mode)
                {
                    echo 'Wrong EAN';
                }
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
    
    protected function check_products_exceptions()
    {
        $this->_CI->load->library('excel');
        
        $xls_path = FCPATH . '/calvin_klein_shock_ean.xls';
        
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        
        $objPHPExcel = $objReader->load($xls_path);
        
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        
        foreach($sheetData as $row)
        {
            $eans_to_exclude[] = (string)preg_replace('/^#/', '', $row['A']);
        }
        
        $this->_eans_to_exclude = $eans_to_exclude;
        
        return TRUE;
    }
}
