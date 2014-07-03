<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of dashboard_model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Dashboard_model extends CI_Model {
    
    private $_orders;
    
    private $_total_count_of_orders = null;
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
        
        $this->load->model('incomes/shipping_costs_model');
        $this->load->model('products/products_model');
        $this->load->model('stokoni/stokoni_model');
    }
    
    public function getOrders($page) {
        
        $filter     = $this->input->post("filter");
        $post_data  = $this->input->post();
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
        
        if(!empty($post_data['provider']))
        {
            $ids = $this->get_orders_ids_by_provider((int)$post_data['provider']);
            if (!empty($where)) {
                $where .= ' AND `a`.`id` IN('.  implode(',', $ids).') ';
            } else {
                $where .= ' WHERE `a`.`id` IN('.  implode(',', $ids).') ';
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
    
    private function get_orders_ids_by_provider($provider_id)
    {
        $ids = array();
        $this->db->cache_on();
        $query = $this->db->select('order_id')
                          ->from('products_sales_history')
                          ->where('provider_id', $provider_id)
                          ->get();
        
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                $ids[] = (int)$row->order_id;
            }
        }
        $this->db->cache_off();
        return $ids;
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
            $order = $result->row();
            $order_arr = (array)$order;
            $order_items = array();
            $order_details = array();
            
            for($i=1; $i<=10; $i++)
            {
                if(!empty($order_arr['sku'.$i]))
                {
                    $order_items[] =  $order_arr['sku'.$i];
                }
            }
            
            for($i=1; $i<=10; $i++)
            {
                if(!empty($order_arr['sku'.$i]))
                {
                    $order_details[$order_arr['sku'.$i]] = array(
                            'quantity'  => $order_arr['cantidad'.$i],
                            'price'     => $order_arr['precio'.$i]
                    );
                }
            }
            
            $this->session->set_flashdata('items_of_order_'.$id, $order_items);
            $this->session->set_flashdata('details_of_order_'.$id, $order_details);
            
            return $order; 
        }
        
        return false;
        
    }
    
    public function save ($data = null) {
        
        if(!$data)
        {
            $data = $this->input->post();
        }
        
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
        
        if(empty($data['id']))
        {
            $query = 'INSERT INTO `pedidos` SET ';
            
            $shipping_price = $this->shipping_costs_model->getPrice((int)$data['shipping_cost_id'])->price;
            
            $data['gasto'] = $this->products_model->calculate_gasto($order_products, $shipping_price, $data['web'], false, $data['pedido']);
        } 
        else 
        {            
            $data['procesado'] = $this->get_procesado($data);
            $query = 'UPDATE `pedidos` SET ';
            
            // If order have modified items
            if(isset($data['items_modified']) && $data['procesado'] != 'CANCELADO')
            {
                // Cancel all past items list
                $this->cancel_order((int)$data['id']);
                
                $shipping_price = $this->shipping_costs_model->get_shipping_price_by_order_id((int)$data['id']);
                
                // Recalculate items again
                $data['gasto'] = $this->products_model->calculate_gasto($order_products, $shipping_price, $data['web'], false, $data['pedido']);
                $data['ingresos'] = 0;
                
                foreach ($order_products as $item)
                {
                    $data['ingresos'] += $item['price'] * $item['quantity'];
                }
                
                // Store in history
                $this->products_model->store_history($data['web'],$data['pedido'],(int)$data['id'],$data['procesado'],$data['fechaentrada']);
                
                // Check items stock
                $this->db->where('csv_exported',0);
                $this->db->where('canceled',0);
                $this->db->where('out_of_stock',1);
                $this->db->where('order_id',(int)$data['id']);
                $query2 = $this->db->get('products_sales_history');
                
                if($query2->num_rows() > 0)
                {
                    $data['procesado'] = 'ROTURASTOCK';
                }
                
                $this->save_order_modifications($data);
            }
            
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
        
        if(isset($data['id']))
        {
            $order_id = $data['id'];
        }
        
        if(empty($data['id']))
        {
            $order_id = $this->db->insert_id();
            $this->products_model->store_history($data['web'],$data['pedido'],$order_id,$data['procesado'],$data['fechaentrada']);
        }
        
        if(isset($data['procesado']))
        {
            $this->set_status((int)$order_id, $data['procesado']);
        }
        
        if ($result)
        {
            return true; 
        }
        
        return false;
    }
    
    private function save_order_modifications($data)
    {
        $order_products_was = $this->session->flashdata('items_of_order_'.$data['id']);
        
        $order_products = array();
            
        for($i=1; $i<=10; $i++)
        {
            if(!empty($data['sku'.$i]))
            {
                $order_products[] = $data['sku'.$i];
            }
        }
        
        $products_added = array_diff($order_products, $order_products_was);
        $products_removed = array_diff($order_products_was, $order_products);
        
        $insert_data = array();
        
        foreach ($products_added as $product) 
        {
            $insert_data[] = array(
                'order_id' => $data['id'],
                'user_id' => $this->ion_auth->get_user_id(),
                'product_sku' => $product, 
                'action' => 1,
                'created_on' => date('Y-m-d H:i:s'),
            );
        }
        foreach ($products_removed as $product) 
        {
            $insert_data[] = array(
                'order_id' => $data['id'],
                'user_id' => $this->ion_auth->get_user_id(),
                'product_sku' => $product, 
                'action' => -1,
                'created_on' => date('Y-m-d H:i:s'),
            );
        }
        
        if(!empty($insert_data))
        $this->db->insert_batch('order_modifications', $insert_data);
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
        
        $user_id = $this->ion_auth->get_user_id();
        if(empty($user_id))
        {
            $user_id = 0;
        }
            
        
        if(is_string($status) && !empty($status))
        {
            if(is_integer($ids))
            {
                   $result = $this->db->query($query, array($status,$ids));

                   $this->touch_status((int)$ids,$status,$user_id);

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

                            $this->touch_status((int)$id,$status,$user_id);

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
     * Save status changes in the history
     * @param type $order_id
     * @param type $status
     * @param type $user_id
     */
    public function touch_status($order_id, $status, $user_id)
    {
        $this->db->select('status');
        $this->db->from('order_status_history');
        $this->db->where('order_id',$order_id);
        $this->db->order_by('id','desc');
        $this->db->limit(1);
        $query = $this->db->get();
        
        if($query->num_rows() === 1)
        {            
            if($query->row()->status == $status)
            {
                return FALSE;
            }
        }
        
        $insert_data = array(
            'order_id' => $order_id,
            'status' => $status,
            'user_id' => $user_id,
            'created_on' => date('Y-m-d H:i:s'),
        );

        $this->db->insert('order_status_history', $insert_data);
    }
    
    public function get_order_status_history($id) 
    {
        $this->db->select('status,user_id,created_on');
        $this->db->from('order_status_history');
        $this->db->where('order_id',$id);
        $this->db->order_by('id','asc');
        $query = $this->db->get();
        
        if($query->num_rows() > 0)
        {
            return $query->result();
        }
        
        return FALSE;
    }


    public function update_status_of_orders()
    {
        $statuses = array(
            'PREPARACION_ENGELSA_FEDEX',
            'PREPARACION_ENGELSA_GLS',
            'PREPARACION_ENGELSA_PACK', 
            'PREPARACION_ENGELSA_TOURLINE', 
        );
        
        $dbprefix = $this->db->dbprefix;
        
        $this->db->select('p.id, p.procesado');
        $this->db->set_dbprefix(null);
        $this->db->from('pedidos as p');
        $this->db->set_dbprefix($dbprefix);
        $this->db->where_in('p.procesado', $statuses);
        $query = $this->db->get();
        
        if($query->num_rows() <= 0)
        {
            return FALSE;
        }
        
        foreach ($query->result() as $order) 
        {
            $query_2 = $this->db->select('id')
                                ->from('products_sales_history')
                                ->where('csv_exported',0)
                                ->where('canceled',0)
                                ->where('order_id',$order->id)
                                ->get();
            
            if($query_2->num_rows() === 0)
            {
                if($order->procesado == 'PREPARACION_ENGELSA_FEDEX')
                {
                    $this->set_status((int)$order->id, 'PEDIDO_ENGELSA_FEDEX');
                }
                elseif($order->procesado == 'PREPARACION_ENGELSA_GLS')
                {
                    $this->set_status((int)$order->id, 'PEDIDO_ENGELSA_GLS');
                }
                elseif($order->procesado == 'PREPARACION_ENGELSA_PACK')
                {
                    $this->set_status((int)$order->id, 'PEDIDO_ENGELSA_PACK');
                }
                elseif($order->procesado == 'PREPARACION_ENGELSA_TOURLINE')
                {
                    $this->set_status((int)$order->id, 'PEDIDO_ENGELSA_TOURLINE');
                }
            }
        }
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
        
        $order->products    = array();
        
        $query = $this->db->select('product_name, sku, provider_name, SUM(quantity) as quantity, order_price as price, provider_id')
                ->from('products_sales_history')
                ->where('order_id', $id)
                ->where('csv_exported', 1)
                ->order_by('id', 'asc')
                ->group_by('sku')
                ->get();
        
        if($query->num_rows > 0)
        {
            $order->products = $query->result();
        }
        
        $order->country     = $this->shipping_costs_model->get_country_name_by_code($order->pais);
        $order->web_field   = $this->web_field_model->get_web_field($order->web);
                
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
    public function cancel_order($order_id)
    {
        if(empty($order_id))
        {
            return false;
        }
        
        // Load models
        $this->load->model('stokoni/stokoni_model');
        
        // Get items
        $this->db->where('order_id =', $order_id);
        $this->db->where('canceled =', 0);
        
        $query = $this->db->get('products_sales_history');
        
        if($query->num_rows() > 0)
        {
            $products = $query->result();
            
            foreach ($products as $product)
            {
                if((int)$product->sold_from_warehouse === 1)
                {
                    $this->stokoni_model->return_product(   (int)$product->warehouse_product_id,
                                                            (int)$product->quantity
                                                        );
                }
                else 
                {
                    $this->products_model->return_product(      
                                                            (int)$product->provider_product_id,
                                                            (int)$product->quantity
                                                        );
                }
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
    
    public function verify_order($order_id)
    {
        // get order items
        
        $query = $this->db->select('id, sku_in_order as sku, quantity, order_price, order_date, order_id, order_name, shipping_price, web, warehouse_product_id')
                ->from('products_sales_history')
                ->where('canceled', 0)
                ->where('csv_exported', 0)
                ->where('order_id', $order_id)
                ->get();
        
        if($query->num_rows() <= 0)
        {
            return 'Error. Order have no items';
        }
        
        $order_products = array();
        $i = 0;
        
        $update_data = array(
            'canceled' => 1
        );
        
        // check every item in order
        foreach ($query->result() as $item)
        {
            $order_products[$i]['sku'] = $item->sku;
            $order_products[$i]['quantity'] = $item->quantity;
            $order_products[$i]['price'] = $item->order_price;
            $order_products[$i]['order_id'] = $item->order_name;
            
            $shipping_cost = $item->shipping_price;
            $web           = $item->web;
            $order_pedido  = $item->order_name;
            $order_date    = $item->order_date;
            
            $i++;
            
            if( !empty($item->warehouse_product_id) )
            {
                $this->stokoni_model->return_product((int)$item->warehouse_product_id, (int)$item->quantity);
            }
            
            $this->db->where('id', $item->id);
            $this->db->update('products_sales_history', $update_data);
        }
        
        $gasto = $this->products_model->calculate_gasto($order_products, $shipping_cost, $web, false);
        
        $this->products_model->store_history($web,$order_pedido,(int)$order_id,$this->getOrder((int)$order_id)->procesado,$order_date);
        
        if($gasto > 0)
        {
            $this->save(array(
                'id' => (int)$order_id,
                'gasto' => $gasto,
                'procesado' => $this->getOrder((int)$order_id)->procesado
            ));
            
            return 'Done';
        }
        else 
        {
            $this->save(array(
                'id' => (int)$order_id,
                'gasto' => 0,
                'procesado' => $this->getOrder((int)$order_id)->procesado
            ));
            
            $query = $this->db->select('id')
                ->from('products_sales_history')
                ->where('canceled', 0)
                ->where('out_of_stock', 1)
                ->where('order_id', $order_id)
                ->get();
            
            if($query->num_rows() > 0)
            {
                $this->set_status((int)$order_id, 'ROTURASTOCK');
                
                $out_of_stock_orders = $this->session->userdata('out_of_stock_orders');
                
                $out_of_stock_order = array(
                    'order_id' => (int)$order_id,
                    'order_name' => $this->getOrder((int)$order_id)->pedido,
                    'date_when_out_of_stock' => date('Y-m-d H:i:s'),
                    'order_status' => 'ROTURASTOCK',
                    'created_on' => date('Y-m-d H:i:s'),
                    'created_by' => (int)$this->ion_auth->get_user_id(),
                );
                
                $out_of_stock_orders[] = $out_of_stock_order;
                
                $this->session->set_userdata($out_of_stock_orders);
                $this->db->insert('roturastock_report', $out_of_stock_order);
                
                return 'Out of stock';
            }
            
            return 'Unsuccess';
        }
        
        return 'Error';
    }
    
    public function get_roturastock_orders($page)
    {
        $this->db->select('rep.date_when_out_of_stock, rep.order_id, rep.order_name, p.procesado as order_status');
        $this->db->from('roturastock_report as rep');
        $dbprefix = $this->db->dbprefix;
        $this->db->set_dbprefix(null);
        $this->db->join('pedidos as p','p.id = rep.order_id');
        $this->db->set_dbprefix($dbprefix);
        $this->db->limit(50,$page);
        $this->db->order_by('rep.order_id','desc');
        $query  = $this->db->get();
        
        if($query->num_rows() <= 0)
        {
            return array();
        }
        
        return $query->result();
    }
    
    public function get_roturastock_orders_count()
    {
        $this->db->select('COUNT(*) as total');
        $this->db->from('roturastock_report as rep');
        $dbprefix = $this->db->dbprefix;
        $this->db->set_dbprefix(null);
        $this->db->join('pedidos as p','p.id = rep.order_id');
        $this->db->set_dbprefix($dbprefix);
        $query  = $this->db->get();
        
        if($query->num_rows() <= 0)
        {
            return false;
        }
        
        return (int)$query->row()->total;
    }
    
    public function get_order_modifications($page)
    {
        $post_data = $this->input->post();
        $this->db->select('order_id, user_id, product_sku, action, created_on');
        $this->db->from('order_modifications');
        
        if(isset($post_data['date_from']) && isset($post_data['date_to']))
        {
            $this->db->where('created_on >=',$post_data['date_from']);
            $this->db->where('created_on <=',$post_data['date_to']);
        }
        
        $this->db->limit(50, $page);
        $query = $this->db->get();
        
        if($query->num_rows > 0)
        {
            return $query->result();
        }
        
        return array();
    }
    public function get_order_modifications_count()
    {
        $post_data = $this->input->post();
        
        if(isset($post_data['date_from']) && isset($post_data['date_to']))
        {
            $this->db->where('created_on >=',$post_data['date_from']);
            $this->db->where('created_on <=',$post_data['date_to']);
        }
        
        return $this->db->select('COUNT(*) as total')
                ->from('order_modifications')
                ->get()->row()->total;
    }
}