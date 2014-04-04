<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * COQUETEO provider sync
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
require_once dirname(__FILE__).'/sync_products.php';

class Sync_products_coqueteo extends Sync_products 
{
    public function __construct() 
    {
        parent::__construct();
        
        $this->_url_service = FCPATH .'upload/fichero.csv';
        
        $this->_provider_name = 'COQUETEO';
        
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
        
        $data_array = explode("\n",$data_file);
        
        $this->_products = array();
        
        $i = 0;
        
        foreach ($data_array as $row)
        {
            $product = explode(";", $row);
            
            if($this->_test_mode)
            {
                echo '<pre>';
                var_dump($product);
                echo '<pre>';
            }
            
            $ean = null;
            $price = 0;
            
            if(isset($product[1]))
            {
                $price = (float)preg_replace('/,/', '.', $product[1]);
            }
            
            if(isset($product[3]))
            {
                $ean = (string)preg_replace('/^#/', '', $product[3]);
            }
            
            if( isset($ean) && preg_match('/^\d{13}/', $ean) )
            {
                
                $this->_products[$i]['sku'] = $ean;
                $this->_products[$i]['product_name'] = trim($product[2]);
                $this->_products[$i]['provider_name'] = $this->_provider_name;
                $this->_products[$i]['price']   = $price;
                if( $price <= floatval(1) || in_array((string)$ean, $this->_eans_to_exclude) || (int)$product[4] <= 2 )
                {
                    $this->_products[$i]['stock']   = 0;
                }
                else 
                {
                    $this->_products[$i]['stock']   = (int)$product[4];
                }
                $this->_products[$i]['brand']   = trim($product[0]);
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
        
        $xls_path = FCPATH . '/bloqueados_coqueteo.xls';
        
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        
        $objPHPExcel = $objReader->load($xls_path);
        
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        
        $eans_to_exclude = array();
        
        foreach($sheetData as $row)
        {
            $eans_to_exclude[] = (string)preg_replace('/^#/', '', $row['B']);
        }
        
        $this->_eans_to_exclude = $eans_to_exclude;
        
        return TRUE;
    }
}
