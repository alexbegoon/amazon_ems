<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Description of grutinet_model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Grutinet_model extends CI_Model
{
    
    private $_products = array(), $_total_count_of_products = 0;


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Return all products from Grutinet
     * @param int $page Limit
     * @return array Array of products 
     */
    public function get_products($page)
    {
        $filter = $this->input->post("filter");
        
        $where = '';
                
        if (!empty($filter['search']))
        {
            $where .= ' WHERE ( `ean` LIKE \'%'.addslashes(trim($filter['search'])).'%\' ';
            $where .= ' OR `product_name` LIKE \'%'.addslashes(trim($filter['search'])).'%\' ';
            $where .= ' OR `brand_name` LIKE \'%'.addslashes(trim($filter['search'])).'%\' ) ';
        }
        
        if (!empty($filter['brand_name']))
        {
            if (!empty($where))
            {
                $where .= ' AND `brand_name` = \''.trim($filter['brand_name']).'\' ';
            }
            else
            {
                $where .= ' WHERE `brand_name` = \''.trim($filter['brand_name']).'\' ';
            }
        }
        
        if ($page)
        {
            $limit = (int)$page.', 50';
        }
        else
        {
            $limit      = '0, 50';
        }
        
        $order_by   = ' `stock` DESC ';
        
        $query = ' SELECT `ean`, `product_name`, `price`, `stock`,  
                          `brand_name` 
                   FROM `'.$this->db->dbprefix('grutinet').'` 
                   '.$where.' 
                   ORDER BY '.$order_by.' 
                   LIMIT '.$limit.' 
        ';
        
        $result = $this->db->query($query);
        
        $this->_products = $result->result();
        
        $query = ' SELECT COUNT(*) as `total_rows` FROM `'.$this->db->dbprefix('grutinet').'` 
                   '.$where.' 
        ';
        
        $result = $this->db->query($query);
        
        $this->_total_count_of_products = $result->row()->total_rows;
        
        return $this->_products;
    }
    
    public function count_products()
    {
        return $this->_total_count_of_products;
    }
    
    /**
     * Get selectbox of all brands detected in Grutinet
     * @return string Html of selectbox
     */
    public function get_brand_options_list()
    {
        $filter = $this->input->post("filter");
        
        $html = '';
        
        $query = ' SELECT DISTINCT `brand_name` 
                   FROM `'.$this->db->dbprefix('grutinet').'` 
                   ORDER BY `brand_name` ASC 
        ';
        
        $result = $this->db->query($query);
        
        $brand_options = null;
        
        if($result->num_rows() > 0)
        {
            $brand_options = $result->result();
        }
        
        $options = array();
        
        $options[''] = '';
        
        if(is_array($brand_options) && !empty($brand_options))
        {
            foreach ($brand_options as $option)
            {
                $options[$option->brand_name] = $option->brand_name;
            }
        }
        
        $html = form_dropdown('filter[brand_name]', $options, $filter['brand_name'], 'id = "brand_name_list"');
        
        return $html;
    }
}