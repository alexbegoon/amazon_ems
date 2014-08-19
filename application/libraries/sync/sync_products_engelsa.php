<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ENGELSA provider sync
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
require_once dirname(__FILE__).'/sync_products.php';

class Sync_products_engelsa extends Sync_products 
{
    public function __construct() 
    {
        parent::__construct();
        
        $this->_url_service = 'http://mayoristas.engelsa.com/servicios/dropshipping.ashx?&usuario=buying&password=Byng895&operacion=listadoarticulos';
        $this->_provider_name = 'ENGELSA';
        
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
            $product = str_getcsv ($row);
            
            if($this->_test_mode)
            {
                echo '<pre>';
                var_dump($product);
                echo '<pre>';
            }
            
            if(isset($product[2]) && preg_match('/^\s*$/', $product[1])===0 && (float)$product[5] > 0 && strlen($product[3])>5)
            {
                $this->_products[$i]['sku'] = trim($product[2]);
                $this->_products[$i]['inner_id'] = $product[1]?trim(strip_tags($product[1])):NULL;
                $this->_products[$i]['inner_sku'] = trim($product[2]);
                $this->_products[$i]['product_name'] = trim($product[3]);
                $this->_products[$i]['provider_name'] = $this->_provider_name;
                $this->_products[$i]['price']   = (float)$product[5];
                if(in_array((string)$this->_products[$i]['sku'], $this->_eans_to_exclude))
                {
                    $this->_products[$i]['stock'] = 0;
                }
                else
                {
                    $this->_products[$i]['stock']   = (int)$product[6];
                }
                $this->_products[$i]['brand']   = trim($product[8]);
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
    
    protected function check_products_exceptions()
    {        
        $this->_eans_to_exclude = array();
        
        $xls_path = FCPATH . '/calvin_klein_shock_ean.xls';
        
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        
        $objPHPExcel = $objReader->load($xls_path);
        
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        
        foreach($sheetData as $row)
        {
            $eans_to_exclude[] = (string)preg_replace('/^#/', '', $row['A']);
        }
        
        $this->_eans_to_exclude = array_merge($this->_eans_to_exclude, $eans_to_exclude);
        
        return TRUE;
    }
}