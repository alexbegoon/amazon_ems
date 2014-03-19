<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * The BuyIn Shopping Center (BSC). Model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Bsc_model extends CI_Model 
{
    private $_total_rows = 0;
    private $_unique_products_found = 0;

    public function __construct() 
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_overview($page)
    {
        $data = array();
        
        $post_data = $this->input->post();
        
        if(isset($post_data['period']))
        {
            $period = (int)$post_data['period'];
        }
        else
        {
            $period = 7;
        }
        
        $start_date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - $period, date('Y')));
        
        $end_date = date('Y-m-d', time());
        
        if( isset($post_data['date_from']) && isset($post_data['date_to']) )
        {
            if( !empty($post_data['date_from']) && !empty($post_data['date_to']) )
            {
                $start_date = $post_data['date_from'];
                $end_date = $post_data['date_to'];
            }
        }
        
        $order_statuses = array('ENVIADO_TOURLINE',
                                'ENVIADO_PACK',
                                'ENVIADO_MEGASUR',
                                'ENVIADO_MARABE',
                                'ENVIADO_GRUTINET',
                                'ENVIADO_GLS',
                                'ENVIADO_FEDEX');
      
        // Get top sales SKUs
        
        $this->db->select('h.sku, SUM(h.quantity) as total_quantity');
        $this->db->from('products_sales_history as h');
        $dbprefix = $this->db->dbprefix;
        $this->db->set_dbprefix(null);
        $this->db->join('pedidos as p','p.id = h.order_id','left');
        $this->db->set_dbprefix($dbprefix);
        $this->db->where('h.created_at >',$start_date);
        $this->db->where('h.created_at <',$end_date);
        $this->db->where_in('p.procesado',$order_statuses);
        $this->db->group_by('h.sku');
        $this->db->order_by('total_quantity','DESC');
        $this->db->limit('50',$page);
        
        $result = $this->db->get();
        
        if($result->num_rows() > 0)
        {
            $top_products = $result->result();
            
            $top_products_skus = array();
            
            foreach ($top_products as $p)
            {
                $top_products_skus[] = $p->sku;
                
                $products = $this->get_products_by_sku($p->sku);
                
                if( count($products) > 0 && is_array($products) )
                {
                    foreach ($products as $product)
                    {
                        $data[] = array(
                            
                            'product_id'            => $product->id,
                            'id'                    => $product->id,
                            'sku'                   => $p->sku,
                            'product_name'          => $product->product_name,
                            'provider_name'         => $product->provider_name,
                            'stock'                 => $product->stock,
                            'price'                 => $product->price,
                            'last_price'            => $product->last_price,
                            'last_price_date'       => $product->last_price_date,
                            'date_of_last_purchase' => $this->get_date_of_last_purchase($product->id),
                            'units_sold_warehouse'  => $this->total_count_warehouse_sales($p->sku, $product->provider_name, $start_date, $end_date),
                            'warehouse_trend'       => $this->get_warehouse_trend($p->sku, $product->provider_name, $start_date, $end_date),
                            'units_sold_buyin'      => $this->total_count_buyin_sales($product->id, $start_date, $end_date),
                            'buyin_trend'           => $this->get_buyin_trend($product->id, $start_date, $end_date),
                            'units_sold_amazon'     => $this->total_count_amazon_sales($product->id, $start_date, $end_date),
                            'amazon_trend'          => $this->get_amazon_trend($product->id, $start_date, $end_date),
                            'is_best_price'         => $this->is_best_price($p->sku),
                            'total_trend'           => $this->get_total_trend($product->id, $start_date, $end_date, $p->sku, $product->provider_name),
                            'quantity_needed'       => $product->quantity_needed,
                            'target_price'          => $product->target_price,
                            'provider_ordered'      => $product->provider_ordered,
                            'provider_order_date'   => $product->provider_order_date,
                            'is_checked'            => $product->is_checked > 0 ? TRUE : FALSE

                        );
                    }
                }
            }
            
            // Get counters
            
            $skus = array();
        
            $this->db->select('h.sku, SUM(h.quantity) as total_quantity');
            $this->db->from('products_sales_history as h');
            $dbprefix = $this->db->dbprefix;
            $this->db->set_dbprefix(null);
            $this->db->join('pedidos as p','p.id = h.order_id','left');
            $this->db->set_dbprefix($dbprefix);
            $this->db->where('h.created_at >',$start_date);
            $this->db->where('h.created_at <',$end_date);
            $this->db->where_in('p.procesado',$order_statuses);
            $this->db->group_by('h.sku');
            $this->db->order_by('total_quantity','DESC');

            $result = $this->db->get();
            
            $this->_unique_products_found = $this->_total_rows = $result->num_rows();            

            return $data;
        }
    }
    
    private function total_count_warehouse_sales($sku, $provider_name, $start_date, $end_date)
    {
        if($sku && $provider_name && $start_date && $end_date)
        {
            $this->db->cache_on();
            
            $this->db->select('id');
            $dbprefix = $this->db->dbprefix;
            $this->db->set_dbprefix(null);
            $this->db->from('stokoni');
            $this->db->set_dbprefix($dbprefix);
            $this->db->where('ean',$sku);
            $this->db->where('proveedor',$provider_name);
            $query = $this->db->get();
            
            if($query->num_rows() == 1)
            {
                $id = $query->row()->id;
                
                $order_statuses = array('ENVIADO_TOURLINE',
                                        'ENVIADO_PACK',
                                        'ENVIADO_MEGASUR',
                                        'ENVIADO_MARABE',
                                        'ENVIADO_GRUTINET',
                                        'ENVIADO_GLS',
                                        'ENVIADO_FEDEX');

                $this->db->select('SUM(p_s_h.quantity) as total_count_warehouse_sales');

                $this->db->from('products_sales_history as p_s_h');
                $dbprefix = $this->db->dbprefix;
                $this->db->set_dbprefix(null);
                $this->db->join('pedidos as p','p.id = p_s_h.order_id','left');
                $this->db->set_dbprefix($dbprefix);
                $this->db->where('p_s_h.warehouse_product_id',$id);
                $this->db->where_in('p.procesado',$order_statuses);
                $this->db->where('p_s_h.created_at >',$start_date);
                $this->db->where('p_s_h.created_at < ',$end_date);
                $query = $this->db->get();
                
                $this->db->cache_off();
            
                return $query->row()->total_count_warehouse_sales;
            }    
        }
        
        return 0;
    }
    
    private function get_warehouse_trend($sku, $provider_name, $start_date, $end_date)
    {
        if($sku && $provider_name && $start_date && $end_date)
        {
            $current_sales = $this->total_count_warehouse_sales($sku, $provider_name, $start_date, $end_date);
            
            // Calculate date range
            $start_date_unix = strtotime($start_date);
            $end_date_unix = strtotime($end_date);
            $datediff = $end_date_unix - $start_date_unix;
            $date_range = floor($datediff/(60*60*24));
            
            $start_base_date = date('Y-m-d', strtotime($start_date) - (60*60*24*$date_range));
            $end_base_date = $start_date;
            
            $base_sales = $this->total_count_warehouse_sales($sku, $provider_name, $start_base_date, $end_base_date);
            
            return $this->calculate_trend_percent($base_sales, $current_sales);
        }
    }

        /**
     * Return a number of unique products
     * @return int
     */
    public function get_unique_products_count()
    {
        return $this->_unique_products_found;
    }
            


    /**
     * Return radio inputs
     * @return string inputs radio html
     */
    public function get_radio_inputs_periods()
    {
        $data = array();
        $html = '';
        
        $data[] = array(
                            'id'        => 'radio1',
                            'name'      => 'period',
                            'value'     => '7',
                            'title'     => 'Last 7 days'
        );
        $data[] = array(
                            'id'        => 'radio2',
                            'name'      => 'period',
                            'value'     => '14',
                            'title'     => 'Last 14 days'
        ); 
        $data[] = array(
                            'id'        => 'radio3',
                            'name'      => 'period',
                            'value'     => '30',
                            'title'     => 'Last 30 days'
        ); 
        $data[] = array(
                            'id'        => 'radio5',
                            'name'      => 'period',
                            'value'     => '60',
                            'title'     => 'Last 2 months'
        ); 
        $data[] = array(
                            'id'        => 'radio6',
                            'name'      => 'period',
                            'value'     => '90',
                            'title'     => 'Last 3 months'
        ); 
        
        $first = TRUE;
        
        foreach ($data as $input)
        {
            $html .= form_radio($input, null, null, set_radio($input['name'], $input['value'], $first)) . form_label($input['title'], $input['id']);
            
            $first = null;
        }
        
        return $html;
    }
    
    /**
     * Return 1 if product have the best price
     * @param int $id
     * @return int
     */
    private function is_best_price($sku)
    {
            $webs = array();
            
            $webs[] = array(
                'web' => 'AMAZON-DE',
                'prefix' => 'de'
            );
            $webs[] = array(
                'web' => 'AMAZON-CO-UK',
                'prefix' => 'uk'
            );
            $webs[] = array(
                'web' => 'AMAZON-USA',
                'prefix' => 'usa'
            );
            
            foreach($webs as $web)
            {
                $this->db->select(' low_price, low_price_delivery ');

                $this->db->from('amazon_sales_rank');

                $this->db->where('ean', $sku);
                $this->db->where('web', $web['web']);

                $this->db->order_by('updated_on','DESC');
                $this->db->limit(1);

                $query = $this->db->get();

                $result = $query->row();

                if($result)
                {
                    if($result->low_price > 0 || $result->low_price_delivery > 0)
                    {
                        return 0;
                    }
                }
            }
            
            return 1;
    }

    private function get_total_trend($id, $start_date, $end_date, $sku, $provider_name)
    {
            $current_sales = $this->total_count_amazon_sales($id, $start_date, $end_date);
            $current_sales += $this->total_count_buyin_sales($id, $start_date, $end_date);
            $current_sales += $this->total_count_warehouse_sales($sku, $provider_name, $start_date, $end_date);
            
            // Calculate date range
            $start_date_unix = strtotime($start_date);
            $end_date_unix = strtotime($end_date);
            $datediff = $end_date_unix - $start_date_unix;
            $date_range = floor($datediff/(60*60*24));
            
            $start_base_date = date('Y-m-d', strtotime($start_date) - (60*60*24*$date_range));
            $end_base_date = $start_date;
            
            $base_sales = $this->total_count_amazon_sales($id, $start_base_date, $end_base_date);
            $base_sales += $this->total_count_buyin_sales($id, $start_base_date, $end_base_date);
            $base_sales += $this->total_count_warehouse_sales($sku, $provider_name, $start_base_date, $end_base_date);
            
            return $this->calculate_trend_percent($base_sales, $current_sales);
    }

    private function get_amazon_trend($id, $start_date, $end_date)
    {
        if($id && $start_date && $end_date)
        {
            $current_sales = $this->total_count_amazon_sales($id, $start_date, $end_date);
            
            // Calculate date range
            $start_date_unix = strtotime($start_date);
            $end_date_unix = strtotime($end_date);
            $datediff = $end_date_unix - $start_date_unix;
            $date_range = floor($datediff/(60*60*24));
            
            $start_base_date = date('Y-m-d', strtotime($start_date) - (60*60*24*$date_range));
            $end_base_date = $start_date;
            
            $base_sales = $this->total_count_amazon_sales($id, $start_base_date, $end_base_date);
            
            return $this->calculate_trend_percent($base_sales, $current_sales);
        }
    }
    
    private function get_buyin_trend($id, $start_date, $end_date)
    {
        if($id && $start_date && $end_date)
        {
            $current_sales = $this->total_count_buyin_sales($id, $start_date, $end_date);
            
            // Calculate date range
            $start_date_unix = strtotime($start_date);
            $end_date_unix = strtotime($end_date);
            $datediff = $end_date_unix - $start_date_unix;
            $date_range = floor($datediff/(60*60*24));
            
            $start_base_date = date('Y-m-d', strtotime($start_date) - (60*60*24*$date_range));
            $end_base_date = $start_date;
            
            $base_sales = $this->total_count_buyin_sales($id, $start_base_date, $end_base_date);
            
            return $this->calculate_trend_percent($base_sales, $current_sales);
        }
    }
    
    private function calculate_trend_percent($base_sales, $current_sales)
    {
        if($base_sales > 0)
        {
            return ( $current_sales/$base_sales*100 ) - 100;
        }
        else 
        {
            if($current_sales == 0)
            {
                return 0;
            }
        }
        
        return 100;
    }

    private function total_count_amazon_sales($id, $start_date, $end_date)
    {
        if($id && $start_date && $end_date)
        {
            $this->db->cache_on();
            
            $web = array('AMAZON','AMAZON-USA','AMAZON-CO-UK','AMAZON-DE','AMAZON-JP');
            
            $order_statuses = array('ENVIADO_TOURLINE',
                                    'ENVIADO_PACK',
                                    'ENVIADO_MEGASUR',
                                    'ENVIADO_MARABE',
                                    'ENVIADO_GRUTINET',
                                    'ENVIADO_GLS',
                                    'ENVIADO_FEDEX');
            
            $this->db->select('SUM(p_s_h.quantity) as total_count_amazon_sales');
            
            $this->db->from('products_sales_history as p_s_h');
            $dbprefix = $this->db->dbprefix;
            $this->db->set_dbprefix(null);
            $this->db->join('pedidos as p','p.id = p_s_h.order_id','left');
            $this->db->set_dbprefix($dbprefix);
            $this->db->where('p_s_h.provider_product_id',$id);
            $this->db->where_in('p.procesado',$order_statuses);
            $this->db->where('p_s_h.created_at >',$start_date);
            $this->db->where('p_s_h.created_at < ',$end_date);
            $this->db->where_in('p_s_h.web',$web);
            $query = $this->db->get();
            
            $this->db->cache_off();
            
            return $query->row()->total_count_amazon_sales;
        }
    }
    
    private function total_count_buyin_sales($id, $start_date, $end_date)
    {
        if($id && $start_date && $end_date)
        {
            $this->db->cache_on();
            
            $web = array('AMAZON','AMAZON-USA','AMAZON-CO-UK','AMAZON-DE','AMAZON-JP');
            
            $order_statuses = array('ENVIADO_TOURLINE',
                                    'ENVIADO_PACK',
                                    'ENVIADO_MEGASUR',
                                    'ENVIADO_MARABE',
                                    'ENVIADO_GRUTINET',
                                    'ENVIADO_GLS',
                                    'ENVIADO_FEDEX');
            
            $this->db->select('SUM(p_s_h.quantity) as total_count_buyin_sales');
            
            $this->db->from('products_sales_history as p_s_h');
            $dbprefix = $this->db->dbprefix;
            $this->db->set_dbprefix(null);
            $this->db->join('pedidos as p','p.id = p_s_h.order_id','left');
            $this->db->set_dbprefix($dbprefix);
            $this->db->where('p_s_h.provider_product_id',$id);
            $this->db->where('p_s_h.created_at >',$start_date);
            $this->db->where('p_s_h.created_at < ',$end_date);
            $this->db->where_not_in('p_s_h.web',$web);
            $this->db->where_in('p.procesado',$order_statuses);
            $query = $this->db->get();
            
            $this->db->cache_off();
            
            return $query->row()->total_count_buyin_sales;
        }
    }

    private function get_products_by_sku($sku)
    {
        $post_data = $this->input->post();
        
        $accepted_providers = array(

            'COQUETEO',
            'ENGELSA',
            'PINTERNACIONAL',
            '_WAREHOUSE'

        );
                
        
        if($sku)
        {
            $this->db->select('
                        p_p.id, p_p.sku, 
                        p_p.product_name, p_p.provider_name,
                        p_p.stock, p_p.price, p_p.quantity_needed, 
                        p_p.target_price, p_p.provider_ordered, DATE(p_p.provider_order_date) as provider_order_date, 
                        p_p.is_checked, 
                        ( SELECT p_p_h.price
                        FROM '.$this->db->dbprefix('providers_products_history').' as p_p_h
                        WHERE p_p_h.product_id = p_p.id
                        AND p_p_h.price != p_p.price 
                        ORDER BY p_p_h.created_on DESC
                        LIMIT 0,1
                        ) as last_price,
                        ( SELECT p_p_h.created_on
                        FROM '.$this->db->dbprefix('providers_products_history').'  as p_p_h
                        WHERE p_p_h.product_id = p_p.id
                        AND p_p_h.price != p_p.price 
                        ORDER BY p_p_h.created_on DESC
                        LIMIT 0,1
                        ) as last_price_date
                        
            ');
            $this->db->from('providers_products as p_p');
            $this->db->where('p_p.sku',$sku);
            $this->db->where_in('p_p.provider_name',$accepted_providers);
            if(isset($post_data['products_mode']))
            {
                switch ($post_data['products_mode'])
                {
                    case '1' : 
                        break;
                    case '2' : 
                        $this->db->where('p_p.provider_ordered',0);
                        break;
                    case '3' : 
                        $this->db->where('p_p.provider_order_date >',date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 30));
                        break;
                }
            }
            $this->db->order_by('p_p.provider_name');
            $query = $this->db->get();

            return $query->result();
        }
    }
    
    private function get_date_of_last_purchase($id)
    {
        if($id)
        {
            $query = $this->db->select('DATE(MAX(created_at)) as date_of_last_purchase')
            
            ->from('products_sales_history')
            ->where('provider_product_id',$id)
            ->get();
            
            return $query->row()->date_of_last_purchase;
        }
    }

    public function get_total_rows()
    {
        return $this->_total_rows;
    }
    
    public function update_product($id)
    {
        $post_data = $this->input->post();
        
        $post_data['updated_on'] = date('Y-m-d H:i:s', time());
        
        return $this->db->update('providers_products', $post_data, array('id' => $id));
    }
    
    public function export_to_excel()
    {
        $this->load->library('excel');
        $this->load->helper('download');
        $this->load->helper('file');
        
        $post_data = $this->input->post();
        
        $file = null;
        
        $objPHPExcel = new PHPExcel();
        
        $objPHPExcel->getProperties()->setCreator("Amazoni4");
        $objPHPExcel->getProperties()->setLastModifiedBy("Amazoni4");
        $objPHPExcel->getProperties()->setTitle("BSC products report. Date: ".date('r', time()));
        $objPHPExcel->getProperties()->setSubject("BSC products report. Date: ".date('r', time()));
        $objPHPExcel->getProperties()->setDescription("BSC products report. Date: ".date('r', time()));
        
        $objPHPExcel->setActiveSheetIndex(0);
        
        $objPHPExcel->getActiveSheet()->setTitle("BSC products report");
        
        // Prepare Excel header
        $header = array(
            'EAN',
            'Product Name',
            'Units',
            'Price'
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
        
        // Get products
        
        $product_ids = array();
        
        $query = $this->db->select('id, sku, product_name, quantity_needed, target_price')
                 ->from('providers_products')
                 ->where('is_checked', 1)
                 ->get();
        
        $products = $query->result();
        
        // Insert data
        if(count($products) > 0)
        {
            $i = 2;
            foreach($products as $p)
            {
                $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(0, $i, $p->sku, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $i)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(1, $i, stripslashes(preg_replace('/^"|"$/','',$p->product_name)), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(2, $i, $p->quantity_needed, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(3, $i, $p->target_price > 0 ? $p->target_price : null, $p->target_price > 0 ? PHPExcel_Cell_DataType::TYPE_NUMERIC : PHPExcel_Cell_DataType::TYPE_STRING);
                $i++;
                
                $product_ids[] = $p->id;
            }
            
            // Write a file
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

            $name = 'bsc_products_report_'.  date('_Y_m_d_H_i_s__', time());
            
            $filename = FCPATH .'upload/'.$name.'.xls';

            $file = $objWriter->save($filename);
            
            $this->mark_products_as_ordered_to_provider($product_ids);
            $this->reset_checkboxes();
            
            force_download($name.'.xls', read_file($filename));

            return read_file($filename);
        }
        
        return false;
    }
    
    private function mark_products_as_ordered_to_provider($ids)
    {
        $data = array();
        
        $data['provider_order_date'] = $data['updated_on'] = date('Y-m-d H:i:s',time());
        $data['provider_ordered']    = 1;
        
        foreach ($ids as $id) 
        {
            $this->db->update('providers_products', $data, array('id' => $id));
        }
    }
    
    public function store_checkboxes()
    {
        $post_data = $this->input->post();
        
        $data = array();
        
        if( isset($post_data['product_id']) )
        {
            if( count($post_data['product_id']) > 0 )
            {
                $data['updated_on'] = date('Y-m-d H:i:s', time());
                $data['is_checked'] = 1;

                $this->db->where_in('id',$post_data['product_id']);
                $this->db->update('providers_products',$data);
            }
        }
    }
    
    private function reset_checkboxes()
    {
        $data = array();
        
        $data['is_checked'] = 0;
        
        $this->db->update('providers_products',$data);
    }
            
            
}