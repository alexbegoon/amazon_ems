<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sync_grutinet. Sync processor for Grutinet
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Sync_grutinet
{
    private $_config = null, $_CI = null, $_db = null;


    public function __construct($config)
    {
        try
        {
            if(empty($config))
            {
                throw new Exception('Have no configuration');
            }
            
            $this->_config = $config;
            
            // Execution time
            ini_set('max_execution_time', $this->_config['max_execution_time']);
            
            // Instance of Codeigniter App
            $this->_CI =& get_instance();

            $this->_CI->load->database();
            $this->_CI->load->library('table');
            
            // Create DB object
            $this->createDBO();

            // Clear temp table if not the Test mode
            if (!$this->_config['test_mode'])
            {
                $this->clear_temp();
            }

            // Extract data from specific path
            if (!$this->_config['test_mode'])
            {
                $products = $this->extract_data();
            }

            // Insert data to GRUTINET temp table
            if (!$this->_config['test_mode'])
            {
                $this->insert_data($this->_db->dbprefix('grutinet_temp'), $products);
            }

            // Compare Grutinet table with temp and ADD the new items if need
            $items_added =  $this->add_new_items();
            $this->sendMail('PRODUCTO NUEVO EN GRUTINET', '', $items_added);


            // Compare Grutinet table with temp and UPDATE the existing items if need
            $this->update_items();

            // Compare Grutinet table with temp and DELETE the items if need
            $items_removed = $this->remove_items();
            $this->sendMail('PRODUCTO ELIMINADO EN GRUTINET', '', $items_removed);
                        
        }
        catch (Exception $ex)
        {
            echo $ex->getMessage();
            $this->sendMail('LA URL DE GRUTINET NO FUNCIONA', 'i am sorry. Provide this email to support. <br><br>'."\n\r\n\r".$ex->getMessage());
        }
    }
    
    private function createDBO()
    {
        $this->_db = $this->_CI->db;
        
        if(!$this->_db)
        {
            throw new Exception('Have no DB connection');
        }
    }
    
    private function clear_temp()
    {
        $query = ' TRUNCATE `'.$this->_db->dbprefix('grutinet_temp').'` ';
        $result = $this->_db->query($query);
    }
    
    private function extract_data()
    {
        $products = array();
        
        if (($response_xml_data = file_get_contents($this->_config['data_url']))===false)
        {
            throw new Exception("Error fetching XML\n");
        } 
        else
        {
            libxml_use_internal_errors(true);
            $data = simplexml_load_string($response_xml_data);
            if (!$data) 
            {
                $error_msg = "Error loading XML\n";
                foreach(libxml_get_errors() as $error)
                {
                    $error_msg .= "\t".$error->message;
                }
                
                throw new Exception($error_msg);
            } 
            else
            {
                foreach ($data as $product)
                {
                    $products[] = array(
                        
                                'ean'                   => (string)$product['ref'],
                                'product_name'          => (string)$product->nombre,
                                'product_description'   => (string)$product->descripcion,
                                'price'                 => (float)$product->precio, 
                                'stock'                 => (integer)$product->stock_disponible,
                                'brand_name'            => (string)$product->marca
                    );
                }
                
                return $products;
            }
        }
    }
    
    private function insert_data($table, $data = array()){
        
        // http://stackoverflow.com/questions/1176352/pdo-prepared-inserts-multiple-rows-in-single-query
        // http://stackoverflow.com/questions/10060721/pdo-mysql-insert-multiple-rows-in-one-query
        # INSERT (name) VALUE (value),(value)
        if (count($data) > 1) 
        {
            $this->_db->trans_begin(); // helps speed up inserts

            $fieldnames = array_keys($data[0]);
            $count_values = count(array_values($data[0]));

            # array(????) untill x from first data
            for($i = 0; $i < $count_values; $i++)
            {
                $placeholder[] = '?';
            }

            $query  = 'INSERT INTO `'.$table.'` 
                       (`'. implode('`, `', $fieldnames) .'`) 
                       VALUES ('. implode(', ', $placeholder).')';

            foreach($data as $item) 
            {
                $this->_db->query($query, $item);
            }

            $this->_db->trans_commit();
        } 
        else 
        {
            $fieldnames = array_keys($data[0]);
            $values     = array_values($data[0]);
            
            foreach ($values as $value)
            {
                $escaped_values[] = $this->_db->escape($value);
            }
            
            $query  = 'INSERT INTO `'.$table.'` 
                       (`'. implode('`, `', $fieldnames) .'`) 
                       VALUES ( '. implode(', ', $escaped_values). ' )';
            
            $this->_db->query($query);
        }
    }
    
    private function add_new_items()
    {
        
        $query = ' SELECT `temp`.* 
                   FROM `'.$this->_db->dbprefix('grutinet').'` AS `current`  
                   RIGHT JOIN `'.$this->_db->dbprefix('grutinet_temp').'` AS `temp` 
                   ON `current`.`ean` = `temp`.`ean` 
                   WHERE `current`.`ean` IS NULL    
        ';
        
        $result = $this->_db->query($query);
        
        if ($result->num_rows() > 0)
        {
            $this->insert_data($this->_db->dbprefix('grutinet'), $result->result('array'));
            
            return $result->result('array');
        }
        
        return FALSE;
    }
    
    private function update_items()
    {
        
        $query = ' SELECT `temp`.* 
                   FROM `'.$this->_db->dbprefix('grutinet').'` AS `current` 
                   LEFT JOIN `'.$this->_db->dbprefix('grutinet_temp').'` AS `temp` 
                   ON `current`.`ean` = `temp`.`ean` 
                   WHERE `current`.`stock`        != `temp`.`stock` OR 
                         `current`.`price`        != `temp`.`price` OR 
                         `current`.`product_name` != `temp`.`product_name` 
        ';
        
        $result = $this->_db->query($query);
        
        if ($result->num_rows() > 0)
        {
            $this->update_data($this->_db->dbprefix('grutinet'), $result->result('array'));
            return $result->result('array');
        }
        
        return FALSE;
    }
    
    private function update_data($table, $data = array())
    {
        
        $query = ' UPDATE `'.$table.'` 
                   SET `price`=?, `stock`=?, `product_name`=?  
                   WHERE `ean`=?
        ';
        
        foreach ($data as $item)
        {
            $this->_db->query($query, array(
                        $item['price'], 
                        $item['stock'], 
                        $item['product_name'], 
                        $item['ean'])
                    );
        }
        
        return true;
        
    }
    
    private function remove_items()
    {
        $query = ' SELECT `current`.* 
                   FROM `'.$this->_db->dbprefix('grutinet').'` AS `current` 
                   LEFT JOIN `'.$this->_db->dbprefix('grutinet_temp').'` AS `temp` 
                   ON `current`.`ean` = `temp`.`ean`  
                   WHERE `temp`.`ean` IS NULL 
        ';
        
        $result = $this->_db->query($query);
        
        if ($result->num_rows() > 0) 
        {
            $this->remove_data($this->_db->dbprefix('grutinet'), $result->result('array'));
            return $result->result('array');
        }        
    }
    
    private function remove_data($table, $data = array()){
        
        $query = ' DELETE FROM `'.$table.'` 
                   WHERE `ean`=? 
        ';
              
        foreach ($data as $item)
        {
           $this->_db->query($query, array($item['ean']));
        }
        
        return true;
    }
    
    private function sendMail($subject, $body, $items = array())
    {
        if (!empty($subject) && !empty($this->_config['mail_to']))
        {
            if (empty($body))
            {
                $body = '';
            }
            
            if (count($items) > 0 && is_array($items))
            {
                
                $body .= '<h3>'.$subject.'</h3>';
                $body .= '<h4>List of items</h4>';
                $tmpl = array ('table_open' => '<table border="1">');
                $this->_CI->table->set_template($tmpl);
                $this->_CI->table->set_heading('EAN', 'NOMBRE', 'STOCK', 'PRICE');
                
                foreach ($items as $item)
                {
                    $this->_CI->table->add_row(
                                    $item['ean'], 
                                    $item['product_name'], 
                                    $item['stock'],
                                    number_format($item['price'], 2)."&euro;"
                                    );
                }
                
                $body .= $this->_CI->table->generate();
                
            }
            
            if (!empty($body))
            {
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                $headers .= 'From: Amazoni_Sync_Process' . "\r\n";

                return mail($this->_config['mail_to'], $subject, $body, $headers);
            }
        }
        
        return false;
    }
}