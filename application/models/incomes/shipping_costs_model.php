<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Shipping costs model
 *
 * @author      Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

class Shipping_costs_model extends CI_Model {
    
    private $_costs = array(), $_total_rows = 0;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Create a new price
     * 
     */
    public function add(){
        
        $post_data = $this->input->post();
        
        if (!empty($post_data)) {
            
            $values = array();
            
            $fields = array(
                'id_shipping_company',
                'country_code',
                'price',
                'web',
                'shipping_type_id',
                'description',
                'regexp'                
            );
            
            foreach ($post_data as $k => $v) {
                if (in_array($k, $fields)) {
                    if (is_numeric($v)) {
                        $values[] = '`' . $k . '` = '.''.$v.'';
                    } else {
                        $values[] = '`' . $k . '` = '.$this->db->escape(trim($v));
                    }
                    
                }         
            }
            
            $query = ' INSERT INTO `'.$this->db->dbprefix('shipping_costs').'` 
                       SET '.implode(', ',$values).' 
            ';
            
            $result = $this->db->query($query);
            
        } else {
            return false;
        }
    }
    
    /**
     * Get all prices
     * 
     * 
     * 
     * @return mixed array objects of costs or boolean false
     * 
     * 
     */
    public function getCosts($page)
    {
        $post_data = $this->input->post();
        
        if ($page)
        {
            $limit = (int)$page.', 50';
        } 
        else
        {
            $limit      = '0, 50';
        }
        
        $where = array();
        
        if(!empty($post_data['search']))
        {
            $where[] = ' (
                        `price` LIKE \'%'.trim($post_data['search']).'%\' OR 
                        `costs`.`web` LIKE \'%'.trim($post_data['search']).'%\' OR 
                        `country`.`name` LIKE \'%'.trim($post_data['search']).'%\' OR 
                        `type`.`shipping_type_name` LIKE \'%'.trim($post_data['search']).'%\' OR 
                        `comp`.`company_name` LIKE \'%'.trim($post_data['search']).'%\'   
                         )
            ';
        }
        
