<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Stokoni model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

class Stokoni_model extends CI_Model
{
        
    private $_products;
    
    private $_total_count_of_products = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * 
     * Return all products from Stokoni table
     * 
     * @param int $page Limit var
     * 
     */
    public function getProducts($page)
    {
        
        $filter = $this->input->post("filter");
        $post_data = $this->input->post();
        
        $where = '';
                
        if (!empty($filter['search'])) {
            $where .= ' WHERE ( `ean` LIKE \'%'.addslashes(trim($filter['search'])).'%\' ';
            $where .= ' OR `proveedor` LIKE \'%'.addslashes(trim($filter['search'])).'%\' ';
            $where .= ' OR `nombre` COLLATE UTF8_GENERAL_CI LIKE \'%'.addslashes(trim($filter['search'])).'%\' ) ';
            
        } 
        
        if (!empty($post_data['provider']))
        {
            if(empty($where))
            {
                $where = ' WHERE `proveedor` = \''.addslashes(trim($post_data['provider'])).'\'  ';
            }
            else 
            {
                $where .= ' AND `proveedor` = \''.addslashes(trim($post_data['provider'])).'\' ';
            }
        }
                        
        if ($page) {
            $limit = (int)$page.', 50';
        } else {
            $limit      = '0, 50';
        }
        
        $order_by   = ' `stock` DESC ';
        
        $query = ' SELECT * FROM `stokoni` 
                   '.$where.' 
                   ORDER BY '.$order_by.' 
                   LIMIT '.$limit.' 
        ';
                
        $result = $this->db->query($query);
        $this->_products = $result->result();
        
        $query = ' SELECT * FROM `stokoni` 
                   '.$where.' 
        ';
        
        $result = $this->db->query($query);
        $this->_total_count_of_products = $result->num_rows();
        
        
        return $this->_products;
        
    }
    
    public function countProducts()
    {
        
        return $this->_total_count_of_products;
        
    }
    
    public function getSummary()
    {
        
        $this->load->model('incomes/taxes_model');
        
        $IVA_tax = $this->taxes_model->getIVAtax();
        
        
        $query = ' SELECT `proveedor` as `provider`, 
                    SUM(`coste` * `stock` * (1 + '.$IVA_tax.'/100)) AS `sub_total_money`,
                    SUM(`coste` * `vendidas` * (1 + '.$IVA_tax.'/100)) AS `sub_total_sold` ,
                    SUM(`stock`) `sub_total_stock`, 
                    SUM(`vendidas`) AS `sub_total_vendidas`, 
                    (SELECT SUM(`coste` * `stock` * (1 + '.$IVA_tax.'/100))  
                    FROM `stokoni` 
                     WHERE `stock` > 0 ) AS `total_money`,
                    (SELECT SUM(`coste` * `vendidas` * (1 + '.$IVA_tax.'/100))  
                     FROM `stokoni` 
                     WHERE `vendidas` > 0 ) AS `total_sold` ,
                    (SELECT SUM(`stock`) 
                     FROM `stokoni` 
                     WHERE `stock` > 0 ) AS `total_stock`, 
                    (SELECT SUM(`vendidas`) 
                     FROM `stokoni` 
                     WHERE `vendidas` > 0 ) AS `total_vendidas`
                    FROM `stokoni` 
                    GROUP BY `proveedor` 
        ';        
                
        $result = $this->db->query($query);
        
        if($result)
        {
            return $result->result();
        }
        
        return false;
    }
    
    public function getProduct($id)
    {
        if(!empty($id))
        {
            $query = ' SELECT * FROM `stokoni` 
                       WHERE `id` = '.(int)$id.' 
            ';
            
            $result = $this->db->query($query);
        
            if($result)
            {
                return $result->row();
            }

            return false;
        
        }
        
        return false;
         
    }
    
    /**
     * Sell product from stokoni.
     * 
     * @param integer $id
     * @param integer $quantity
     * @return boolean true on success
     * 
     */
    public function sell_product($id, $quantity)
    {       
        if(is_integer($id) && is_integer($quantity) && $id > 0 && $quantity > 0)
        {
            $this->db->trans_begin();
            
            $product = $this->getProduct($id);
            
            if(!empty($product))
            {
                if($quantity <= $product->stock && $quantity > 0)
                {                    
                    try
                    {
                        $stock      = (int)$product->stock      - (int)$quantity;
                        $vendidas   = (int)$product->vendidas   + (int)$quantity;

                        $query = ' UPDATE `stokoni` 
                                   SET `stock` = '.$stock.',  
                                       `vendidas` = '.$vendidas.' 
                                   WHERE `id` = '.$product->id.' 
                        ';
                        
                        $this->db->query($query);
                                                
                        $this->note_product_to_temp($product, $quantity);
                                               
                        $this->db->trans_commit();
                        
                        return true;
                    }
                    catch (PDOException $e)
                    {
                        $this->db->trans_rollback();
                        return false;
                    }
                }
                
                $this->db->trans_rollback();
                
                return false;
                
            }
            
            $this->db->trans_rollback();
            
            return false;
            
        }
        
        return false;
    }
    
