<?php
/**
 * Wrapper for Amazon MWS services
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Amazon_MWS
{
    public function __construct()
    {
        require_once 'MarketplaceWebService/config.php';
        require_once 'MarketplaceWebService/Interface.php';
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
        
        return new MarketplaceWebService_Client(
            AWS_ACCESS_KEY_ID, 
            AWS_SECRET_ACCESS_KEY, 
            $config,
            APPLICATION_NAME,
            APPLICATION_VERSION);

    }
    
    public function submit_feed_request($feed,$feed_type)
    {
        require_once 'MarketplaceWebService/Model/SubmitFeedRequest.php';
               
        var_dump($feed);
        
//        $marketplaceIdArray = array("Id" => array('A13V1IB3VIYZZH',
//                                                  'A1PA6795UKMFR9',
//                                                  'APJ6JRA9NG5V4',
//                                                  'A1RKKUPIHCS9HS',
//                                                  'A1F83G8C2ARO7P'
//                                                                    ));
//        
//        $feedHandle = @fopen('php://memory', 'rw+');
//        fwrite($feedHandle, $feed);
//        rewind($feedHandle);
//
//        $request = new MarketplaceWebService_Model_SubmitFeedRequest();
//        $request->setMerchant(MERCHANT_ID);
//        $request->setMarketplaceIdList($marketplaceIdArray);
//        $request->setFeedType($feed_type);
//        $request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));
//        rewind($feedHandle);
//        $request->setPurgeAndReplace(false);
//        $request->setFeedContent($feedHandle);
//
//        rewind($feedHandle);
//        
//        $this->invoke_submit_feed($this->instance_of_client('gb'), $request);
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
 
 /**
  * Update stock level on all Amazon
  * @param array $data
  * 
  */
 public function update_stock($data)
 {
     if(empty($data))
     {
         return FALSE; 
     }
     
     //prepare xml feed
     $xml = '
            <?xml version="1.0" encoding="utf-8" ?>
            <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
            <Header>
            <DocumentVersion>1.01</DocumentVersion>
            <MerchantIdentifier>$merchant_token</MerchantIdentifier>
            </Header>
            <MessageType>Inventory</MessageType>
            <Message>
    ';
     
     $i = 1;
     foreach ($data as $product)
     {
           $xml .= '<MessageID>'.$i.'</MessageID>
                    <OperationType>Update</OperationType>
                    <Inventory>
                    <SKU>'.$product->sku.'</SKU>
                    <Quantity>'.$product->stock.'</Quantity>
                    <FulfillmentLatency>1</FulfillmentLatency>
                    </Inventory>'; 
           $i++;
     }
     
     $xml .= '
            </Message>
            </AmazonEnvelope>
    ';
     $this->submit_feed_request($xml, '_POST_INVENTORY_AVAILABILITY_DATA_');
 }
 
 
}