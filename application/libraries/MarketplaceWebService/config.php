<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
  
   define ('DATE_FORMAT', 'Y-m-d\TH:i:s\Z');

   /************************************************************************
    * REQUIRED
    *
    * * Access Key ID and Secret Acess Key ID, obtained from:
    * http://aws.amazon.com
    *
    * IMPORTANT: Your Secret Access Key is a secret, and should be known
    * only by you and AWS. You should never include your Secret Access Key
    * in your requests to AWS. You should never e-mail your Secret Access Key
    * to anyone. It is important to keep your Secret Access Key confidential
    * to protect your account.
    ***********************************************************************/
    define('AWS_ACCESS_KEY_ID', 'AKIAJTWAINLUEN7OVHHQ');
    define('AWS_SECRET_ACCESS_KEY', 'eo3GB74fHunaBYxA8CfVV4CFalAlCMs6UGxEtlf4');

   /************************************************************************
    * REQUIRED
    * 
    * All MWS requests must contain a User-Agent header. The application
    * name and version defined below are used in creating this value.
    ***********************************************************************/
    define('APPLICATION_NAME', 'AMAZONI');
    define('APPLICATION_VERSION', '4.0');
    
   /************************************************************************
    * REQUIRED
    * 
    * All MWS requests must contain the seller's merchant ID and
    * marketplace ID.
    ***********************************************************************/
    define ('MERCHANT_ID', 'AYQ0NSWRNOTO4');