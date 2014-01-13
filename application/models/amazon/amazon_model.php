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
            'tax'
            
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
    
    
}