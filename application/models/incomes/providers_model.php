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
        
        $this->load->helper('file');
        $this->load->library('excel');
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
        
        // Assign extra items
        $this->db->where('provider_id',$provider_id);
        $this->db->where('date_needed',date('Y-m-d'));
        $this->db->where('provider_order_id IS NULL', null, false);
        $this->db->update('provider_order_extra_items',array(
            'provider_order_id'=>$provider_order_id,
            'modified_by'=>(int)$this->ion_auth->get_user_id(),
            'modified_on'=>date('Y-m-d H:i:s'),
            ));
                
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
    
    public function get_csv_format($provider_id)
    {
        $provider = $this->getProvider($provider_id);
        
        if($provider)
        {
            if($provider->csv_format)
                return explode(';',$provider->csv_format);
        }
        
        return array('{sku}','{quantity}');
    }
    
    public function get_xls_format($provider_id)
    {
        $provider = $this->getProvider($provider_id);
        
        if($provider)
        {
            if($provider->xls_format)
                return explode(';',$provider->xls_format);
        }
        
        return array('{sku}','{quantity}');
    }
    
    
    public function get_provider_id_by_order_id($provider_order_id)
    {
        $query = $this->db->select('provider_id')
                 ->from('provider_orders')
                 ->where('id',$provider_order_id)
                 ->get();
        
        if($query->num_rows() === 1)
        {
            return $query->row()->provider_id;
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
                . ', p.inner_id, p.inner_sku ')
                 ->from('products_sales_history as h')
                 ->join('provider_order_items as i','i.order_item_id = h.id','inner')
                 ->join('providers_products as p','h.provider_product_id = p.id','left')
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
    
    public function get_provider_order_extra_items($order_id)
    {
        $query = $this->db->select('p.*, SUM(ex.quantity) as quantity, ROUND((p.price * SUM(ex.quantity)),2)  as price')->
                        where('provider_order_id',$order_id)->
                        from('provider_order_extra_items as ex')->
                        join('providers_products as p','ex.product_id = p.id','left')->
                        group_by('p.sku')->
                        order_by('quantity','DESC')->
                        get();
        
        if($query->num_rows() > 0)
        {
            return $query->result();
        }
                            
        return  FALSE;
    }

    public function send_order ($id)
    {        
        $provider_id = $this->get_provider_id_by_order_id($id);
        
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
        
        if($provider->send_xls==1)
        {
            $file = $this->export_csv_model->download_provider_order($id);
            $this->email->attach(FCPATH .'upload/'.$file->name);
        }
        
        if($provider->send_csv==1)
        {
            $file_csv = $this->export_csv_model->download_provider_order_csv($id);
            $this->email->attach(FCPATH .'upload/'.$file_csv->name);
        }
        
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
    
    public function compare_new_provider_with_exist($data)
    {
        $file_path = $data['upload_data']['full_path'];
        
        $inputFileType = PHPExcel_IOFactory::identify($file_path);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setReadDataOnly(true);

        /**  Load $inputFileName to a PHPExcel Object  **/  
        $objPHPExcel = $objReader->load($file_path);
        
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        
        $products = array();
        
        foreach ($sheetData as $row)
        {
            if(!isset($row['A']))
            {
                continue;
            }
            
            if(empty($row['A']))
            {
                continue;
            }
            
            if(preg_match('/^\d+$/', $row['A']) !== 1)
            {
                continue;
            }
            
            $sku = '';
            
            if(preg_match("/\d{3,12}/", $row['A']) === 1)
            {
                $sku = str_pad($row['A'], 13, '0', STR_PAD_LEFT);
            }
            
            if(preg_match("/\d{13}/", $sku) !== 1)
            {
                continue;
            }
            
            if(!isset($row['C']))
            {
                continue;
            }
            
            if(empty($row['C']))
            {
                continue;
            }
            
            $products['eans'][] = $sku;
            
            $products['list'][$sku] = array(
                'sku'=>$sku,
                'ean'=>$sku,
                'product_name'=>$row['B']?$row['B']:'No_name__',
                'price'=>$row['C'],
                'stock'=>$row['D']?$row['D']:0,
                'provider_name'=>'__NEW_PROVIDER__',
            );
        }
        
        if(!isset($products['eans']))
        {
            log_message('INFO', 'In uploaded file for provider comparsion, products not exist, or incorrect file format');
            return FALSE;
        }
        
        if(empty($products['eans']))
        {
            log_message('INFO', 'In uploaded file for provider comparsion, products not exist, or incorrect file format');
            return FALSE;
        }
        
        $products['eans'] = array_unique($products['eans']);
        
        // Get data for 1 list. A list of products that this new provider has and the others Providers do not have.
        
        $list_1 = array();
        $our_analogs = array();
        $query = $this->db->select('sku')->
                            from('providers_products')->
                            where_in('sku',$products['eans'])->
                            get();
        
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            $our_analogs[] = $row->sku;
        }
        
        $unique_eans = array_diff($products['eans'], $our_analogs);
        
        foreach ($unique_eans as $ean)
        {
            $list_1[] = $products['list'][$ean];
        }
                
        
        // Get data for 2 list. A list of products that this new provider has and we have in Products but with stock = 0.
        
        $list_2 = array();
        
        $query = $this->db->select('sku')->
                            from('providers_products')->
                            where_in('sku',$products['eans'])->
                            group_by('sku')->
                            having('SUM(stock)',0)->
                            get();
        
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                $list_2[] = $products['list'][$row->sku];
            }
        }
        
        // Get data for 3 list. A list of products in which this provider has better price that the price that we have now with the other 3 providers that we have in Products. 
        // This list have to include: EAN, Product name, Our Price in Product, The price of new Provider and the difference.
        
        $list_3 = array();
        
        $this->db->select('sku, price, provider_name');
        $this->db->from('providers_products');        
        $this->db->where_in('sku',$products['eans']);
        $this->db->order_by('price','desc');
        $query = $this->db->get();
        $our_lowest_prices = array();
        if($query->num_rows() > 0)
        {
            $our_products = $query->result();
            
            foreach ($our_products as $row) 
            {
                $our_price = $row->price;
                
                if($row->provider_name == 'ENGELSA')
                {
                    $our_price = $row->price / 1.04;
                }
                
                if(isset($our_lowest_prices[$row->sku]))
                {
                    if($our_lowest_prices[$row->sku] > $our_price)
                    {
                        $our_lowest_prices[$row->sku] = $our_price;
                    }
                }
                else
                {
                    $our_lowest_prices[$row->sku]=$our_price;
                }
            }
            
            foreach ($our_products as $row)
            {
                if(!isset($our_lowest_prices[$row->sku]))
                {
                    continue;
                }
                
                if($products['list'][$row->sku]['price'] < $our_lowest_prices[$row->sku])
                {
                    $list_3[$row->sku] = $products['list'][$row->sku];
                    $list_3[$row->sku]['our_price'] = $our_lowest_prices[$row->sku];
                }
                
            }
        }
        
        $objPHPExcel = new PHPExcel();
        
        $objPHPExcel->getProperties()->setCreator("Amazoni4");
        $objPHPExcel->getProperties()->setLastModifiedBy("Amazoni4");
        $objPHPExcel->getProperties()->setTitle("Providers comparison. Date: ".date('r', time()));
        $objPHPExcel->getProperties()->setSubject("Providers comparison. Date: ".date('r', time()));
        $objPHPExcel->getProperties()->setDescription("Providers comparison. Date: ".date('r', time()));
        
        $objPHPExcel->setActiveSheetIndex(0);
        
        $objPHPExcel->getActiveSheet()->setTitle("Unique items");
        
        $header = array(
            
            'EAN',
            'Product name',
            'Product price',
            'In Stock',
            
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
        
        foreach ($list_1 as $row)
        {
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(0, $i, $row['sku'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $i)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(1, $i, $row['product_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(2, $i, $row['price'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(3, $i, $row['stock'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $i++;
        }
        $objPHPExcel->createSheet(1);
        $objPHPExcel->setActiveSheetIndex(1);
        
        $objPHPExcel->getActiveSheet()->setTitle("Zero stock alternative");
        
        $header = array(
            
            'EAN',
            'Product name',
            'Product price',
            'In Stock',
            
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
        
        foreach ($list_2 as $row)
        {
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(0, $i, $row['sku'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $i)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(1, $i, $row['product_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(2, $i, $row['price'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(3, $i, $row['stock'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $i++;
        }
        $objPHPExcel->createSheet(2);
        $objPHPExcel->setActiveSheetIndex(2);
        
        $objPHPExcel->getActiveSheet()->setTitle("Best offer");
        
        $header = array(
            
            'EAN',
            'Product name',
            'In Stock',
            'Our Lowest Product Price',
            'New Provider Product Price',
            'Difference',
            
            
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
        
        foreach ($list_3 as $row)
        {
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(0, $i, $row['sku'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $i)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(1, $i, $row['product_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(2, $i, $row['stock'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(3, $i, round($row['our_price'],2), PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(4, $i, round($row['price'],2), PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(5, $i, round($row['our_price']-$row['price'],2), PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $i++;
        }
        
        
        // Write a file
        $file = new stdClass();
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        
        $file->name = 'providers_comparsion_'.date('Y_m_d__h_i_s').'.xls';
        
        $file->path = FCPATH .'upload/'.$file->name;
        
        $objWriter->save($file->path);
        
        $file->data = read_file($file->path);
                
        return $file;
    }
    
    public function save_extra_items_order($data)
    {
        $insert_data = array();
        $products = array_unique($data['products']);
        
        foreach ($products as $key => $product) 
        {            
            if(preg_match('/\d+$/', $product, $product_id) !== 1)
            {
                continue;
            }
            
            $insert_data[] = array(
                'product_id' => $product_id[0],
                'provider_order_id' => $data['date_needed'][$key]<=date('Y-m-d')?$data['order_id']:NULL,
                'provider_id' => $this->get_provider_id_by_order_id($data['order_id']),
                'provider_name' => $this->get_provider_name_by_order_id($data['order_id']),
                'product_sku' => $this->products_model->get_product_by_id($product_id[0])->sku,
                'product_name' => $this->products_model->get_product_by_id($product_id[0])->product_name,
                'quantity' => $data['quantity_needed'][$key],
                'date_needed' => $data['date_needed'][$key],
                'reason' => $data['reasons'][$key],
                'created_by' => (int)$this->ion_auth->get_user_id(),
                'created_on' => date('Y-m-d H:i:s'),
                'modified_by' => (int)$this->ion_auth->get_user_id(),
                'modified_on' => date('Y-m-d H:i:s'),
            );
        }
        
        if(!empty($insert_data))
            $this->db->insert_batch('provider_order_extra_items',$insert_data);
        
        $this->session->set_flashdata('provider_order_extra_items', 'Products successfully added to provider order.');
    }
}