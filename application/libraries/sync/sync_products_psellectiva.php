<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PSELLECTIVA provider sync
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
require_once dirname(__FILE__).'/sync_products.php';

class Sync_products_psellectiva extends Sync_products 
{
    public function __construct() 
    {
        parent::__construct();
        
        $this->_url_service = 'http://buyincomercioweb.ademan.com:15469/catalogo.56223.txt.php';
//        $this->_url_service = FCPATH .'catalogo.perfuemria.selectiva.xml';
        $this->_provider_name = 'PSELLECTIVA';
        
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
        
        $data_array = explode("<br>",$data_file);
        
        $this->_products = array();
        
        $i = 0;
        
        foreach ($data_array as $row)
        {
            $product = explode("|", $row);
            
            if($this->_test_mode)
            {
                echo '<pre>';
                var_dump($product);
                echo '<pre>';
            }
            
            if(isset($product[3]) && preg_match('/^\d{6,13}/', $product[3])===1 && preg_match('/^\s*$/', $product[1])===0 && (float)$product[5] > 0 && strlen($product[3])>5)
            {
                $this->_products[$i]['sku'] = trim($product[3]);
                $this->_products[$i]['inner_id'] = $product[0]?trim(strip_tags($product[0])):NULL;
                $this->_products[$i]['inner_sku'] = trim($product[3]);
                $this->_products[$i]['provider_image_url'] = $product[10]?trim($product[10]):NULL;
                $this->_products[$i]['product_name'] = trim($product[1]);
                $this->_products[$i]['provider_name'] = $this->_provider_name;
                $this->_products[$i]['price']   = (float)$product[5] * 1.04;
                if(in_array((string)$this->_products[$i]['sku'], $this->_eans_to_exclude) || (int)$product[8] <= 0)
                {
                    $this->_products[$i]['stock'] = 0;
                }
                else
                {
                    $this->_products[$i]['stock']   = (int)$product[8];
                }
                $this->_products[$i]['brand']   = trim($product[2]);
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