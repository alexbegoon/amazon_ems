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
    
    public function getSummary() 
    {
        $this->update_other_costs();
        
        $start_date = $this->input->post("date_from") ? $this->input->post("date_from") : date('Y-m-01', time());
        $end_date   = $this->input->post("date_to") ? $this->input->post("date_to") : date('Y-m-d', time());     
        $this->db->cache_on();
        $where = ' WHERE ( `p`.`fechaentrada` >= \''.$start_date.'\' AND `p`.`fechaentrada` <= \''.$end_date.'\') ';
        $where .= ' AND ( `p`.`procesado` LIKE \'ENVIADO%\' ) ';
        
        $query = ' SELECT `p`.`web`, COUNT( *) AS `total_orders`, ROUND(SUM(`p`.`ingresos`),2) AS `ingresos`,
                          ROUND(SUM(`p`.`gasto`),2) AS `gasto`, 
                                CASE
                                    WHEN `p`.`web` = \'AMAZON-USA\' 
                                        THEN ROUND((SUM(`p`.`ingresos`) - SUM( `p`.`gasto` ) / (1 + ('.$this->_taxes['IVA_tax'].' / 100))),2)
                                    ELSE ROUND((SUM(`p`.`ingresos` - `p`.`gasto`) / (1 + ('.$this->_taxes['IVA_tax'].' / 100))),2)
                                END `net_profit`,
                                ROUND((SUM(`p`.`ingresos`) - SUM(`p`.`gasto`)),2) as `profit`,
                                ROUND(SUM(`p`.`ingresos`) * ('.$this->_taxes['IVA_tax'].' / 100) ,2) as taxes,
                                CASE
                                    WHEN `p`.`web` = \'AMAZON-USA\' 
                                        THEN ROUND(((SUM(`p`.`ingresos`) - SUM( `p`.`gasto` ) / (1 + ('.$this->_taxes['IVA_tax'].' / 100))))/SUM(`p`.`ingresos`) * 100 ,2)
                                    ELSE ROUND(((SUM(`p`.`ingresos` - `p`.`gasto`) / (1 + ('.$this->_taxes['IVA_tax'].' / 100))))/SUM(`p`.`ingresos`) * 100 ,2)
                                END as `percentage` 
                                
                   FROM `pedidos` AS `p` 
                   '.$where.' 
                   GROUP BY `p`.`web`  
        ';
        
        $result = $this->db->query($query);
        $this->db->cache_off();
        if ($result) {
            return $result->result();
        }
        
        return false;
        
    }
    
    public function getOrders($page) 
    {
        $start_date = $this->input->post("date_from") ? $this->input->post("date_from") : date('Y-m-01', time());
        $end_date   = $this->input->post("date_to") ? $this->input->post("date_to") : date('Y-m-d', time());        
        
        $where = ' WHERE ( `p`.`fechaentrada` >= \''.$start_date.'\' AND `p`.`fechaentrada` <= \''.$end_date.'\') ';
        $where .= ' AND ( `p`.`procesado` LIKE \'ENVIADO%\'  ) ';
        
        if ($page) {
            $limit = (int)$page.', 50';
        } else {
            $limit      = '0, 50';
        }
        
        $order_by = 'ORDER BY `p`.`fechaentrada` DESC ';
        $this->db->cache_on();        
        $query = ' SELECT `p`.`id`, `p`.`pedido`, `p`.`procesado`, `p`.`fechaentrada`, 
                          `p`.`web`, `p`.`ingresos`, ROUND(`p`.`gasto`, 2) as `gasto`, 
                          CASE
                                WHEN `p`.`web` = \'AMAZON-USA\' 
                                    THEN ROUND((`p`.`ingresos` - (`p`.`gasto`) / (1 + ('.$this->_taxes['IVA_tax'].' / 100))),2)
                                ELSE ROUND( (`p`.`ingresos` - `p`.`gasto`) / (1 + ('.$this->_taxes['IVA_tax'].' / 100)),2)
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
        $this->db->cache_off();
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
        $this->_taxes['PAYPAL']     = 0;
        $this->_taxes['SAGEPAY']    = 0;
        $this->_taxes['TPV']        = 0;
        
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
        
        $query = $this->db
                ->select('percent, fixed_cost')
                ->from('taxes')
                ->where('name','PAYPAL')
                ->get();
        
        if($query->num_rows() == 1)
        {
            $this->_taxes['PAYPAL'] = $query->row();
        }
        $query = $this->db
                ->select('percent, fixed_cost')
                ->from('taxes')
                ->where('name','TPV')
                ->get();
        
        if($query->num_rows() == 1)
        {
            $this->_taxes['TPV'] = $query->row();
        }
        $query = $this->db
                ->select('percent, fixed_cost')
                ->from('taxes')
                ->where('name','SAGEPAY')
                ->get();
        
        if($query->num_rows() == 1)
        {
            $this->_taxes['SAGEPAY'] = $query->row();
        }
    }
    
    public function get_other_costs()
    {
        if(!empty($this->_other_costs))
        {
            return $this->_other_costs;
        }
        
        $query = $this->db
                ->select('name, code, ROUND(price,2) as price, '
                        .'description, sign, read_only')
                ->from('other_costs')
                ->get();
        
        if($query->num_rows() > 0)
        {
            $this->_other_costs = $query->result();
            return $this->_other_costs;
        }
        
        return false;
    }
    
    private function update_other_costs()
    {
        $data = $this->input->post();
        
        $query = $this->db
                ->select('code')
                ->from('other_costs')
                ->get();
        
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row) 
            {
                if(isset($data[$row->code]))
                {
                    $d['price'] = $data[$row->code];

                    $this->db->where('code', $row->code)
                            ->update('other_costs',$d);
                }
            }
        }
        
        return true;
    }
    
    public function get_payment_fees()
    {
        $data = array();
        
        $start_date = $this->input->post("date_from") ? $this->input->post("date_from") : date('Y-m-01', time());
        $end_date   = $this->input->post("date_to") ? $this->input->post("date_to") : date('Y-m-d', time());
        $this->db->cache_on();
        $dbprefix = $this->db->dbprefix;
        
        $this->db->select('ROUND(SUM(ingresos)  / 100 * '.$this->_taxes['PAYPAL']->percent.' + count(*) * '.$this->_taxes['PAYPAL']->fixed_cost.', 2) as paypal_total_fees');
        $this->db->set_dbprefix(null);
        $this->db->from('pedidos');
        $this->db->set_dbprefix($dbprefix);
        $this->db->where("formadepago COLLATE UTF8_GENERAL_CI LIKE '%paypal%'");
        $this->db->like("procesado","ENVIADO");
        $this->db->where('fechaentrada >=',$start_date);
        $this->db->where('fechaentrada <=',$end_date);
                
        $query = $this->db->get();
        
        if($query->num_rows() == 1)
        {
            $data['paypal_total_fees'] = $query->row()->paypal_total_fees;
        }
        
        $this->db->select('ROUND(SUM(ingresos)  / 100 * '.$this->_taxes['SAGEPAY']->percent.' + count(*) * '.$this->_taxes['SAGEPAY']->fixed_cost.', 2) as sagepay_total_fees');
        $this->db->set_dbprefix(null);
        $this->db->from('pedidos');
        $this->db->set_dbprefix($dbprefix);
        $this->db->where("formadepago COLLATE UTF8_GENERAL_CI LIKE '%sagepay%'");
        $this->db->like("procesado","ENVIADO");
        $this->db->where('fechaentrada >=',$start_date);
        $this->db->where('fechaentrada <=',$end_date);
                
        $query = $this->db->get();
        
        if($query->num_rows() == 1)
        {
            $data['sagepay_total_fees'] = $query->row()->sagepay_total_fees;
        }
        
        $this->db->select('ROUND(SUM(ingresos)  / 100 * '.$this->_taxes['TPV']->percent.' + count(*) * '.$this->_taxes['TPV']->fixed_cost.', 2) as tpv_total_fees');
        $this->db->set_dbprefix(null);
        $this->db->from('pedidos');
        $this->db->set_dbprefix($dbprefix);
        $this->db->where("formadepago COLLATE UTF8_GENERAL_CI NOT LIKE '%paypal%'");
        $this->db->where("formadepago COLLATE UTF8_GENERAL_CI NOT LIKE '%sagepay%'");
        $this->db->like("procesado","ENVIADO");
        $this->db->where('fechaentrada >=',$start_date);
        $this->db->where('fechaentrada <=',$end_date);
                
        $query = $this->db->get();
        
        if($query->num_rows() == 1)
        {
            $data['tpv_total_fees'] = $query->row()->tpv_total_fees;
        }
        $this->db->cache_off();
        return $data;
    }
    
}