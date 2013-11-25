<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Name: Currency
 * 
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 * 
 * 
 */
class Currency
{
    
    private $_defaultCurrency = 'EUR';
    private $_exchangeRates = array();
    private $_CI = null; // Instance of CodeIgniter
    
    public function __construct($uri = NULL)
    {
        $this->_CI =& get_instance();
        $this->_CI->load->database();
        $this->getExchangeRates();
    }
    
    private function getExchangeRates()
    {
        $query = 'SELECT `rates`.`rate`, `currencies`.`currency_code_3` AS `currency`  
                  FROM `'.$this->_CI->db->dbprefix('exchange_rates').'` AS `rates` 
                  LEFT JOIN `'.$this->_CI->db->dbprefix('currencies').'` AS `currencies` 
                  USING(`currency_id`) 
        ';
        
        $result = $this->_CI->db->query($query);
        
        if ($result)
        {
            $this->_exchangeRates = $result->result();
        } else {
            return false;
        }
    }
    
    /**
     * Converts the input into its corresponding exchange rate
     * 
     * @param mixed $transaction An array containing currency and price e.g. array('currency' => 'USD', 'price' => '146.80') 
     * or Object containing currency and price as property
     *                              
     * @return string A string containing the computed exchange rate in the default currency e.g., USD 146.80
     */
    public function convertCurrency($transaction)
    {
        if (is_array($transaction)) 
        {
            return $this->convertCurrencyByArray($transaction);
        } 
        elseif(is_object($transaction))
        {
            return $this->convertCurrencyByObject($transaction);
        }
    }
    
    private function convertCurrencyByArray($transaction)
    {
        if (!empty($transaction))
        {
            if(!empty($transaction['currency']) && !empty($transaction['price']))
            {
                if ($transaction['currency'] == $this->_defaultCurrency)
                {
                    return $transaction;
                }
                foreach($this->_exchangeRates as $rate)
                {
                    if ($this->filterCallback($rate, $transaction['currency']))
                    {
                        $rate_object = $rate;
                        break;
                    }
                }
                if (empty($rate_object))
                {
                    return $transaction;
                }
                if ($rate_object->rate == 0)
                {
                    return $transaction;
                }
                if (!empty($transaction['shipping_price']))
                {
                    $transaction['shipping_price'] = ((float)$transaction['shipping_price'] / (float)$rate_object->rate);
                }
                $transaction['price']       = ((float)$transaction['price'] / (float)$rate_object->rate);
                $transaction['currency']    = $this->_defaultCurrency;
                
                return $transaction;
                
            } 
            else
            {
                return false;
            }
        } 
        else 
        {
            return false;
        }
    }
    
    private function convertCurrencyByObject($transaction)
    {
        if (!empty($transaction))
        {
            if(!empty($transaction->currency) && !empty($transaction->price))
            {
                if ($transaction->currency == $this->_defaultCurrency)
                {
                    return $transaction;
                }
                foreach($this->_exchangeRates as $rate)
                {
                    if ($this->filterCallback($rate, $transaction['currency']))
                    {
                        $rate_object = $rate;
                        break;
                    }
                }
                if (empty($rate_object))
                {
                    return $transaction;
                }
                if ($rate_object->rate == 0)
                {
                    return $transaction;
                }
                if (!empty($transaction->shipping_price))
                {
                    $transaction->shipping_price = ((float)$transaction->shipping_price / (float)$rate_object->rate);
                }
                $transaction->price         = ((float)$transaction->price / (float)$rate_object->rate);
                $transaction->currency      = $this->_defaultCurrency;
                
                return $transaction;
                
            } 
            else
            {
                return $transaction;
            }
        } 
        else 
        {
            return $transaction;
        }
    }
    
    private function filterCallback(stdClass &$obj, $search)
    {
        return $obj->currency == $search;
    }
}
