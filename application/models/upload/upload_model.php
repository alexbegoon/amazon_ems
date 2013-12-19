<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Upload model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

class Upload_model extends CI_Model {
        
    private $_orders = array(), $_prices = array(), $_stock_in_engelsa = array(), $_stock_in_warehouse = array(), $_data = array();

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('stokoni/stokoni_model');
        $this->load->model('engelsa/engelsa_model');
        $this->load->model('incomes/shipping_costs_model');
        $this->load->model('incomes/taxes_model');
        $this->load->model('products/products_model');
    }
    
    public function getOrders($file_path, $service) {
        
        if (!empty($file_path) && !empty($service)) {
            $data = $this->parseTXTfile($file_path, $service);
            
            $this->_orders = $this->convertCurrency($data);
            
            if ($this->_orders)
            {
                $this->clearTemp();
                $this->storeData($this->_orders);
                $this->groupDuplicates();
                $this->set_gasto_to_temp();
                $this->set_procesado_to_temp();

                $query = ' SELECT * 
                           FROM `'.$this->db->dbprefix('pedidos_temp').'`   
                ';

                $result = $this->db->query($query);

                if ($result)
                {
                    return $result->result();
                }
                else
                {
                    return false;
                }        
            }    
        }
    }
    
    private function parseTXTfile($file_path, $service) {
        
        $txt_file    = file_get_contents($file_path);
        
        
        if ($txt_file) {
            
            switch ($service) {
                case 'Europe' : $web = 'AMAZON';
                    break;
                case 'USA'    : $web = 'AMAZON-USA';
                    break;
            }
            
            $rows = explode("\n", $txt_file);
            array_shift($rows); // Delete header
            
            $info = array();
            
            foreach($rows as $row => $data)
            {
                //get row data
                if (!empty($data)) {
                    $data = utf8_encode($data);
                    $row_data = explode("\t", $data);
                    
                    $info[$row]['pedido']         = $row_data[0];
                    $info[$row]['nombre']         = $row_data[5];
                    $info[$row]['fechaentrada']   = date('Y-m-d', time());
                    $info[$row]['direccion']      = trim($row_data[17] . ' ' . $row_data[18]);
                    $info[$row]['telefono']       = str_replace(' ', '', $row_data[6]);
                    $info[$row]['codigopostal']   = str_replace(' ', '', $row_data[22]);
                    $info[$row]['pais']           = $row_data[23];
                    $info[$row]['estado']         = $row_data[20];
                    $info[$row]['web']            = $web;
                    $info[$row]['sku']            = str_replace('FR#BUYIN2012-', '#', str_replace('DE-', '#', $row_data[7]));
                    if($row_data[9] >= 1)
                    {
                        $info[$row]['price']      = $row_data[11] / $row_data[9];
                    }
                    $info[$row]['quantity']       = $row_data[9];
                    $info[$row]['currency']       = $row_data[10];
                    $info[$row]['shipping_price'] = $row_data[13];
                    $info[$row]['procesado']      = $this->getProcesado($info[$row]['sku'], $info[$row]['quantity'], $info[$row]['pedido']);
                    $info[$row]['in_stokoni']     = $this->get_in_stokoni($info[$row]['pedido']);
                }
                
            }
            
            if (!empty($info)) {
                return $info;
            } else {
                return false;
            }
            
        }
        
        return false;
        
    }
    
    private function convertCurrency($data)
    {
        $this->load->library('currency');
        
        if (!empty($data))
        {
            $new_data = array();
            foreach ($data as $order)
            {
                $new_data[] = $this->currency->convertCurrency($order);
            }
            return $new_data;
        }
        else
        {
            return false;
        }
        
    }
    
    private function storeData($data)
    {
        if (!empty($data))
        {
            $query = ' INSERT INTO `'.$this->db->dbprefix('pedidos_temp').'` 
                       (pedido, nombre, fechaentrada, direccion, telefono, 
                        codigopostal, pais, estado, procesado, web, sku1, 
                        precio1, cantidad1, ingresos, in_stokoni) 
                       VALUES 
                       (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ';
            
            $params = array(); 
            
            foreach ($data as $order)
            {
                $params[] = $order['pedido'];
                $params[] = $order['nombre'];
                $params[] = $order['fechaentrada'];
                $params[] = $order['direccion'];
                $params[] = $order['telefono'];
                $params[] = $order['codigopostal'];
                $params[] = $order['pais'];
                $params[] = $order['estado'];
                $params[] = $order['procesado'];
                $params[] = $order['web'];
                $params[] = $order['sku'];
                $params[] = $order['price'];
                $params[] = (int)$order['quantity'];
                $params[] = $this->get_ingresos($order['price'],$order['shipping_price'],$order['quantity']);
                $params[] = $order['in_stokoni'];
                       
                try
                {
                    $this->db->query($query, $params);
                }
                catch (PDOException $e)
                {
                    echo $e->getMessage();  
                }       
                $params = array();
            }
        } 
        else 
        {
            return false;
        }
    }
    
    private function clearTemp()
    {
        $query = ' TRUNCATE `'.$this->db->dbprefix('pedidos_temp').'` ';
        
        return $this->db->query($query);
    }
    
    private function groupDuplicates()
    {
        // Extract duplicates
        $query = ' SELECT `id`, `pedido` 
                   FROM `'.$this->db->dbprefix('pedidos_temp').'` 
                   GROUP BY `pedido` 
                   HAVING COUNT(*) > 1 
        ';
        
        $result = $this->db->query($query);
        
        if ($result)
        {
            $duplicates = $result->result();
            
            foreach ($duplicates as $order)
            {
                $query = ' SELECT * 
                           FROM `'.$this->db->dbprefix('pedidos_temp').'` 
                           WHERE `pedido` = \''.$order->pedido.'\'  
                ';
                
                $duplicate_orders = $this->db->query($query);
                
                if ($duplicate_orders)
                {
                    $duplicate_orders = $duplicate_orders->result();
                    
                    $query = ' INSERT INTO `'.$this->db->dbprefix('pedidos_temp').'` 
                               (pedido, nombre, fechaentrada, 
                                direccion, telefono, codigopostal, 
                                pais, estado, procesado, 
                                web, 
                                sku1, precio1, cantidad1, 
                                sku2, precio2, cantidad2, 
                                sku3, precio3, cantidad3, 
                                sku4, precio4, cantidad4, 
                                sku5, precio5, cantidad5, 
                                sku6, precio6, cantidad6, 
                                sku7, precio7, cantidad7, 
                                sku8, precio8, cantidad8, 
                                sku9, precio9, cantidad9, 
                                sku10, precio10, cantidad10, 
                                ingresos, in_stokoni) 
                                VALUES 
                                (?, ?, ?, 
                                 ?, ?, ?, 
                                 ?, ?, ?, 
                                 ?, ?, ?, 
                                 ?, ?, ?, 
                                 ?, ?, ?, 
                                 ?, ?, ?, 
                                 ?, ?, ?, 
                                 ?, ?, ?, 
                                 ?, ?, ?, 
                                 ?, ?, ?, 
                                 ?, ?, ?, 
                                 ?, ?, ?, 
                                 ?, ?, ?                                  
                                )
                    ';
                    
                    $params     = array();
                    $ingresos   = 0;
                    $status     = '';
                    $in_stokoni = null;
                    
                    $first_order = $duplicate_orders[0];
                    $params[] = $first_order->pedido;
                    $params[] = $first_order->nombre;
                    $params[] = $first_order->fechaentrada;
                    $params[] = $first_order->direccion;
                    $params[] = $first_order->telefono;
                    $params[] = $first_order->codigopostal;
                    $params[] = $first_order->pais;
                    $params[] = $first_order->estado;
                    
                    for ( $i = 1; $i <= 10; $i++)
                    {
                        if (!empty($duplicate_orders[$i-1]->procesado) && $duplicate_orders[$i-1]->procesado == 'ROTURASTOCK')
                        {
                            $status = 'ROTURASTOCK';
                            break;
                        }
                        else
                        {
                            $status = 'NO';
                        }
                    }
                    
                    foreach ($duplicate_orders as $fields)
                    {
                        if($fields->in_stokoni == 1)
                        {
                            $in_stokoni = 1;
                        }
                        elseif($fields->in_stokoni == 0)
                        {
                            $in_stokoni = 0;
                            break;
                        }
                    }
                    
                    $params[] = $status;
                    $params[] = $first_order->web;
                    
                    for ( $i = 1; $i <= 10; $i++)
                    {
                        if (!empty($duplicate_orders[$i-1]))
                        {
                            $params[]  = $duplicate_orders[$i-1]->sku1;
                            $params[]  = $duplicate_orders[$i-1]->precio1;
                            $params[]  = $duplicate_orders[$i-1]->cantidad1;
                            $ingresos += $duplicate_orders[$i-1]->ingresos;
                        } 
                        else
                        {
                            $params[]  = null;
                            $params[]  = null;
                            $params[]  = 0;
                        }
                    }
                    
                    $params[] = $ingresos;
                    $params[] = $in_stokoni;
                    
                    $this->removeOrdersFromTemp($first_order->pedido);
                    
                    $this->db->query($query, $params);
                } 
                else 
                {
                    return false;
                }
            }
        }
        else
        {
            return false;
        }
    }
    
    private function removeOrdersFromTemp($pedido)
    {
        if (!empty($pedido))
        {
            $query = ' DELETE FROM `'.$this->db->dbprefix('pedidos_temp').'` 
                     WHERE `pedido` = \''.$pedido.'\'
            ';
            
            $this->db->query($query);
        }
    }
            
    private function getProcesado($sku, $qty, $pedido = null)
    {
        
        return 'NO';
           
    }
    
    private function getGasto($order, $safe_mode = true)
    {
        if(!empty($order))
        {
            $order_products = array();
            $shipping_price     = $this->getShippingPrice($order['pais'],$order['web']);
            $j = 0;    
            for($i=1; $i<=10; $i++)
            {
                if(empty($order['sku'.$i]))
                {
                    continue;
                }
                $order_products[$j]['sku']        = $order['sku'.$i];
                $order_products[$j]['price']      = (float)$order['precio'.$i];
                $order_products[$j]['quantity']   = (int)$order['cantidad'.$i];
                $order_products[$j]['order_id']   = $order['pedido'];
                $j++;
            }
            
            return $this->products_model->calculate_gasto($order_products,$shipping_price,$order['web'],$safe_mode);
            
        }
        else
        {
            return false;
        }
    }
    
    private function getPriceFromEngelsa($sku)
    {
        $sku = str_replace('#', '', $sku);
        
        return $this->engelsa_model->get_price($sku);
    }
    
    private function getShippingPrice($country_code,$web)
    {
        return $this->shipping_costs_model->get_shipping_price($web,$country_code);
    }
    
    private function getIVAtax()
    {
        return $this->taxes_model->getIVAtax();
    }
    
    public function storeOrders()
    {
        $query = ' SELECT * 
                   FROM `'.$this->db->dbprefix('pedidos_temp').'`   
        ';

        $result = $this->db->query($query);

        if ($result)
        {
            $orders = $result->result('array');
            
            $query = 'INSERT INTO `pedidos` 
                     (  `pedido`,`nombre`,`fechaentrada`,
                        `fechadepago`,`direccion`,`telefono`,
                        `codigopostal`,`pais`,`estado`,
                        `procesado`,`sku1`,`precio1`,`sku2`,`precio2`,
                        `sku3`,`precio3`,`sku4`,`precio4`,`sku5`,`precio5`,
                        `sku6`,`precio6`,`sku7`,`precio7`,`sku8`,`precio8`,
                        `sku9`,`precio9`,`sku10`,`precio10`,`cantidad1`,
                        `cantidad2`,`cantidad3`,`cantidad4`,`cantidad5`,
                        `cantidad6`,`cantidad7`,`cantidad8`,`cantidad9`,
                        `cantidad10`,`ingresos`,`web`,`comentarios`,
                        `tracking`,`correo`,`gasto`,`localidad`,`formadepago`, `in_stokoni`) 
                     VALUES 
                     (  ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ) 
            ';
            
            $affected_rows = 0;
            
            foreach ($orders as $order)
            {
                if(!$this->isExists($order))
                {
                    $affected_rows++;
                    $this->db->query($query, array(
                                     $order['pedido'],
                                     $order['nombre'],
                                     $order['fechaentrada'],
                                     $order['fechadepago'],
                                     $order['direccion'],
                                     $order['telefono'],
                                     $order['codigopostal'],
                                     $order['pais'],
                                     $order['estado'],
                                     $order['procesado'],
                                     $order['sku1'],
                                     $order['precio1'],
                                     $order['sku2'],
                                     $order['precio2'],
                                     $order['sku3'],
                                     $order['precio3'],
                                     $order['sku4'],
                                     $order['precio4'],
                                     $order['sku5'],
                                     $order['precio5'],
                                     $order['sku6'],
                                     $order['precio6'],
                                     $order['sku7'],
                                     $order['precio7'],
                                     $order['sku8'],
                                     $order['precio8'],
                                     $order['sku9'],
                                     $order['precio9'],
                                     $order['sku10'],
                                     $order['precio10'],
                                     $order['cantidad1'],
                                     $order['cantidad2'],
                                     $order['cantidad3'],
                                     $order['cantidad4'],
                                     $order['cantidad5'],
                                     $order['cantidad6'],
                                     $order['cantidad7'],
                                     $order['cantidad8'],
                                     $order['cantidad9'],
                                     $order['cantidad10'],
                                     $order['ingresos'],
                                     $order['web'],
                                     $order['comentarios'],
                                     $order['tracking'],
                                     $order['correo'],
                                     $this->getGasto($order, false),
                                     $order['localidad'],
                                     $order['formadepago'],
                                     $order['in_stokoni']) );
                    
                    $this->products_model->store_history($order['web'],
                                                         $order['pedido'],
                                                         $this->db->insert_id(),
                                                         $order['procesado'],
                                                         $order['fechaentrada']);
                }
            }
            
            return array('success' => true, 'affected_rows' => $affected_rows);
            
        }
        else
        {
            return false;
        }            
    }
    
    
    /**
     * Check order existing in pedido table
     * 
     * @param object $order
     * 
     */
    private function isExists($order)
    {
        if(!empty($order) && is_array($order))
        {
            $query = ' SELECT `id` 
                       FROM `pedidos` 
                       WHERE `pedido` = \''.$order['pedido'].'\' 
                       AND   `web` = \''.$order['web'].'\'     
            ';
            
            $result = $this->db->query($query);
            
            if($result->num_rows() > 0)
            {
                return true;
            }
            else 
            {
                return false;
            }
            
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Calculate gasto for all orders of temp table
     * 
     */
    private function set_gasto_to_temp()
    {
         $query = ' SELECT * 
                    FROM `'.$this->db->dbprefix('pedidos_temp').'`  
         ';

         $result = $this->db->query($query);

         if ($result)
         {
             $orders = $result->result('array');
             
             $query = ' UPDATE `'.$this->db->dbprefix('pedidos_temp').'` 
                        SET `gasto` = ?  
                        WHERE `id` = ?  
            ';
             
             foreach ($orders as $order)
             {
                 $gasto = $this->getGasto($order, true);
                 $this->db->query($query, array($gasto, $order['id']));
             }
         }
         else
         {
             return false;
         }  
    }
    
    /**
     * Set procesado to temp table
     */
    private function set_procesado_to_temp()
    {
        $query = ' SELECT * 
                    FROM `'.$this->db->dbprefix('pedidos_temp').'`  
         ';

         $result = $this->db->query($query);

         if ($result)
         {
             $orders = $result->result('array');
             
             $query = ' UPDATE `'.$this->db->dbprefix('pedidos_temp').'` 
                        SET `procesado` = ?  
                        WHERE `id` = ?  
            ';
             
             foreach ($orders as $order)
             {
                 if(isset($this->products_model->products_sales_history_data[$order['web']][$order['pedido']]['out_of_stock']))
                 {
                     if($this->products_model->products_sales_history_data[$order['web']][$order['pedido']]['out_of_stock'] == true)
                     {
                         $procesado = 'ROTURASTOCK';
                     }
                 }
                 else
                 {
                     $procesado = 'NO';
                 }   
                 $this->db->query($query, array($procesado, $order['id']));
             }
         }
         else
         {
             return false;
         }  
    }

    public function overwriteGasto()
        {
        $web = array('AMAZON', 'AMAZON-USA');
        
        $start_time = '2013-08-01';
        
        $query = 'SELECT * FROM `pedidos` 
                  WHERE `fechaentrada` > \''.$start_time.'\'  
                  AND `web` IN( \''.implode('\', \'',$web).'\' ) 
                    
        ';
        
        $result = $this->db->query($query);
        
        if($result)
        {
            $orders = $result->result('array');
            $new_data = array();
            foreach($orders as $order)
            {
                $order['gasto'] = $this->getGasto($order, true);
                   
                $new_data[] = $order;
            }
            
            
            $query = ' UPDATE `pedidos` 
                       SET `gasto` = ?  
                       WHERE `id` = ?  
            ';
            
            foreach($new_data as $order2)
            {
                
                if ($order2['gasto'] != 0)
                {
                    $result = $this->db->query($query, array($order2['gasto'], $order2['id']));
                }
            }
        }
    }
    
    private function get_in_stokoni($pedido)
    {
        if(isset($this->_data['in_stokoni_field'][$pedido]))
        {
            return $this->_data['in_stokoni_field'][$pedido];
        }
        
        return null;
    }
    
    private function get_ingresos($price, $shipping_price, $quantity)
    {
        $tax = $this->taxes_model->get_tax_by_name('Amazon')->percent;
        
        $ingreso = ($price * $quantity + $shipping_price) - (($price * $quantity * $tax / 100) + ($shipping_price * $tax / 100));
        
        return $ingreso;
    }
}