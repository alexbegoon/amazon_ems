<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Export CSV files model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Export_csv_model extends CI_Model
{
    
    private $_filename_template = 'CSV_export_?';
    private $_file_extension    = '.csv';


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('dashboard/dashboard_model');
        $this->load->model('engelsa/engelsa_model');
        
        $this->load->helper('my_string_helper');
    }
    
    public function prepare_file($service)
    {
        $file = new stdClass();
        
        $file->name = $this->construct_file_name($service);
        
        $file->data = $this->get_file_data($service);
        
        if(!empty($file->name) && !empty($file->data))
        {
            return $file;
        }
        return false;
    }
    
    private function construct_file_name($service)
    {
//        if(is_string($service) && !empty($service))
//        {
//            $name = str_replace('?', $service, $this->_filename_template);
//        }
//        else
//        {
//            $name = str_replace('_?', '', $this->_filename_template);
//        }
        
        if($service == 'generar_gls')
        {
            $date = date('d-m-Y', time());
        
            $name = 'GLS-ENVIADOS-'.$date;

            return $name . $this->_file_extension;
        }
        
        if($service == 'generar_fedex')
        {
            $date = date('d-m-Y', time());
        
            $name = 'FEDEX-ENVIADOS-'.$date;

            return $name . '.txt';
        }
        
        $date = date('j-n-Y', time());
        
        $name = str_replace('?', $date, $this->_filename_template);
        
        return $name . $this->_file_extension;
    }
    
    private function get_file_data($service)
    {
        if(is_string($service) && !empty($service))
        {
            $method = 'get_file_data_' . $service;
            return $this->{$method}();
        }
        return false;        
    }
    
    private function get_file_data_fedex_gls()
    {
        $header = "Pedido Número: E34387 -- Dirección: Buyin Comercio Web SL Baza S/N Edif. ICR 2H Pol. Ind. Juncaril cp:18220 Albolote Granada Teléfono: 958490405;;"."\r\n";
        $header .="NOMBRE;EAN DEL PRODUCTO;CANTIDAD;PRECIO"."\r\n";
        
        $query = 'SELECT * FROM `pedidos` 
                  WHERE `procesado` = \'PREPARACION_ENGELSA_FEDEX\' 
                  OR `procesado` = \'PREPARACION_ENGELSA_GLS\' 
                  OR `procesado` = \'PREPARACION_ENGELSA_PACK\' 
        ';
        
        $result = $this->db->query($query);
        
        if ($result)
        {
            
            $orders = $result->result('array');
            
            $products = array();        
            $ids = array();
            
            foreach ($orders as $order)
            {
                for($i=1; $i<=10; $i++)
                {
                    if(!empty($order['sku'.$i]))
                    {
                        $order['sku'.$i] = str_replace('#', '', $order['sku'.$i]);
                        $order['sku'.$i] = '#'.$order['sku'.$i];
                        
                        if(array_key_exists($order['sku'.$i],$products))
                        {
                            $products[$order['sku'.$i]]['qty'] += $order['cantidad'.$i];
                        }
                        else
                        {
                            $product_in_engelsa = $this->engelsa_model->get_product($order['sku'.$i]);
                            if($product_in_engelsa)
                            {
                                $name   = $product_in_engelsa->name;
                                $price  = $product_in_engelsa->price;
                            }
                            else
                            {
                                $name   = 'no name';
                                $price  = 0;
                            }
                            
                            $products[$order['sku'.$i]] = array('qty' => $order['cantidad'.$i],
                                                                'sku' => $order['sku'.$i],
                                                                'name' => utf8_decode($name),
                                                                'price' => (float)$price);
                        } 
                    } 
                }
                
                if($order['procesado'] == 'PREPARACION_ENGELSA_FEDEX')
                {
                    $this->dashboard_model->set_status((int)$order['id'], 'PEDIDO_ENGELSA_FEDEX');
                }
                elseif($order['procesado'] == 'PREPARACION_ENGELSA_GLS')
                {
                    $this->dashboard_model->set_status((int)$order['id'], 'PEDIDO_ENGELSA_GLS');
                }
                elseif($order['procesado'] == 'PREPARACION_ENGELSA_PACK')
                {
                    $this->dashboard_model->set_status((int)$order['id'], 'PEDIDO_ENGELSA_PACK');
                }
            }
            
            if(empty($products))
            {
                return false;
            }
            
            $this->load->model('stokoni/stokoni_model'); 
            
            $products_in_stock_temp = $this->stokoni_model->get_all_products_from_temp();// These products were sold from warehouse since last file downloading
            $this->stokoni_model->clear_temp_stock();
                                
            if($products_in_stock_temp)
            {
                foreach($products_in_stock_temp as $product_in_stock_temp)
                {
                    if(isset($products['#'.$product_in_stock_temp->ean]))
                    {
                        $products['#'.$product_in_stock_temp->ean]['qty'] -= $product_in_stock_temp->quantity;

                        if($products['#'.$product_in_stock_temp->ean]['qty'] == 0)
                        {
                            unset($products['#'.$product_in_stock_temp->ean]);
                        }
                    }
                }
            }
            
            foreach ($products as $key => $row)    
            {
                $qty[$key]  = $row['qty'];
            }
            
            array_multisort($qty, SORT_DESC, $products);
            
            // Create Total for products from Engelsa
            $total_price_engelsa = 0;
            foreach($products as $product)
            {
                $total_price_engelsa += ( $product['qty'] * $product['price'] );
            }
            $products[] = array('name'=>'', 'qty'=>'', 'sku'=>'TOTAL COST', 'price' =>  number_format($total_price_engelsa, 2). " ".chr(128));
            
            
            if($products_in_stock_temp)
            {
                // Delimeter
                for($i=0; $i<=2; $i++)
                {
                    $products[] = array('name'=>'', 'sku'=>'', 'qty'=>'');
                }
                $products[] = array('name'=>'The products that were sold from warehouse', 'sku'=>'', 'qty'=>'');
                $products[] = array('name'=>'NAME', 'sku'=>'EAN', 'qty'=>'CANTIDAD', 'price'=>'PRECIO');

                foreach($products_in_stock_temp as $product_in_stock_temp)
                {
                    $products[] = array('qty' => $product_in_stock_temp->quantity,
                                        'sku' => '#'.$product_in_stock_temp->ean,
                                        'name' => utf8_decode($product_in_stock_temp->name), 
                                        'price' => (float)$product_in_stock_temp->price);
                }   
                
                $products[] = array('name'=>'', 'qty'=>'', 'sku'=>'TOTAL COST', 'price'=>  number_format($products_in_stock_temp[0]->total_price, 2) . " ".chr(128));
            }
            
            $file_body = '';
            
            $file_body .= utf8_decode($header);
            
            foreach ($products as $product)
            {
                if(isset($product['price']))
                {
                    if(is_float($product['price']))
                    {
                        $price = $product['price'];
                        $file_body .= $product['name'].";".$product['sku'].";".$product['qty'].";".number_format($price, 2). " ".chr(128)."\r\n";
                    }
                    elseif(is_string($product['price'])) 
                    {
                        $file_body .= $product['name'].";".$product['sku'].";".$product['qty'].";".$product['price']."\r\n";
                    }
                }
                else 
                {
                    $file_body .= $product['name'].";".$product['sku'].";".$product['qty']."\r\n";
                }
            }
            return $file_body;
        }
        
        return false;
    }
    
    private function get_file_data_generar_gls()
    {
        $query = ' SELECT * 
                   FROM  `pedidos` 
                   WHERE  `procesado` =  \'PEDIDO_ENGELSA_GLS_AQUI\' 
                   OR     `procesado` =  \'PEDIDO_MARABE_AQUI\' 
        ';
        
        $result = $this->db->query($query);
        
        if($result)
        {
            $orders = $result->result();
            
            $row = '';
            
            foreach ($orders as $order)
            {
                $row .= getInnerSubstring(str_replace(';', ' ', $order->pedido), '-') . ';';
                $row .= str_replace(';', ' ', $order->nombre) . ';';
                $row .= str_replace(';', ' ', $order->direccion) . ';';
                $row .= str_replace(';', ' ', $order->codigopostal) . ';';
                $row .= str_replace(';', ' ', $order->estado) . ';';
                $row .= str_replace(';', ' ', $order->pais) . ';';
                $row .= 2 . ';';
                $row .= str_replace(';', ' ', $order->telefono) . ';';
//                if(strlen(str_replace(';', ' ', $order->direccion)) <= 33)
//                {
                $row .= str_replace(';', ' ', $order->direccion) . ';';
                $row .= '' . ';';
//                } 
//                else
//                {                    
//                    $position_of_end_I = strrpos(substr(str_replace(';', ' ', $order->direccion), 0, 32), ' ');
//                    
//                    $row .= substr(str_replace(';', ' ', $order->direccion), 0, $position_of_end_I) . ';';
//                    $row .= substr(str_replace(';', ' ', $order->direccion), $position_of_end_I) . ';';
//                }
                $row .= str_replace(';', ' ', $order->correo ? $order->correo : 'info@buyin.es') . ';';
                $row .= "\r\n";
                
                $this->dashboard_model->set_status((int)$order->id, 'ENVIADO_GLS');
            }
            return utf8_decode($row);
        }
        
        return false;
    }
    
    private function get_file_data_generar_fedex()
    {
        $query = ' SELECT * 
                   FROM  `pedidos` 
                   WHERE  `procesado` =  \'PEDIDO_ENGELSA_FEDEX_AQUI\' 
        ';
        
        $result = $this->db->query($query);
        
        if($result)
        {
            $orders = $result->result();
            
            $row = '';
            
            foreach ($orders as $order)
            {
                $row .= 0 . ',';
                $row .= "\"020\"1" . ',';
                $row .= "\"" . getInnerSubstring(str_replace(',', ' ', $order->pedido), '-') . "\"12" . ',';
                $row .= "\"" . str_replace(',', ' ', $order->nombre) . "\"13" . ',';
                $row .= "\"" . str_replace(',', ' ', $order->direccion) . "\"15" . ',';
                $row .= "\"" . str_replace(',', ' ', $order->estado) . "\"16" . ',';
                $row .= "\"" . str_replace(',', ' ', $order->estado) . "\"17" . ',';
                $row .= "\"" . str_replace(',', ' ', $order->codigopostal) . "\"18" . ',';
                $row .= "\"" . str_replace(',', ' ', $order->telefono) . "\"112" . ',';
                $row .= "\"5\"50" . ',';
                $row .= "\"" . str_replace(',', ' ', $order->pais) . "\"79" . ',';
                $row .= "\"gift makeup\"119" . ',';
                $row .= "\"4000\"68" . ',';
                $row .= "\"USD\"113" . ',';
                $row .= "\"Y\"1681" . ',';
                $row .= "\"Y\"2806" . ',';
                $row .= "\"Y\"31" . ',';
                $row .= "\"BUYIN\"";
                $row .= "\r\n";
                
                $this->dashboard_model->set_status((int)$order->id, 'ENVIADO_FEDEX');
            }
            
            return $row;
        }
        
        return false;
    }
    
    private function get_file_data_generar_pack()
    {
        $query = ' SELECT * 
                       FROM `pedidos` 
                       WHERE `procesado` = \'PEDIDO_ENGELSA_PACK_AQUI\' 
            ';
        
        $result = $this->db->query($query);
        
        if($result)
        {
            $orders = $result->result();
                        
            foreach ($orders as $order)
            {
                $this->dashboard_model->set_status((int)$order->id, 'ENVIADO_PACK');
            }
        }
    }
    
    public function get_summary($service)
    {
        if($service == 'generar_gls_summary')
        {
            $query = ' SELECT *,
                       (SELECT SUM(`ingresos`) 
                        FROM `pedidos` 
                        WHERE  `procesado` LIKE  \'PEDIDO_ENGELSA_GLS_AQUI\' 
                        OR     `procesado` LIKE  \'PEDIDO_MARABE_AQUI\' 
                        ) AS `total_ingresos`, 
                       (SELECT SUM(`gasto`) 
                        FROM `pedidos` 
                        WHERE  `procesado` LIKE  \'PEDIDO_ENGELSA_GLS_AQUI\' 
                        OR     `procesado` LIKE  \'PEDIDO_MARABE_AQUI\' 
                        ) AS `total_gasto` 
                       FROM  `pedidos` 
                       WHERE  `procesado` LIKE  \'PEDIDO_ENGELSA_GLS_AQUI\' 
                       OR     `procesado` LIKE  \'PEDIDO_MARABE_AQUI\' 
            ';
        }
        
        if($service == 'generar_fedex_summary')
        {
            $query = ' SELECT *,
                       (SELECT SUM(`ingresos`) 
                        FROM `pedidos` 
                        WHERE  `procesado` LIKE  \'PEDIDO_ENGELSA_FEDEX_AQUI\' 
                        ) AS `total_ingresos`, 
                       (SELECT SUM(`gasto`) 
                        FROM `pedidos` 
                        WHERE  `procesado` LIKE  \'PEDIDO_ENGELSA_FEDEX_AQUI\' 
                        ) AS `total_gasto`  
                       FROM  `pedidos` 
                       WHERE  `procesado` LIKE  \'PEDIDO_ENGELSA_FEDEX_AQUI\' 
            ';
        }
        
        if($service == 'fedex_gls_summary')
        {
            $query = ' SELECT * FROM `pedidos` 
                       WHERE `procesado` = \'PREPARACION_ENGELSA_FEDEX\' 
                       OR `procesado` = \'PREPARACION_ENGELSA_GLS\' 
                       OR `procesado` = \'PREPARACION_ENGELSA_PACK\' 
            ';
        }
        
        if($service == 'generar_pack_summary')
        {
            $query = ' SELECT *, 
                       (SELECT SUM(`ingresos`) 
                        FROM `pedidos` 
                        WHERE  `procesado` =  \'PEDIDO_ENGELSA_PACK_AQUI\' 
                        ) AS `total_ingresos`, 
                       (SELECT SUM(`gasto`) 
                        FROM `pedidos` 
                        WHERE  `procesado` =  \'PEDIDO_ENGELSA_PACK_AQUI\' 
                        ) AS `total_gasto` 
                       FROM `pedidos` 
                       WHERE `procesado` = \'PEDIDO_ENGELSA_PACK_AQUI\' 
            ';
        }
        
        $result = $this->db->query($query);
        
        if($result)
        {
            return $result->result();
        }
        
        return false;
    }
    
    public function get_orders_for_printer($service)
    {
        //Load models
        $this->load->model('incomes/shipping_costs_model');
        $this->load->model('products/products_model');
        
        $orders_for_printer = array();
        
        $orders = $this->get_summary($service);
        
        if($orders)
        {
            foreach ($orders as $order)
            {
                $country = $this->shipping_costs_model->get_country_name_by_code($order->pais);
                
                $order_for_printer = new stdClass();
                
                $order_for_printer->id                  = $order->pedido;
                $order_for_printer->name                = $order->nombre;
                $order_for_printer->address             = $order->direccion;
                $order_for_printer->zip                 = $order->codigopostal;
                $order_for_printer->city                = $order->estado;
                $order_for_printer->payment_method      = $order->formadepago;
                $order_for_printer->web                 = $order->web;
                $order_for_printer->country             = $country;
                
                for($i = 1; $i <= 10; $i++)
                {
                    $sku = 'sku'.$i;
                    $qty = 'cantidad'.$i;
                    $product_name = 'product_name_'.$i;
                    $unit    = 'unit_'.$i;
                    
                    if(!empty($order->$sku))
                    {
                        if($this->products_model->get_product($order->$sku,$order->web))
                        {
                            $order_for_printer->$product_name = $this->products_model->get_product($order->$sku,$order->web)->product_name;
                        }
                        else
                        {
                            $order_for_printer->$product_name = '';
                        }
                        
                        $order_for_printer->$unit = $order->$qty;
                        $order_for_printer->$sku = $order->$sku;
                    }
                }    
                
                $orders_for_printer[] = $order_for_printer;
            }
        }
        
        return $orders_for_printer;
    }
            
}
    