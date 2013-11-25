<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of recurrent_model
 *
 * @author Alexander Begoon
 */
class Recurrent_model extends CI_Model {
    
    private $_recurrent_buyers = array();
    private $_total_number_of_buyers = 0;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function getRecurrentBuyers($page) {
        
        if (!empty($this->_recurrent_buyers)){
            return $this->_recurrent_buyers;  
        }
        
        $filter = $this->input->post("filter");
        $where  = '';
        $where .= ' `correo` IS NOT NULL AND `correo` != \'\'   ';
        
        if (!empty($filter['search'])) {
            $where .= ' AND ( `nombre` LIKE \'%'.$filter['search'].'%\' ';
            $where .= ' OR `correo` LIKE \'%'.$filter['search'].'%\' ) ';
        } 
        
        if ($page) {
            $limit = (int)$page.', 50';
        } else {
            $limit      = '0, 50';
        }
        
        $query = ' SELECT `nombre`, `correo`, COUNT(*) `total_number`, ROUND(SUM(`ingresos`),2) `total_amount` 
                    FROM `pedidos`  
                    WHERE '.$where.'
                    GROUP BY `correo` 
                    HAVING COUNT(`correo`) > 1 
                    ORDER BY `total_number` DESC 
                    LIMIT '.$limit.' 
        ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            $this->_recurrent_buyers = $result->result();
        }
        
        // Total rows count
        $query = ' SELECT `nombre`, `correo`, COUNT(*) `total_number` 
                    FROM `pedidos`  
                    WHERE '.$where.' 
                    GROUP BY `correo` 
                    HAVING COUNT(`correo`) > 1 
        ';
        
        $total = $this->db->query($query);
        
        if ($total) {
            $this->_total_number_of_buyers  = $total->num_rows();
        }
        
        return $this->_recurrent_buyers;
        
    }
    
    public function countBuyers() {
        
        return $this->_total_number_of_buyers;
        
    }
    
    public function getOrders() {
        
        $email = $this->input->post("email");
        
        $query = ' SELECT * FROM `pedidos` 
                   WHERE `correo` = \''.(string)$email.'\'    
        ';
        
        $query = $this->db->query($query);
        
//        print_r($query) ;die;
        
        if ($query) {
            return $query->result();
        }
        
        return false;
    }
}