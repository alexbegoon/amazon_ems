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
    private $_IVA_tax = 0;


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('dashboard/dashboard_model');
        $this->load->model('engelsa/engelsa_model');
        $this->load->model('incomes/providers_model');
        $this->load->model('incomes/taxes_model');
        $this->load->model('products/products_model');
        $this->load->model('stokoni/stokoni_model');
        
        $this->_IVA_tax = $this->taxes_model->getIVAtax();
        
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
        
        if($service == 'generar_new_products_coqueteo')
        {
            $date = date('d-m-Y_H-i-s', time());
        
            $name = 'Coqueteo_new_products_'.$date;

            return $name . '.xls';
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
                
        $query = 'SELECT * FROM `pedidos` 
                  WHERE `procesado` = \'PREPARACION_ENGELSA_FEDEX\' 
                     OR `procesado` = \'PREPARACION_ENGELSA_GLS\' 
                     OR `procesado` = \'PREPARACION_ENGELSA_PACK\' 
                     OR `procesado` = \'PREPARACION_ENGELSA_TOURLINE\' 
        ';
        
        $result = $this->db->query($query);
        
        if ($result)
        {
            
            $orders = $result->result('array');
            
            $data = array();
            
            // This report only should content products from ENGELSA and PINTERNACIONAL, also from WAREHOUSE
            
            $data['ENGELSA']            = array();
            $data['PINTERNACIONAL']     = array();
            $data['WAREHOUSE']          = array();
            
            $data['ENGELSA']['products']        = array();
            $data['PINTERNACIONAL']['products'] = array();
            $data['WAREHOUSE']['products']      = array();
            $engelsa_products                   = null;
            $pinternacional_products            = null;
            $warehouse_products                 = null;
            
            
            $data['ENGELSA']['meta'] = array(
                
                'pos_1' => 'ENGELSA PROVIDER',
                'pos_2' => 'NOMBRE',
                'pos_3' => 'EAN DEL PRODUCTO',
                'pos_4' => 'CANTIDAD',
                'pos_5' => 'PRECIO',
                'pos_6' => 'TOTAL COST'               
                
            );
            
            $data['PINTERNACIONAL']['meta'] = array(
                
                'pos_1' => 'PINTERNACIONAL PROVIDER',
                'pos_2' => 'NOMBRE',
                'pos_3' => 'EAN DEL PRODUCTO',
                'pos_4' => 'CANTIDAD',
                'pos_5' => 'PRECIO',
                'pos_6' => 'TOTAL COST'               
                
            );
            
            $data['WAREHOUSE']['meta'] = array(
                
                'pos_1' => 'OUR WAREHOUSE',
                'pos_2' => 'NAME',
                'pos_3' => 'EAN',
                'pos_4' => 'CANTIDAD',
                'pos_5' => 'PRECIO',
                'pos_6' => 'TOTAL COST'               
                
            );
            
            foreach ($orders as $order)
            {
                $this->db->where('order_id =',$order['id']);
                $this->db->where('canceled =',0);
                $query = $this->db->get('products_sales_history');
                
                if($query->num_rows() > 0)
                {
                    $order_products = $query->result();
                    
                    foreach ($order_products as $order_product)
                    {   
                        if(!empty($order_product->provider_name))
                        {
                            if($order_product->provider_name == 'ENGELSA' && !preg_match('/^#/', $order_product->sku))
                            {
                                $data[$order_product->provider_name]['products']['#'.$order_product->sku][] = $order_product;
                            }
                            else 
                            {
                                $data[$order_product->provider_name]['products'][$order_product->sku][] = $order_product;
                            }
                        }
                    }
                }
                                
                $update_data = array(
                    'csv_exported' => 1,
                    'csv_export_date' => date('Y-m-d H:i:s', time())
                            );

                $this->db->where('order_id', $order['id']);
                $this->db->update('products_sales_history', $update_data); 
                
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
                elseif($order['procesado'] == 'PREPARACION_ENGELSA_TOURLINE')
                {
                    $this->dashboard_model->set_status((int)$order['id'], 'PEDIDO_ENGELSA_TOURLINE');
                }
            }
            
            $file_body = '';
            
            $file_body .= utf8_decode($header)."\r\n";
            
            $data_rows = array();
            
            $data_rows[] = array('','','','','','','','');
            $data_rows[] = array('','','','','','','','');
            
            // ENGELSA header
            $data_rows[] = array(
                $data['ENGELSA']['meta']['pos_1'],
                '',
                '',
                '',
                '',
                '',
                '',
                ''                
            );
            $data_rows[] = array(
                $data['ENGELSA']['meta']['pos_2'],
                $data['ENGELSA']['meta']['pos_3'],
                $data['ENGELSA']['meta']['pos_4'],
                $data['ENGELSA']['meta']['pos_5'],
                '',
                '',
                '',
                ''                
            );
            $total_engelsa = 0;
            if(isset($data['ENGELSA']['products']))
            {
                foreach ($data['ENGELSA']['products'] as $product_sku => $product)
                {
                    $total_count = 0;
                    $subtotal_price = 0;
                    foreach ($product as $v)
                    {
                        $total_count += $v->quantity;
                        
                        $current_provider_price = $this->products_model->get_product_by_id((int)$v->provider_product_id)->price;
                        
                        $subtotal_price += $current_provider_price * $v->quantity * (1 + ($this->_IVA_tax / 100));
                    }
                    $engelsa_products[] = array(
                            preg_replace('/^\"+|^\'+|\"+$|\'+$/', '', trim(utf8_decode($product[0]->product_name))),
                            '"'.$product_sku.'"',
                            $total_count,
                            number_format($subtotal_price, 2). " ".chr(128),
                            '',
                            '',
                            '',
                            ''                
                                    );
                    $total_engelsa += $subtotal_price; 
                }
                
                // Sort products
                if(is_array($engelsa_products))
                {
                    usort($engelsa_products, array("Export_csv_model", "cmp"));
                
                    foreach($engelsa_products as $row)
                    {
                        $data_rows[] = $row;
                    }
                }
            }
            
            
            // ENGELSA footer
            $data_rows[] = array(
                '',
                '',
                $data['ENGELSA']['meta']['pos_6'],
                number_format($total_engelsa, 2). " ".chr(128),
                '',
                '',
                '',    
                ''                
            );
            $data_rows[] = array('','','','','','','','');
            $data_rows[] = array('','','','','','','','');
            $data_rows[] = array('','','','','','','','');
            
            // PINTERNACIONAL header
            $data_rows[] = array(
                $data['PINTERNACIONAL']['meta']['pos_1'],
                '',
                '',
                '',
                '',
                '',
                '',
                ''                
            );
            $data_rows[] = array(
                $data['PINTERNACIONAL']['meta']['pos_2'],
                $data['PINTERNACIONAL']['meta']['pos_3'],
                $data['PINTERNACIONAL']['meta']['pos_4'],
                $data['PINTERNACIONAL']['meta']['pos_5'],
                '',
                '',
                '',
                ''                
            );
            $total_pinternacional = 0;
            if(isset($data['PINTERNACIONAL']['products']))
            {
                foreach ($data['PINTERNACIONAL']['products'] as $product)
                {
                    $total_count = 0;
                    $subtotal_price = 0;
                    foreach ($product as $v)
                    {
                        $total_count += $v->quantity;
                            
                        $current_provider_price = $this->products_model->get_product_by_id((int)$v->provider_product_id)->price;
                        
                        $subtotal_price += $current_provider_price * $v->quantity * (1 + ($this->_IVA_tax / 100));
                    }
                    $pinternacional_products[] = array(
                            preg_replace('/^\"+|^\'+|\"+$|\'+$/', '', trim(utf8_decode($product[0]->product_name))),
                            '"'.$product[0]->sku_in_order.'"',
                            $total_count,
                            number_format($subtotal_price, 2). " ".chr(128),
                            '',
                            '',
                            '',
                            ''                
                                    );
                    $total_pinternacional += $subtotal_price; 
                }
                
                // Sort products
                if(is_array($pinternacional_products))
                {
                    usort($pinternacional_products, array("Export_csv_model", "cmp"));
                
                    foreach($pinternacional_products as $row)
                    {
                        $data_rows[] = $row;
                    }
                }
            }
            
            
            // PINTERNACIONAL footer
            $data_rows[] = array(
                '',
                '',
                $data['PINTERNACIONAL']['meta']['pos_6'],
                number_format($total_pinternacional, 2). " ".chr(128),
                '',
                '',
                '',    
                ''                
            );
            $data_rows[] = array('','','','','','','','');
            $data_rows[] = array('','','','','','','','');
            $data_rows[] = array('','','','','','','','');
            
            // WAREHOUSE header
            $data_rows[] = array(
                $data['WAREHOUSE']['meta']['pos_1'],
                '',
                '',
                '',
                '',
                '',
                '',
                ''                
            );
            $data_rows[] = array(
                $data['WAREHOUSE']['meta']['pos_2'],
                $data['WAREHOUSE']['meta']['pos_3'],
                $data['WAREHOUSE']['meta']['pos_4'],
                $data['WAREHOUSE']['meta']['pos_5'],
                '',
                '',
                '',
                ''                
            );
            $total_warehouse = 0;
            if(isset($data['_WAREHOUSE']['products']))
            {
                foreach ($data['_WAREHOUSE']['products'] as $product)
                {
                    $total_count = 0;
                    $subtotal_price = 0;
                    foreach ($product as $v)
                    {
                        $total_count += $v->quantity;
                        
                        $current_warehouse_price = $this->stokoni_model->getProduct((int)$v->warehouse_product_id)->price;
                        
                        $subtotal_price += $current_warehouse_price * $v->quantity * (1 + ($this->_IVA_tax / 100));
                    }
                    $warehouse_products[] = array(
                            preg_replace('/^\"+|^\'+|\"+$|\'+$/', '', trim(utf8_decode($product[0]->product_name))),
                            '"'.$product[0]->sku_in_order.'"',
                            $total_count,
                            number_format($subtotal_price, 2). " ".chr(128),
                            '',
                            '',
                            '',
                            ''                
                                    );
                    $total_warehouse += $subtotal_price; 
                }
                
                // Sort products
                if(is_array($warehouse_products))
                {
                    usort($warehouse_products, array("Export_csv_model", "cmp"));
                
                    foreach($warehouse_products as $row)
                    {
                        $data_rows[] = $row;
                    }
                }
            }
            
            // WAREHOUSE footer
            $data_rows[] = array(
                '',
                '',
                $data['WAREHOUSE']['meta']['pos_6'],
                number_format($total_warehouse, 2). " ".chr(128),
                '',
                '',
                '',    
                ''                
            );
            $data_rows[] = array('','','','','','','','');
            $data_rows[] = array('','','','','','','','');
            $data_rows[] = array('','','','','','','','');
            
            foreach ($data_rows as $row)
            {
                $file_body .= implode(";", $row)."\r\n";
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
                
                $this->dashboard_model->set_status((int)$order->id, 'PTE_ENVIO_GLS');
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
                
                $this->dashboard_model->set_status((int)$order->id, 'PTE_ENVIO_FEDEX');
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
                $this->dashboard_model->set_status((int)$order->id, 'PTE_ENVIO_PACK');
            }
        }
    }
    
    private function get_file_data_generar_tourline()
    {
        $query = ' SELECT * 
                       FROM `pedidos` 
                       WHERE `procesado` = \'PEDIDO_ENGELSA_TOURLINE_AQUI\' 
            ';
        
        $result = $this->db->query($query);
        
        if($result)
        {
            $orders = $result->result();
                        
            foreach ($orders as $order)
            {
                $this->dashboard_model->set_status((int)$order->id, 'PTE_ENVIO_TOURLINE');
            }
        }
    }
    
    private function get_file_data_generar_stokoni()
    {
        $query = ' SELECT * 
                       FROM `pedidos` 
                       WHERE `procesado` = \'PEDIDO_ENGELSA_GLS_AQUI\' 
                       OR `procesado` = \'PEDIDO_FEDEX_TOURLINE_AQUI\' 
                       OR `procesado` = \'PEDIDO_ENGELSA_FEDEX_AQUI\' 
            ';
        
        $result = $this->db->query($query);
        
        if($result)
        {
            $orders = $result->result();
                        
//            foreach ($orders as $order)
//            {
//                $this->dashboard_model->set_status((int)$order->id, 'PTE_ENVIO_TOURLINE');
//            }
        }
    }
    
    public function get_summary($service)
    {
        if($service == 'generar_gls_summary')
        {
            $query = ' SELECT *, LOWER(`languages`.`language`) as `language`, 
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
                       FROM  `pedidos` as `pedidos`
                       LEFT JOIN `'.$this->db->dbprefix('web_field').'` as `web_field` 
                       USING(`web`) 
                       LEFT JOIN `'.$this->db->dbprefix('languages').'` as `languages` 
                       ON  `languages`.`code` = `web_field`.`template_language`  
                       WHERE  `pedidos`.`procesado` LIKE  \'PEDIDO_ENGELSA_GLS_AQUI\' 
                       OR     `pedidos`.`procesado` LIKE  \'PEDIDO_MARABE_AQUI\' 
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
                       OR `procesado` = \'PREPARACION_ENGELSA_TOURLINE\' 
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
        
        if($service == 'generar_tourline_summary')
        {
            $query = ' SELECT *, 
                       (SELECT SUM(`ingresos`) 
                        FROM `pedidos` 
                        WHERE  `procesado` =  \'PEDIDO_ENGELSA_TOURLINE_AQUI\' 
                        ) AS `total_ingresos`, 
                       (SELECT SUM(`gasto`) 
                        FROM `pedidos` 
                        WHERE  `procesado` =  \'PEDIDO_ENGELSA_TOURLINE_AQUI\' 
                        ) AS `total_gasto` 
                       FROM `pedidos` 
                       WHERE `procesado` = \'PEDIDO_ENGELSA_TOURLINE_AQUI\' 
            ';
        }
        
        if($service == 'generar_stokoni_summary')
        {
            $query = ' SELECT *,
                       (SELECT SUM(`ingresos`) 
                        FROM `pedidos` 
                        WHERE  `procesado` = \'PEDIDO_ENGELSA_GLS_AQUI\' 
                       OR `procesado` = \'PEDIDO_FEDEX_TOURLINE_AQUI\' 
                       OR `procesado` = \'PEDIDO_ENGELSA_FEDEX_AQUI\' 
                        ) AS `total_ingresos`, 
                       (SELECT SUM(`gasto`) 
                        FROM `pedidos` 
                        WHERE  `procesado` = \'PEDIDO_ENGELSA_GLS_AQUI\' 
                       OR `procesado` = \'PEDIDO_FEDEX_TOURLINE_AQUI\' 
                       OR `procesado` = \'PEDIDO_ENGELSA_FEDEX_AQUI\' 
                        ) AS `total_gasto` 
                       FROM `pedidos` 
                       WHERE `procesado` = \'PEDIDO_ENGELSA_GLS_AQUI\' 
                       OR `procesado` = \'PEDIDO_FEDEX_TOURLINE_AQUI\' 
                       OR `procesado` = \'PEDIDO_ENGELSA_FEDEX_AQUI\' 
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
                $order_for_printer->other_info          = $order; 
                
                for($i = 1; $i <= 10; $i++)
                {
                    $sku = 'sku'.$i;
                    $qty = 'cantidad'.$i;
                    $product_name = 'product_name_'.$i;
                    $unit    = 'unit_'.$i;
                    
                    if(!empty($order->$sku))
                    {
                        if($this->products_model->get_product($order->$sku,$order->web)[0])
                        {
                            $order_for_printer->$product_name = $this->products_model->get_product($order->$sku,$order->web)[0]->product_name;
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
     
    private static function cmp($a, $b) 
    {
        return $b[2] - $a[2];
    }
    
    private function get_file_data_generar_new_products_coqueteo()
    {
        $this->load->library('excel');
        
        $file = null;
        
        $objPHPExcel = new PHPExcel();
        
        $objPHPExcel->getProperties()->setCreator("Amazoni4");
        $objPHPExcel->getProperties()->setLastModifiedBy("Amazoni4");
        $objPHPExcel->getProperties()->setTitle("Coqueteo new products report. Date: ".date('r', time()));
        $objPHPExcel->getProperties()->setSubject("Coqueteo new products report. Date: ".date('r', time()));
        $objPHPExcel->getProperties()->setDescription("Coqueteo new products report. Date: ".date('r', time()));
        
        $objPHPExcel->setActiveSheetIndex(0);
        
        $objPHPExcel->getActiveSheet()->setTitle("Coqueteo new products report");
        
        // Get new products from DB
        $products = array();
        
        $this->db->select('p.id');
        $this->db->from('providers_products as p');
        $this->db->group_by('p.sku');
        $this->db->having('COUNT(p.sku) = 1');
        
        $query = $this->db->get();
        
        $unique_id = array();
        foreach ( $query->result('array') as $row)
        {
            $unique_id[] = $row['id'];
        }
        
        $this->db->select('p.sku, p.product_name, p.price, p.stock, p.provider_name, p.brand, p.created_on, p.updated_on');
        $this->db->from('providers_products as p');
        $this->db->where_in('p.id', $unique_id);
        $this->db->where('p.provider_name =', 'COQUETEO');
        $this->db->order_by('p.stock', 'DESC');
        
        $query = $this->db->get();
        
        $products = $query->result('array');
        
        // Prepare data
                
        $header = array(
            'EAN',
            'Product Name',
            'Brand Name',
            'Stock',
            'Price',
            'Provider Name',
            'Creation Date',
            'Update Date'
        );
        
        $i = 0;
        foreach ($header as $cell)
        {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, 1, $cell);
            $i++;
        }
        
        $i = 2;
        
        foreach ($products as $product)
        {
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(0, $i, $product['sku'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(1, $i, $product['product_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(2, $i, $product['brand'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(3, $i, $product['stock'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(4, $i, $product['price'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(5, $i, $product['provider_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(6, $i, $product['created_on'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(7, $i, $product['updated_on'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }
        
        // Write a file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        
        $filename = FCPATH .'upload/'.$this->construct_file_name('generar_new_products_coqueteo');
        
        $file = $objWriter->save($filename);
                
        return read_file($filename);
    }
}