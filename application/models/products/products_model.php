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

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        
        // Load models
        $this->load->model('incomes/providers_model');
        $this->load->model('incomes/web_field_model');
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
        
        $query = ' SELECT `sku`, `product_name`, `provider_name`, `price`, `stock` 
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
            
            return $this->_products;
        }
        
        return false;        
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
    
    private function update_products_table($products)
    {
        //Load model
        $this->load->model('incomes/providers_model');
        
        $summary = new stdClass();
        $summary->affected_rows = 0;
        
        $this->db->trans_begin();
        
        $query = ' INSERT INTO 
                   `'.$this->db->dbprefix('providers_products').'` 
                   (`sku`, `product_name`, `provider_name`, `provider_id` ,`price`, `stock`)
                   VALUES
                   (?,?,?,?,?,?)
                   ON DUPLICATE KEY UPDATE 
                   `product_name` = ?,  `price` = ?, `stock` = ?
        ';
        
        if(!empty($products))
        {
            foreach ($products as $product)
            {
                $provider_id = $this->providers_model->get_provider_id_by_name($product['provider_name']);
                
                if($provider_id != 0 && is_integer($provider_id) )
                {
                    $this->db->query($query, array(
                                                    $product['sku'],
                                                    $product['product_name'],
                                                    $product['provider_name'],
                                                    $provider_id,
                                                    $product['price'],
                                                    $product['stock'],
                                                    $product['product_name'],
                                                    $product['price'],
                                                    $product['stock']
                    ));
                    
                    $summary->affected_rows += $this->db->affected_rows();
                }
            }
        }
        
        $this->db->trans_commit();
        
        return $summary;
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
     * Return product using SKU and WEB as index
     * @param string $sku
     * @param string $web
     * @return mixed Object of product or boolean false on unsuccess
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
                    
                    $query = ' SELECT `product_name`, `sku`, `provider_name` 
                               FROM `'.$this->db->dbprefix('providers_products').'` 
                               WHERE `sku` = \''.$sku.'\' AND `provider_name` = \''.$provider_name.'\' 
                    ';
                    
                    $result = $this->db->query($query);

                    if($result->num_rows() == 1)
                    {
                        $this->_products['products_by_web_and_sku'][$sku][$web] = $result->row();
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
                          `precio` as `price`, `stock`, \'ENGELSA\' as `provider_name` 
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
                            \'GRUTINET\' as `provider_name` 
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
                $products[$i] = $this->get_product($order->{'sku'.$i}, $order->web);
                $products[$order->{'sku'.$i}] = $this->get_product($order->{'sku'.$i}, $order->web);
                $products['product_'.$i] = $this->get_product($order->{'sku'.$i}, $order->web);
            }
        }
        
        return $products;
    }
}