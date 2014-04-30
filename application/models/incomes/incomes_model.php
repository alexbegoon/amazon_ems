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
        
        
        $this->load->helper('download');
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
                                        THEN ROUND((SUM(`p`.`ingresos`) - SUM( `p`.`gasto` ) / (1 + ('.$this->_taxes['IVA_tax'].' / 100)))/(1 + ('.$this->_taxes['IVA_tax'].' / 100)),2)
                                    ELSE ROUND((SUM(`p`.`ingresos` - `p`.`gasto`) / (1 + ('.$this->_taxes['IVA_tax'].' / 100))),2)
                                END `net_profit`,
                                CASE
                                    WHEN `p`.`web` = \'AMAZON-USA\' 
                                        THEN ROUND((SUM(`p`.`ingresos`) - SUM(`p`.`gasto`)/(1 + ('.$this->_taxes['IVA_tax'].' / 100))),2)
                                    ELSE ROUND((SUM(`p`.`ingresos`) - SUM(`p`.`gasto`)),2) 
                                END `profit`,
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
        
        $search = $this->input->post("search");
        
        if(!empty($search))
        {
            $where .= ' AND ( `p`.`pedido` COLLATE UTF8_GENERAL_CI LIKE \'%'.$search.'%\' '
                    . ' OR  `p`.`nombre` COLLATE UTF8_GENERAL_CI LIKE \'%'.$search.'%\' '
                    . ' OR  `p`.`direccion` COLLATE UTF8_GENERAL_CI LIKE \'%'.$search.'%\' '
                    . ' OR  `p`.`correo` LIKE \'%'.$search.'%\' '
                    . ' OR  `p`.`id` LIKE \'%'.$search.'%\' '
                    . ' OR  `p`.`web` LIKE \'%'.$search.'%\' '
                    . ' OR  `p`.`fechaentrada` LIKE \'%'.$search.'%\' '
                    . ' ) ';
        }
        
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
    
    public function get_excel_file($post_data)
    {
        $file = new stdClass();
        
        $this->load->library('excel');
        
        $start_date = $post_data["date_from"] ? $post_data["date_from"] : date('Y-m-01', time());
        $end_date   = $post_data["date_to"]   ? $post_data["date_to"]   : date('Y-m-d', time());
        
        $objPHPExcel = new PHPExcel();
        
        $objPHPExcel->getProperties()->setCreator("Amazoni4");
        $objPHPExcel->getProperties()->setLastModifiedBy("Amazoni4");
        $objPHPExcel->getProperties()->setTitle("Incomes report. Date from ".$start_date." to ".$end_date);
        $objPHPExcel->getProperties()->setSubject("Incomes report. Date from ".$start_date." to ".$end_date);
        $objPHPExcel->getProperties()->setDescription("Incomes report. Date from ".$start_date." to ".$end_date);
        
        $objPHPExcel->setActiveSheetIndex(0);
        
        $objPHPExcel->getActiveSheet()->setTitle("Incomes report");
        
        // Prepare data
                
        $header = array(
            'WEB',
            'Orders Shipped',
            'Ingreso',
            'Gasto',
            'Profit',
            'Taxes',
            'Net Profit',
            'Percentage'
        );
        
        $i = 0;
        foreach ($header as $cell)
        {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, 1, $cell);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($i, 1)->getFill()
            ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => 'ededed')
            ));
            $i++;
        }
        
        $data = $this->getSummary();
        
        $i = 2;
        foreach ($data as $row)
        {
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(0, $i, $row->web, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $i)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(1, $i, $row->total_orders, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(2, $i, $row->ingresos, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(3, $i, $row->gasto, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(4, $i, $row->profit, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(5, $i, $row->taxes, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(6, $i, $row->net_profit, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(7, $i, $row->percentage, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $i++;
        }
        
        // Write a file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        
        $filename = FCPATH .'upload/incomes_report__'.date('Y_m_d', time()).'xls';
        
        $objWriter->save($filename);
        
        $file->name = 'incomes_report__'.date('Y_m_d', time()).'.xls';
        $file->data = read_file($filename);
                
        return $file;
    }
    
}