<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Amazon model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Amazon_model extends CI_Model 
{
    private $_total_count, $_total_count_price_rules;
    
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        
        // Load models
        $this->load->model('incomes/providers_model');
    }
    
    /**
     * Write in the log table
     * @param type $FeedSubmissionId
     * @param type $FeedType
     * @param type $SubmittedDate
     * @param type $FeedProcessingStatus
     * @param type $RequestId
     * @return int Last ID
     */
    public function log_response($FeedSubmissionId=null,
                                 $FeedType=null,
                                 $SubmittedDate=null,
                                 $FeedProcessingStatus=null,
                                 $RequestId=null,
                                 $StartedProcessingDate=null,
                                 $CompletedProcessingDate=null,
                                 $RequestResult=null)
    {
        $log_row = new stdClass();
        
        $log_row->Feed_Submission_Id        = $FeedSubmissionId;
        $log_row->Feed_Type                 = $FeedType;
        $log_row->Submitted_Date            = $SubmittedDate;
        $log_row->Feed_Processing_Status    = $FeedProcessingStatus;
        $log_row->Request_Id                = $RequestId;
        $log_row->Started_Processing_Date   = $StartedProcessingDate;
        $log_row->Completed_Processing_Date = $CompletedProcessingDate;
        $log_row->Request_Result            = $RequestResult;
        
        $this->db->insert('amazon_requests_log', $log_row);
        
        return $this->db->insert_id();
    }
    
    public function update_log()
    {      
        $this->load->library('amazon_mws');
        $this->output->enable_profiler(TRUE);
        $this->amazon_mws->check_feed_submission_result('us',USA_MERCHANT_ID);
        $this->amazon_mws->check_feed_submission_result('gb',MERCHANT_ID);
    }
    
    public function get_all_info($page=0)
    {
        
        
        $this->db->order_by('id','desc');
        $query = $this->db->get('amazon_requests_log', 50, $page);
        
        return $query->result();
    }
    
    public function get_total_count()
    {
        return $this->db->count_all('amazon_requests_log');
    }
    
    public function is_request_completed($FeedSubmissionId)
    {
        $this->db->where('Feed_Submission_Id',$FeedSubmissionId);
        $this->db->where('Feed_Processing_Status','_DONE_');
        $query = $this->db->get('amazon_requests_log');
        
        if($query->num_rows() > 0)
        {
            if($query->row()->Completed_Processing_Date)
            {
                return TRUE;
            }
        }
        
        return FALSE;
    }
    
    public function save_rule($data)
    {
        if(empty($data))
        {
            return false;
        }
        
        $data['provider_id'] = $data['provider'];
        
        if(!empty($data['provider_id']))
        {
            $data['provider_name'] = $this->providers_model->getProvider((int)$data['provider_id'])->name;
        }
        
        $values = array();
        $fields = array(
            
            'provider_id',
            'provider_name',
            'web',
            'currency_id',
            'multiply',
            'sum',
            'transport',
            'marketplace',
            'tax',
            'ean'
            
        );
        
        foreach ($data as $k => $v) 
        {
            if (in_array($k, $fields)) 
            {
                $values[$k] = $v;
            }         
        }
        
        if(!empty($data['id']))
        {
            $this->db->where('id', $data['id']);
            $this->db->update('amazon_price_rules', $values); 
        }
        else 
        {
            $this->db->insert('amazon_price_rules', $values); 
        }
        
        return '1';
    }
    
    public function get_all_price_rules($page)
    {
        $this->db->select('*');
        $this->db->from('amazon_price_rules');
        $this->db->join('currencies', 'currencies.currency_id = amazon_price_rules.currency_id', 'left');
        $this->db->limit(50,$page);
        
        $result = $this->db->get();
        
        $this->_total_count_price_rules = $this->db->count_all('my_table');
        
        return $result->result();
    }
    
    public function get_total_count_of_rules()
    {
        return $this->_total_count_price_rules;
    }
    
    public function get_price_rule($id)
    {
        $this->db->select('*');
        $this->db->from('amazon_price_rules');
        $this->db->join('currencies', 'currencies.currency_id = amazon_price_rules.currency_id', 'left');
        $this->db->where('id =',$id);
        $query = $this->db->get();
        
        return $query->row();
    }
    
    public function delete_rule($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('amazon_price_rules');
        
        return '1';
    }
    
    public function get_sales_rank($page)
    {
        $this->load->library('amazon_mws');
        
//        $this->amazon_mws->request_report(MERCHANT_ID,'gb', '_GET_NEMO_MERCHANT_LISTINGS_DATA_');
        
//        $this->amazon_mws->check_status_of_reports(MERCHANT_ID,'gb');
//        $this->amazon_mws->get_report_ids(MERCHANT_ID,'gb', array('7950418516'));
//        $this->amazon_mws->get_report(MERCHANT_ID,'gb','32079204044');
//        $this->amazon_mws->get_report(MERCHANT_ID,'gb','7948303260');
    }
    
    public function sync_reports_with_amazon()
    {
        
        
    }
    
    public function parse_sellercentral_data($upload_data, $web)
    {
        $this->load->library('unzip');
        $this->load->library("html_dom");
        $this->load->helper('file');
        $this->load->model('products/products_model');
        
        error_reporting(E_ALL ^ E_NOTICE);
        
        $this->db->trans_begin();
        
        $data = array();
        
        if($upload_data['file_ext'] == '.zip')
        {
            // Optional: Only take out these files, anything else is ignored
            $this->unzip->allow(array('htm', 'html', 'txt', 'chm', 'HTM', 'HTML'));

            $extract_path = FCPATH.'upload/'.md5(time()).'/';
            
            // Create input folder
            mkdir($extract_path);
            
            // Extract files
            $this->unzip->extract($upload_data['full_path'], $extract_path);

            $files_tree = get_dir_file_info($extract_path, false);

            // Clear temp table
            $this->db->truncate('amazon_sales_rank_temp');

            foreach ($files_tree as $file)
            {
                $parsed_data = $this->parse_sellercentral_file($file['server_path'], $web);
                
                if($parsed_data)
                {
                    $this->store_sales_rank_to_temp($parsed_data);
                }
            }
        }
        else
        {
            $parsed_data = $this->parse_sellercentral_file($upload_data['full_path'], $web);
            
            // Clear temp table
            $this->db->truncate('amazon_sales_rank_temp');
                
            if($parsed_data)
            {
                $this->store_sales_rank_to_temp($parsed_data);
            }
        }
        
        $this->db->trans_commit();
        
        $this->db->order_by('sales_rank', 'DESC');
        return $this->db->get('amazon_sales_rank_temp')->result();
        
    }
    
    private function store_sales_rank_to_temp($d)
    {
        if($d)
        {
             return $this->db->insert_batch('amazon_sales_rank_temp', $d);
        }
    }
    
    public function store_sellercentral_data()
    {
        $response = null;
        
        $temp_data = $this->db->get('amazon_sales_rank_temp')->result('array');
        
        if($temp_data)
        {
            $data = array();
            
            $this->db->trans_begin();
            
            foreach($temp_data as $row)
            {
                // Unset IDs of temp table
                unset($row['id']);
                $data[] = $row;
                
                if(!empty($row['ean']))
                {
                    // Store data to providers products table
                    $providers_products_row = array();

                    switch($row['web'])
                    {
                        case 'AMAZON-CO-UK' :
                            $providers_products_row['sales_rank_uk'] = $row['sales_rank'];
                            break;
                        case 'AMAZON-DE' :
                            $providers_products_row['sales_rank_de'] = $row['sales_rank'];
                            break;
                    }

                    $providers_products_row['updated_on'] = date('Y-m-d H:i:s', time());

                    $this->db->where('sku', $row['ean']);
                    $this->db->update('providers_products', $providers_products_row); 
                }
            }
            
            $this->db->trans_commit();
            
            $this->db->trans_begin();
            $this->db->insert_batch('amazon_sales_rank', $data);
            $this->db->trans_commit();
            
            $response['affected_rows'] = $this->db->affected_rows();
            
            // Clear temp table
            $this->db->truncate('amazon_sales_rank_temp');
        }
        
        return $response;
    }

    private function parse_sellercentral_file($file_path, $web)
    {
        $data = array();
        
        $tr_wrapper = array();
        
        $this->html_dom->loadHTMLfile($file_path);
        
        $tr_wrapper[] =  $this->html_dom->find("tr#inventory-content td table", 3)->find('tbody tr');
        
        if($tr_wrapper)
        {
            foreach ($tr_wrapper as $trs)
            {
                foreach($trs as $cell)
                {

                    $ean = '';
                    $brand = '';
                    
                    if(!$cell->find('td', 5)->first_child())
                    {
                        continue;
                    }
                    $asin_isbn = trim($cell->find('td', 5)->first_child()->innertext);
                    $merchant_sku = trim($cell->find('td', 4)->innertext);

                    $datetime = date('Y-m-d H:i:s', time());

                    $product = $this->products_model->get_product(trim($cell->find('td', 4)->innertext),$web);

                    if($product)
                    {
                        $ean = $this->products_model->get_product(trim($cell->find('td', 4)->innertext),$web)[0]->sku;
                        $brand = $this->products_model->get_product(trim($cell->find('td', 4)->innertext),$web)[0]->brand;

                        if(empty($brand))
                        {
                            $brand = '';
                        }
                    }
                    
                    if(!empty($asin_isbn) && !empty($merchant_sku))
                    {
                        if($web == 'AMAZON-USA')
                        {
                            $data[] = array(

                                'ean' => (string)$ean,
                                'product_name' => (string)trim($cell->find('td', 6)->first_child()->innertext),
                                'brand_name' => (string)$brand,
                                'web' => (string)$web,
                                'sales_rank' => 0,
                                'created_on' => $datetime,
                                'updated_on' => $datetime,
                                'sales_rank_category_name' => '',
                                'merchant_sku' => (string)$merchant_sku,
                                'asin_isbn' => (string)$asin_isbn,
                                'status' => (string)trim($cell->find('td', 3)->first_child()->innertext),
                                'fee_preview' => (float)$this->get_numeric_innertext($cell->find('td', 9)->first_child()->innertext),
                                'fee_preview_currency_code' => (string)$this->get_currency_code($cell->find('td', 9)->first_child()->innertext),
                                'low_price' => (float)$this->get_numeric_innertext('>'.$cell->find('td', 12)->find('div a',0)->innertext.'<'),
                                'low_price_currency_code' => (string)$this->get_currency_code($cell->find('td', 12)->find('div a',0)->innertext),
                                'low_price_delivery' => (float)$this->get_numeric_innertext('>'.$cell->find('td', 12)->find('div div span',1)->innertext.'<'),
                                'low_price_delivery_currency_code' => (string)$this->get_currency_code($cell->find('td', 12)->find('div div span',1)->innertext),
                                'your_price' => (float)$this->get_value_attr($cell->find('td', 10)->first_child()->first_child()->innertext),
                                'your_price_currency_code' => (string)$this->get_currency_code($cell->find('td', 10)->first_child()->first_child()->innertext),
                                'your_price_delivery' => (float)$this->get_numeric_innertext($cell->find('td', 10)->first_child()->last_child()->innertext),
                                'your_price_delivery_currency_code' => (string)$this->get_currency_code($cell->find('td', 10)->first_child()->last_child()->innertext),
                                'product_condition' => (string)trim($cell->find('td', 11)->first_child()->innertext),
                                'products_in_stock' => (int)$this->get_value_attr($cell->find('td', 8)->first_child()->innertext)

                            );
                        }
                        else 
                        {
                            $data[] = array(

                                'ean' => (string)$ean,
                                'product_name' => (string)trim($cell->find('td', 6)->first_child()->innertext),
                                'brand_name' => (string)$brand,
                                'web' => (string)$web,
                                'sales_rank' => $this->get_first_numeric_data($cell->find('td', 12)->first_child()->innertext),
                                'created_on' => $datetime,
                                'updated_on' => $datetime,
                                'sales_rank_category_name' => (string)trim($cell->find('td', 12)->find('div div',0)->innertext),
                                'merchant_sku' => (string)$merchant_sku,
                                'asin_isbn' => (string)$asin_isbn,
                                'status' => (string)trim($cell->find('td', 3)->first_child()->innertext),
                                'fee_preview' => (float)$this->get_numeric_innertext($cell->find('td', 8)->first_child()->innertext),
                                'fee_preview_currency_code' => (string)$this->get_currency_code($cell->find('td', 8)->first_child()->innertext),
                                'low_price' => (float)$this->get_numeric_innertext('>'.$cell->find('td', 11)->find('div a',0)->innertext.'<'),
                                'low_price_currency_code' => (string)$this->get_currency_code($cell->find('td', 11)->find('div a',0)->innertext),
                                'low_price_delivery' => (float)$this->get_numeric_innertext('>'.$cell->find('td', 11)->find('div div span',1)->innertext.'<'),
                                'low_price_delivery_currency_code' => (string)$this->get_currency_code($cell->find('td', 11)->find('div div span',1)->innertext),
                                'your_price' => (float)$this->get_value_attr($cell->find('td', 9)->first_child()->first_child()->innertext),
                                'your_price_currency_code' => (string)$this->get_currency_code($cell->find('td', 9)->first_child()->first_child()->innertext),
                                'your_price_delivery' => (float)$this->get_numeric_innertext($cell->find('td', 9)->first_child()->last_child()->innertext),
                                'your_price_delivery_currency_code' => (string)$this->get_currency_code($cell->find('td', 9)->first_child()->last_child()->innertext),
                                'product_condition' => (string)trim($cell->find('td', 10)->first_child()->innertext),
                                'products_in_stock' => (int)$this->get_value_attr($cell->find('td', 7)->first_child()->innertext)

                            );
                        }
                    }
                }
            }
        }
        
        return $data;
    }
    
    private function get_value_attr($str)
    {
        if(!$str)
        {
            return '';
        }
        
        // Parse attribute value
        preg_match_all('/(value=".[^"]{1,}"|value=.[^"]{1,})/i', 
                        $str, 
                        $result);
        
        $matches = array();
        
        if($result[0][0])
        {
            if(preg_match('/".*"/i', $result[0][0], $matches))
            return str_replace(',', '.', trim(trim($matches[0], '"')));
        }
        
        return '';
    }
    
    private function get_numeric_innertext($str)
    {
        if(!$str)
        {
            return 0;
        }
        
        preg_match('/>[^<>]+</i', 
                        $str, 
                        $match);
        
        if($match)
        {
            if(preg_match('/\d+(?:[,|.]\d+)?/', 
                                $match[0], 
                                $result))
                {
                    return str_replace(',', '.',$result[0]);
                }
        }
        return 0;
    }
    
    private function get_first_numeric_data($str)
    {
        if(!$str)
        {
            return 0;
        }
        if(preg_match('/\d+(?:\,\d+)?/', 
                        $str, 
                        $result))
        {
            return (int)preg_replace('/,/', '', $result[0]);
        }
        
        return 0;
    }

        private function get_currency_code($str)
    {
        if(preg_match('/€|EUR/i', $str))
        {
            return 'EUR';
        }
        if(preg_match('/£|GBP/i', $str))
        {
            return 'GBP';
        }
        if(preg_match('/\$|USD/i', $str))
        {
            return 'USD';
        }
        
        return '';
    }
}