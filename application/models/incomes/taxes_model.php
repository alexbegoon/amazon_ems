<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Taxes model
 *
 * @author      Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

class Taxes_model extends CI_Model {
    
    private $_taxes = array();

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Create a new tax
     * 
     */
    public function add(){
        
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
            
            $query = ' INSERT INTO `'.$this->db->dbprefix('taxes').'` 
                       SET '.implode(', ',$values).' 
            ';
            
            $result = $this->db->query($query);
            
        } else {
            return false;
        }
    }
    
    /**
     * Get all taxes
     * 
     * 
     * 
     * @return mixed array objects of taxes or boolean false
     * 
     * 
     */
    public function getTaxes(){
        
        $query = ' SELECT `id`, `name`, ROUND(`percent`, 2) AS `percent`, 
                          ROUND(`fixed_cost`, 2) AS `fixed_cost` 
                   FROM `'.$this->db->dbprefix('taxes').'` 
                   ORDER BY `id` ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            return $result->result();
        }
        
        return false;
        
    }
    
    public function getIVAtax()
    {
        if(isset($this->_taxes['IVA']))
        {
            return $this->_taxes['IVA'];
        }
        
        $query = ' SELECT `percent` 
                   FROM `'.$this->db->dbprefix('taxes').'` 
                   WHERE `name` = \'IVA\' ';
        
        $result = $this->db->query($query);
        
        if ($result->num_rows() == 1) 
        {
            $this->_taxes['IVA'] = $result->row()->percent;
            return $this->_taxes['IVA'];
        }
        
        return false;
    }
    
    /**
     * Remove this tax
     * 
     * @param int $id Id of tax 
     * @return boolean true on success
     */
    public function remove($id){
        
        $query = ' DELETE FROM `'.$this->db->dbprefix('taxes').'` 
                   WHERE `id` =  '.(int)$id.' ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            return true;
        }
        
        return false;
    }
    
    
    /**
     * 
     * Update this tax 
     * 
     * @param int $id
     * @return boolean true on success
     */
    public function edit($id){
        
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
        
        $query = ' UPDATE `'.$this->db->dbprefix('taxes').'` 
                   SET '.implode(', ',$values).' 
                   WHERE `id` = '.(int)$id.' 
        ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Return single tax
     * 
     * @param int $id
     * @return mixed boolean false on unsuccess or single object of tax
     */
    public function getTax($id){
        
        $query = ' SELECT `id`, `name`, ROUND(`percent`, 2) AS `percent`, 
                          ROUND(`fixed_cost`, 2) AS `fixed_cost` 
                   FROM `'.$this->db->dbprefix('taxes').'` 
                   WHERE `id` = '.(int)$id.' ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            return $result->row_object();
        }
        
        return false;
        
    }
    
    /**
     * Get tax using name
     * 
     * @param string $name Tax name
     * @return mixed Object or boolean false
     */
    public function get_tax_by_name($name)
    {
        if(isset($this->_taxes['get_tax_by_name'][$name]))
        {
            return $this->_taxes['get_tax_by_name'][$name];
        }
        
        $query = ' SELECT `id`, `name`, `percent`, `fixed_cost` 
                   FROM `'.$this->db->dbprefix('taxes').'` 
                   WHERE `name` = \''.$name.'\' ';
        
        $result = $this->db->query($query);
        
        if ($result) 
        {   
            $this->_taxes['get_tax_by_name'][$name] = $result->row_object();
            return $this->_taxes['get_tax_by_name'][$name];
        }
        
        return false;
    }
    
}