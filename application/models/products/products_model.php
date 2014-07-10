<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Products model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Products_model extends CI_Model 
{
    private $_products          = array();
    private $_products_quantity = 0;
    
    public $products_sales_history_data = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        
        // Load models
        $this->load->model('incomes/providers_model');
        $this->load->model('incomes/web_field_model');
        $this->load->model('incomes/exchange_rates_model');
        $this->load->model('incomes/taxes_model');
        $this->load->model('virtuemart/virtuemart_model');
        $this->load->model('stokoni/stokoni_model');
        
        // Load  libraries
        $this->load->library('email');
        $this->load->library('table');
    }
    
    public function get_products($page)
    {
        $post_data = $this->input->post();
        
        $where = '';
        
        if(isset($post_data['search']) && !empty($post_data['search']))
        {
            $where .= ' WHERE ( `sku` LIKE \'%'.addslashes(trim($post_data['search'])).'%\' ';
            $where .= ' OR `product_name` LIKE \'%'.addslashes(trim($post_data['search'])).'%\' ';
            $where .= ' OR `provider_name` LIKE \'%'.addslashes(trim($post_data['search'])).'%\' ) ';
        }
        
        if(isset($post_data['provider']) && !empty($post_data['provider']))
        {
            if(!empty($where))
            {
                $where .= ' AND `provider_name` = \''.trim($post_data['provider']).'\' ';
            }
            else 
            {
                $where .= ' WHERE `provider_name` = \''.trim($post_data['provider']).'\' ';
            }
        }
        
        if ($page)
        {
            $limit = ' LIMIT '.(int)$page.', 50';
        }
        else
        {
            $limit      = ' LIMIT 0, 50';
        }
       
        $order_by = ' ORDER BY `product_name` ';
        
        if($order_by_data = get_order_by_info())
        {
            $order_by = ' ORDER BY `'.$order_by_data['order_by'].'` '.$order_by_data['order_option'].' ';
        }
        
        $query = ' SELECT `sku`, `product_name`, `provider_name`, `price`, `id`, 
                                `stock`, `sales_rank_de`, `sales_rank_uk`, `updated_on` 
                   FROM `'.$this->db->dbprefix('providers_products').'` 
                   '.$where.' 
                   '.$order_by.' 
                   '.$limit.' 
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() > 0)
        {
            $this->_products            = $result->result();  
            
            $query = ' SELECT COUNT(*) as `total_count` 
                        FROM `'.$this->db->dbprefix('providers_products').'` 
                        '.$where.'  
            ';
            
            $result = $this->db->query($query);
            
            $this->_products_quantity   = (int)$result->row()->total_count;
            
            $this->_products = $this->get_amazon_low_prices($this->_products);
            
            return $this->_products;
        }
        
        return false;        
    }
    
    private function get_amazon_low_prices($products)
    {
        $data = array();
        
        if(is_array($products) && count($products) > 0)
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
            
            foreach($products as $p)
            {
                foreach($webs as $web)
                {
                    $this->db->select(' ROUND(low_price,2) as low_price_'.$web['prefix'].', 
                                        low_price_currency_code as low_price_currency_code_'.$web['prefix'].', 
                                        ROUND(low_price_delivery,2) as low_price_delivery_'.$web['prefix'].', 
                                        low_price_delivery_currency_code as low_price_delivery_currency_code_'.$web['prefix'].'
                                      ');
                    
                    $this->db->from('amazon_sales_rank');

                    $this->db->where('ean', $p->sku);
                    $this->db->where('web', $web['web']);

                    $this->db->order_by('updated_on','DESC');
                    $this->db->limit(1);

                    $query = $this->db->get();

                    $result = $query->row();

                    if($result)
                    {
                        $p->{'low_price_'.$web['prefix']} = $result->{'low_price_'.$web['prefix']};
                        $p->{'low_price_currency_code_'.$web['prefix']} = $result->{'low_price_currency_code_'.$web['prefix']};
                        $p->{'low_price_currency_symbol_'.$web['prefix']} = 
                                $this->exchange_rates_model->get_currency_symbol_by_code(
                                                    $result->{'low_price_currency_code_'.$web['prefix']}
                                );
                        $p->{'low_price_delivery_'.$web['prefix']} = $result->{'low_price_delivery_'.$web['prefix']};
                        $p->{'low_price_delivery_currency_code_'.$web['prefix']} = $result->{'low_price_delivery_currency_code_'.$web['prefix']};
                        $p->{'low_price_delivery_currency_symbol_'.$web['prefix']} = 
                                $this->exchange_rates_model->get_currency_symbol_by_code(
                                                    $result->{'low_price_delivery_currency_code_'.$web['prefix']}
                                );
                    }
                    else
                    {
                        $p->{'low_price_'.$web['prefix']}                            = null;
                        $p->{'low_price_currency_code_'.$web['prefix']}              = null;
                        $p->{'low_price_currency_symbol_'.$web['prefix']}            = null;
                        $p->{'low_price_delivery_'.$web['prefix']}                   = null;
                        $p->{'low_price_delivery_currency_code_'.$web['prefix']}     = null;
                        $p->{'low_price_delivery_currency_symbol_'.$web['prefix']}   = null;
                    }
                }
                
                $data[] = $p;
            }
        }
        
        return $data;
    }

        public function get_providers_statistic()
    {
        $this->db->select('COUNT(*) as total_products, p.provider_name, p.provider_id, 
        (SELECT COUNT(*) as total_with_stock FROM '.$this->db->dbprefix('providers_products').' 
        WHERE stock>0 AND provider_name = p.provider_name) as total_products_with_stock');
        $this->db->from('providers_products as p');
        $this->db->group_by('provider_name');
        
        $query = $this->db->get();
        
        return $query->result();
    }

    public function update_providers_statistic()
    {
        $statistic = $this->get_providers_statistic();
        
        $insert_data = array();
        
        if(is_array($statistic) && count($statistic) > 0)
        {
            foreach ($statistic as $r)
            {
                $r->created_on = $r->updated_on = date('Y-m-d H:i:s',time());
                $insert_data[] = (array)$r;
            }
            
            if(is_array($insert_data) && count($insert_data) > 0)
            {
                return $this->db->insert_batch('providers_products_statistic_history',$insert_data);
            }
        }
        
        return FALSE;
    }
    
    public function get_provider_statistic_history($provider_name)
    {
        // Number of days
        $days = 31;

        $this->db->select('*, DAYOFYEAR(created_on) as day_of_year, YEAR(created_on) as year, 
            DATE_FORMAT(created_on, \'%M, %a %e\') as date_name, 
            AVG(total_products) as total_products, 
            AVG(total_products_with_stock) as total_products_with_stock');
        $this->db->from('providers_products_statistic_history');
        $this->db->where('provider_name',$provider_name);
        $this->db->where('created_on >=',date('Y-m-d H:i:s',time() - $days * SECONDS_PER_DAY));
        $this->db->where('created_on <=',date('Y-m-d H:i:s',time()));
        $this->db->group_by('DAYOFYEAR(created_on)');
        
        $query = $this->db->get();
        
        return $query->result();
    }

    public function count_products()
    {
        return $this->_products_quantity;
    }
    
    public function upload_products($data)
    {
        if(empty($data))
        {
            return false;
        }
        
        $file_path = $data['upload_data']['full_path'];
        
        $products = $this->parse_file($file_path);
        
        $result = $this->update_products_table($products);
        
        return $result;        
    }
    
    private function parse_file($file_path)
    {
        $this->load->helper('file');
        
        $products = array();
        
        $file_string = read_file($file_path);

        if(empty($file_string))
        {
            return false;
        }
        
        $rows = explode("\n", $file_string);
        
        //Check file format, prepare columns order
        
        $file_headers = array(  'sku'           => 'SKU', 
                                'product_name'  => 'Product name', 
                                'provider_name' => 'Provider',
                                'price'         => 'Price',
                                'stock'         => 'Stock'); // These headers only possible
        
        $header = explode(';', trim($rows[0])); // Header of file uploaded
        $header = array_map('trim', $header); // Trim all values
        
        $order_array = array(); // This array will store order of the columns
        
        foreach ($file_headers as $k => $v)
        {
            $result  = array_search($v, $header, true);
            
            if($result === false)
            {
                return false;
            }
            
            $order_array[$k] = $result;
            
        }
        
        array_shift($rows); // Delete header
        
        foreach ($rows as $row)
        {
            $row = explode(';', $row);
            if( !empty($row[$order_array['product_name']]) && 
                !empty($row[$order_array['provider_name']]) && 
                !empty($row[$order_array['sku']]) )
            {
                $products[] = array(
                    'sku'           => trim($row[$order_array['sku']]),
                    'product_name'  => trim($row[$order_array['product_name']]),
                    'provider_name' => trim($row[$order_array['provider_name']]),
                    'price'         => (float)trim($row[$order_array['price']]),
                    'stock'         => (int)$row[$order_array['stock']]
                );
            }
        }
        
        return $products;
    }
    
    /**
     * Fix length of the SKU/EAN
     * @param type $products
     * @return array
     */
    private function fix_products_sku($products)
    {
        if(count($products)<=0)
        {
            return $products;
        }
        
        foreach ($products as $p)
        {
            if(preg_match("/\d{6,12}/", $p['sku']) === 1)
            {
                $p['sku'] = str_pad($p['sku'], 13, '0', STR_PAD_LEFT);
            }
            $fixed_products[] = $p;
        }
        
        return $fixed_products;
    }



    public function update_products_table($products)
    {
        //Load model
        $this->load->model('incomes/providers_model');
        $products = $this->fix_products_sku($products);
        $summary = new stdClass();
        $summary->affected_rows = 0;
        
        $products_to_insert = array();
        $products_to_update = array();
        
        $existing_products = $this->are_products_exists($products);
        
        if(!empty($products))
        {
            $this->db->trans_begin();
            // Reset stock before update
            reset($products);
            $first_key = key($products);
            if($this->providers_model->get_provider_id_by_name($products[$first_key]['provider_name']))
            {
                $this->db->where('provider_name', $products[$first_key]['provider_name']);
                $data = array('stock' => 0, 'updated_on' => date('Y-m-d H:i:s', time()));
                $this->db->update('providers_products', $data);
            }
            
            foreach ($products as $product)
            {
                $provider_id = $this->providers_model->get_provider_id_by_name($product['provider_name']);
                
                if($provider_id != 0 && is_integer($provider_id) )
                {
                    $product['provider_id'] = $provider_id;
                    
                    if( !isset($existing_products[$product['sku'].'_'.$product['provider_name']]) )
                    {
                        $product['created_on'] = $product['updated_on'] = date('Y-m-d H:i:s', time());
                        $products_to_insert[] = $product;
                    }
                    else
                    {
                        if($product['stock'] <= 0)
                        {
                            $product['provider_ordered'] = 0;
                        }
                        $product['id'] = $existing_products[$product['sku'].'_'.$product['provider_name']];
                        $product['updated_on'] = date('Y-m-d H:i:s', time());
                        $products_to_update[] = $product;
                    }
                }
            }
            $this->batch_insert($products_to_insert);
            $summary->affected_rows += $this->db->affected_rows();
            $this->batch_update($products_to_update);
            $summary->affected_rows += $this->db->affected_rows();
            
            $this->db->trans_commit();
            
            //Notify staff
            $this->notify_staff_about_new_products($products_to_insert);
            
            // Store products history
            $this->store_products_history();
            
            // Fire statistic updater
            $this->update_providers_statistic();
        }
        
        return $summary;
    }
    
    /**
     * Insert products to providers_products table
     * @param type $data
     */
    private function batch_insert($data)
    {
        if(count($data) > 0)
        {
            return $this->db->insert_batch('providers_products', $data); 
        }
        return FALSE;
    }
    
    /**
     * Update products in providers_products table
     * @param type $data
     */
    private function batch_update($data)
    {
        if(count($data) > 0)
        {
            return $this->db->update_batch('providers_products', $data, 'id'); 
        }
        return FALSE;
    }

    /**
     * Make a flashdata of Products table
     */
    private function store_products_history()
    {
        $query =  $this->db->select('p.id as product_id, p.price, p.stock, NOW() as created_on')
                           ->from('providers_products as p')
                           ->where('price != ( SELECT p_h.price FROM '.$this->db->dbprefix('providers_products_history').' as p_h '
                                   . 'WHERE p_h.product_id = p.id '
                                   . 'ORDER BY p_h.created_on DESC '
                                   . 'LIMIT 0,1 )')
                           ->or_where('stock != ( SELECT p_h.stock FROM '.$this->db->dbprefix('providers_products_history').' as p_h '
                                      . 'WHERE p_h.product_id = p.id '
                                      . 'ORDER BY p_h.created_on DESC '
                                      . 'LIMIT 0,1 )')
                           ->or_where('( SELECT p_h.id FROM '.$this->db->dbprefix('providers_products_history').' as p_h '
                                      . 'WHERE p_h.product_id = p.id '
                                      . 'ORDER BY p_h.created_on DESC '
                                      . 'LIMIT 0,1 ) IS NULL')
                           ->get();
        
        $data = $query->result('array');
        
        if(count($data) > 0)
        {
            $this->db->trans_begin();

            $res = $this->db->insert_batch('providers_products_history', $data);

            $this->db->trans_commit();
            
            return $res;
        }

        return FALSE;
    }

    /**
     * Check product existance
     * @param type $sku
     * @param type $provider_name
     * @return boolean True if exists.
     */
    private function is_product_exists($sku,$provider_name)
    {
        $hash = strtolower(substr(preg_replace('/\d/','',md5((string)(rand(0,100).rand(0,100).rand(0,100).rand(0,100)))),0,10));
        
        $this->db->select('id as '.$hash.', id as product_id');
        $this->db->where('sku =',$sku);
        $this->db->where('provider_name =',$provider_name);
        $query = $this->db->get('providers_products',1,0);
        
        if($query->num_rows > 0)
        {
            return (int)$query->row()->product_id;
        }
        
        return FALSE;
    }
    
    /**
     * Check existance of an array of products. data format array( array( 'sku' => 'sku1', 'provider_name' => 'provider_name'), ... )
     * @param array $data
     * @return array return ID of products, which exists
     */
    private function are_products_exists($data)
    {
        if( !is_array($data) || count($data) <= 0 )
        {
            return FALSE;
        }
        $return=array();    
        reset($data);
        $first_key = key($data);
        
        $this->db->select('id, sku, provider_name');
        $this->db->where(' ( `sku` = \''.$data[$first_key]['sku'].'\' AND `provider_name` = \''.$data[$first_key]['provider_name'].'\' ) ');
        
        array_shift($data);
        
        foreach ($data as $item) 
        {
            $this->db->or_where(' ( `sku` = \''.$item['sku'].'\' AND `provider_name` = \''.$item['provider_name'].'\' ) ');
        }
        
        $query = $this->db->get('providers_products');
        
        $products_exists = $query->result('array');
        
        foreach ($products_exists as $product) 
        {
            $return[$product['sku'].'_'.$product['provider_name']] = (int)$product['id'];
        }
        
        return $return;
    }

    public function get_help_info()
    {
        $html = '';
        
        $this->load->library('table');
        
        $tmpl = array ( 'table_open'  => '<table id="table_format">' );

        $this->table->set_template($tmpl);

        $data = array(
                     array('SKU','Product name','Provider','Price','Stock'),
                     array('#sku1', 'Product name', 'ENGELSA', 2.45, 15),
                     array('34124123412', 'Some product', 'ENGELSA', 12343.34, 12),
                     array('12341234214', 'Other product', 'ENGELSA', 0, 0)	
                     );

        ;
        
        $html .= '<p>';
        $html .= 'The format of the file should be like the table from below. NOTE, the header required ("SKU","Product name","Provider","Price","Stock").';
        $html .= '</p>';
        $html .= '<p>';
        $html .= 'Extension is .csv , cell separator is \';\' (semicolon), text separator is \' " \' (double quotes).';
        $html .= '</p>';
        $html .= '<p>';
        $html .= 'Name of Provider is case sensitive, system will check existance of such Provider.';
        $html .= '</p>';
        $html .= '<p>';
        $html .= 'Price separator is \'.\' (dot).';
        $html .= '</p>'; 
        $html .= '<p>';
        $html .= 'You can copy paste table from below to the Excel table and continue work with such format.';
        $html .= '</p>';
        $html .= '<br>';
        $html .= '<br>';
        $html .= $this->table->generate($data);
        
        return $html;
    }
    
    /**
     * Return product by ID
     * @param int $id
     * @return boolean
     */
    public function get_product_by_id($id)
    {
        if($id <= 0)
        {
            return false;
        }
        
        $this->db->where('id =', (int)$id);
        
        $query = $this->db->get('providers_products');
        
        if($query->num_rows() == 1)
        {
            return $query->row();
        }
        
        return false;
        
    }

        /**
     * Return product using SKU and WEB as index; First appears most cheaper!
     * @param string $sku
     * @param string $web
     * @return mixed Object of product/products(if product have more than one provider) or boolean false on unsuccess
     */
    public function get_product($sku,$web)
    {
        if(!empty($sku) && !empty($web) && is_string($web) && is_string($sku))
        {
            if(isset($this->_products['products_by_web_and_sku'][$sku][$web]))
            {
                return $this->_products['products_by_web_and_sku'][$sku][$web];
            }
            
            $provider_name = $this->providers_model->get_provider_name($sku,$web);
            
            if(!empty($provider_name))
            {
                $provider_id = $this->providers_model->get_provider_id_by_name($provider_name);
                
                if(!empty($provider_id) && is_integer($provider_id))
                {
                    $regexps = $this->web_field_model->get_regexps($web, $provider_id);
                    
                    if(!empty($regexps->sku_regexp_2))
                    {
                        $sku = preg_replace($regexps->sku_regexp_2, '', $sku);
                    }
                    
                    $query = ' SELECT `product_name`, `sku`, `provider_name`, `id`, 
                                        `price`, `provider_id`, `stock`, `brand` 
                               FROM `'.$this->db->dbprefix('providers_products').'` 
                               WHERE `sku` = \''.$sku.'\' 
                               ORDER BY `price` 
                    ';
                    
                    $result = $this->db->query($query);

                    if($result->num_rows() >= 1)
                    {
                        $this->_products['products_by_web_and_sku'][$sku][$web] = $result->result();
                        return $this->_products['products_by_web_and_sku'][$sku][$web];
                    }
                }
            }
        }
        
        return FALSE;
    }
    
    public function sync_with_engelsa()
    {
        $products = array();
        
        $query = ' SELECT `ean` as `sku`, `descripcion` as `product_name`, 
                          `precio` as `price`, `stock`, \'ENGELSA\' as `provider_name`, `nombre_marca` as `brand` 
                   FROM `'.$this->db->dbprefix('engelsa').'` 
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() > 0)
        {
            $products = $result->result('array');
            
            $this->update_products_table($products);
        }
            
        return false;
    }
    
    public function sync_with_grutinet()
    {
        $products = array();
        
        $query = ' SELECT `ean` as `sku`, `product_name`, `price`, `stock`, 
                            \'GRUTINET\' as `provider_name` , `brand_name` as `brand` 
                   FROM `'.$this->db->dbprefix('grutinet').'` 
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() > 0)
        {
            $products = $result->result('array');
            
            $this->update_products_table($products);
        }
            
        return false;
    }
    
    /**
     * Return list of products for the order by order ID
     * @param type $id
     * @return array
     */
    public function get_products_of_order($id)
    {
        if(!is_integer($id))
        {
            return false;
        }
        
        $products = array();
        
        $this->load->model('dashboard/dashboard_model');
        
        $order = $this->dashboard_model->getOrder($id);
        
        for($i=1;$i<=10;$i++)
        {
            if(!empty($order->{'sku'.$i}))
            {
                $products[$i] = $this->get_product($order->{'sku'.$i}, $order->web)[0];
                $products[$order->{'sku'.$i}] = $this->get_product($order->{'sku'.$i}, $order->web)[0];
                $products['product_'.$i] = $this->get_product($order->{'sku'.$i}, $order->web)[0];
            }
        }
        
        return $products;
    }
    
    /**
     * Global method for calculate gasto.
     * order_products is array parameter, should have next format:
     * order_products[$i]['sku'] - required
     * order_products[$i]['quantity'] - required
     * order_products[$i]['price'] - required
     * order_products[$i]['order_id'] - required
     * @param array $order_products
     * @param float $shipping_cost
     * @param string $web
     * @param boolean $safe_mode
     * @return float
     * 
     * 
     */
    public function calculate_gasto($order_products, $shipping_cost, $web, $safe_mode = true, $order_id = null)
    {
        // Init gasto total variable
        $gasto = 0;
        
        $IVA_tax = $this->taxes_model->getIVAtax();
        $valid = TRUE;
                
        // Check product array
        if(!is_array($order_products) || count($order_products) == 0)
        {
            $message = 'We cant calculate Gasto without order products data; Web: '.$web;
            log_message('INFO', $message);
            return 0;
        }
        
        // Setup Order ID for LOG messages
        if(isset($order_products[0]['order_id']) && empty($order_id) && !empty($order_products[0]['order_id']))
        {
            $order_id = trim($order_products[0]['order_id']);
        }
        
        // Check shipping cost
        if(!is_numeric($shipping_cost) || $shipping_cost <= 0)
        {
            $message = 'We cant calculate Gasto without shipping price; Order ID: '.$order_id.'; Web: '.$web;
            log_message('INFO', $message);
            return 0; // We cant calculate Gasto without shipping price
        }
        
        $products_sales_history_data = array();
        
        foreach ($order_products as $row) 
        {
            // Check SKU
            if(!isset($row['sku']) || empty($row['sku']))
            {
                $message = 'We cant calculate Gasto without SKU; Order ID: '.$order_id.'; Web: '.$web;
                log_message('INFO', $message);
                $valid = FALSE;
                continue;
            }
            
            // Check quantity
            if(!isset($row['quantity']) || $row['quantity'] <= 0)
            {
                $message = 'We cant calculate Gasto without quantity info; Order ID: '.$order_id.'; Web: '.$web;
                log_message('INFO', $message);
                $valid = FALSE;
                continue;
            }
            
            // Firstly we need to check product existance at our Warehouse
            if($this->stokoni_model->find_product_by_ean($row['sku']))
            {
                
                // Now we need to check stock at Warehouse
                $warehouse_stock = 0;
                
                foreach ($this->stokoni_model->find_product_by_ean($row['sku']) as $v)
                {
                    $warehouse_stock += $v->stock;
                }
                
                // Warehouse have enough product stock
                if($warehouse_stock >= $row['quantity'])
                {
                    $quantity_temp = $row['quantity'];
                    foreach ($this->stokoni_model->find_product_by_ean($row['sku']) as $v)
                    {
                        if($v->stock >= $quantity_temp)
                        {
                            $gasto += $v->coste * $quantity_temp;
                            
                            if(!$safe_mode)
                            {
                                
                            // Save data
                            $products_sales_history_data[$row['sku']][] = array('sku_in_order' => $row['sku'],
                                                                                'sku' => $v->ean,
                                                                                'product_name' => $v->nombre,
                                                                                'provider_name' => '_WAREHOUSE',
                                                                                'provider_id' => 0,
                                                                                'provider_price' => null,
                                                                                'order_price' => $row['price'],
                                                                                'warehouse_price' => $v->coste,
                                                                                'warehouse_product_id' => $v->id,
                                                                                'quantity' => $quantity_temp,
                                                                                'sold_from_warehouse' => 1,
                                                                                'web' => $web,
                                                                                'order_id' => $order_id,
                                                                                'order_status' => null,
                                                                                'order_date' => null,
                                                                                'canceled' => 0,
                                                                                'shipping_price' => $shipping_cost
                                                                    
                                                                                                        );
                            
                            $this->stokoni_model->sell_product((int)$v->id, (int)$quantity_temp);
                            
                            }
                            
                            $quantity_temp = 0;
                            
                            // We can break loop, because have enough products
                            break;
                        }
                        else
                        {
                            $gasto += $v->coste * $v->stock;
                            $quantity_temp -= $v->stock;

                            if(!$safe_mode)
                            {
                            // Save data
                            $products_sales_history_data[$row['sku']][] = array('sku_in_order' => $row['sku'],
                                                                                'sku' => $v->ean,
                                                                                'product_name' => $v->nombre,
                                                                                'provider_name' => '_WAREHOUSE',
                                                                                'provider_id' => 0,
                                                                                'provider_price' => null,
                                                                                'order_price' => $row['price'],
                                                                                'warehouse_price' => $v->coste,
                                                                                'warehouse_product_id' => $v->id,
                                                                                'quantity' => $v->stock,
                                                                                'sold_from_warehouse' => 1,
                                                                                'web' => $web,
                                                                                'order_id' => $order_id,
                                                                                'order_status' => null,
                                                                                'order_date' => null,
                                                                                'canceled' => 0,
                                                                                'shipping_price' => $shipping_cost
                                    );

                            $this->stokoni_model->sell_product((int)$v->id, (int)$v->stock);

                            }
                        }
                    }
                    
                    continue;
                }
                else
                {
                    // Warehouse have product stock less than need 
                    $quantity_temp = $row['quantity'];
                    
                    foreach ($this->stokoni_model->find_product_by_ean($row['sku']) as $v)
                    {
                        if($v->stock > 0)
                        {
                            $gasto += $v->coste * $v->stock;
                            $quantity_temp -= $v->stock;

                            if(!$safe_mode)
                            {
                            // Save data
                            $products_sales_history_data[$row['sku']][] = array('sku_in_order' => $row['sku'],
                                                                                'sku' => $v->ean,
                                                                                'product_name' => $v->nombre,
                                                                                'provider_name' => '_WAREHOUSE',
                                                                                'provider_id' => 0,
                                                                                'provider_price' => null,
                                                                                'order_price' => $row['price'],
                                                                                'warehouse_price' => $v->coste,
                                                                                'warehouse_product_id' => $v->id,
                                                                                'quantity' => $v->stock,
                                                                                'sold_from_warehouse' => 1,
                                                                                'web' => $web,
                                                                                'order_id' => $order_id,
                                                                                'order_status' => null,
                                                                                'order_date' => null,
                                                                                'canceled' => 0,
                                                                                'shipping_price' => $shipping_cost
                                );

                            $this->stokoni_model->sell_product((int)$v->id, (int)$v->stock);

                            }
                        }
                    }
                    
                    // Try to get products from Providers
                    // Check product existance at providers_product table
                    if(!$this->get_product($row['sku'], $web))
                    {
                        $message = 'We cant calculate Gasto. Product not found at providers_products table, but this product exist at our Warehouse. Our Warehouse have no enough quantity for this order. Out of stock. SKU: '.$row['sku'].'; Order ID: '.$order_id.'; Web: '.$web;
                        log_message('INFO', $message);
                        $valid = FALSE;
                        $products_sales_history_data['out_of_stock'] = TRUE; // Mark order as out of stock product exist
                        if(!$safe_mode)
                        {
                            $warehouse_product = $this->stokoni_model->find_product_by_ean($row['sku'])[0];
                            // Save data
                            $products_sales_history_data[$row['sku']][] = array('sku_in_order' => $row['sku'],
                                                                            'sku' => $warehouse_product->ean,
                                                                            'product_name' => $warehouse_product->nombre,
                                                                            'provider_name' => '_WAREHOUSE',
                                                                            'provider_id' => 0,
                                                                            'provider_price' => null,
                                                                            'order_price' => $row['price'],
                                                                            'warehouse_price' => null,
                                                                            'warehouse_product_id' => $warehouse_product->id,
                                                                            'quantity' => $quantity_temp,
                                                                            'sold_from_warehouse' => 1,
                                                                            'web' => $web,
                                                                            'order_id' => $order_id,
                                                                            'order_status' => null,
                                                                            'order_date' => null,
                                                                            'out_of_stock' => 1,
                                                                            'canceled' => 0,
                                                                            'shipping_price' => $shipping_cost
                            );
                        
                        }
                        continue;
                    }
                    
                    $provider_product = $this->get_product($row['sku'], $web);
                    
                    foreach ($provider_product as $v)
                    {
                        if($v->stock >= $quantity_temp)
                        {
                            $gasto += $v->price * $quantity_temp;
                            
                            if(!$safe_mode)
                            {
                            // Save data
                            $products_sales_history_data[$row['sku']][] = array('sku_in_order' => $row['sku'],
                                                                                'sku' => $v->sku,
                                                                                'product_name' => $v->product_name,
                                                                                'provider_name' => $v->provider_name,
                                                                                'provider_id' => $v->provider_id,
                                                                                'provider_price' => $v->price,
                                                                                'order_price' => $row['price'],
                                                                                'warehouse_price' => null,
                                                                                'warehouse_product_id' => null,
                                                                                'provider_product_id' => $v->id,
                                                                                'quantity' => $quantity_temp,
                                                                                'sold_from_warehouse' => 0,
                                                                                'web' => $web,
                                                                                'order_id' => $order_id,
                                                                                'order_status' => null,
                                                                                'order_date' => null,
                                                                                'canceled' => 0,
                                                                                'shipping_price' => $shipping_cost  
                                                                                
                                );
                            
                                $this->sell_product((int)$v->id, (int)$quantity_temp);

                            }
                            
                            $quantity_temp = 0;
                            
                            // We can break loop, because have enough products
                            break;
                        }
                        else
                        {
                            if($v->stock > 0)
                            {
                                $gasto += $v->price * $v->stock;
                                
                                $quantity_temp -= $v->stock;
                                
                                if(!$safe_mode)
                                {
                                // Save data
                                $products_sales_history_data[$row['sku']][] = array('sku_in_order' => $row['sku'],
                                                                                    'sku' => $v->sku,
                                                                                    'product_name' => $v->product_name,
                                                                                    'provider_name' => $v->provider_name,
                                                                                    'provider_id' => $v->provider_id,
                                                                                    'provider_price' => $v->price,
                                                                                    'order_price' => $row['price'],
                                                                                    'warehouse_price' => null,
                                                                                    'warehouse_product_id' => null,
                                                                                    'provider_product_id' => $v->id,
                                                                                    'quantity' => $v->stock,
                                                                                    'sold_from_warehouse' => 0,
                                                                                    'web' => $web,
                                                                                    'order_id' => $order_id,
                                                                                    'order_status' => null,
                                                                                    'order_date' => null,
                                                                                    'canceled' => 0,
                                                                                    'shipping_price' => $shipping_cost
                                    );
                                
                                $this->sell_product((int)$v->id, (int)$v->stock);

                                }
                            }
                        }
                    }
                    
                    // Check quantity of product, that we need to get as Credit
                    if($quantity_temp != $row['quantity'] && $quantity_temp > 0)
                    {
                        // Get most cheap product
                        $gasto += $provider_product[0]->price * $quantity_temp;
                        
                        if(!$safe_mode)
                        {
                        // Save data
                        $products_sales_history_data[$row['sku']][] = array('sku_in_order' => $row['sku'],
                                                                            'sku' => $provider_product[0]->sku,
                                                                            'product_name' => $provider_product[0]->product_name,
                                                                            'provider_name' => $provider_product[0]->provider_name,
                                                                            'provider_id' => $provider_product[0]->provider_id,
                                                                            'provider_price' => $provider_product[0]->price,
                                                                            'order_price' => $row['price'],
                                                                            'warehouse_price' => null,
                                                                            'warehouse_product_id' => null,
                                                                            'provider_product_id' => $provider_product[0]->id,
                                                                            'quantity' => $quantity_temp,
                                                                            'provider_reserve_quantity' => $quantity_temp,
                                                                            'sold_from_warehouse' => 0,
                                                                            'web' => $web,
                                                                            'order_id' => $order_id,
                                                                            'order_status' => null,
                                                                            'order_date' => null,
                                                                            'canceled' => 0,
                                                                            'shipping_price' => $shipping_cost
                            );
                        
                            $this->sell_product((int)$provider_product[0]->id, (int)$quantity_temp);

                        }
                    }
                    else
                    {
                        if($quantity_temp > 0)
                        {
                            $message = 'We cant calculate Gasto. Product out of stock. SKU: '.$row['sku'].'; Order ID: '.$order_id.'; Web: '.$web;
                            log_message('INFO', $message);
                            $products_sales_history_data['out_of_stock'] = TRUE; // Mark order as out of stock product exist
                            $valid = FALSE;
                            if(!$safe_mode)
                            {
                            // Save data
                            $products_sales_history_data[$row['sku']][] = array('sku_in_order' => $row['sku'],
                                                                                'sku' => $provider_product[0]->sku,
                                                                                'product_name' => $provider_product[0]->product_name,
                                                                                'provider_name' => $provider_product[0]->provider_name,
                                                                                'provider_id' => $provider_product[0]->provider_id,
                                                                                'provider_price' => $provider_product[0]->price,
                                                                                'order_price' => $row['price'],
                                                                                'warehouse_price' => null,
                                                                                'warehouse_product_id' => null,
                                                                                'provider_product_id' => $provider_product[0]->id,
                                                                                'quantity' => $quantity_temp,
                                                                                'provider_reserve_quantity' => 0,
                                                                                'sold_from_warehouse' => 0,
                                                                                'web' => $web,
                                                                                'order_id' => $order_id,
                                                                                'order_status' => null,
                                                                                'order_date' => null,
                                                                                'out_of_stock' => 1,
                                                                                'canceled' => 0,
                                                                                'shipping_price' => $shipping_cost
                                );
                            
                                $this->sell_product((int)$provider_product[0]->id, (int)$quantity_temp);

                            }
                            continue;
                        }
                    }
                    
                    continue;
                }
            }
            
            // We have no such products at Warehouse. Try to get products from Providers
            
            // Check product existance at providers_product table
            if(!$this->get_product($row['sku'], $web))
            {
                $message = 'We cant calculate Gasto. Product not found. SKU: '.$row['sku'].'; Order ID: '.$order_id.'; Web: '.$web;
                log_message('INFO', $message);
                $valid = FALSE;
                continue;
            }
            
            $provider_product = $this->get_product($row['sku'], $web);
            $quantity_temp = $row['quantity'];
            
            foreach ($provider_product as $v)
            {
                if($v->stock >= $quantity_temp)
                {
                    $gasto += $v->price * $quantity_temp;
                    
                    if(!$safe_mode)
                    {
                    // Save data
                    $products_sales_history_data[$row['sku']][] = array('sku_in_order' => $row['sku'],
                                                                        'sku' => $v->sku,
                                                                        'product_name' => $v->product_name,
                                                                        'provider_name' => $v->provider_name,
                                                                        'provider_id' => $v->provider_id,
                                                                        'provider_price' => $v->price,
                                                                        'order_price' => $row['price'],
                                                                        'warehouse_price' => null,
                                                                        'warehouse_product_id' => null,
                                                                        'provider_product_id' => $v->id,
                                                                        'quantity' => $quantity_temp,
                                                                        'sold_from_warehouse' => 0,
                                                                        'web' => $web,
                                                                        'order_id' => $order_id,
                                                                        'order_status' => null,
                                                                        'order_date' => null,
                                                                        'canceled' => 0,
                                                                        'shipping_price' => $shipping_cost
                        );
                    
                    $this->sell_product((int)$v->id, (int)$quantity_temp);

                    }
                    $quantity_temp = 0;    
                    // We can break loop, because have enough products
                    break;
                }
                else
                {
                    if($v->stock > 0)
                    {
                        $gasto += $v->price * $v->stock;
                        $quantity_temp -= $v->stock;
                        
                        if(!$safe_mode)
                        {
                        // Save data
                        $products_sales_history_data[$row['sku']][] = array('sku_in_order' => $row['sku'],
                                                                            'sku' => $v->sku,
                                                                            'product_name' => $v->product_name,
                                                                            'provider_name' => $v->provider_name,
                                                                            'provider_id' => $v->provider_id,
                                                                            'provider_price' => $v->price,
                                                                            'order_price' => $row['price'],
                                                                            'warehouse_price' => null,
                                                                            'warehouse_product_id' => null,
                                                                            'provider_product_id' => $v->id,
                                                                            'quantity' => $v->stock,
                                                                            'sold_from_warehouse' => 0,
                                                                            'web' => $web,
                                                                            'order_id' => $order_id,
                                                                            'order_status' => null,
                                                                            'order_date' => null,
                                                                            'canceled' => 0,
                                                                            'shipping_price' => $shipping_cost
                            );
                        
                            $this->sell_product((int)$v->id, (int)$v->stock);

                        }
                    }
                }
            }
            
            // Check quantity of product, that we need to get as Credit
            if($quantity_temp != $row['quantity'] && $quantity_temp > 0)
            {
                // Get most cheap product
                $gasto += $provider_product[0]->price * $quantity_temp;

                if(!$safe_mode)
                {
                // Save data
                $products_sales_history_data[$row['sku']][] = array('sku_in_order' => $row['sku'],
                                                                    'sku' => $provider_product[0]->sku,
                                                                    'product_name' => $provider_product[0]->product_name,
                                                                    'provider_name' => $provider_product[0]->provider_name,
                                                                    'provider_id' => $provider_product[0]->provider_id,
                                                                    'provider_price' => $provider_product[0]->price,
                                                                    'order_price' => $row['price'],
                                                                    'warehouse_price' => null,
                                                                    'warehouse_product_id' => null,
                                                                    'provider_product_id' => $provider_product[0]->id,
                                                                    'quantity' => $quantity_temp,
                                                                    'provider_reserve_quantity' => $quantity_temp,
                                                                    'sold_from_warehouse' => 0,
                                                                    'web' => $web,
                                                                    'order_id' => $order_id,
                                                                    'order_status' => null,
                                                                    'order_date' => null,
                                                                    'canceled' => 0,
                                                                    'shipping_price' => $shipping_cost
                    );
                
                $this->sell_product((int)$provider_product[0]->id, (int)$quantity_temp);

                }
            }
            else
            {
                if($quantity_temp > 0)
                {
                    $message = 'We cant calculate Gasto. Product out of stock. SKU: '.$row['sku'].'; Order ID: '.$order_id.'; Web: '.$web;
                    log_message('INFO', $message);
                    $products_sales_history_data['out_of_stock'] = TRUE; // Mark order as out of stock product exist
                    $valid = FALSE;
                    if(!$safe_mode)
                    {
                    // Save data
                    $products_sales_history_data[$row['sku']][] = array('sku_in_order' => $row['sku'],
                                                                        'sku' => $provider_product[0]->sku,
                                                                        'product_name' => $provider_product[0]->product_name,
                                                                        'provider_name' => $provider_product[0]->provider_name,
                                                                        'provider_id' => $provider_product[0]->provider_id,
                                                                        'provider_price' => null,
                                                                        'order_price' => $row['price'],
                                                                        'warehouse_price' => null,
                                                                        'warehouse_product_id' => null,
                                                                        'provider_product_id' => $provider_product[0]->id,
                                                                        'quantity' => $quantity_temp,
                                                                        'provider_reserve_quantity' => 0,
                                                                        'sold_from_warehouse' => 0,
                                                                        'web' => $web,
                                                                        'order_id' => $order_id,
                                                                        'order_status' => null,
                                                                        'order_date' => null,
                                                                        'out_of_stock' => 1,
                                                                        'canceled' => 0,
                                                                        'shipping_price' => $shipping_cost
                        );
                    
                    $this->sell_product((int)$provider_product[0]->id, (int)$quantity_temp);

                    }
                    
                    continue;
                }
                
            }
        }
        
        $this->products_sales_history_data[$web][$order_id] = $products_sales_history_data;
        
        if(!$valid)
        {
            return 0;
        }
        
        $gasto *= ( 1 + (1/100*$IVA_tax));

        $gasto += ( $shipping_cost * ( 1 + (1/100*$IVA_tax)) );
        
        return $gasto;
    }
    
    /**
     * Save order products sales history.
     * Put all order products to history.
     * @param string $web
     * @param string $order_id ID that system receive from e-Shop. (pedido field in pedidos table)
     * @param int $order_unique_id Our ID in pedidos table
     * @param string $order_status Procesado of order
     * @param string $order_date Date of order
     * @return boolean true on success
     * 
     */
    public function store_history($web, $order_id, $order_unique_id, $order_status, $order_date)
    {
        if(isset($this->products_sales_history_data[$web][$order_id]))
        {
            $info = $this->products_sales_history_data[$web][$order_id];
            
            // Unset out of stock marker
            if(isset($this->products_sales_history_data[$web][$order_id]['out_of_stock']))
            {
                unset($this->products_sales_history_data[$web][$order_id]['out_of_stock']);
            }
        }
        else
        {
            $info = null;
        }
        
        if(is_array($info) && count($info) > 0)
        {
            foreach ($info as $row)
            {
                if(is_array($row))
                {
                    foreach ($row as $data)
                    {
                        $data['order_status'] = $order_status;
                        $data['order_date']   = date('Y-m-d H:i:s',strtotime($order_date));
                        $data['order_id']     = (int)$order_unique_id; 
                        $data['order_name']   = $order_id;
                        $data['created_at']   = date('Y-m-d H:i:s',time());
                        
                        log_message('INFO','Save story for order ID '.$data['order_id']);
                        
                        $this->db->insert('products_sales_history', $data);
                    }
                }
            }
            
            return true;
        }
        
        $msg = 'Cant save order history info. Have no any data. Web: '.$web.'; Order ID: '.$order_id;
        log_message('INFO', $msg);
        return false;
    }
    
    /**
     * Delete product from providers_products table.
     * Be careful!
     * @param int $id
     * @return bool Boolean
     */
    public function delete_product($id)
    {
        if(is_int($id) && $id > 0)
        {
            return $this->db->delete('providers_products', array('id' => $id));
        }
        
        return FALSE;
    }
    
    /**
     * Sell product by id
     * @param int $id
     * @param int $quantity
     * @return boolean true on success
     */
    private function sell_product($id, $quantity)
    {        
        // check product
        if(!$this->get_product_by_id($id))
        {
            return FALSE;
        }
        
        // check stock
        if($this->get_product_by_id($id)->stock >= $quantity)
        {
            return $this->db->where('id', $id)
                            ->set('stock', 'stock - '.$quantity, FALSE)
                            ->set('updated_on', date('Y-m-d H:i:s', time()))
                            ->update('providers_products');
        }
        else
        {
            return $this->db->where('id', $id)
                            ->set('stock', 0)
                            ->set('updated_on', date('Y-m-d H:i:s', time()))
                            ->update('providers_products');
        }
        return TRUE;
    }
    
    /**
     * If order cancelled, then return the item to stock.
     * @param int $id
     * @param int $quantity
     * @return boolean true on success
     */
    public function return_product($id, $quantity)
    {
        // check product
        if(!$this->get_product_by_id($id))
        {
            return FALSE;
        }
        
        return $this->db->where('id', $id)
                        ->set('stock', 'stock + '.$quantity, FALSE)
                        ->set('updated_on', date('Y-m-d H:i:s', time()))
                        ->update('providers_products');
    }
    
    /**
     * Notify staff about new products in the system.
     * @param type $products
     * @return boolean
     */
    private function notify_staff_about_new_products($products)
    {
        if(count($products) <= 0){return FALSE;}
        
        $body = '<h2>PRODUCTO NUEVO</h2><br><br>';
        
        $tmpl = array ('table_open' => '<table border="2">');

        $this->table->set_template($tmpl);
        $this->table->set_heading('EAN', 'NOMBRE', 'STOCK', 'PRECIO', 'PROVEEDOR');
        
        foreach($products as $p)
        {
            $this->table->add_row($p['sku'],$p['product_name'],$p['stock'],$p['price'],$p['provider_name']);
        }
        
        $body .= $this->table->generate();
        
        // Get emails that we need to notify
        $emails = $this->get_emails_that_should_be_notified();
        
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('no_reply@amazoni.com.es', 'Amazoni4 System');
        $this->email->to($emails);
        $this->email->subject('PRODUCTO NUEVO');
        $this->email->message($body);
        
        if(!$this->email->send())
        {
            log_message('INFO',$this->email->print_debugger());
        }
    }
    
    /**
     * Get emails that should be notified by system
     * @return mixed
     */
    private function get_emails_that_should_be_notified()
    {
        $emails = array();
        
        $query = $this->db->select('email')
                          ->where('active',1)
                          ->where('receive_notifications',1)
                          ->get('users');
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
               $emails[] = $row->email;
            }
        }
        
        return $emails;
    }
    
    /**
     * Get dropdown of all available translation languages
     * @return string
     */
    public function get_translation_languages_dropdown()
    {        
        $query = $this->db->get('translation_languages');
        
        foreach ($query->result() as $row)
        {
            $options[$row->language_code] = $row->language_name;
        }
        
        $extra = ' id="translation_languages" ';
        
        $selected = $this->input->post('language_code');
        
        return form_dropdown('language_code', $options, $selected, $extra);
    }
    
    public function get_product_translation($sku, $language_code)
    {
        if ( $this->is_translation_locked($sku, $language_code) !== false )
        {
            return false;
        }
        
        if(strlen($language_code) < 5)
        {
            $language_code = 'de-DE';
        }
        
        $query = $this->db->where('sku', $sku)
                          ->where('language_code', $language_code)
                          ->get('products_translation');
        
        $this->lock_translation($sku, $language_code);
        
        return $query->row_array();
    }
    
    public function save_translation($data, $safe_mode = true)
    {        
        if($safe_mode)
        {
            if ( $this->is_translation_locked($data['sku'], $data['language_code']) !== false )
            {
                return false;
            }
        }
        
        if( count($this->get_product_translation($data['sku'], $data['language_code'])) <= 0 )
        {
            $data['created_on'] = $data['updated_on'] = date('Y-m-d H:i:s',time());
            
            $this->db->insert('products_translation', $data); 
        }
        else 
        {
            $data['updated_on'] = date('Y-m-d H:i:s',time());
            
            $this->db->where('sku', $data['sku']);
            $this->db->where('language_code', $data['language_code']);
            $this->db->update('products_translation', $data); 
        }
        
        if($safe_mode)
        {
            $this->lock_translation($data['sku'], $data['language_code']);
        }
    }
    
    /**
     * Check translation for a lock by another user
     * Return false if not locked. Or return user id who locked.
     * @param type $sku
     * @param type $language_code
     * @return boolean
     */
    public function is_translation_locked($sku, $language_code)
    {
        $query = $this->db
                 ->select('id, locked_by')
                 ->from('products_translation')
                 ->where('sku',$sku)
                 ->where('language_code', $language_code)
                 ->where('locked_by != ', 0)
                 ->get();
        
        if($query->num_rows() > 0)
        {
            if( (int)$query->row()->locked_by === (int)$this->ion_auth->get_user_id() )
            {
                return false;
            }
            
            if( !$this->is_translation_lock_expired($query->row()->id) )
            {
                return (int)$query->row()->locked_by;
            }
        }
        
        return false;
    }
    
    private function is_translation_lock_expired($id)
    {
        $query = $this->db
                 ->select('id')
                 ->from('products_translation')
                 ->where('id',$id)
                 ->where( 'locked_on < ', date('Y-m-d H:i:s', time() - 3600) )
                 ->get();
        
        if($query->num_rows() > 0)
        {
            $data = array(
                'locked_on' => null,
                'locked_by' => 0
            );
            
            $this->db
                    ->where('id',$id)
                    ->update('products_translation', $data);
            
            return true;
        }
        
        return false;
    }
    
    private function lock_translation($sku, $language_code)
    {
        $data = array(
            'locked_on' => date('Y-m-d H:i:s', time()),
            'locked_by' => (int)$this->ion_auth->get_user_id()
        );
        
        $this->db
        ->where('sku',$sku)
        ->where('language_code', $language_code)
        ->update('products_translation', $data);
    }
    
    public function import_products_meta($iteration)
    {
        $rules = array(
            'de-DE' => 'KOSMETIK',
            'en-AU' => 'COSMETICS',
            'en-GB' => 'COSMETICS',
            'en-US' => 'COSMETICS',
            'es-ES' => 'BUYIN',
            'fr-FR' => 'COSMETIQUES',
            'it-IT' => 'COSMETICIONE',
            'nl-NL' => 'BUYIN',
            'nn-NO' => 'BUYIN',
            'pt-PT' => 'BUYIN',
            'sv-SE' => 'BUYIN',
        );
        
        // Get unique sku's
        $skus = array();
        
        $query = $this->db
                ->select('DISTINCT sku')
                ->from('providers_products')
                ->where('provider_name','PINTERNACIONAL')
                ->or_where('provider_name','COQUETEO')
                ->or_where('provider_name','ENGELSA')
                ->or_where('provider_name','EUCERIN')
                ->or_where('provider_name','MIRAFARMA')
                ->limit(50, 50*$iteration)
                ->get();
        
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                $skus[] = $row->sku;
            }
        }
        
        foreach ($rules as $key => $value) 
        {
            $data = $this->virtuemart_model->extract_products_metainfo($value, $key, $skus);
            foreach ($data as $t)
            {
                $this->save_translation($t, false);
            }
        }
        
        redirect('/products/import_products_meta/'.++$iteration, 'refresh');
    }
        
    public function export_product_meta($sku)
    {
        if( empty($sku) )
        {
            return false;
        }
        
        $webs = $this->web_field_model->get_all_web_fields();
        
        // Get all translations for this sku
        $query = $this->db
                ->select('language_code, sku, product_name, product_desc, '
                        .'product_s_desc, meta_desc, meta_keywords, custom_title, '
                        .'slug')
                ->from('products_translation')
                ->where('sku', $sku)
                ->get();
        
        foreach($query->result_array() as $translation)
        {
            foreach($webs as $web)
            {
                $this->virtuemart_model->update_product_meta($web->web, $translation['language_code'], $sku, $translation);
            }
        }
        
        $this->unlock_translation($sku);
    }
    
    private function unlock_translation($sku)
    {
        $data = array(
            'locked_by' => 0,
            'locked_on' => null
        );
        
        $this->db
                ->where('sku',$sku)
                ->update('products_translation', $data);
    }
    
    public function export_all_translations_to_website($web)
    {
        $site = $this->web_field_model->get_web_field($web);
        $languages_list = array();
        
        // Get available languages
        $query = $this->db
                ->select('DISTINCT language_code')
                ->from('products_translation')
                ->get();
                
        foreach($query->result() as $row)
        {
            $language = strtolower(str_replace('-', '_', $row->language_code));
            // Check installed language
            if(strpos($site->installed_languages, $language) !== false)
            {
                $languages_list[] = $row->language_code;
            }
        }
        
        // Get all translations for this website
        
        foreach ($languages_list as $language)
        {
            $virtuemart_version = $this->virtuemart_model->check_version($web);
            $lang_suffix = strtolower(str_replace('-', '_', $language));
            
            if($virtuemart_version == '2.0.0.0')
            {
                $query = $this->db
                ->select('sku, product_name, product_desc, '
                        .'product_s_desc')
                ->from('products_translation')
                ->where('language_code', $language)
                ->get();
            }
            else 
            {
                $query = $this->db
                ->select('concat("#",sku) as product_sku, product_name, product_desc, '
                        .'product_s_desc,')
                ->from('products_translation')
                ->where('language_code', $language)
                ->get();
            }
            
            $data = $query->result_array();
                    
            $this->virtuemart_model->update_product_meta_batch($web, $data, $language);
        }
    }
    
    public function search_sku_by_phrase($phrase, $provider_name)
    {
        $query = $this->db->
                
                select('CONCAT("#", sku, " -- ", product_name, " ( ", provider_name," )", " id: ",id) as value, CONCAT("#", sku, " -- ", product_name, " ( ", provider_name," )") as label')->
                from('providers_products')->
                where('provider_name',$provider_name)->
                like('sku',$phrase)->
                order_by('product_name')->
                limit(50)->
                get();
                
        return $query->result();
    }
}