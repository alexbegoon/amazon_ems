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
        $provider_id = $this->get_provider_id_by_name($provider_name);
        
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
        
        
        $query = $this->db->get('provider_orders',50,$page);
        
        return $query->result();
    }
    
    public function count_all_providers_orders()
    {
        $query = $this->db->select('COUNT(*) as total')
                ->from('provider_orders')
                ->get();
        
        return $query->row()->total;
    }
}