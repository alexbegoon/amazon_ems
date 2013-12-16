<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Amazon model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Amazon_model extends CI_Model 
{
    private $_total_count;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
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
                
        $this->amazon_mws->check_feed_submission_result('gb',MERCHANT_ID);
        $this->amazon_mws->check_feed_submission_result('us',USA_MERCHANT_ID);
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
}