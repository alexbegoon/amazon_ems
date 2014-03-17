<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * The BuyIn Shopping Center (BSC). Model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Bsc_model extends CI_Model 
{
    private $_total_rows = 0;

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
        
        if( isset($post_data['provider']) )
        {
            if( !empty($post_data['provider']) )
            {
                $this->db->where('h.provider_name',$post_data['provider']);
            }
        }
        
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
                            'units_sold_buyin'      => $this->total_count_buyin_sales($product->id, $start_date, $end_date),
                            'buyin_trend'           => $this->get_buyin_trend($product->id, $start_date, $end_date),
                            'units_sold_amazon'     => $this->total_count_amazon_sales($product->id, $start_date, $end_date),
                            'amazon_trend'          => $this->get_amazon_trend($product->id, $start_date, $end_date),
                            'is_best_price'         => $this->is_best_price($p->sku),
                            'total_trend'           => $this->get_total_trend($product->id, $start_date, $end_date),
                            'quantity_needed'       => '',
                            'target_price'          => '',
                            'provider_order'        => '',
                            'provider_order_date'   => ''

                        );
                    }
                }
            }
            
            $this->_total_rows = $this->db->count_all('providers_products');

            return $data;
        }
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

    private function get_total_trend($id, $start_date, $end_date)
    {
            $current_sales = $this->total_count_amazon_sales($id, $start_date, $end_date);
            $current_sales += $this->total_count_buyin_sales($id, $start_date, $end_date);
            
            // Calculate date range
            $start_date_unix = strtotime($start_date);
            $end_date_unix = strtotime($end_date);
            $datediff = $end_date_unix - $start_date_unix;
            $date_range = floor($datediff/(60*60*24));
            
            $start_base_date = date('Y-m-d', strtotime($start_date) - (60*60*24*$date_range));
            $end_base_date = $start_date;
            
            $base_sales = $this->total_count_amazon_sales($id, $start_base_date, $end_base_date);
            $base_sales += $this->total_count_buyin_sales($id, $start_base_date, $end_base_date);
            
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
        if($sku)
        {
            $query = $this->db->select('
                        p_p.id, p_p.sku, 
                        p_p.product_name, p_p.provider_name,
                        p_p.stock, p_p.price,
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
                        
            ')
            ->from('providers_products as p_p')
            ->where('p_p.sku',$sku)
            ->order_by('p_p.provider_name')
            ->get();

            return $query->result();
        }
    }
    
    private function get_date_of_last_purchase($id)
    {
        if($id)
        {
            $query = $this->db->select('MAX(created_at) as date_of_last_purchase')
            
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
}