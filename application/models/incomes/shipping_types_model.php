<?php
/**
 * Description of shipping_types_model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Shipping_types_model extends CI_Model
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Return all shipping types.
     * 
     * @return mixed
     */
    public function get_shipping_types()
    {
        
        $query = ' SELECT * 
                   FROM `'.$this->db->dbprefix('shipping_types').'` 
                   ORDER BY `shipping_type_name` 
        ';
                
        $result = $this->db->query($query);
        
        if ($result)
        {
            return $result->result();
        }
        
        return FALSE;
    }
    
    /**
     * Return single shipping type by id.
     * @param int $id
     * @return mixed
     */
    public function get_shipping_type($id)
    {
        
        $query = ' SELECT * 
                   FROM `'.$this->db->dbprefix('shipping_types').'` 
                   WHERE `shipping_type_id` = '.(int)$id.' 
        ';
                
        $result = $this->db->query($query);
        
        if ($result->num_rows() === 1)
        {
            return $result->row();
        }
        
        return FALSE;
    }
    
    /**
     * Add the new shipping type
     */
    public function add()
    {
        $post_data = $this->input->post();
        
        if (!empty($post_data)) {
            
            $values = array();
            
            $fields = array(
                'shipping_type_name',
                'shipping_type_description',
                'shipping_type_keywords',
                'shipping_type_regexp'             
            );
            
            foreach ($post_data as $k => $v) {
                if (in_array($k, $fields)) {
                    if (is_numeric($v)) {
                        $values[] = '`' . $k . '` = '.$v.'';
                    } else {
                        $values[] = '`' . $k . '` = '.$this->db->escape(trim($v));
                    }
                    
                }         
            }
            
            $query = ' INSERT INTO `'.$this->db->dbprefix('shipping_types').'` 
                       SET '.implode(', ',$values).' 
            ';
            
            $this->db->query($query);
            
        } else {
            return false;
        }
    }
    
    public function edit($id)
    {
        $post_data = $this->input->post();
        
        if (!empty($post_data)) {
            
            $values = array();
            
            $fields = array(
                'shipping_type_name',
                'shipping_type_description',
                'shipping_type_keywords',
                'shipping_type_regexp'             
            );
            
            foreach ($post_data as $k => $v) {
                if (in_array($k, $fields)) {
                    if (is_numeric($v)) {
                        $values[] = '`' . $k . '` = '.$v.'';
                    } else {
                        $values[] = '`' . $k . '` = '.$this->db->escape(trim($v));
                    }
                    
                }         
            }
        
            if (empty($values))
            {
                return false;
            }

            $query = ' UPDATE `'.$this->db->dbprefix('shipping_types').'` 
                       SET '.implode(', ',$values).' 
                       WHERE `shipping_type_id` = '.(int)$id.' 
            ';

            $result = $this->db->query($query);

            if ($result)
            {
                return true;
            }

            return false;
        }
    }
    
    /**
     * Remove this type
     * @param int $id
     */
    public function remove($id)
    {
        
        $query = ' DELETE FROM `'.$this->db->dbprefix('shipping_types').'` 
                   WHERE `shipping_type_id` =  '.(int)$id.' ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            return true;
        }
        
        return false;
    }

        /**
     * Return radio inputs for all Shipping types
     * @param string $name
     * @return string
     */
    public function get_radio_inputs_shipping_types($name = 'shipping_type_id')
    {
        $html = '';
                
        $query = ' SELECT `shipping_type_name`, \''.$name.'\' as `name`, 
                            `shipping_type_id` as `value`, 
                           CONCAT(`shipping_type_id`, `shipping_type_name`) as `id`, 
                           `shipping_type_name` as `title` 
                   FROM `'.$this->db->dbprefix('shipping_types').'` 
                   ORDER BY `shipping_type_name` 
        ';
        
        $result = $this->db->query($query);
        
        if ($result)
        {
            $shipping_types = $result->result('array');
            
            foreach ($shipping_types as $shipping_type)
            {                
                $html .= form_radio($shipping_type, null, null, set_radio($shipping_type['name'], $shipping_type['value'])) . form_label($shipping_type['title'], $shipping_type['id']);    
            }
        }
        
        return $html;
    }
    
    /**
     * Return options list of all existing shipping types
     * @param int $selected
     * @param string $name
     * @param string $extra
     * @return string
     */
    public function get_shipping_types_list($selected = null, $name = 'shipping_type_id', $extra = 'id="shipping_types_list"')
    {
        $html = '';
                
        $query = ' SELECT `shipping_type_id`, `shipping_type_name`  
                   FROM `'.$this->db->dbprefix('shipping_types').'` 
                   ORDER BY `shipping_type_name`    
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows > 0)
        {
            $web_fields = $result->result();
            
            $options = array();
            
            $options[''] = '';
            
            foreach ($web_fields as $web_field)
            {
                $options[$web_field->shipping_type_id] = $web_field->shipping_type_name;
            }
            
            $html = form_dropdown($name, $options, $selected, $extra);
        }
        
        return $html;
    }
    
    /**
     * Try to find shipping type using key phrase
     * @param string $phrase
     * @return mixed Object or boolean false on unsuccess
     */
    public function find_type_by_key_phrase($phrase)
    {
        $types = $this->get_shipping_types();
        
        $results = array();
        
        foreach ($types as $type)
        {
            $regexp = $type->shipping_type_regexp;
            
            if(!empty($regexp))
            {
                $total_found = preg_match_all($regexp, $phrase);
                
                if($total_found > 0)
                {
                    $results[$type->shipping_type_id] = $total_found;
                }
            }
        }
        
        if(count($results) > 0)
        {
            $maxs = array_keys($results, max($results));
        
            if(isset($maxs[0]) && $maxs[0] > 0)
            {
                if(count(array_keys($results, $results[$maxs[0]])) > 1) return false;
                return $this->get_shipping_type($maxs[0]);            
            }
        }
        
        return false;
    }
}