        if(!empty($post_data['filter_country_code']))
        {
            $where[] = ' (
                        `costs`.`country_code` = '.$this->db->escape(trim($post_data['filter_country_code'])).' 
                         )
            ';
        }
        
        if(!empty($post_data['filter_id_shipping_company']))
        {
            $where[] = ' (
                        `comp`.`id` = '.$this->db->escape(trim($post_data['filter_id_shipping_company'])).' 
                         )
            ';
        }
        
        if(!empty($post_data['filter_web']))
        {
            $where[] = ' (
                        `costs`.`web` = '.$this->db->escape(trim($post_data['filter_web'])).' 
                         )
            ';
        }
        
        if(!empty($post_data['filter_shipping_type_id']))
        {
            $where[] = ' (
                        `type`.`shipping_type_id` = '.$this->db->escape(trim($post_data['filter_shipping_type_id'])).' 
                         )
            ';
        }
        
        $where = implode(' AND ', $where);
        
        if(empty($where))
        {
            $where = ' 1 ';
        }
        
        $query = ' SELECT `costs`.`id`, COUNT(`costs`.`id`) as `total_rows`,  
                          ROUND(`costs`.`price`, 2) AS `price`,
                          `comp`.`company_name`,
                          `comp`.`company_code`, `comp`.`company_website`, 
                          `costs`.`country_code`, `country`.`name`, `costs`.`timestamp`, 
                          `costs`.`web`, `type`.`shipping_type_name`,
                          `type`.`shipping_type_id`, 
                          `comp`.`id` as `company_id`
                   FROM `'.$this->db->dbprefix('shipping_costs').'` AS `costs` 
                   LEFT JOIN `'.$this->db->dbprefix('shipping_companies').'` AS `comp` 
                   ON `costs`.`id_shipping_company` = `comp`.`id` 
                   LEFT JOIN `'.$this->db->dbprefix('countries').'` AS `country` 
                   ON `costs`.`country_code` = `country`.`code` 
                   LEFT JOIN `'.$this->db->dbprefix('shipping_types').'` AS `type` 
                   USING(`shipping_type_id`)  
                   WHERE '.$where.' 
        ';
                
        $result = $this->db->query($query);
        
        if ($result) {
            $this->_total_rows = (int)$result->row()->total_rows;
        }
            
        $query = ' SELECT `costs`.`id`, ROUND(`costs`.`price`, 2) AS `price`, `comp`.`company_name`,
                          `comp`.`company_code`, `comp`.`company_website`, 
                          `costs`.`country_code`, `country`.`name`, `costs`.`timestamp`, 
                          `costs`.`web`, `type`.`shipping_type_name`,
                          `type`.`shipping_type_id`, 
                          `comp`.`id` as `company_id`
                   FROM `'.$this->db->dbprefix('shipping_costs').'` AS `costs` 
                   LEFT JOIN `'.$this->db->dbprefix('shipping_companies').'` AS `comp` 
                   ON `costs`.`id_shipping_company` = `comp`.`id` 
                   LEFT JOIN `'.$this->db->dbprefix('countries').'` AS `country` 
                   ON `costs`.`country_code` = `country`.`code` 
                   LEFT JOIN `'.$this->db->dbprefix('shipping_types').'` AS `type` 
                   USING(`shipping_type_id`)  
                   WHERE '.$where.' 
                   ORDER BY `country`.`name`, `id`  
                   LIMIT '.$limit.' 
                        
        ';
                
        $result = $this->db->query($query);
        
        if ($result) {
            return $result->result();
        }
        
        return false;
        
    }
    
    /**
     * Remove this price
     * 
     * @param int $id Id of price 
     * @return boolean true on success
     */
    public function remove($id){
        
        $query = ' DELETE FROM `'.$this->db->dbprefix('shipping_costs').'` 
                   WHERE `id` =  '.(int)$id.' ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            return true;
        }
        
        return false;
    }
    
    
    /**
     * 
     * Update this price 
     * 
     * @param int $id
     * @return boolean true on success
     */
    public function edit($id){
        
        $post_data = $this->input->post();
        
        $values = array();
        
        $fields = array(
                'id_shipping_company',
                'country_code',
                'price',
                'web',
                'shipping_type_id',
                'description',
                'regexp'                
            );
            
        foreach ($post_data as $k => $v) {
                if (in_array($k, $fields)) {
                    if (is_numeric($v)) {
                        $values[] = '`' . $k . '` = '.''.$v.'';
                    } else {
                        $values[] = '`' . $k . '` = '.$this->db->escape(trim($v));
                    }
                    
                }         
            }
        
        if (empty($values)) {
            return false;
        }
        
        $query = ' UPDATE `'.$this->db->dbprefix('shipping_costs').'` 
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
     * Return single price
     * 
     * @param int $id
     * @return mixed boolean false on unsuccess or single object of cost
     * @deprecated since version #193
     */
    public function getPrice($id){
        
        $query = ' SELECT *, ROUND(`price`, 2) AS `price` FROM `'.$this->db->dbprefix('shipping_costs').'` 
                   WHERE `id` = '.(int)$id.' ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            return $result->row_object();
        }
        
        return false;
        
    }
    
    /**
     * Return single price by country code
     * @param string $country_code Code of country. 2 chars.
     * @return mixed 0 or float price
     * @deprecated since version #193
     */
    public function get_price_by_code($country_code)
    {
        if(isset($this->_costs[$country_code]))
        {
            return $this->_costs[$country_code];
        }
        
        $query = ' SELECT `price` FROM `'.$this->db->dbprefix('shipping_costs').'` 
                   WHERE `country_code` = \''.$country_code.'\' ';
        
        $result = $this->db->query($query);
        
        if ($result->num_rows() == 1)
        {
            $this->_costs[$country_code] = $result->row_object()->price;
            return $this->_costs[$country_code];
        }
        
        return 0;
    }
    
    /**
     * Return name of country using country code (2 char)
     * @param string  $country_code Country code
     * @return mixed    Name of country or boolean false on unsuccess
     */
    public function get_country_name_by_code($country_code)
    {
        if(!empty($country_code) && is_string($country_code))
        {
            $query = ' SELECT `name` 
                       FROM `'.$this->db->dbprefix('countries').'` 
                       WHERE `code` = \''.$country_code.'\' 
            ';
            
            $result = $this->db->query($query);
            
            if($result->num_rows() == 1)
            {
                return $result->row()->name;
            }
        }
        
        return FALSE;
    }


    /**
     * Return option list of countries
     * 
     * 
     * @param string $selected
     * @return mixed boolean false on unsuccess or string of options list
     * 
     * 
     * 
     */
    function getCountries($selected = null)
    {
            $query = ' SELECT `name`, `code` 
                       FROM `'.$this->db->dbprefix('countries').'`                        
            ';
            
            $result = $this->db->query($query);
            
            if ($result) {
                $html = '';
                $countries = $result->result();
                
                $html .= '<option value=""></option>';
                
                foreach($countries as $country) {
                    if ((string)$selected === (string)$country->name || (string)$selected === (string)$country->code) {
                        $attr = 'selected="selected"';
                    } else {
                        $attr = '';
                    }    
                    $html .= '<option value="'.$country->code.'" '.$attr.'>'.htmlentities($country->name).' ('.$country->code.')</option>';
                }
                
                return $html;
                
            } else {
                return false;
            }
    }
    
    /**
     * Return options list of all existing shipping companies
     * @param int $selected Id of selected Shipping company
     * @return mixed boolean false on unsuccess or string of options list
     */
    function getShippingCompanies($selected = null)
    {
            $query = ' SELECT `company_name`, `id` 
                       FROM `'.$this->db->dbprefix('shipping_companies').'`                        
            ';
            
            $result = $this->db->query($query);
            
            if ($result) {
                $html = '';
                $companies = $result->result();
                
                $html .= '<option value=""></option>';
                
                foreach($companies as $company) {
                    if ((int)$selected === (int)$company->id) {
                        $attr = 'selected="selected"';
                    } else {
                        $attr = '';
                    }    
                    $html .= '<option value="'.$company->id.'" '.$attr.'>'.htmlentities($company->company_name).'</option>';
                }
                
                return $html;
                
            } else {
                return false;
            }
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
        
        $this->load->model('incomes/shipping_types_model');
        
        return $this->shipping_types_model->get_shipping_types_list($selected, $name, $extra);
        
    }
    
    /**
     * Return radio inputs for all Shipping types
     * @param string $name
     * @return string
     */
    public function get_radio_inputs_shipping_types($name = 'shipping_type_id')
    {
        
        $this->load->model('incomes/shipping_types_model');
        
        return $this->shipping_types_model->get_radio_inputs_shipping_types($name);
        
    }
    
    public function count_total()
    {
        return $this->_total_rows;
    }
    
    /**
     * Return the shipping price, using WEB, country code (2 char) and shipping phrase
     * @param string $web
     * @param string $country_code
     * @param string $shipping_phrase
     * @return mixed Float or boolean false on unsuccess
     */
    public function get_shipping_price($web, $country_code, $shipping_phrase = null)
    {
        
        if(empty($web) || empty($country_code))
        {
            return FALSE;
        }
        
        $query = ' SELECT `price` 
                   FROM `'.$this->db->dbprefix('shipping_costs').'` 
                   WHERE `web` = \''.$web.'\' 
                   AND   `country_code` = \''.$country_code.'\' 
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() === 1)
        {
            return $result->row()->price;
        }
        
        // Have no shipping cost for such web and country   
        if($result->num_rows() === 0)
        {
            $msg = 'Shipping price not found for web: '.$web;
            $msg .= ' and country code: '.$country_code;
            log_message('info', $msg);
            
            return false;
        }
        
        if(empty($shipping_phrase))
        {
            $msg = 'Shipping price not found. Shipping phrase is empty';
            $msg .= ' ; Country code: '.$country_code;
            $msg .= ' ; Web: '.$web;
            log_message('info', $msg);
            
            return false;
        }
        
        $shipping_company = $this->get_shipping_company($shipping_phrase);
        
        if(empty($shipping_company))
        {
            $msg = 'Shipping price not found. Shipping phrase: '.$shipping_phrase;
            $msg .= ' ; Country code: '.$country_code;
            $msg .= ' ; Web: '.$web;
            log_message('info', $msg);
            
            return false;
        }
        
        $query = ' SELECT `price` 
                   FROM `'.$this->db->dbprefix('shipping_costs').'` 
                   WHERE `web` = \''.$web.'\' 
                   AND   `country_code` = \''.$country_code.'\' 
                   AND   `id_shipping_company` = '.$shipping_company->id.'  
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() === 1)
        {
            return $result->row()->price;
        }
        
        if($result->num_rows() === 0)
        {
            $msg = 'Shipping price not found. Shipping phrase: '.$shipping_phrase;
            $msg .= ' ; Country code: '.$country_code;
            $msg .= ' ; Web: '.$web;
            log_message('info', $msg);
            
            return false;
        }
        
        $shipping_type = $this->get_shipping_type($shipping_phrase);
        
        if(empty($shipping_type))
        {
            $msg = 'Shipping price not found. Can not find shipping type. Shipping phrase: '.$shipping_phrase;
            $msg .= ' ; Country code: '.$country_code;
            $msg .= ' ; Web: '.$web;
            log_message('info', $msg);
            
            return false;
        }
        
        $query = ' SELECT `price` 
                   FROM `'.$this->db->dbprefix('shipping_costs').'` 
                   WHERE `web` = \''.$web.'\' 
                   AND   `country_code` = \''.$country_code.'\' 
                   AND   `id_shipping_company` = '.$shipping_company->id.' 
                   AND   `shipping_type_id` = '.$shipping_type->shipping_type_id.' 
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() === 1)
        {
            return $result->row()->price;
        }
        
        if($result->num_rows() === 0)
        {
            $msg = 'Shipping price not found. Can not find shipping type. Shipping phrase: '.$shipping_phrase;
            $msg .= ' ; Country code: '.$country_code;
            $msg .= ' ; Web: '.$web;
            log_message('info', $msg);
            
            return false;
        }
        
        $msg = 'Shipping price not found. Shipping phrase: '.$shipping_phrase;
        $msg .= ' ; Country code: '.$country_code;
        $msg .= ' ; Web: '.$web;
        log_message('info', $msg);
        
        return false;
        
    }
    
    public function get_shipping_price_by_order_id($order_id)
    {
        $query = $this->db->select('shipping_price')
                ->from('products_sales_history')
                ->where('order_id',$order_id)
                ->limit(1)
                ->order_by('id','desc')
                ->get();
        
        if($query->num_rows() === 1)
        {
            return (float)$query->row()->shipping_price;
        }
        
        return false;
    }
    
    /**
     * Get shipping company using key phrase
     * @return mixed Company object or boolean false on unsuccess
     */
    private function get_shipping_company($shipping_phrase)
    {
        
        $this->load->model('incomes/shipping_companies_model');
        
        return $this->shipping_companies_model->find_company_by_key_phrase($shipping_phrase);
        
    }
    
    /**
     * Get shipping type using key phrase
     * @param mixed Type object or boolean false on unsuccess
     */
    private function get_shipping_type($shipping_phrase)
    {
        
        $this->load->model('incomes/shipping_types_model');
        
        return $this->shipping_types_model->find_type_by_key_phrase($shipping_phrase);
        
    }
}