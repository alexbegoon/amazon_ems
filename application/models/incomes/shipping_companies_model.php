<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Shipping companies model
 *
 * @author      Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

class Shipping_companies_model extends CI_Model {
    
    private $_shipping_companies = array();

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Create a new company
     * 
     */
    public function add(){
        
        $post_data = $this->input->post();
        
        if (!empty($post_data)) {
            
            $values = array();
            
            $fields = array(
                'company_name',
                'company_code',
                'company_website',
                'company_description',
                'company_regexp'
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
            
            $query = ' INSERT INTO `'.$this->db->dbprefix('shipping_companies').'` 
                       SET '.implode(', ',$values).' 
            ';
            
            $result = $this->db->query($query);
            
        } else {
            return false;
        }
    }
    
    /**
     * Get all companies
     * 
     * 
     * 
     * @return mixed array objects of companies or boolean false
     * 
     * 
     */
    public function getCompanies(){
        
        $query = ' SELECT * FROM `'.$this->db->dbprefix('shipping_companies').'` 
                   ORDER BY `id` ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            return $result->result();
        }
        
        return false;
        
    }
    
    /**
     * Remove this company
     * 
     * @param int $id Id of company 
     * @return boolean true on success
     */
    public function remove($id){
        
        $query = ' DELETE FROM `'.$this->db->dbprefix('shipping_companies').'` 
                   WHERE `id` =  '.(int)$id.' ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            return true;
        }
        
        return false;
    }
    
    
    /**
     * 
     * Update this company 
     * 
     * @param int $id
     * @return boolean true on success
     */
    public function edit($id){
        
        $post_data = $this->input->post();
        
        $values = array();
            
        $fields = array(
                'company_name',
                'company_code',
                'company_website',
                'company_description',
                'company_regexp'
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
        
        if (empty($values)) {
            return false;
        }
        
        $query = ' UPDATE `'.$this->db->dbprefix('shipping_companies').'` 
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
     * Return single company
     * 
     * @param int $id
     * @return mixed boolean false on unsuccess or single object of company
     */
    public function getCompany($id){
        
        if(isset($this->_shipping_companies[$id]))
        {
            return $this->_shipping_companies[$id];
        }
        
        $query = ' SELECT * FROM `'.$this->db->dbprefix('shipping_companies').'` 
                   WHERE `id` = '.(int)$id.' ';
        
        $result = $this->db->query($query);
        
        if ($result) 
        {
            $this->_shipping_companies[$id] = $result->row_object();
            return $this->_shipping_companies[$id];
        }
        
        return false;
        
    }
    
    /**
     * Return single company by company code
     * @param string $code Company code
     * @return mixed company object or boolean false on unsuccess
     */
    public function get_company_by_code($code)
    {
        if(isset($this->_shipping_companies[$code]))
        {
            return $this->_shipping_companies[$code];
        }
        if(empty($code))
        {
            return false;
        }
        
        $query = ' SELECT * FROM `'.$this->db->dbprefix('shipping_companies').'` 
                   WHERE `company_code` = \''.$code.'\' ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            
            $this->_shipping_companies[$code] = $result->row_object();
            return $this->_shipping_companies[$code];
        }
        
        return false;
    }
    
    /**
     * Try to find company using key phrase
     * @param string $phrase
     * @return mixed Object or boolean false on unsuccess
     */
    public function find_company_by_key_phrase($phrase)
    {
        $companies = $this->getCompanies();
        
        $results = array();
        
        foreach ($companies as $company)
        {
            $regexp = $company->company_regexp;
            
            if(!empty($regexp))
            {
                $total_found = preg_match_all($regexp, $phrase);
                
                if($total_found > 0)
                {
                    $results[$company->id] = $total_found;
                }
            }
        }
        
        if(count($results) > 0)
        {
            $maxs = array_keys($results, max($results));
        
            if(isset($maxs[0]) && $maxs[0] > 0)
            {
                if(count(array_keys($results, $results[$maxs[0]])) > 1) return false;
                return $this->getCompany($maxs[0]);            
            }
        }
        
        return false;
    }
}