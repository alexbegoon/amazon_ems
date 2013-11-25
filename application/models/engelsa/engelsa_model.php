<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Engelsa model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

class Engelsa_model extends CI_Model {
    
    private $_products, $_brand_options;
    
    private $_total_count_of_products = null;
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * 
     * Return all products from Engelsa table
     * 
     * @param int $page Limit var
     * 
     */
    public function getProducts($page){
        
        $filter = $this->input->post("filter");
        
        $where = '';
                
        if (!empty($filter['search'])) {
            $where .= ' WHERE ( `ean` LIKE \'%'.addslashes(trim($filter['search'])).'%\' ';
            $where .= ' OR `descripcion` LIKE \'%'.addslashes(trim($filter['search'])).'%\' ';
            $where .= ' OR `nombre_marca` LIKE \'%'.addslashes(trim($filter['search'])).'%\' ) ';
        } 
        
        if (!empty($filter['nombre_marca'])) {
            if (!empty($where)) {
                $where .= ' AND `nombre_marca` LIKE \'%'.trim($filter['nombre_marca']).'%\' ';
            } else {
                $where .= ' WHERE `nombre_marca` LIKE \'%'.trim($filter['nombre_marca']).'%\' ';
            }
        }
                
        if ($page) {
            $limit = (int)$page.', 50';
        } else {
            $limit      = '0, 50';
        }
        
        $order_by   = ' `stock` DESC ';
        
        $query = ' SELECT *, ROUND(`precio`,2) AS `precio` FROM `'.$this->db->dbprefix('engelsa').'` 
                   '.$where.' 
                   ORDER BY '.$order_by.' 
                   LIMIT '.$limit.' 
        ';
                
        $result = $this->db->query($query);
        $this->_products = $result->result();
        
        $query = ' SELECT * FROM `'.$this->db->dbprefix('engelsa').'` 
                   '.$where.' 
        ';
        
        $result = $this->db->query($query);
        $this->_total_count_of_products = $result->num_rows();
        
        
        return $this->_products;
        
    }
    
    public function countProducts(){
        
        return $this->_total_count_of_products;
        
    }
    
    public function getBrandOptions(){
        
        $filter = $this->input->post("filter");
        $html = '';
        
        $query = ' SELECT DISTINCT `nombre_marca` 
                   FROM `'.$this->db->dbprefix('engelsa').'` 
                   ORDER BY `nombre_marca` ASC 
        ';
        
        $result = $this->db->query($query);
        $this->_brand_options = $result->result();
        
        
        
        if (!empty($this->_brand_options)) {
            
            $html .= '<option value="" ></option>';
            foreach ($this->_brand_options as $opt) {
                
                if (stripslashes(trim($filter['nombre_marca'])) === trim(str_replace('"', "", $opt->nombre_marca))) {
                    $selected = ' selected ';
                } else {
                    $selected = ' ';
                }
                $html .= '<option value="'.addslashes(str_replace('"', "", $opt->nombre_marca)).'" '.$selected.'>'. str_replace('"', '', trim($opt->nombre_marca, '"')).'</option>';
            }
            
            return $html;
        } 
        
        return false;
    }
    
    /**
     * Get single product from Engelsa table
     * @param string $ean Ean of product
     * @return mixed object of product or boolean false on unsuccess
     * 
     */
    public function get_product($ean)
    {
        $ean = str_replace('#', '', $ean);
        
        $query = ' SELECT *, `precio` AS `price`, REPLACE(`descripcion`, \'"\', \'\') AS `name`, 
                           `nombre_marca` AS `brand` 
                   FROM `'.$this->db->dbprefix('engelsa').'` 
                   WHERE `ean` = \''.$ean.'\' 
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() == 1)
        {
            return $result->row(); 
        }
        
        return false;
        
    }
    
    /**
     * Get product price
     * @param string $ean EAN of product
     * @return mixed 0 int or float price
     */
    public function get_price($ean)
    {
        $product = $this->get_product($ean);
        
        if(isset($product->price))
        {
            return $product->price;
        }
        
        return 0;
    }
    
    /**
     * Sell product from Engelsa
     * @param string $ean
     * @param int $quantity
     * @return boolean false on unsuccess or true
     */
    public function sell_product($ean, $quantity)
    {
        if(!empty($ean) && !empty($quantity) && is_integer($quantity))
        {
            $this->db->trans_begin();
            $product = $this->get_product($ean);
            
            if($product)
            {
                try
                {
                    if($product->stock >= $quantity)
                    {
                        $query = ' UPDATE `'.$this->db->dbprefix('engelsa').'` 
                                   SET `stock` = `stock` - '.$quantity.' 
                                   WHERE `ean` = \''.$product->ean.'\' 
                        ';
                        
                        $this->db->query($query);
                        
                        $this->db->trans_commit();
                        
                        return true;
                    }
                    else
                    {
                        $this->db->trans_rollback();
                        return false;
                    }
                }
                catch (PDOException $e)
                {
                    $this->db->trans_rollback();
                    return false;
                }
            }
            else
            {
                $this->db->trans_rollback();
                return false;
            }
        }
        else
        {
            return false;
        }
    }
}