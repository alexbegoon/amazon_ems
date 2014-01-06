<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of dashboard_model
 *
 * @author sanchezz
 */
class Dashboard_model extends CI_Model {
    
    private $_orders;
    
    private $_total_count_of_orders = null;
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
        
        $this->load->model('incomes/shipping_costs_model');
        $this->load->model('products/products_model');
    }
    
    public function getOrders($page) {
        
        $filter     = $this->input->post("filter");
        $orders_ids = $this->input->post("orders_ids");
        
        if (!empty($filter['change_to']))
        {
            $this->set_status($orders_ids, $filter['change_to']);
        }
        
        $where = '';
        
        if (!empty($filter['search'])) {
            $where .= ' WHERE ( `a`.`id` LIKE \'%'.$filter['search'].'%\' ';
            $where .= ' OR `a`.`pedido` COLLATE UTF8_GENERAL_CI LIKE \'%'.$filter['search'].'%\' ';
            $where .= ' OR `a`.`nombre` COLLATE UTF8_GENERAL_CI LIKE \'%'.$filter['search'].'%\' ';
            $where .= ' OR `a`.`direccion` COLLATE UTF8_GENERAL_CI LIKE \'%'.$filter['search'].'%\' ';
            $where .= ' OR `a`.`tracking` LIKE \'%'.$filter['search'].'%\' ';
            $where .= ' OR `a`.`comentarios` COLLATE UTF8_GENERAL_CI LIKE \'%'.$filter['search'].'%\' ';
            $where .= ' OR `a`.`correo` LIKE \'%'.$filter['search'].'%\' ';
            
            for ($i = 1; $i <=10; $i++) {
                $where .= ' OR `a`.`sku'.$i.'` LIKE \'%'.$filter['search'].'%\' ';
            }
            $where .= ' OR `a`.`fechaentrada` LIKE \'%'.$filter['search'].'%\' ) ';
        } 
        
        
        if (!empty($filter['web'])) {
            if (!empty($where)) {
                $where .= ' AND `a`.`web` = \''.$filter['web'].'\' ';
            } else {
                $where .= ' WHERE `a`.`web` = \''.$filter['web'].'\' ';
            }
        }
        
        if (!empty($filter['procesado'])) {
            if (!empty($where)) {
                $where .= ' AND `a`.`procesado` = \''.$filter['procesado'].'\' ';
            } else {
                $where .= ' WHERE `a`.`procesado` = \''.$filter['procesado'].'\' ';
            }
        }
                
        if ($page) {
            $limit = (int)$page.', 50';
        } else {
            $limit      = '0, 50';
        }
        
        $order_by   = '`a`.`id` DESC'; 
                
        $query = ' SELECT `a`.`id`, `a`.`pedido`, `a`.`nombre`, `a`.`fechaentrada`, 
                          `a`.`direccion`, `a`.`pais`, `a`.`procesado`, 
                          `a`.`ingresos`, `a`.`web`, `a`.`comentarios`, 
                          `a`.`tracking`, `a`.`correo`, `a`.`in_stokoni`, \'\' as `total_number`,
                          `a`.`gasto` 
                   FROM `pedidos` AS `a` 
                   '.$where.' 
                   ORDER BY '.$order_by.'  
                   LIMIT '.$limit.'             
        ';
        
        $result = $this->db->query($query);
        
        $this->_orders = $result->result();
        
        $this->find_recurrent_buyers($this->_orders);
        $this->check_orders($this->_orders);
        
        $query = ' SELECT COUNT(*) as `total_rows` FROM `pedidos` AS `a` '.$where.' ';
        $result = $this->db->query($query);
        if($result->num_rows() == 1)
        {
            $this->_total_count_of_orders = $result->row()->total_rows;
        }
        
        
        return $this->_orders;
    }
    
    public function countOrders() {
        
        return $this->_total_count_of_orders;
    }
    
    public function getOrder($id) {
        
        $query = ' SELECT *, 
                   (`ingresos` - (
                    `cantidad1` * `precio1` + 
                    IFNULL(`cantidad2`,0) * IFNULL(`precio2`,0) + 
                    IFNULL(`cantidad3`,0) * IFNULL(`precio3`,0) + 
                    IFNULL(`cantidad4`,0) * IFNULL(`precio4`,0) + 
                    IFNULL(`cantidad5`,0) * IFNULL(`precio5`,0) + 
                    IFNULL(`cantidad6`,0) * IFNULL(`precio6`,0) + 
                    IFNULL(`cantidad7`,0) * IFNULL(`precio7`,0) + 
                    IFNULL(`cantidad8`,0) * IFNULL(`precio8`,0) + 
                    IFNULL(`cantidad9`,0) * IFNULL(`precio9`,0) + 
                    IFNULL(`cantidad10`,0) * IFNULL(`precio10`,0) 
                    
                    )) as `shipping_cost`  

                    FROM `pedidos` WHERE id = '.(int)$id.' 
            
        ';
        
        $result = $this->db->query($query);
        
        if ($result->num_rows() == 1)
        {
            return $result->row(); 
        }
        
        return false;
        
    }
    
    public function save ($data = null) {
        
        if(!$data)
        {
            $data = $this->input->post();
        }
        
        if(empty($data['id']))
        {
            $query = 'INSERT INTO `pedidos` SET ';
            
            $shipping_price = $this->shipping_costs_model->getPrice((int)$data['shipping_cost_id'])->price;
            
            $order_products = array();
            
            for($i=1; $i<=10; $i++)
            {
                if(!empty($data['sku'.$i]))
                {
                    $order_products[] = array(
                            'sku'       => $data['sku'.$i],
                            'quantity'  => $data['cantidad'.$i],
                            'price'     => $data['precio'.$i],
                            'order_id'  => $data['pedido']
                    );
                }
            }
            
            $data['gasto'] = $this->products_model->calculate_gasto($order_products, $shipping_price, $data['web'], false, $data['pedido']);
        } 
        else 
        {            
            $data['procesado'] = $this->get_procesado($data);
            $query = 'UPDATE `pedidos` SET ';
            
            if(!$this->is_order_canceled((int)$data['id']))
            {
                if($data['procesado'] == 'CANCELADO')
                {
                    $this->cancel_order((int)$data['id']);
                }
            }
        }
        
        
        
        // Available fields in the pedidos table
        $fields = array(
            'pedido',
            'nombre',
            'fechaentrada',
            'fechadepago',
            'direccion',
            'telefono',
            'codigopostal',
            'pais',
            'estado',
            'procesado',
            'sku1',
            'precio1',
            'sku2',
            'precio2',
            'sku3',
            'precio3',
            'sku4',
            'precio4',
            'sku5',
            'precio5',
            'sku6',
            'precio6',
            'sku7',
            'precio7',
            'sku8',
            'precio8',
            'sku9',
            'precio9',
            'sku10',
            'precio10',
            'cantidad1',
            'cantidad2',
            'cantidad3',
            'cantidad4',
            'cantidad5',
            'cantidad6',
            'cantidad7',
            'cantidad8',
            'cantidad9',
            'cantidad10',
            'ingresos',
            'web',
            'comentarios',
            'tracking',
            'correo',
            'gasto',
            'localidad',
            'formadepago',
            'in_stokoni',
            'magnet_msg_received'
        );
        
        foreach ($data as $field => $value) 
        {
            if (in_array($field, $fields)) 
            {
                $sql_array[] = ' `'.$field.'` = \''.trim(addslashes($value)).'\' ';
            } 
        }
        
        $query .= implode(', ', $sql_array);
        
        if(!empty($data['id']))
        {
            $query .= ' WHERE `id` = '.$data['id'].' ';
        }
        
        $result = $this->db->simple_query($query);
        
        if(empty($data['id']))
        {
            $this->products_model->store_history($data['web'],$data['pedido'],$this->db->insert_id(),$data['procesado'],$data['fechaentrada']);
        }
        
        if ($result)
        {
            return true; 
        }
        
        return false;
    }
    
    /**
     * 
     * Set status (Procesado) for one or more orders
     * 
     * @param mixed $ids Id of order or array of Ids
     * @param string $status status (Procesado)
     * @return boolean true on success or false
     */
    public function set_status($ids, $status)
    {
        $query = ' UPDATE `pedidos` 
                   SET `procesado` = ? 
                   WHERE `id` = ? 
        ';
        
        if(is_string($status) && !empty($status))
        {
            if(is_integer($ids))
            {
                   $result = $this->db->query($query, array($status,$ids));
                   if($result)
                   {
                       return true;
                   }
                   else
                   {
                       return false;
                   }
            }

            if(is_array($ids))
            {
                    foreach($ids as $id)
                    {
                        if((int)$id != 0)
                        {
                            $result = $this->db->query($query, array($status,(int)$id));
                        }
                        if(!$result)
                        {
                            return false;
                        }
                    }
                    
                    return true;
                    
            }
        }
            
        return false;
    }
    
    /**
     * Get order by pedido field
     * @param string $pedido Pedido of order
     * @param boolean $with_empty_tracking Boolean flag. If true will return pedido with empty tracking
     * @return mixed array of objects or boolean false on unsuccess
     */
    public function get_order_by_pedido($pedido, $with_empty_tracking = false)
    {
        if(empty($pedido))
        {
            return false;
        }
        
        if($with_empty_tracking)
        {
            $query = ' SELECT * FROM `pedidos` WHERE `pedido` LIKE \'%'.$pedido.'%\' 
                   AND ( `tracking` IS NULL OR `tracking` = \'\' OR `tracking` = \' \' ) 
            ';
        }
        else
        {
            $query = ' SELECT * FROM `pedidos` WHERE `pedido` LIKE \'%'.$pedido.'%\' 
            ';
        }
            
        $result = $this->db->query($query);
        
        if ($result->num_rows() > 0)
        {
            return $result->result(); 
        }
        
        return false;
    }
    
    /**
     * Get list of countries, that available. It means that country have Shipping company and shipping cost.
     * @param string $pais Country code of selected
     * @return string
     */
    public function get_pais_list($pais = null)
    {
        $html = '';
        
        $query = 'SELECT `countries`.`full_name`, `countries`.`code`,
                        `countries`.`name`
                  FROM `'.$this->db->dbprefix('shipping_costs').'` as `available_countries` 
                  LEFT JOIN `'.$this->db->dbprefix('countries').'` as `countries` 
                  ON  `available_countries`.`country_code` = `countries`.`code` 
        ';
        
        $result = $this->db->query($query);
        
        if($result)
        {
            $country_list = $result->result();
                        
            $final_array = array();
            
            foreach($country_list as $country)
            {
                $final_array[$country->code] = $country->name . ' ('.$country->code.')';
            }
            
            $html .= form_dropdown('pais', $final_array, $pais, 'id="select_pais" required="required"'); 
        }
        
        return $html;
    }
    
    public function get_available_coutries_to_ship($web)
    {
        if(empty($web))
        {
            return FALSE;
        }
        
        $html = array();
        
        $query = '
                    SELECT DISTINCT `countries`.`code`, `countries`.`full_name`, 
                        `countries`.`name`
                    FROM `'.$this->db->dbprefix('shipping_costs').'` as `available_countries` 
                    LEFT JOIN `'.$this->db->dbprefix('countries').'` as `countries` 
                    ON  `available_countries`.`country_code` = `countries`.`code` 
                    WHERE `available_countries`.`web` = \''.$web.'\'
        ';
        
        $result = $this->db->query($query);
        
        if($result)
        {
            $country_list = $result->result();
                        
            $final_array = array();
            
            foreach($country_list as $country)
            {
                $html[$country->code] = '<option value="'.$country->code.'">'.$country->name . ' ('.$country->code.')'.'</option>'; 
            }            
        }
        
        return $html;
        
    }
    
    public function get_available_shipping($country_code, $web)
    {
        if(empty($country_code) || empty($web))
        {
            return FALSE;
        }
        
        $html = array();
                
        $this->db->select('*, shipping_costs.id as shipping_costs_id');
        $this->db->from('shipping_costs');
        $this->db->join('shipping_companies', 'shipping_companies.id = shipping_costs.id_shipping_company', 'left');
        $this->db->join('shipping_types', 'shipping_types.shipping_type_id = shipping_costs.shipping_type_id', 'left');
        $this->db->where('country_code =', $country_code);
        $this->db->where('web =', $web);
        
        $query = $this->db->get();
        
        if($query->num_rows() > 0)
        {
            $shipping_arr = $query->result();
            
            foreach ($shipping_arr as $shipping)
            {
                $html[$shipping->shipping_costs_id] = '<option value="'.$shipping->shipping_costs_id.'">'.$shipping->company_name . ' ('.$shipping->shipping_type_name.') - '.  number_format($shipping->price, 2).' &euro;</option>';
            }
        }
                
        return $html;
        
    }

    private function find_recurrent_buyers($orders)
    {
        if(empty($orders) && !is_array($orders))
        {
            return false;
        }
        
        $this->db->cache_on();
        
        $query = ' SELECT CASE 
                            WHEN COUNT(*) > 0 
                            THEN COUNT(*) 
                            ELSE \' \' 
                            END `total_number` 
                   FROM `pedidos` 
                   WHERE `correo` = ? 
                   GROUP BY `correo` 
                   HAVING COUNT(`correo`) > 1 
        ';
        
        foreach ($orders as $order)
        {
            if(!empty($order->correo) && is_string($order->correo))
            {
                $result = $this->db->query($query, array($order->correo));
                if(is_object($result->row()))
                {
                    $order->total_number = $result->row()->total_number;
                }
            }
        }
        
        $this->db->cache_off();
    }
    
    public function get_order_for_printer($id)
    {
        $this->load->model('incomes/web_field_model');
        $this->load->model('incomes/shipping_costs_model');
        $this->load->model('products/products_model');
        
        $order = $this->getOrder((int)$id);
        
        $order->products = $this->products_model->get_products_of_order((int)$id);
        $order->country = $this->shipping_costs_model->get_country_name_by_code($order->pais);
        $order->web_field = $this->web_field_model->get_web_field($order->web);
                
        return $order;
    }
    
    /**
     * Return correct procesado when update the order
     * @param array $data
     * @return mixed
     */
    private function get_procesado($data)
    {
        
        // The rules if order takes the Tracking number then check old procesado
        $rules = array(
            'PTE_ENVIO_GLS'         => 'ENVIADO_GLS',
            'PTE_ENVIO_FEDEX'       => 'ENVIADO_FEDEX',
            'PTE_ENVIO_PACK'        => 'ENVIADO_PACK',
            'PTE_ENVIO_TOURLINE'    => 'ENVIADO_TOURLINE'
        );
                
        if(isset($data['id']))
        {
            $order = $this->getOrder((int)$data['id']);
            
            if(!$order)
            {
                if(isset($data['procesado']))
                {
                    return $data['procesado'];
                }

                return 'NO';
            }
            
            // If we are try to set Tracking number
            if(empty($order->tracking) && !empty($data['tracking']))
            {
                if(array_key_exists($order->procesado, $rules))
                {
                    $this->set_status((int)$order->id, $rules[$order->procesado]);
                    return $rules[$order->procesado];
                }
            }
        }
        
        if(isset($data['procesado']))
        {
            return $data['procesado'];
        }
        
        return 'NO';
    }
    
    public function get_order_detail_info($id)
    {
        if(!is_integer($id) || empty($id))
        {
            return FALSE;
        }
        
        $this->db->where('order_id =',$id);
        $query = $this->db->get('products_sales_history');
                
        return $query->result();
    }
    
    private function check_orders($orders)
    {
        if(empty($orders) && !is_array($orders))
        {
            return false;
        }
        
        $this->db->cache_on();
        
        foreach ($orders as $order)
        {
            $order->have_errors = false;
            $order->warehouse_sales = false;
            
            $this->db->where('order_id =', $order->id);
            $query = $this->db->get('products_sales_history');
            
            if($query->num_rows() > 0)
            {
                $items = $query->result();
                
                foreach ($items as $item)
                {
                    if($item->sold_from_warehouse == 1)
                    {
                        $order->warehouse_sales = true;
                    }
                }
            }
            else 
            {
                $order->have_errors = true;
                $order->warehouse_sales = false;
            }
        }
        
        $this->db->cache_off();
    }
    
    /**
     * Cancel order
     * @param int $order_id
     * @return boolean
     */
    private function cancel_order($order_id)
    {
        if(empty($order_id))
        {
            return false;
        }
        
        // Load models
        $this->load->model('stokoni/stokoni_model');
        
        // Get items that sold from warehouse
        $this->db->where('order_id =', $order_id);
        $this->db->where('sold_from_warehouse =', 1);
        $this->db->where('canceled =', 0);
        
        $query = $this->db->get('products_sales_history');
        
        if($query->num_rows() > 0)
        {
            $products = $query->result();
            
            foreach ($products as $product)
            {
                $this->stokoni_model->return_product(   (int)$product->warehouse_product_id,
                                                        (int)$product->quantity
                                                    );
            }
        }
        
        // Mark order as canceled
        $this->db->where('order_id =', $order_id);
        $this->db->where('canceled =', 0);
        
        $data = array(
               'canceled' => 1
            );
        
        $this->db->update('products_sales_history', $data); 

        return true;
    }
    
    /**
     * Check. Is order canceled?
     * @param int $id
     * @return boolean
     */
    private function is_order_canceled($id)
    {
        
        $query = ' SELECT count(*) as total
                   FROM pedidos
                   WHERE id = '.(int)$id.' 
                   AND procesado = \'CANCELADO\'
        
        ';
        
        $result = $this->db->query($query);
        
        if($result->row()->total > 0)
        {
            return true;
        }
        
        return false;
    }
}