    /**
     * Looking for product by EAN in Stockoni
     * 
     * @param string $ean
     * @return mixed Product object or boolean false
     */
    public function find_product_by_ean($ean)
    {
        if(empty($ean))
        {
            return false;
        }
                
        $ean = str_replace('#', '', trim($ean));
        
        $query = ' SELECT * 
                   FROM `stokoni` 
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
     * 
     * Store product to temp table stock_temp.
     * This is need for report of sold products from warehouse
     * 
     * @param object $product Object of single product
     * @param integer $quantity Quantity of product
     * @return boolean true on success or false
     */
    private function note_product_to_temp($product, $quantity)
    {
        if(is_object($product) && $quantity > 0)
        {
            // Only we need ENGELSA products
            if($product->proveedor != 'ENGELSA')
            {
                return false;
            }
            
            $query = ' INSERT INTO `'.$this->db->dbprefix('stock_temp').'` 
                       (`ean`, `price`, `quantity`, `name`)
                       VALUES
                       (\''.$product->ean.'\', '.$product->coste.', 
                           '.$quantity.', \''.$product->nombre.'\') 
                       ON DUPLICATE KEY UPDATE `quantity` = `quantity` + '.$quantity.' 
            ';
            
            $result = $this->db->query($query);
            return $result;
            
        }
        
        return false;
    }
    
    /**
     * Get all products from stock temp table
     * 
     * @return mixed array of objects or boolean false on unsuccess
     * 
     */
    public function get_all_products_from_temp()
    {
         $query = ' SELECT *, 
                    (SELECT SUM(`price` * `quantity`) FROM `'.$this->db->dbprefix('stock_temp').'` ) AS `total_price`  
                    FROM `'.$this->db->dbprefix('stock_temp').'`  
                    ORDER BY `quantity` DESC     
         ';

         $result = $this->db->query($query);
         
         if($result->num_rows() > 0)
         {
             return $result->result();
         }
        
         return false;
    }
    
    public function clear_temp_stock()
    {
        $query = 'TRUNCATE `'.$this->db->dbprefix('stock_temp').'` ';
        return $this->db->query($query);
    }
    
    /**
     * Add product to the Stock (stokoni table).
     * @param array $product Array of product
     * @return boolean true on success or false
     */
    public function add_product($product)
    {
        if(empty($product))
        {
            return false;
        }
        
        $query = 'INSERT INTO `stokoni` SET ';
        
        $fields = array();
        
        foreach ($product as $field => $value)
        {
            if($field == 'provider')
            {
                $field = 'proveedor';
            }
            
            if(is_numeric($value) && $field != 'ean')
            {
                $fields[] = ' `'.$field.'` = '.trim($value).' ';
            }
            else 
            {
                $fields[] = ' `'.$field.'` = \''.trim(addslashes($value)).'\' ';
            }
        }
        
        $query .= implode(', ', $fields);
        
        $result = $this->db->simple_query($query);
        
        if ($result)
        {
            return true; 
        }
        
        return false;
    }
    
    /**
     * Save updates of product
     * @param array $post_data
     * @return boolean True on success or false
     */
    public function save($post_data)
    {
        if(empty($post_data) || empty($post_data['id']))
        {
            return FALSE;
        }       
        
        $query = ' UPDATE `stokoni` 
                   SET `nombre` = \''.addslashes($post_data['nombre']).'\', 
                       `ean` = \''.$post_data['ean'].'\', 
                       `coste` = \''.$post_data['coste'].'\', 
                       `stock` = \''.$post_data['stock'].'\', 
                       `proveedor` = \''.$post_data['provider'].'\', 
                       `fechaDeCompra` = \''.$post_data['fechaDeCompra'].'\', 
                       `vendidas` = \''.$post_data['vendidas'].'\' 
                   WHERE `id` = '.$post_data['id'].'           
        ';
        
        return $this->db->query($query);
    }
    
    public function upload_stock_to_amazon()
    {
        $this->load->library('amazon_mws');
        
        $product = new stdClass();
        
        $product->sku   = '#24021909237490';
        $product->stock = 34;
        
        $data = array($product);
        
        $this->amazon_mws->update_stock($data);
    }
}