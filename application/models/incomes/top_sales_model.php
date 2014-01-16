<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Top sales model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Top_sales_model extends CI_Model
{
    const START_FROM = '2013-08-01';
    const USE_START_DATE = false;  // If true , then extract orders from pedidos where order date start from START_FROM costant, else last 15 days orders will be processed.

    private $_total_rows = 0;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        
        // Load model
        $this->load->model('incomes/providers_model');
        $this->load->model('products/products_model');
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
                            'value'     => '1',
                            'title'     => 'Last month'
        );
        $data[] = array(
                            'id'        => 'radio2',
                            'name'      => 'period',
                            'value'     => '3',
                            'title'     => 'Last three months'
        ); 
        $data[] = array(
                            'id'        => 'radio3',
                            'name'      => 'period',
                            'value'     => '6',
                            'title'     => 'Last six months'
        ); 
        $data[] = array(
                            'id'        => 'radio4',
                            'name'      => 'period',
                            'value'     => '12',
                            'title'     => 'This year'
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
     * Return radio inputs of all Providers
     * @return string html
     */
    public function get_radio_inputs_providers()
    {
        
        $html = '';
        
        $query = ' SELECT `providers`.`id` as `value`, `providers`.`name` as `title`, 
                          \'provider\' as `name`, CONCAT(\'provider_\', `providers`.`id`) as `id` 
                   FROM `'.$this->db->dbprefix('providers').'` as `providers` 
                   ORDER BY `providers`.`name`
        ';
        
        $result = $this->db->query($query);
        
        if ($result)
        {
            $providers = $result->result('array');
            
            foreach ($providers as $provider)
            {                
                $html .= form_radio($provider, null, null, set_radio($provider['name'], $provider['value'])) . form_label($provider['title'], $provider['id']);                
            }
        }
        
        return $html;
    }
    
    public function get_products_list($post_data, $page)
    {
        $products = array();
        
        if(isset($post_data['period']))
        {
            $period = (int)$post_data['period'];
        }
        else
        {
            $period = 1;
        }
        
        $start_date = date('Y-m-d', mktime(0, 0, 0, date('m') - $period, date('d'), date('Y')));
        
        $provider_id = null;
        
        if(isset($post_data['provider']))
        {
            $provider_id = (int)$post_data['provider'];
        }
        
        $where = ' WHERE `order_date` > \''.$start_date.'\' ';
        
        if(!empty($provider_id))
        {
            $where .= ' AND `provider_id` = '.$provider_id.' ';
        }
        
        if(!empty($post_data['search']))
        {
            $where .= ' AND ( `sku` LIKE \'%'.trim($post_data['search']).'%\' OR 
                              `order_date` LIKE \'%'.trim($post_data['search']).'%\' OR 
                              `product_name` LIKE \'%'.addslashes(trim($post_data['search'])).'%\' OR 
                              `provider_name` LIKE \'%'.trim($post_data['search']).'%\'   
            ) ';
        }
        
        if(!empty($post_data['web']))
        {
            $where .= ' AND ( `web` = \''.trim($post_data['web']).'\' 
            ) ';
        }
        
        $order_by = ' `total_sold` DESC ';
        
        if(!empty($post_data['order_by']) && !empty($post_data['order_option']))
        {
            $order_by = $post_data['order_by'] . ' ' . $post_data['order_option'];
        }
        
        if ($page)
        {
            $limit = (int)$page.', 50';
        } 
        else
        {
            $limit      = '0, 50';
        }
        
        $query = ' SELECT `sku`, `product_name`, SUM(`sales_price` * `quantity`) as `total_sold`, 
                          SUM(`quantity`) as `total_quantity`, `provider_name`, MAX(`order_date`) as `last_date_purchase` 
                   FROM `'.$this->db->dbprefix('top_sales').'` 
                   '.$where.' 
                   GROUP BY `sku` 
                   ORDER BY '.$order_by.'
                   LIMIT '.$limit.'
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() > 0)
        {
            $products = $result->result();
        }
        
        $query = ' SELECT `sku` 
                   FROM `'.$this->db->dbprefix('top_sales').'` 
                   '.$where.' 
                   GROUP BY `sku`  
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() > 0)
        {
            $this->_total_rows = $result->num_rows();
        }
        
        return $products;
    }
    
    public function get_product_details($sku)
    {
        if(empty($sku) && !is_string($sku))
        {
            return false;
        }
        
        $post_data = $this->input->post();
        
        if(isset($post_data['period']))
        {
            $period = (int)$post_data['period'];
        }
        else
        {
            $period = 1;
        }
        
        $start_date = date('Y-m-d', mktime(0, 0, 0, date('m') - $period, date('d'), date('Y')));
        
        $query = ' SELECT `top`.`web`, SUM(`top`.`sales_price` * `top`.`quantity`) as `total_sold`, 
                           SUM(`top`.`quantity`) as `total_quantity`, 
                           MAX(`top`.`order_date`) as `last_date_purchase`, 
                           `top`.`sku`, `top`.`product_name`, 
                           `orders`.`pais` as `country`
                   FROM `'.$this->db->dbprefix('top_sales').'` as `top`
                   LEFT JOIN `pedidos` as `orders` 
                   ON `orders`.`id` = `order_id` 
                   WHERE `sku` = \''.$sku.'\' AND `order_date` > \''.$start_date.'\' 
                   GROUP BY `web`, `country` 
                   ORDER BY `total_sold` DESC
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() > 0)
        {
            return $result->result();            
        }
        
        return false;
    }

        public function total_rows()
    {
        return $this->_total_rows;
    }

        public function sync_with_pedidos()
    {
        $products = $this->extract_products();
                
        return $this->store_products($products);
    }
    
    private function extract_products()
    {
        $products = array();
        
        if(self::USE_START_DATE)
        {
            $where = ' WHERE `fechaentrada` >= \''.self::START_FROM.'\' ';
        }
        else
        {
            $start_from_date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 15, date('Y')));
            
            $where = ' WHERE `fechaentrada` >= \''.$start_from_date.'\' ';
        }
        
        $query = ' SELECT * FROM `pedidos` 
                   '.$where.'  
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() > 0)
        {
            $orders = $result->result();
            
            foreach ($orders as $order)
            {
                $products_from_order = $this->get_products_from_order($order);
                
                if(is_array($products_from_order) && count($products_from_order) > 0)
                {
                    foreach ($products_from_order as $item)
                    {
                        $products[] = $item;
                    }   
                }
            }
        }
        
        return $products;
    }
    
    private function store_products($products)
    {
        if(empty($products))
        {
            return FALSE;
        }
        
        $query = ' INSERT INTO 
                   `'.$this->db->dbprefix('top_sales').'` 
                   (`sku`, `sku_in_order`, `product_name`, `quantity`, 
                    `sales_price`, `provider_name`, 
                    `provider_id`, `web`, `order_id`, 
                    `order_date` 
                    )
                   VALUES
                   (?,?,?,?,?,?,?,?,?,?)
        ';
        
        foreach ($products as $product)
        {
            if(!$this->is_exists($product))
            {
                $this->db->query($query, array(
                                    $product->sku,
                                    $product->sku_in_order,
                                    $product->product_name,
                                    $product->quantity,
                                    $product->sales_price,
                                    $product->provider_name,
                                    $product->provider_id,
                                    $product->web,  
                                    $product->order_id,
                                    $product->order_date
                ));
            }
        }
    }
    
    private function is_exists($product)
    {
        if(empty($product))
        {
            return FALSE;
        }
        
        $query = 'SELECT `id` 
                  FROM `'.$this->db->dbprefix('top_sales').'` 
                  WHERE `sku` = \''.$product->sku.'\' 
                  AND   `provider_name` = \''.$product->provider_name.'\' 
                  AND   `web` = \''.$product->web.'\' 
                  AND   `order_id` = '.$product->order_id.'  
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() == 1)
        {
            return TRUE;
        }
        
        return FALSE;
    }

        private function get_products_from_order($order)
    {
        $products = array();
        
        for($i = 1; $i <= 10; $i++)
        {
            $sku_field      = 'sku'.$i;
            $price_field    = 'precio'.$i;
            $quantity_field = 'cantidad'.$i;
            
            if(!empty($order->$sku_field))
            {
                $product = new stdClass();
                
                $product->sku_in_order  = $order->$sku_field;
                $product->web           = $order->web;
                $product->quantity      = (int)$order->$quantity_field;
                $product->sales_price   = (float)$order->$price_field;
                $product->order_id      = $order->id;
                $product->order_date    = $order->fechaentrada;
                $product->provider_name = $this->get_provider_name($order->$sku_field,$product->web);
                $product->provider_id   = $this->get_provider_id($product->provider_name);
                $product->product_name  = $this->get_product_name($order->$sku_field,$product->web);
                $product->sku           = $this->get_original_sku_of_provider($order->$sku_field,$product->web);
                                
                if(!empty($product->provider_name) && !empty($product->provider_id) && !empty($product->product_name) && !empty($product->sku))
                {
                    $products[] = $product;
                }                
            }
            else
            {
                continue;
            }
        }
        
        return $products;
    }
    
    private function get_provider_name($sku,$web)
    {
        return $this->providers_model->get_provider_name($sku,$web);
    }
    
    private function get_provider_id($provider_name)
    {
        return $this->providers_model->get_provider_id_by_name($provider_name);
    }
    
    private function get_product_name($sku,$web)
    {
        $product = $this->products_model->get_product($sku,$web);
        
        if($product[0])
        {
            return $product[0]->product_name;
        }
        
        return FALSE;
    }
    
    /**
     * Return original sku that provide such provider
     * @param string $sku
     * @param string $web
     * @param string $provider_name
     */
    private function get_original_sku_of_provider($sku,$web)
    {
        $product = $this->products_model->get_product($sku,$web);
        
        if($product[0])
        {
            return $product[0]->sku;
        }
        
        return $sku;
    }
    
    /**
     * According to issue #258
     * helps to fix Amazon's orders pricing. 
     * Use only ONE time.
     */
    public function fix_top_sales_table()
    {
        $this->db->or_where('web =','AMAZON');
        $this->db->or_where('web =','AMAZON-USA');
        $result = $this->db->get('top_sales');
        
        $orders = $result->result();
        
        foreach ($orders as $order)
        {
            if($order->quantity >= 1)
            {
                $order->sales_price = $order->sales_price / $order->quantity;
            }
            
            $this->db->where('id', $order->id);
            $this->db->update('top_sales', $order);
            
        }
    }
}