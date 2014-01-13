<?php
/**
 * Wrapper for Amazon MWS services
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Amazon_MWS
{
    private $_CI = null; // Instance of CodeIgniter


    public function __construct()
    {
        require_once 'MarketplaceWebService/config.php';
        require_once 'MarketplaceWebService/Interface.php';
        require_once 'MarketplaceWebService/Mock.php';
        
        
        
        // Instance of Codeigniter App
        $this->_CI =& get_instance();
        
        //Load
        $this->_CI->load->model('amazon/amazon_model');
    }
    
    /**
     * Return service URLs. (MWS endpoint URL) 
     * @param string $country_code
     */
    public function get_service_urls($country_code=null)
    {
        
        $service_urls = array(
            
                'de' => 'https://mws.amazonservices.de',
                'gb' => 'https://mws.amazonservices.co.uk',
                'es' => 'https://mws.amazonservices.es',
                'fr' => 'https://mws.amazonservices.fr',
                'it' => 'https://mws.amazonservices.it',
                'us' => 'https://mws.amazonservices.com',
                'jp' => 'https://mws.amazonservices.jp',
                'cn' => 'https://mws.amazonservices.com.cn',
                'ca' => 'https://mws.amazonservices.ca',
                'in' => 'https://mws.amazonservices.in'
            
        );
        
        if(empty($country_code))
        {
            return $service_urls;
        }
            
        if(array_key_exists($country_code, $service_urls))
        {
            return $service_urls[$country_code];
        }
        
        return FALSE;       
        
    }
    
    /**
     * Instantiate Implementation of MarketplaceWebService
     * @param string $country_code 2 char country code
     * @return \MarketplaceWebService_Client object
     */
    public function instance_of_client($country_code)
    {
        require_once 'MarketplaceWebService/Client.php';
        
        $config = array (
            'ServiceURL' => $this->get_service_urls($country_code),
            'ProxyHost' => null,
            'ProxyPort' => -1,
            'MaxErrorRetry' => 3,
          );
        
        if($country_code == 'us')
        {
            return new MarketplaceWebService_Client(
                USA_AWS_ACCESS_KEY_ID, 
                USA_AWS_SECRET_ACCESS_KEY, 
                $config,
                APPLICATION_NAME,
                APPLICATION_VERSION);
        }
        
        return new MarketplaceWebService_Client(
            AWS_ACCESS_KEY_ID, 
            AWS_SECRET_ACCESS_KEY, 
            $config,
            APPLICATION_NAME,
            APPLICATION_VERSION);

    }
    
    public function submit_feed_request($feed,$feed_type,$country_code,$merchant_id)
    {
        require_once 'MarketplaceWebService/Model/SubmitFeedRequest.php';
        
        sleep(2);
        $feedHandle = @fopen(APPPATH . 'logs/'.$feed_type.date('Y-m-d_h_m_s',time()).'.xml', 'w+');
        fwrite($feedHandle, $feed);
        rewind($feedHandle);

        $request = new MarketplaceWebService_Model_SubmitFeedRequest();
        $request->setMerchant($merchant_id);
        //$request->setMarketplaceIdList($marketplaceIdArray);
        $request->setFeedType($feed_type);
        $request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));
        rewind($feedHandle);
        $request->setPurgeAndReplace(false);
        $request->setFeedContent($feedHandle);

        rewind($feedHandle);
        
        $this->invoke_submit_feed($this->instance_of_client($country_code), $request);
        
        @fclose($feedHandle);
    }
    
    /**
  * Submit Feed Action Sample
  * Uploads a file for processing together with the necessary
  * metadata to process the file, such as which type of feed it is.
  * PurgeAndReplace if true means that your existing e.g. inventory is
  * wiped out and replace with the contents of this feed - use with
  * caution (the default is false).
  *   
  * @param MarketplaceWebService_Interface $service instance of MarketplaceWebService_Interface
  * @param mixed $request MarketplaceWebService_Model_SubmitFeed or array of parameters
  */
  private function invoke_submit_feed(MarketplaceWebService_Interface $service, $request) 
  {
      try {
              $response = $service->submitFeed($request);
              
                echo ("Service Response\n");
                echo ("=============================================================================\n");

                echo("        SubmitFeedResponse\n");
                if ($response->isSetSubmitFeedResult()) { 
                    echo("            SubmitFeedResult\n");
                    $submitFeedResult = $response->getSubmitFeedResult();
                    if ($submitFeedResult->isSetFeedSubmissionInfo()) { 
                        echo("                FeedSubmissionInfo\n");
                        $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
                        if ($feedSubmissionInfo->isSetFeedSubmissionId()) 
                        {
                            echo("                    FeedSubmissionId\n");
                            echo("                        " . $feedSubmissionInfo->getFeedSubmissionId() . "\n");
                        }
                        if ($feedSubmissionInfo->isSetFeedType()) 
                        {
                            echo("                    FeedType\n");
                            echo("                        " . $feedSubmissionInfo->getFeedType() . "\n");
                        }
                        if ($feedSubmissionInfo->isSetSubmittedDate()) 
                        {
                            echo("                    SubmittedDate\n");
                            echo("                        " . $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                        }
                        if ($feedSubmissionInfo->isSetFeedProcessingStatus()) 
                        {
                            echo("                    FeedProcessingStatus\n");
                            echo("                        " . $feedSubmissionInfo->getFeedProcessingStatus() . "\n");
                        }
                        if ($feedSubmissionInfo->isSetStartedProcessingDate()) 
                        {
                            echo("                    StartedProcessingDate\n");
                            echo("                        " . $feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT) . "\n");
                        }
                        if ($feedSubmissionInfo->isSetCompletedProcessingDate()) 
                        {
                            echo("                    CompletedProcessingDate\n");
                            echo("                        " . $feedSubmissionInfo->getCompletedProcessingDate()->format(DATE_FORMAT) . "\n");
                        }
                    } 
                } 
                if ($response->isSetResponseMetadata()) { 
                    echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId()) 
                    {
                        echo("                RequestId\n");
                        echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                } 

                echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
                
                $this->_CI->amazon_model->log_response(
                        $feedSubmissionInfo->getFeedSubmissionId(),
                        $feedSubmissionInfo->getFeedType(),
                        $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT),
                        $feedSubmissionInfo->getFeedProcessingStatus(),
                        $responseMetadata->getRequestId()
                        );
                
     } catch (MarketplaceWebService_Exception $ex) {
         echo("Caught Exception: " . $ex->getMessage() . "\n");
         echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         echo("Error Code: " . $ex->getErrorCode() . "\n");
         echo("Error Type: " . $ex->getErrorType() . "\n");
         echo("Request ID: " . $ex->getRequestId() . "\n");
         echo("XML: " . $ex->getXML() . "\n");
         echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
         
         $msg = "Caught Exception: " . $ex->getMessage() . "\n";
         $msg .= "Response Status Code: " . $ex->getStatusCode() . "\n";
         $msg .= "Error Code: " . $ex->getErrorCode() . "\n";
         $msg .= "Error Type: " . $ex->getErrorType() . "\n";
         $msg .= "Request ID: " . $ex->getRequestId() . "\n";
         $msg .= "XML: " . $ex->getXML() . "\n";
         $msg .= "ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n";
         
         log_message('error', $msg);
     }
 }
 
 /**
  * Update stock level on all Amazon
  * @param array $data
  * 
  */
 public function update_stock($data,$country_code,$merchant_id)
 {
     if(empty($data))
     {
         return FALSE; 
     }
     
     //prepare xml feed
     $xml = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
            <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
            <Header>
            <DocumentVersion>1.01</DocumentVersion>
            <MerchantIdentifier>$merchant_id</MerchantIdentifier>
            </Header>
            <MessageType>Inventory</MessageType>
            <PurgeAndReplace>false</PurgeAndReplace>
            
EOD;
     
     $i = 1;
     foreach ($data as $product)
     {
         //B003OWXKGM
           $xml .= <<<EOD
            <Message>
            <MessageID>$i</MessageID>
            <OperationType>Update</OperationType>
            <Inventory>
            <SKU>$product->ean</SKU>
            <Quantity>$product->stock</Quantity>
            </Inventory>
            </Message>
                   
EOD;
           $i++;
     }
     
     $xml .= <<<EOD
            </AmazonEnvelope>
EOD;
     
     $this->submit_feed_request($xml, '_POST_INVENTORY_AVAILABILITY_DATA_',$country_code,$merchant_id);
 }
 
 public function update_prices($data,$country_code,$merchant_id)
 {
     if(empty($data))
     {
         return FALSE; 
     }
     
     //prepare xml feed
     $xml = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
<Header>
<DocumentVersion>1.01</DocumentVersion>
<MerchantIdentifier>$merchant_id</MerchantIdentifier>
</Header>
<MessageType>Price</MessageType>
EOD;
     
     $i = 1;
     foreach ($data as $product)
     {
           $xml .= <<<EOD
<Message>
<MessageID>$i</MessageID>
<Price>
<SKU>$product->sku</SKU>
<StandardPrice currency="$product->currency">$product->price</StandardPrice>
</Price>
</Message>
EOD;
           $i++;
     }
     
     $xml .= <<<EOD
</AmazonEnvelope>
EOD;
     
     $this->submit_feed_request($xml, '_POST_PRODUCT_PRICING_DATA_',$country_code,$merchant_id);
 }

 public function check_feed_submission_result($country_code,$merchant_id)
 {
        require_once 'MarketplaceWebService/Model/GetFeedSubmissionListRequest.php';
        require_once 'MarketplaceWebService/Model/StatusList.php';
      
        $request = new MarketplaceWebService_Model_GetFeedSubmissionListRequest();
        $request->setMerchant($merchant_id);

        $statusList = new MarketplaceWebService_Model_StatusList();
        
        $available_statuses = array('_DONE_', '_SUBMITTED_', '_CANCELLED_', '_IN_PROGRESS_', '_IN_SAFETY_NET_', '_UNCONFIRMED_');
        
        foreach ($available_statuses as $status)
        {
            $request->setFeedProcessingStatusList($statusList->withStatus($status));
            $this->invokeGetFeedSubmissionList($this->instance_of_client($country_code), $request, $merchant_id);
        }
 }
 
  /**
  * Get Feed Submission List Action Sample
  * returns a list of feed submission identifiers and their associated metadata
  *   
  * @param MarketplaceWebService_Interface $service instance of MarketplaceWebService_Interface
  * @param mixed $request MarketplaceWebService_Model_GetFeedSubmissionList or array of parameters
  */
  public function invokeGetFeedSubmissionList(MarketplaceWebService_Interface $service, $request, $merchant_id) 
  {
      try {
              $response = $service->getFeedSubmissionList($request);
              
                echo ("Service Response\n");
                echo ("=============================================================================\n");
                if ($response->isSetResponseMetadata()) { 
                    echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId()) 
                    {
                        echo("                RequestId\n");
                        echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                } 
                echo("        GetFeedSubmissionListResponse\n");
                if ($response->isSetGetFeedSubmissionListResult()) { 
                    echo("            GetFeedSubmissionListResult\n");
                    $getFeedSubmissionListResult = $response->getGetFeedSubmissionListResult();
                    if ($getFeedSubmissionListResult->isSetNextToken()) 
                    {
                        echo("                NextToken\n");
                        echo("                    " . $getFeedSubmissionListResult->getNextToken() . "\n");
                    }
                    if ($getFeedSubmissionListResult->isSetHasNext()) 
                    {
                        echo("                HasNext\n");
                        echo("                    " . $getFeedSubmissionListResult->getHasNext() . "\n");
                    }
                    $feedSubmissionInfoList = $getFeedSubmissionListResult->getFeedSubmissionInfoList();
                    foreach ($feedSubmissionInfoList as $feedSubmissionInfo) {
                        echo("                FeedSubmissionInfo\n");
                        if ($feedSubmissionInfo->isSetFeedSubmissionId()) 
                        {
                            echo("                    FeedSubmissionId\n");
                            echo("                        " . $feedSubmissionInfo->getFeedSubmissionId() . "\n");
                        }
                        if ($feedSubmissionInfo->isSetFeedType()) 
                        {
                            echo("                    FeedType\n");
                            echo("                        " . $feedSubmissionInfo->getFeedType() . "\n");
                        }
                        if ($feedSubmissionInfo->isSetSubmittedDate()) 
                        {
                            echo("                    SubmittedDate\n");
                            echo("                        " . $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                        }
                        if ($feedSubmissionInfo->isSetFeedProcessingStatus()) 
                        {
                            echo("                    FeedProcessingStatus\n");
                            echo("                        " . $feedSubmissionInfo->getFeedProcessingStatus() . "\n");
                        }
                        if ($feedSubmissionInfo->isSetStartedProcessingDate()) 
                        {
                            echo("                    StartedProcessingDate\n");
                            echo("                        " . $feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT) . "\n");
                        }
                        if ($feedSubmissionInfo->isSetCompletedProcessingDate()) 
                        {
                            echo("                    CompletedProcessingDate\n");
                            echo("                        " . $feedSubmissionInfo->getCompletedProcessingDate()->format(DATE_FORMAT) . "\n");
                        }
                        
                        if (!$this->_CI->amazon_model->is_request_completed($feedSubmissionInfo->getFeedSubmissionId()))
                        {
                            $this->_CI->amazon_model->log_response(
                                        $feedSubmissionInfo->getFeedSubmissionId(),
                                        $feedSubmissionInfo->getFeedType(),
                                        $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT),
                                        $feedSubmissionInfo->getFeedProcessingStatus(),
                                        $responseMetadata->getRequestId(),
                                        @$feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT),
                                        @$feedSubmissionInfo->getCompletedProcessingDate()->format(DATE_FORMAT),
                                        $this->get_request_result($feedSubmissionInfo->getFeedSubmissionId(),$service,$merchant_id)
                                );
                        }
                        
                    }
                } 
                

                echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
     } catch (MarketplaceWebService_Exception $ex) {
         echo("Caught Exception: " . $ex->getMessage() . "\n");
         echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         echo("Error Code: " . $ex->getErrorCode() . "\n");
         echo("Error Type: " . $ex->getErrorType() . "\n");
         echo("Request ID: " . $ex->getRequestId() . "\n");
         echo("XML: " . $ex->getXML() . "\n");
         echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
         
         $msg = "Caught Exception: " . $ex->getMessage() . "\n";
         $msg .= "Response Status Code: " . $ex->getStatusCode() . "\n";
         $msg .= "Error Code: " . $ex->getErrorCode() . "\n";
         $msg .= "Error Type: " . $ex->getErrorType() . "\n";
         $msg .= "Request ID: " . $ex->getRequestId() . "\n";
         $msg .= "XML: " . $ex->getXML() . "\n";
         $msg .= "ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n";
         
         log_message('error', $msg);
     }
 }
  
 public function get_request_result($FeedSubmissionId, $service, $merchant_id)
 {
     require_once 'MarketplaceWebService/Model/GetFeedSubmissionResultRequest.php';
     
     $this->_CI->load->helper('file');
     
     sleep(2);
     
     $current_time  = date('Y-m-d_H-i-s',time());
     
     $request = new MarketplaceWebService_Model_GetFeedSubmissionResultRequest();
     $request->setMerchant($merchant_id);
     $request->setFeedSubmissionId($FeedSubmissionId);    
     $request->setFeedSubmissionResult(@fopen(APPPATH . 'logs/request_result_for_'.$FeedSubmissionId.'_'.$current_time.'.xml', 'w+'));
     $service->getFeedSubmissionResult($request);
          
     return read_file(APPPATH . 'logs/request_result_for_'.$FeedSubmissionId.'_'.$current_time. '.xml');
 }
 
 /**
  * Get Feed Submission Result Action Sample
  * retrieves the feed processing report
  *   
  * @param MarketplaceWebService_Interface $service instance of MarketplaceWebService_Interface
  * @param mixed $request MarketplaceWebService_Model_GetFeedSubmissionResult or array of parameters
  */
  public function invokeGetFeedSubmissionResult(MarketplaceWebService_Interface $service, $request) 
  {
      try {
              $response = $service->getFeedSubmissionResult($request);
              
                echo ("Service Response\n");
                echo ("=============================================================================\n");

                echo("        GetFeedSubmissionResultResponse\n");
                if ($response->isSetGetFeedSubmissionResultResult()) {
                  $getFeedSubmissionResultResult = $response->getGetFeedSubmissionResultResult(); 
                  echo ("            GetFeedSubmissionResult");
                  
                  if ($getFeedSubmissionResultResult->isSetContentMd5()) {
                    echo ("                ContentMd5");
                    echo ("                " . $getFeedSubmissionResultResult->getContentMd5() . "\n");
                  }
                }
                if ($response->isSetResponseMetadata()) { 
                    echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId()) 
                    {
                        echo("                RequestId\n");
                        echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                } 

                echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
     } catch (MarketplaceWebService_Exception $ex) {
         echo("Caught Exception: " . $ex->getMessage() . "\n");
         echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         echo("Error Code: " . $ex->getErrorCode() . "\n");
         echo("Error Type: " . $ex->getErrorType() . "\n");
         echo("Request ID: " . $ex->getRequestId() . "\n");
         echo("XML: " . $ex->getXML() . "\n");
         echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
     }
 }
}