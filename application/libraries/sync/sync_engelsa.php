<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @version     1.0.0
 * 
 * @copyright   Copyright (C) 2013. All rights reserved.
 * 
 * @author      Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

/**
 * Sync process of Engelsa table
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Sync_engelsa {
    
    private $_config, $_dbo, $_data = array(), $_CI = null;
    
    public function __construct($config) {
        
        try {
            // Prepare configuration
            if(empty($config['config']) || $config['config'] == '')
            {
                echo 'Have no configuration';
                die;
            }

            require_once strtolower($config['config']).'.php';

            $this->_config = new $config['config'];

            if (!is_object($this->_config)) {
                echo 'Have no configuration';
                die;
            }

            // Execution time
            ini_set('max_execution_time', $this->_config->max_execution_time);

            // Instance of Codeigniter App
            $this->_CI =& get_instance();

            $this->_CI->load->database();
            
            // Create DB object
            $this->createDBO();

            // Clear temp table if not the Test mode
            if (!$this->_config->test_mode) {
                $this->clearTemp();
            }

            // Extract data from specific path
            if (!$this->_config->test_mode) {
                $this->extractData();
            }

            // Insert data to Engelsa temp table
            if (!$this->_config->test_mode) {
                $this->insertData('engelsa_temp', $this->_data);
            }

            // Compare Engelsa table with temp and ADD the new items if need
            $items_added =  $this->addNewItems();
            $this->sendMail('PRODUCTO NUEVO EN ENGELSA', '', $items_added);


            // Compare Engelsa table with temp and UPDATE the existing items if need
            $this->updateItems();

            // Compare Engelsa table with temp and DELETE the items if need
            $items_removed = $this->removeItems();
            $this->sendMail('PRODUCTO ELIMINADO EN ENGELSA', '', $items_removed);

        } catch(Exception $ex) {
            echo $ex->getMessage();
            $this->sendMail('LA URL DE ENGELSA NO FUNCIONA', 'i am sorry <br>'.$ex->getMessage());
        }
        
        
    }
    
    private function createDBO() {
        if (empty($this->_dbo)) {
            $this->_dbo = new PDO( 
            $this->_CI->db->hostname.';
                charset=utf8', 
             ''.$this->_CI->db->username.'', 
             ''.$this->_CI->db->password.'');
        }
                
        if(!is_a($this->_dbo, 'PDO'))
        {
            throw new Exception('Cant connect to DB');
        }
    }
    
    private function clearTemp(){
        
        $query = ' TRUNCATE '.$this->_config->prefix.'_engelsa_temp ';
        
        try {
            $stmt = $this->_dbo->query($query);
        } catch(PDOException $ex) {
            echo $ex->getMessage();
        }
        
    }
    
    private function extractData(){
        
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $this->_config->data_url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $html = curl_exec($handle);

        $product_list = preg_split ('/$\R?^/m', $html);

        if(preg_match('/OK./', $product_list[0]) != 1)
        {
            throw new Exception('Cant read Engelsa product link');
        }
        
        $i = 0;
        $j = 0;
        foreach ($product_list as $row) {

            $product_arr = str_getcsv($row, ',', '"');
            
            if ($i >= $this->_config->start_from_row) {
                
                $this->_data[$j] = array('ean'          => $product_arr[2], 
                                         'precio'       => (float)$product_arr[5],
                                         'descripcion'  => $product_arr[3],
                                         'stock'        => $product_arr[6],
                                         'nombre_marca' => $product_arr[8]  
                );
                
                $j++;
                
            }
            $i++;
            
        }

        curl_close($handle);
    }
    
    private function insertData($table, $data = array()){
        
        if (count($data) > 0) 
        {
            $this->_CI->db->insert_batch($table, $data);
        }
    }
    
    private function addNewItems(){
        
        $query = ' SELECT `temp`.* 
                   FROM `'.$this->_config->prefix.'_engelsa` AS `current`  
                   RIGHT JOIN `'.$this->_config->prefix.'_engelsa_temp` AS `temp` 
                   ON `current`.`ean` = `temp`.`ean` 
                   WHERE `current`.`ean` IS NULL    
        ';
        
        try {
            $stmt = $this->_dbo->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($result) > 0) {
                $this->insertData('engelsa', $result);
            }
        } catch(PDOException $ex) {
            echo $ex->getMessage();
        }
        
        return $result;
    }
    
    private function updateItems(){
        
        $query = ' SELECT `temp`.* 
                   FROM `'.$this->_config->prefix.'_engelsa` AS `current` 
                   LEFT JOIN `'.$this->_config->prefix.'_engelsa_temp` AS `temp` 
                   ON `current`.`ean` = `temp`.`ean` 
                   WHERE `current`.`stock` != `temp`.`stock` OR
                         `current`.`precio` != `temp`.`precio` OR 
                         `current`.`descripcion` != `temp`.`descripcion` 
        ';
        
        try {
            $stmt = $this->_dbo->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($result) > 0) {
                $this->updateData('engelsa', $result);
            }
        } catch(PDOException $ex) {
            echo $ex->getMessage();
        }
        
        return $result;
        
    }
    
    private function updateData($table, $data = array()){
        
        $update_data = array();
        
        foreach ($data as $item) {
            $update_data[] = array( 'precio'        => $item['precio'], 
                                    'stock'         => $item['stock'], 
                                    'descripcion'   => $item['descripcion'],
                                    'ean'           => $item['ean']);
        }
        
        return $this->_CI->db->update_batch($table, $update_data, 'ean');
        
    }
    
    private function removeItems(){
        
        $query = ' SELECT `current`.* 
                   FROM `'.$this->_config->prefix.'_engelsa` AS `current` 
                   LEFT JOIN `'.$this->_config->prefix.'_engelsa_temp` AS `temp` 
                   ON `current`.`ean` = `temp`.`ean`  
                   WHERE `temp`.`ean` IS NULL 
        ';
        
        try {
            $stmt = $this->_dbo->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($result) > 0) {
                $this->removeData($this->_config->prefix.'_engelsa', $result);
            }
        } catch(PDOException $ex) {
            echo $ex->getMessage();
        }
        
        return $result;
        
    }
    
    private function removeData($table, $data = array()){
        
        $query = ' DELETE FROM `'.$table.'` 
                   WHERE `ean`=? 
        ';
        
        $stmt = $this->_dbo->prepare($query);
        
        foreach ($data as $item) {
            $stmt->execute(array($item['ean']));
        }
        
        return true;
    }
    
    private function sendMail($subject, $body, $items = array()){
        
        if (!empty($subject) && !empty($this->_config->mail_to)) {
            
            if (empty($body)) {
                $body = '';
            }
            
            if (count($items) > 0 && is_array($items))
            {
                
                $body .= '<h3>'.$subject.'</h3>';
                $body .= '<h4>List of items</h4>';
                $body .= '<table border="1">';
                $body .= '<tr>';
                $body .= '<th>EAN</th><th>NOMBRE</th><th>STOCK</th><th>PRICE</th>';
                $body .= '</tr>';
                
                foreach ($items as $item) {
                    $body .= '<tr>';
                    $body .= '<td>'.$item['ean'].'</td>';
                    $body .= '<td>'.$item['descripcion'].'</td>';
                    $body .= '<td>'.$item['stock'].'</td>';
                    $body .= '<td>'.round($item['precio'], 2).'</td>';
                    $body .= '</tr>';
                }
                
                $body .= '</table>';
                
            }
            
            if (!empty($body)) {
                
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                $headers .= 'From: Amazoni_Sync_Process' . "\r\n";

                return mail($this->_config->mail_to, $subject, $body, $headers);
            }
        }
    }
}