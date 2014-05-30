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
    private $_file_header       = "Pedido Número: E34387 -- Dirección: Buyin Comercio Web SL Baza S/N Edif. ICR 2H Pol. Ind. Juncaril cp:18220 Albolote Granada Teléfono: 958490405 \r\n";
    private $_IVA_tax = 0;
    private $_orders_statuses = array();


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
        $this->load->library('excel');
        
        $this->_IVA_tax = $this->taxes_model->getIVAtax();
        
        $this->load->helper('my_string_helper');
        $this->load->helper('file');
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
        if($service == 'export_engelsa')
        {
            $date = date('d-m-Y_H-i-s', time());
        
            $name = 'engelsa_products_order_'.$date;

            return $name . '.xls';
        }
        if($service == 'export_pinternacional')
        {
            $date = date('d-m-Y_H-i-s', time());
        
            $name = 'pinternacional_products_order_'.$date;

            return $name . '.xls';
        }
        if($service == 'export_coqueteo')
        {
            $date = date('d-m-Y_H-i-s', time());
        
            $name = 'coqueteo_products_order_'.$date;

            return $name . '.xls';
        }
        if($service == 'export__warehouse')
        {
            $date = date('d-m-Y_H-i-s', time());
        
            $name = 'warehouse_products_order_'.$date;

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
    
    private function _get_provider_order_xls($provider_order_id)
    {
        // Prepare data
        
        $products = $this->providers_model->get_provider_order($provider_order_id);
        
        $provider_name = $this->providers_model->get_provider_name_by_order_id($provider_order_id);
        
        $objPHPExcel = new PHPExcel();
        
        $objPHPExcel->getProperties()->setCreator("Amazoni4");
        $objPHPExcel->getProperties()->setLastModifiedBy("Amazoni4");
        $objPHPExcel->getProperties()->setTitle($provider_name." products order. Date: ".date('r', time()));
        $objPHPExcel->getProperties()->setSubject($provider_name." products order. Date: ".date('r', time()));
        $objPHPExcel->getProperties()->setDescription($provider_name." products order. Date: ".date('r', time()));
        
        $objPHPExcel->setActiveSheetIndex(0);
        
        $objPHPExcel->getActiveSheet()->setTitle(humanize($provider_name)." order");
        
        $header = array(
            
            'NOMBRE',
            'EAN DEL PRODUCTO',
            'CANTIDAD',
            
        );
        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $this->_file_header);
        
        $i = 0;
        foreach ($header as $cell)
        {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, 2, $cell);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($i, 2)->getFill()
            ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => 'ededed')
            ));
            $i++;
        }
        
        $i = 3;
        $total_cost = 0;
        foreach ($products as $product)
        {
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(0, $i, $product->product_name, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(1, $i, $product->sku, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1, $i)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(2, $i, $product->quantity, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $i++;
        }
        
        
        // Write a file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        
        $filename = FCPATH .'upload/'.  $this->construct_file_name('export_'. strtolower($provider_name));
        
        $file = $objWriter->save($filename);
                
        return read_file($filename);
    }
    
    public function download_provider_order($id)
    {
        $file = new stdClass();
        
        $provider_name = $this->providers_model->get_provider_name_by_order_id($id);
        
        $file->data = $this->_get_provider_order_xls($id);
        $file->name = $this->construct_file_name('export_'. strtolower($provider_name));
        
        if(!empty($file->data))
        {
            return $file;
        }
        return FALSE;
    }
    
    private function get_file_data_export_engelsa()
    {
        $provider_order_id = $this->providers_model->create_provider_order('ENGELSA');
        
        if($provider_order_id === false)
        {
            return FALSE;
        }
        
        return $this->_get_provider_order_xls($provider_order_id);
    }
    
    private function get_file_data_export_pinternacional()
    {
        $provider_order_id = $this->providers_model->create_provider_order('PINTERNACIONAL');
        
        if($provider_order_id === false)
        {
            return FALSE;
        }
        
        return $this->_get_provider_order_xls($provider_order_id);
    }
    
    private function get_file_data_export_coqueteo()
    {
        $provider_order_id = $this->providers_model->create_provider_order('COQUETEO');
        
        if($provider_order_id === false)
        {
            return FALSE;
        }
        
        return $this->_get_provider_order_xls($provider_order_id);
    }
    
    private function get_file_data_export_warehouse()
    {
        $provider_order_id = $this->providers_model->create_provider_order('_WAREHOUSE');
        
        if($provider_order_id === false)
        {
            return FALSE;
        }
        
        return $this->_get_provider_order_xls($provider_order_id);
    }
    
    public function batch_update_orders_statuses()
    {
        if(count($this->_orders_statuses) > 0)
        {
            foreach ($this->_orders_statuses as $r)
            {
                $this->dashboard_model->set_status((int)$r['id'], $r['status']);
            }
            
            return true;
        }
        
        return FALSE;
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
        
        if(  $service == 'fedex_gls_summary' 
             || $service == 'export_engelsa_summary'
             || $service == 'export_pinternacional_summary'
             || $service == 'export_coqueteo_summary'
             || $service == 'export_warehouse_summary'
        )
        {
            $query = ' SELECT * FROM `pedidos` 
                       WHERE `procesado` = \'PREPARACION_ENGELSA_FEDEX\' 
                       OR `procesado` = \'PREPARACION_ENGELSA_GLS\' 
                       OR `procesado` = \'PREPARACION_ENGELSA_PACK\' 
                       OR `procesado` = \'PREPARACION_ENGELSA_TOURLINE\' 
                       ORDER BY `id` ASC 
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
        $date_from      = $this->input->post("date_from");
        $date_to        = $this->input->post("date_to");
        
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
        if( !empty($date_from) && !empty($date_to) )
        {
            $this->db->where('p.created_on >=', $date_from);
            $this->db->where('p.created_on <=', $date_to);
        }
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
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($i, 1)->getFill()
            ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => 'ededed')
            ));
            $i++;
        }
        
        $i = 2;
        
        foreach ($products as $product)
        {
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(0, $i, $product['sku'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $i)->getFont()->setBold(true);
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