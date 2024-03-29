<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Exchange rates model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

class Exchange_rates_model extends CI_Model {
    
    private $_buffer_data;


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
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
            
            $query = ' INSERT INTO `'.$this->db->dbprefix('exchange_rates').'` 
                       SET '.implode(', ',$values).' 
            ';
            
            $result = $this->db->query($query);
            
        } else {
            return false;
        }
    }

    public function getRates()
    {
        $query = ' SELECT `currencies`.*, `rates`.* 
                   FROM `'.$this->db->dbprefix('exchange_rates').'` AS `rates` 
                   LEFT JOIN `'.$this->db->dbprefix('currencies').'` AS `currencies`
                   USING (`currency_id`)      
                   ORDER BY `rates`.`id` ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            return $result->result();
        }
        
        return false;
    }
    
    public function getRate($id)
    {
        $query = ' SELECT * 
                   FROM `'.$this->db->dbprefix('exchange_rates').'` 
                   WHERE `id` = '.(int)$id.' ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            return $result->row_object();
        }
        
        return false;
    }

    public function getCurrenciesList($selected = null)
    {
        
        $query = ' SELECT * 
                   FROM `'.$this->db->dbprefix('currencies').'`                 
        ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            $html = '';
            $currencies = $result->result();
            
            $html .= '<option value=""></option>';
            
            foreach($currencies as $currency) {
                if ((int)$selected === (int)$currency->currency_id) {
                    $attr = 'selected="selected"';
                } else {
                    $attr = '';
                }    
                $html .= sprintf('<option value="%u" %s>%s</option>', $currency->currency_id, $attr, htmlentities($currency->currency_symbol . ' - ' . $currency->currency_name . ' (' . $currency->currency_code_3 . ')') );                
            }
                return $html;
        }
        
        return false;
        
    }
    
    public function edit($id)
    {
        
        $post_data = $this->input->post();
        
        $values = array();
            
        foreach ($post_data as $k => $v) {
            if ($k !== 'task' && $k !== 'id') {
                if (is_numeric($v)) {
                        $values[] = $k . ' = '.''.$v.'';
                    } else {
                        $values[] = $k . ' = '.'\''.$v.'\'';
                    }
            }         
        }
        
        if (empty($values)) {
            return false;
        }
        
        $query = ' UPDATE `'.$this->db->dbprefix('exchange_rates').'` 
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
        
        $query = ' DELETE FROM `'.$this->db->dbprefix('exchange_rates').'` 
                   WHERE `id` =  '.(int)$id.' ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Return 3 char currency code
     * @param int $currency_id
     */
    public function get_currency_code($currency_id)
    {
        if(isset($this->_buffer_data['currency_codes'][$currency_id]))
        {
            return $this->_buffer_data['currency_codes'][$currency_id];
        }
        
        $this->db->where('currency_id',$currency_id);
        $query = $this->db->get('currencies');
        
        if($query->num_rows() == 1)
        {
            $this->_buffer_data['currency_codes'][$currency_id] = $query->row()->currency_code_3;
            
            return $this->_buffer_data['currency_codes'][$currency_id];
        }
        
        return FALSE;
        
    }
    
    /**
     * Return currency symbol using 3 chars currency code
     * @param string $currency_code 3 chars currency code
     */
    public function get_currency_symbol_by_code($currency_code)
    {
        if(isset($this->_buffer_data['currency_symbols'][$currency_code]))
        {
            return $this->_buffer_data['currency_symbols'][$currency_code];
        }
        
        if(!empty($currency_code) && strlen($currency_code) === 3)
        {
            $this->db->select('currency_symbol');
            $this->db->where('currency_code_3', $currency_code);
            $query = $this->db->get('currencies',1,0);
            
            if($query->num_rows() === 1)
            {
                $this->_buffer_data['currency_symbols'][$currency_code] = $query->row()->currency_symbol;
                
                return $this->_buffer_data['currency_symbols'][$currency_code];
            }
        }
        
        return null;
    }
}