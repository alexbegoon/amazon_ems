<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Incomes model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

class Incomes_model extends CI_Model {
        
    private $_total_count_of_orders = NULL, $_orders = NULL, $_taxes = array(), $_other_costs = null;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->getTaxes();
    }
    
    public function getSummary($month = NULL, $year = NULL) {
        
        $this->update_other_costs();
        
        if (empty($month)) {
            $month = date('m', time());
        }
        
        if (empty($year)) {
            $year = date('Y', time());
        }
        
        $start_date = $year.'-'.$month.'-1';
        $end_date   = date("Y-m-d", strtotime(date("Y-m-d", strtotime($start_date)) . " +1 month"));
                
        $where = ' WHERE ( `p`.`fechaentrada` >= \''.$start_date.'\' AND `p`.`fechaentrada` < \''.$end_date.'\') ';
        $where .= ' AND ( `p`.`procesado` = \'ENVIADO_GLS\' OR 
                          `p`.`procesado` = \'ENVIADO_GRUTINET\' OR 
                          `p`.`procesado` = \'ENVIADO_FEDEX\' OR 
                          `p`.`procesado` = \'ENVIADO_MEGASUR\' OR 
                          `p`.`procesado` = \'ENVIADO_MARABE\' ) ';
        
        $query = ' SELECT `p`.`web`, COUNT( *) AS `total_orders`, ROUND(SUM(`p`.`ingresos`),2) AS `ingresos`,
                          ROUND(SUM(`p`.`gasto`),2) AS `gasto`, 
                                CASE
                                    WHEN `p`.`web` = \'AMAZON\' 
                                        THEN ROUND((SUM(`p`.`ingresos` - `p`.`gasto`) / (1 + ('.$this->_taxes['IVA_tax'].' / 100)) - SUM(`p`.`ingresos`) * ('.$this->_taxes['Amazon_tax'].' / 100)),2)
                                    WHEN `p`.`web` = \'AMAZON-USA\' 
                                        THEN ROUND((SUM(`p`.`ingresos` - `p`.`gasto`) / (1 + ('.$this->_taxes['IVA_tax'].' / 100)) - SUM(`p`.`ingresos`) * ('.$this->_taxes['Amazon_tax'].' / 100)),2)
                                    ELSE ROUND((SUM(`p`.`ingresos`) - SUM(`p`.`gasto`)),2) 
                                END `profit`   
                   FROM `pedidos` AS `p` 
                   '.$where.' 
                   GROUP BY `p`.`web`  
        ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            return $result->result();
        }
        
        return false;
        
    }
    
    public function getOrders($page, $month = NULL, $year = NULL) {
        
        if (empty($month)) {
            $month = date('m', time());
        }
        
        if (empty($year)) {
            $year = date('Y', time());
        }
        
        $start_date = $year.'-'.$month.'-1';
        $end_date   = date("Y-m-d", strtotime(date("Y-m-d", strtotime($start_date)) . " +1 month"));
        
        $where = ' WHERE ( `p`.`fechaentrada` >= \''.$start_date.'\' AND `p`.`fechaentrada` < \''.$end_date.'\') ';
        $where .= ' AND ( `p`.`procesado` = \'ENVIADO_GLS\' OR 
                          `p`.`procesado` = \'ENVIADO_GRUTINET\' OR 
                          `p`.`procesado` = \'ENVIADO_FEDEX\' OR 
                          `p`.`procesado` = \'ENVIADO_MEGASUR\' OR 
                          `p`.`procesado` = \'ENVIADO_MARABE\' ) ';
        
        if ($page) {
            $limit = (int)$page.', 50';
        } else {
            $limit      = '0, 50';
        }
        
        $order_by = 'ORDER BY `p`.`fechaentrada` DESC ';
        
        $items = '';
        
        for ($i = 1; $i <= 1; $i++) {
            $items .= ' (SELECT `precio` 
                         FROM `'.$this->db->dbprefix('engelsa').'` AS `engelsa_'.$i.'` 
                         WHERE REPLACE(`p`.`sku'.$i.'`, "#", "") = `engelsa_'.$i.'`.`ean` ) 
                         AS `item_price_'.$i.'`,      
            ';
        }
        
        $query = ' SELECT `p`.`id`, `p`.`pedido`, `p`.`procesado`, `p`.`fechaentrada`, 
                          `p`.`web`, `p`.`ingresos`, ROUND(`p`.`gasto`, 2) as `gasto`, 
                          CASE
                                WHEN `p`.`web` = \'AMAZON\' 
                                    THEN ROUND(((`p`.`ingresos` - `p`.`gasto`) / (1 + ('.$this->_taxes['IVA_tax'].' / 100)) - (`p`.`ingresos`) * ('.$this->_taxes['Amazon_tax'].' / 100)),2)
                                WHEN `p`.`web` = \'AMAZON-USA\' 
                                    THEN ROUND(((`p`.`ingresos` - `p`.`gasto`) / (1 + ('.$this->_taxes['IVA_tax'].' / 100)) - (`p`.`ingresos`) * ('.$this->_taxes['Amazon_tax'].' / 100)),2)
                                ELSE ROUND((`p`.`ingresos` - `p`.`gasto`),2) 
                            END `profit`  
                   FROM `pedidos` AS `p` 
                   '.$where.' 
                   '.$order_by.'     
                   LIMIT '.$limit.' 
        ';
        
        $result = $this->db->query($query);
        if ($result) {
            $this->_orders = $result->result();
        }
        
        $query = ' SELECT `p`.`id` 
                   FROM `pedidos` AS `p` 
                   '.$where.' 
        ';
        
        $result = $this->db->query($query);
        
        if ($result) {
            $this->_total_count_of_orders = $result->num_rows();
        }
        
        return $this->_orders;
    }
    
    public function countOrders() {
        
        return $this->_total_count_of_orders;
        
    }
            
    private function getTaxes()
    {
        $this->_taxes['IVA_tax']    = 0;
        $this->_taxes['Amazon_tax'] = 0;
        
        $query = ' SELECT `percent` 
                   FROM `'.$this->db->dbprefix('taxes').'` 
                   WHERE `name` = \'IVA\' 
        ';
        
        $result = $this->db->query($query);
        
        if ($result->num_rows() == 1)
        {
            $this->_taxes['IVA_tax'] = $result->row()->percent;
        }
        
        $query = ' SELECT `percent` 
                   FROM `'.$this->db->dbprefix('taxes').'` 
                   WHERE `name` = \'Amazon\' 
        ';
        
        $result = $this->db->query($query);
        
        if ($result->num_rows() == 1)
        {
            $this->_taxes['Amazon_tax'] = $result->row()->percent;
        }
    }
    
    public function get_other_costs()
    {
        if(!empty($this->_other_costs))
        {
            return $this->_other_costs;
        }
        
        $query = ' SELECT 
                    (SELECT ROUND(`price`,2) 
                     FROM `'.$this->db->dbprefix('other_costs').'` 
                     WHERE `code` = \'advertisement\'  ) 
                    AS `advertisement_cost`, 
                    (SELECT ROUND(`price`,2) 
                     FROM `'.$this->db->dbprefix('other_costs').'` 
                     WHERE `code` = \'rappel\'  ) 
                    AS `sales_rappel` 
        ';
        
        $result = $this->db->query($query);
        
        if($result)
        {
            $this->_other_costs = $result->row();
            return $this->_other_costs;
        }
        
        return false;
    }
    
    private function update_other_costs()
    {
        if(is_numeric($this->input->post("advertisement_cost")))
        {
            $query = ' UPDATE `'.$this->db->dbprefix('other_costs').'` 
                        SET `price` = '.(float)$this->input->post("advertisement_cost").' 
                        WHERE `code` = \'advertisement\' 
             ';

             $this->db->query($query);
        }
        
        if(is_numeric($this->input->post("sales_rappel")))
        {
        $query = ' UPDATE `'.$this->db->dbprefix('other_costs').'` 
                   SET `price` = '.(float)$this->input->post("sales_rappel").' 
                   WHERE `code` = \'rappel\' 
        ';
        
        $this->db->query($query);
        }
        
        return true;
    }
    
}