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
}