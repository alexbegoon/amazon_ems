<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Providers model
 *
 * @author      Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

class Providers_model extends CI_Model
{
    private $_providers = array(), $_buffer_data = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * 
     * Get all providers
     * 
     * @return mixed array or boolean false on unsuccess
     */
    public function getProviders()
    {
        $query = 'SELECT * 
                  FROM `'.$this->db->dbprefix('providers').'` 
                  ORDER BY `id`     
        ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            return $result->result();
        }
        
        return false;
    }
    
    public function add()
    {
        $post_data = $this->input->post();
        
        if (!empty($post_data)) {
            
            $values = array();
            
            foreach ($post_data as $k => $v) {
                if ($k !== 'task') {
                    if (is_numeric($v)) {
                        $values[] = $k . ' = '.''.$v.'';
                    } else {
                        $values[] = $k . ' = '.'\''.$v.'\'';
                    }
                    
                }         
            }
            
            $query = ' INSERT INTO `'.$this->db->dbprefix('providers').'` 
                       SET '.implode(', ',$values).' 
            ';
            
            return $this->db->query($query);
            
        } else {
            return false;
        }
        
    }
    
    public function getProvider($id)
    {
        if (!empty($id) && $id != 0)
        {
            if(isset($this->_buffer_data['provider'][$id]))
            {
                return $this->_buffer_data['provider'][$id];
            }
            
            $query = ' SELECT * FROM `'.$this->db->dbprefix('providers').'` 
                       WHERE `id` = '.(int)$id.' ';

             $result = $this->db->query($query);

             if ($result->num_rows() == 1)
             {
                 $this->_buffer_data['provider'][$id] = $result->row_object();
                 return $this->_buffer_data['provider'][$id];
             }

             return false;
        }
        else
        {
            return false;
        }
        
    }
    
    public function edit($id)
    {
        $post_data = $this->input->post();
        
        $values = array();
            
        foreach ($post_data as $k => $v) {
            if ($k !== 'task' && $k !== 'id') {
                $values[] = $k . ' = '.'\''.$v.'\'';
            }         
        }
        
        if (empty($values)) {
            return false;
        }
        
        $query = ' UPDATE `'.$this->db->dbprefix('providers').'` 
                   SET '.implode(', ',$values).' 
                   WHERE `id` = '.(int)$id.' 
        ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            return true;
        }
        
        return false;
    }
    
    public function remove($id)
    {
        if(!empty($id)&& $id != 0)
        {
             $query = ' DELETE FROM `'.$this->db->dbprefix('providers').'` 
                        WHERE `id` =  '.(int)$id.' ';

             $result = $this->db->query($query);

             if ($result) {
                 return true;
             }

             return false;

        }
        
        return false;
        
    }
    
    /**
     * 
     * @param mixed $id Id or Name of selected provider
     * @param boolean $key_as_value put key as value in select options
     * @return string select box
     */
    public function get_providers_list($id, $key_as_value = false, $empty_first_option = false, $extra = 'id="providers_list"')
    {
        $html = '';
        
        $query = 'SELECT id, name 
                  FROM `'.$this->db->dbprefix('providers').'` 
                  ORDER BY `name` 
        ';
        
        $result = $this->db->query($query);
        
        if ($result)
        {
            $providers = $result->result();
            
            $options = array();
            
            if($empty_first_option)
            {
                $options[''] = '';
            }
            
            foreach ($providers as $provider)
            {
                if($key_as_value)
                {
                    $options[$provider->name] = $provider->name;
                }
                else
                {
                    $options[$provider->id] = $provider->name;
                }
            }
            
            $html = form_dropdown('provider', $options, $id, $extra );
        }
        
        return $html;
    }
    
    /**
     * Return provider ID by name if exists
     * @param string $name name of provider
     * @return mixed integer or boolean false on unsuccess
     */
    public function get_provider_id_by_name($name)
    {
        if(empty($name) || !is_string($name))
        {
            return false;
        }
        
        if(isset($this->_providers[$name]['id']))
        {
            return $this->_providers[$name]['id'];
        }
        
        $query = ' SELECT `id` 
                   FROM `'.$this->db->dbprefix('providers').'` 
                   WHERE `name` = \''.$name.'\'     
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() == 1)
        {
            $this->_providers[$name]['id'] = (int)$result->row()->id;
            return $this->_providers[$name]['id'];
        }
        
        return false;
    }
    
    /**
     * Return provider name using SKU nd WEB field of product.
     * This method use RegExp, that provided for that WEB
     * @param string $sku
     * @param string $web
     * @return mixed Return name of provider or boolean false on unsuccess
     */
    public function get_provider_name($sku,$web)
    {
        if(!empty($sku) && !empty($web) && is_string($web) && is_string($sku))
        {
            if(isset($this->_buffer_data['provider_name'][$web]))
            {
                return $this->_buffer_data['provider_name'][$web];
            }
            
            if(isset($this->_buffer_data['provider_name'][$sku][$web]))
            {
                return $this->_buffer_data['provider_name'][$sku][$web];
            }
            
            $query = ' SELECT `provider_id`, `sku_regexp`
                       FROM `'.$this->db->dbprefix('web_provider').'` 
                       WHERE `web` = \''.$web.'\' 
            ';
            
            $result = $this->db->query($query);
            
            if($result->num_rows() == 1) // One web have only one provider
            {
                $provider = $this->getProvider($result->row()->provider_id);
                
                if($provider)
                {
                    $this->_buffer_data['provider_name'][$web] = $provider->name;
                    return $provider->name;
                }
            }
            elseif ($result->num_rows > 1) // One web have more than one provider
            {
                $web_providers = $result->result();
                
                foreach ($web_providers as $row)
                {
                    $regexp = stripslashes($row->sku_regexp);
                    
                    if(empty($regexp))
                    {
                        $msg = $web.' have more than one product provider. Please setup regexp for provider ID: '.$row->provider_id;
                        log_message('INFO', $msg);
                    }
                    
                    if(preg_match($regexp, $sku) === 1)
                    {
                        $provider = $this->getProvider($row->provider_id);
                
                        if($provider)
                        {
                            $this->_buffer_data['provider_name'][$sku][$web] = $provider->name;
                            return $provider->name;
                        }
                    }
                }
            }
        }
        
        return false;
    }
    
    public function create_provider_order($provider_name)
    {
        if($provider_name == '_WAREHOUSE')
        {
            $provider_id = 0;
        }
        else
        {
            $provider_id = $this->get_provider_id_by_name($provider_name);
        }
        
        if($provider_id === false)
        {
            return false;
        }
        
        // Get order items which ready for provider order
        $order_items = $this->_get_order_items($provider_id);
        
        if($order_items === false || count($order_items) <= 0)
        {
            return false;
        }
        
        $this->db->trans_begin();
        
        $insert_data = array(
            
            'provider_id' => $provider_id,
            'provider_name' => $provider_name,
            'created_on' => date('Y-m-d H:i:s'),
            'created_by' => (int)$this->ion_auth->get_user_id(),
            
        );
        
        $this->db->insert('provider_orders', $insert_data);
        
        $provider_order_id = $this->db->insert_id();
        $insert_data = array();
        
        foreach ($order_items as $item) 
        {
            $insert_data[] = array(
                'provider_order_id'     => $provider_order_id,
                'order_item_id'         => $item->id,
                'provider_price'        => $item->latest_provider_price,
                'quantity'              => $item->quantity,
                'created_on'            => date('Y-m-d H:i:s'),
                'created_by'            => (int)$this->ion_auth->get_user_id(),
            );
            
            $update_data = array(
                'csv_exported' => 1,
                'csv_export_date' => date('Y-m-d H:i:s'),
            );
            
            $this->db->where('id', $item->id);
            $this->db->update('products_sales_history', $update_data);
        }
        
        $this->db->insert_batch('provider_order_items', $insert_data);
                
        $this->dashboard_model->update_status_of_orders();
//        $this->db->trans_rollback();
        
        $this->db->trans_commit();
        
        return $provider_order_id;
        
    }
    
    public function get_provider_order_items_ids($provider_order_id)
    {
        $query = $this->db->select('order_item_id')
                 ->from('provider_order_items')
                 ->where('provider_order_id',$provider_order_id)
                 ->get();
        
        if($query->num_rows() === 0)
        {
            return FALSE;
        }
        
        $data = array();
        
        foreach ($query->result() as $row)
        {
            $data[] = $row->order_item_id;
        }
        
        return $data;
    }
    
    public function get_provider_name_by_order_id($provider_order_id)
    {
        $query = $this->db->select('provider_name')
                 ->from('provider_orders')
                 ->where('id',$provider_order_id)
                 ->get();
        
        if($query->num_rows() === 1)
        {
            return $query->row()->provider_name;
        }
        
        return '';
    }
    
    private function _get_order_items($provider_id)
    {
        $statuses = array(
            'PREPARACION_ENGELSA_FEDEX',
            'PREPARACION_ENGELSA_GLS',
            'PREPARACION_ENGELSA_PACK', 
            'PREPARACION_ENGELSA_TOURLINE', 
        );
        
        $dbprefix = $this->db->dbprefix;
        
        $this->db->select(
                '   h.id, '
                . ' h.quantity, '
                . ' prod.price as latest_provider_price'
                );
        $this->db->set_dbprefix(null);
        $this->db->from('pedidos as p');
        $this->db->set_dbprefix($dbprefix);
        $this->db->join('products_sales_history as h', 'p.id = h.order_id', 'left');
        $this->db->join('providers_products as prod', 'prod.id = h.provider_product_id', 'left');
        $this->db->where_in('p.procesado', $statuses);
        $this->db->where('h.canceled', 0);
        $this->db->where('h.out_of_stock', 0);
        $this->db->where('h.csv_exported', 0);
        $this->db->where('h.provider_id', $provider_id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) 
        {
            return $query->result();
        }
        
        return false;
    }
    
    public function get_provider_orders($page) 
    {
        $post_data = $this->input->post();
        
        if(!empty($post_data['provider']))
        {
            $this->db->where('provider_id',$post_data['provider']);
        }
        if(!empty($post_data['date_from']))
        {
            $this->db->where('created_on >=',$post_data['date_from']);
        }
        if(!empty($post_data['date_to']))
        {
            $this->db->where('created_on <=',$post_data['date_to']);
        }
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('provider_orders',50,$page);
        
        return $query->result();
    }
    
    public function count_all_providers_orders()
    {
        $post_data = $this->input->post();
        
        if(!empty($post_data['provider']))
        {
            $this->db->where('provider_id',$post_data['provider']);
        }
        if(!empty($post_data['date_from']))
        {
            $this->db->where('created_on >=',$post_data['date_from']);
        }
        if(!empty($post_data['date_to']))
        {
            $this->db->where('created_on <=',$post_data['date_to']);
        }
        
        $this->db->select('COUNT(*) as total');
        $this->db->from('provider_orders');
        $query = $this->db->get();
        
        return $query->row()->total;
    }
    
    public function get_provider_order($id)
    {
        $query = $this->db->select(''
                . 'h.product_name, h.sku, SUM(i.quantity) as quantity,  '
                . ' CASE 
                        WHEN i.provider_price IS NULL THEN ROUND((h.warehouse_price * SUM(i.quantity)),2)
                        ELSE ROUND((i.provider_price * SUM(i.quantity)),2)
                    END price '
                . '')
                 ->from('products_sales_history as h')
                 ->join('provider_order_items as i','i.order_item_id = h.id','inner')
                 ->where('i.provider_order_id',(int)$id)
                 ->group_by('h.sku')
                 ->order_by('quantity','desc')
                 ->get();
        
        if($query->num_rows() === 0)
        {
            return FALSE;
        }
        
        return $query->result();
    }
    
    public function send_order ($id)
    {
        $query = $this->db->select('provider_id')
                ->from('provider_orders')
                ->where('id', $id)
                ->get();
        
        $provider_id = $query->row()->provider_id;
        
        $provider = $this->getProvider((int)$provider_id);
        
        $this->load->library('email');
        
        $config['validate'] = TRUE;
        $this->email->initialize($config);
        
        $this->email->from('info@buyin.es', 'BuyIn Compras');
        $this->email->reply_to('info@buyin.es');
        $this->email->to($provider->emails_list); 
        $this->email->cc($provider->cc_emails_list); 
        $this->email->subject($provider->email_subject);
        $this->email->message($provider->email_content);
        
        $file = $this->export_csv_model->download_provider_order($id);
        
        $this->email->attach(FCPATH .'upload/'.$file->name);
        
        if(!$this->email->send())
        {
            log_message('ERROR', $this->email->print_debugger());
        }
        else
        {
            $this->mark_order_as_sent($id);
        }
        
        return TRUE;
    }
    
    private function mark_order_as_sent($id)
    {
        $this->db->where('id',$id);
        $this->db->update('provider_orders', array('sent_to_provider' => 1, 'sending_date'=>date('Y-m-d H:i:s')));
    }
    
    public function process_error_products($data)
    {
        if(empty($data['products']))
        {
            return FALSE;
        }
        
        $this->session->unset_userdata('provider_order_error');
        
        $products = array_unique($data['products']);
        
        $result = array();
        
        foreach ($products as $key => $product) 
        {            
            if(preg_match('/\d+$/', $product, $product_id) !== 1)
            {
                continue;
            }
            
            $status = '';
            
            $quantity_need = $this->get_product_quantity_from_order_by_product_id($data['provider_order_id'], $product_id[0]);
            
            
            
            $result[$key]['product_name'] = $product;
            $result[$key]['product_available_quantity'] = $data['available_quantity'][$key];
            $result[$key]['reasons'] = $data['reasons'][$key];
            $result[$key]['product_quantity_needed'] = $quantity_need;
            
            
            
            if($result[$key]['product_available_quantity'] >= $result[$key]['product_quantity_needed'])
            {
                $status='<span class="green">Provider ('.$data['provider_name'].') has enough products for this order. No actions need.</span>';
            }
            elseif($result[$key]['product_available_quantity'] < $result[$key]['product_quantity_needed']
                    && $quantity_need!==0)
            {
                $alternative_offers = $this->get_alternative_offers($product_id[0],$data['provider_name']);
                
                if(empty($alternative_offers))
                {
                    $status='<span class="error">Product not available in other sources</span>';
                }
                $total_alternative_offers_count=0;
                if(isset($alternative_offers['warehouse']))
                {
                    $status = 'Product found in Warehouse: <br>';
                    $i=1;
                    foreach ($alternative_offers['warehouse'] as $offer) 
                    {
                        $status .= $i++.'. #'.$offer->sku.' - ('.$offer->provider_name.') price: '.$offer->price.', in stock: '.$offer->stock.';<br>';
                        $total_alternative_offers_count+=$offer->stock;
                    }
                }
                
                if(isset($alternative_offers['providers']))
                {
                    $status = 'Product found in Other Providers stock: <br>';
                    $i=1;
                    foreach ($alternative_offers['providers'] as $offer) 
                    {
                        $status .= $i++.'. #'.$offer->sku.' - ('.$offer->provider_name.') price: '.$offer->price.', in stock: '.$offer->stock.';<br>';
                        $total_alternative_offers_count+=$offer->stock;
                    }
                }
                if(!empty($alternative_offers))
                {
                    $status .= '<hr>';
                    $status .= 'Total alternative offers is '.$total_alternative_offers_count.'.';

                    if( $total_alternative_offers_count>=($result[$key]['product_quantity_needed'] - $result[$key]['product_available_quantity']) )
                    {
                        $status .= '<br><span class="green">Enough for replacement.</span>';
                    }
                    else 
                    {
                        $status .= '<br><span class="error">Not enough for replacement.</span>';
                    }
                }
            }
            
            if($quantity_need===0)
            {
                $status = 'Provider <a href="javascript:void(0);" onclick="Amazoni.get_provider_order('.$data['provider_order_id'].', \''.base64_url_encode(current_url()).'\');">order with ID '.$data['provider_order_id'].'</a>'
                        . ' have no this product. Check SKU of the product again.';
            }
            
            $result[$key]['status'] = $status;
            
            $this->log_provider_order_error($data, $quantity_need, $product_id[0], $data['available_quantity'][$key], $data['reasons'][$key], $status);
        }
        
        return $result;
    }
    
    private function get_product_quantity_from_order_by_product_id($provider_order_id, $product_id)
    {
        $query=$this->db->
                select('SUM(h.quantity) as quantity')->
                from('products_sales_history as h')->
                join('provider_order_items as i','h.id = i.order_item_id','inner')->
                where('h.provider_product_id',$product_id)->
                where('i.provider_order_id',$provider_order_id)->
                group_by('h.provider_product_id')->
                get();
        
        if($query->num_rows() === 0)
        {
            return 0;
        }
        
        return $query->row()->quantity;
    }
    
    private function get_alternative_offers($product_id, $exclude_provider)
    {
        $offers = array();
        
        $product = $this->products_model->get_product_by_id($product_id);
        // Check other providers
        $query=$this->db->
                select('sku, provider_name, ROUND(price,2) as price, stock')->
                from('providers_products')->
                where('stock >',0)->
                where('sku',$product->sku)->
                where('provider_name !=',$exclude_provider)->
                order_by('price')->
                get();
        
        if($query->num_rows() > 0)
        {
            $offers['providers'] = $query->result();
        }
        // Check warehouse
        
        $warehouse_products = $this->stokoni_model->find_product_by_ean($product->sku);
        
        if($warehouse_products)
        {
            $offers['warehouse'] = $warehouse_products;
        }
        
        return $offers;
    }
    
    private function log_provider_order_error($data, $quantity_need, $product_id, $available_quantity, $reason,$status)
    {
        $product = $this->products_model->get_product_by_id($product_id);
        
        $insert_data=array(
            'provider_order_id'=>$data['provider_order_id'],
            'product_id'=>$product_id,
            'product_sku'=>$product->sku,
            'product_name'=>$product->product_name,
            'provider_name'=>$product->provider_name,
            'quantity_needed'=>$quantity_need,
            'quantity_available'=>$available_quantity,
            'reason'=>$reason,
            'system_solution'=>empty($status)?NULL:$status,
            'created_on'=>date('Y-m-d H:i:s'),
            'created_by'=>(int)$this->ion_auth->get_user_id(),
        );
        
        $provider_order_error = $this->session->userdata('provider_order_error');
        
        $provider_order_error[] = $insert_data;
        
        $this->session->set_userdata('provider_order_error', $provider_order_error);
        
        $this->db->insert('provider_order_errors', $insert_data);
    }
    
    public function process_customer_orders_after_provider_error()
    {
        $provider_order_error = $this->session->userdata('provider_order_error');
        
        if (empty($provider_order_error))
        {
            log_message('INFO', 'Trying to process customer orders after provider error with empty errors array');
            return FALSE;
        }
        
        $result = array();
        $customer_orders = array();
        
        // Get customer orders items that will be modified
        foreach($provider_order_error as $row)
        {
            $query = $this->db->
                        select('h.id, h.order_status, h.order_id')->
                        from('products_sales_history as h')->
                        join('provider_order_items as i','i.order_item_id = h.id','inner')->
                        where('i.provider_order_id',$row['provider_order_id'])->
                        where('h.provider_product_id',$row['product_id'])->
                        where('h.canceled',0)->
                        order_by('h.id','asc')->
                        get();
            
            if($query->num_rows() === 0)
            {
                continue;
            }
            
            foreach ($query->result() as $item)
            {
                $update_data[] = array(
                    'csv_exported'=>0,
                    'csv_export_date'=>null,
                    'id'=>$item->id
                );
                
                $this->dashboard_model->set_status((int)$item->order_id,$item->order_status);
                
                $customer_orders[] = $item->order_id;
            }
            
            $this->db->where('id',$row['product_id']);
            $this->db->update('providers_products',array('stock'=>$row['quantity_available']));
        }
        
        // Update items
        if(!empty($update_data))
        {
            $this->db->update_batch('products_sales_history',$update_data,'id');
        }
                
        // Start verifying orders
        if(!empty($customer_orders))
        {
            foreach (array_unique($customer_orders) as $order_id) 
            {
                $result['statuses'][$order_id] = $this->dashboard_model->verify_order($order_id);

                $result['orders'][$order_id] = $this->dashboard_model->getOrder($order_id);
            }
        }
        
        return $result;
    }
}