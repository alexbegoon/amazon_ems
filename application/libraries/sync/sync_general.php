<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Parent class. Sync process.
 *
 * @author      Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Sync_general 
{
    public $input_dbo = null, $output_dbo = null;
    
    protected $_config, $_orders, $_CI = null, $_data;

    public function __construct($web)
    {
        
        if(empty($web))
        {
            echo 'Have no configuration';
            die;
        }
        
        $this->_config = new stdClass();
        
        $this->_config->web = (string)$web;
        
        if (!is_object($this->_config)) {
            echo 'Have no configuration';
            die;
        }
        
        // Instance of Codeigniter App
        $this->_CI =& get_instance();
        
        $this->_CI->load->database();
        
        // Load models from CI
        $this->_CI->load->model('stokoni/stokoni_model');
        $this->_CI->load->model('engelsa/engelsa_model');
        $this->_CI->load->model('incomes/shipping_costs_model');
        $this->_CI->load->model('incomes/taxes_model');
        $this->_CI->load->model('incomes/web_field_model');
        $this->_CI->load->library('currency');
        
        $web = $this->_CI->web_field_model->get_web_field($this->_config->web);
        $this->_config->output_host          = $web->hostname;
        $this->_config->output_user          = $web->username;
        $this->_config->output_pass          = $web->password;
        $this->_config->output_db            = $web->database;
        $this->_config->output_prefix        = $web->dbprefix;
        $this->_config->test_mode            = $web->test_mode == '1' ? true : false;
        $this->_config->virtuemart_version   = $web->virtuemart_version;
        $this->_config->start_time           = $web->start_time;
        $this->_config->web                  = $web->web;
        
        if(!empty($web->installed_languages))
        {
            $this->_config->languages = array_flip(array_map( 'trim', explode(',', $web->installed_languages)));
        }
        
        $this->createDBO();
        $this->extractOrders();
        
        if (!$this->_config->test_mode) {
            $this->storeOrders();
        } else {
            $this->showOrders();
        }
    }
    
    /**
     * Construct DB objects
     * 
     */
    private function createDBO() {
        
        if (empty($this->input_dbo)) {
            $this->input_dbo = new PDO(
                $this->_CI->db->hostname.';
                charset=utf8', 
             ''.$this->_CI->db->username.'', 
             ''.$this->_CI->db->password.'');
        }
        
        if (empty($this->output_dbo)) {
            $this->output_dbo = new PDO( 
            ''.$this->_config->output_host.';
                 dbname='.$this->_config->output_db.';
                charset=utf8', 
             ''.$this->_config->output_user.'', 
             ''.$this->_config->output_pass.'');
        }
        
        return true;
    }
    
    /**
     * store orders to buyin.eu DB
     * 
     * 
     */
    private function storeOrders() {
        
        if ($this->_orders) {
            
            foreach ($this->_orders as $order) {
                
                if(!$this->isExist($order)) {
                    
                    $order = $this->convert_currency($order);
                    
                    $order[9]   = $this->getProcesado($order);
                    $order[45]  = $this->getGasto($order, false); //calculate Gasto
                    unset($order[48]); // Unset order status. We need no this in pedido table
                    unset($order[49]); // Unset order shipping phrase. We need no this in pedido table
                    // Unset order currencies
                    unset($order[50]);
                    unset($order[51]);
                    unset($order[52]);
                    unset($order[53]);
                    unset($order[54]);
                    unset($order[55]);
                    unset($order[56]);
                    unset($order[57]);
                    unset($order[58]);
                    unset($order[59]);
                    unset($order[60]);
                    
                    $order[48]  = $this->get_stokoni_status($order);
                    
                    try {
                        
                        $stmt = $this->input_dbo->prepare("INSERT INTO pedidos (`pedido`, `nombre`, `fechaentrada`, 
                        `fechadepago`, `direccion`, `telefono`, `codigopostal`, `pais`, `estado`, 
                        `procesado`, `sku1`, `precio1`, `cantidad1`, `sku2`, `precio2`, `cantidad2`,  
                        `sku3`, `precio3`, `cantidad3`, `sku4`, `precio4`, `cantidad4`, `sku5`, 
                        `precio5`, `cantidad5`, `sku6`, `precio6`, `cantidad6`, `sku7`, `precio7`, 
                        `cantidad7`, `sku8`, `precio8`, `cantidad8`, `sku9`, `precio9`, `cantidad9`, `sku10`, `precio10`, `cantidad10`, 
                        `ingresos`, `web`, `comentarios`, `tracking`, `correo`, `gasto`, `localidad`, 
                        `formadepago`, `in_stokoni`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ");
                    
                        $stmt->execute($order);
                        
                    } catch(PDOException $ex) {
                        echo $ex->getMessage();
                    }
                }
            }
        }
        
        return false;
        
    }
    
    /**
     * Show orders for tests
     * 
     */
    private function showOrders() {
        
        echo '<pre>';
        print_r($this->_orders);
        echo '</pre>';
    }
    
    /**
     * Looking for existing order
     * 
     * 
     */
    private function isExist($order) {
                
        $query = 'SELECT id FROM pedidos 
                  WHERE pedido = \''.$order[0].'\' AND 
                        web    = \''.$this->_config->web.'\' 
        ';
                
        try {
            $stmt = $this->input_dbo->query($query);
            $result = $stmt->rowCount();
        } catch(PDOException $ex) {
            echo $ex->getMessage();
        }
        
        if ($result > 0) {
            return true;
        } else {
            return false;
        }
        
    }
    
    /**
     * Return Gasto
     * @param array $order Order array
     * @param boolean $safe_mode Safe mode. If safe mode , then gasto calculates without stock changes in the Engelsa and Stokoni tables.By Default safe mode on!
     * @return mixed 0 or gsato float
     */
    private function getGasto($order, $safe_mode = true) {
        
        if (is_array($order) && !empty($order)) {
            
            $gasto = 0;
            
            if (empty($order['id'])) {
                
                for ($i = 10; $i <= 37; $i += 3){
                    if (strlen($order[$i]) == 14){
                        $sku                = str_replace('#', '', $order[$i]);
                        $quantity           = (int)$order[$i+2];
                        $price              = $this->getPriceFromEngelsa($sku);
                        $product_in_stokoni = $this->_CI->stokoni_model->find_product_by_ean($sku);
                        $product_in_engelsa = $this->_CI->engelsa_model->get_product($sku);
                            
                        if($product_in_stokoni && $product_in_stokoni->stock > 0)
                        {
                            if($product_in_stokoni->stock >= $quantity)
                            {
                                $gasto += ( $product_in_stokoni->coste * $quantity );
                                if(!$safe_mode)
                                {
                                    $this->_CI->stokoni_model->sell_product((int)$product_in_stokoni->id, (int)$quantity);
                                }
                            }
                            else
                            {
                                if ($price > 0)
                                {
                                    $gasto += ( ( $product_in_stokoni->coste * $product_in_stokoni->stock ) + ( $price * ( $quantity - $product_in_stokoni->stock ) ) );
                                    if(!$safe_mode)
                                    {
                                        $this->_CI->stokoni_model->sell_product((int)$product_in_stokoni->id, (int)$product_in_stokoni->stock);
                                    }
                                    if($product_in_engelsa->stock >= $quantity - $product_in_stokoni->stock)
                                    {   
                                        if(!$safe_mode)
                                        {
                                            $this->_CI->engelsa_model->sell_product($sku, $quantity - $product_in_stokoni->stock);
                                        }    
                                    }
                                } 
                                else
                                {
                                    return 0; // We cant calculate gasto if dont know price
                                }
                            }
                        }
                        else 
                        {
                            if ($price > 0)
                            {
                                $gasto += ( $price * $quantity );
                                
                                if($product_in_engelsa->stock >= $quantity)
                                {
                                    if(!$safe_mode)
                                    {
                                        $this->_CI->engelsa_model->sell_product($sku, $quantity);
                                    }
                                }
                            } 
                            else
                            {
                                return 0; // We cant calculate gasto if dont know price
                            }
                        }
                            
                    } else {
                        continue;
                    }
                }

                $shipping_cost  = $this->getShippingCost($order[7],$order[41],$order[49]);
                $IVA_tax        = $this->getIVAtax();

                if ($shipping_cost > 0 && $IVA_tax > 0 && $gasto > 0) {

                    $gasto *= ( 1 + (1/100*$IVA_tax));

                    $gasto += ( $shipping_cost * ( 1 + (1/100*$IVA_tax)) );

                    return $gasto;

                } else {
                    return 0; // We cant calculate gasto with Zero shipping cost and Zero Taxes.
                }
                
            } else {
                
                for ($i = 1; $i <= 10; $i++){
                    if (strlen($order['sku'.$i]) == 14){
                        $sku                = str_replace('#', '', $order['sku'.$i]);
                        $quantity           = (int)$order['cantidad'.$i];
                        $price              = $this->getPriceFromEngelsa($sku);
                        $product_in_stokoni = $this->_CI->stokoni_model->find_product_by_ean($sku);
                        $product_in_engelsa = $this->_CI->engelsa_model->get_product($sku);
                                                
                        if($product_in_stokoni && $product_in_stokoni->stock > 0)
                        {
                            if($product_in_stokoni->stock >= $quantity)
                            {
                                $gasto += ( $product_in_stokoni->coste * $quantity );
                                if(!$safe_mode)
                                {
                                    $this->_CI->stokoni_model->sell_product((int)$product_in_stokoni->id, (int)$quantity);
                                }
                            }
                            else
                            {
                                if ($price > 0)
                                {
                                    $gasto += ( ( $product_in_stokoni->coste * $product_in_stokoni->stock ) + ( $price * ( $quantity - $product_in_stokoni->stock ) ) );
                                    if(!$safe_mode)
                                    {
                                        $this->_CI->stokoni_model->sell_product((int)$product_in_stokoni->id, (int)$product_in_stokoni->stock);
                                    }
                                    if($product_in_engelsa->stock >= $quantity - $product_in_stokoni->stock)
                                    {
                                        if(!$safe_mode)
                                        {       
                                            $this->_CI->engelsa_model->sell_product($sku, $quantity - $product_in_stokoni->stock);
                                        }
                                    }
                                } 
                                else
                                {
                                    return 0; // We cant calculate gasto if dont know price
                                }
                            }
                        }
                        else 
                        {
                            if ($price > 0)
                            {
                                $gasto += ( $price * $quantity );
                                
                                if($product_in_engelsa->stock >= $quantity)
                                {
                                    if(!$safe_mode)
                                    {
                                        $this->_CI->engelsa_model->sell_product($sku, $quantity);
                                    }
                                }
                            } 
                            else
                            {
                                return 0; // We cant calculate gasto if dont know price
                            }
                        }

                    } else {
                        continue;
                    }
                }

                $shipping_cost  = $this->getShippingCost($order['pais'],$order['web'],$order['shipping_phrase']);
                $IVA_tax        = $this->getIVAtax();

                if ($shipping_cost > 0 && $IVA_tax > 0 && $gasto > 0) {

                    $gasto *= ( 1 + (1/100*$IVA_tax));

                    $gasto += ( $shipping_cost * ( 1 + (1/100*$IVA_tax)) );

                    return $gasto;

                } else {
                    return 0; // We cant calculate gasto with Zero shipping cost and Zero Taxes.
                }
                
            }
            
        } else {
            
            return 0;
            
        }
    }
    
    private function getPriceFromEngelsa($sku)
    {
        return $this->_CI->engelsa_model->get_price($sku);
    }
    
    private function getShippingCost($country_code,$web,$shipping_phrase)
    {
        return $this->_CI->shipping_costs_model->get_shipping_price($web,$country_code,$shipping_phrase);
    }
    
    private function getIVAtax()
    {
        return $this->_CI->taxes_model->getIVAtax();
    }
    
    /**
     * Return procesado. ENALMACEN if one of all product exists in the Stokoni table. Else NO.
     * 
     * @param array $order Order
     * @return string NO or ROTURASTOCK or PAGADO
     * 
     */
    private function getProcesado($order)
    {
        $stokoni_flags = array(); //Store flag 1 if product stock in stockoni not less than quantity
        
        for ($i = 10; $i <= 37; $i += 3)
        {
            if (!empty($order[$i]))
            {
                $sku            = $order[$i];
                $quantity       = (int)$order[$i+2];
                
                $product_in_stokoni = $this->_CI->stokoni_model->find_product_by_ean($sku);
                $product_in_engelsa = $this->_CI->engelsa_model->get_product($sku);
                
                if($product_in_stokoni)
                {
                    if($product_in_stokoni->stock >= $quantity)
                    {
                        $stokoni_flags[] = 1;
                        continue;
                    }
                    else 
                    {
                        $stokoni_flags[] = 0;
                    }
                }
                
                if($product_in_engelsa && $product_in_stokoni)
                {
                    if($product_in_engelsa->stock >= $quantity - $product_in_stokoni->stock)
                    {
                        $stokoni_flags[] = 0;
                        continue;
                    }
                    else
                    {
                        return 'ROTURASTOCK';
                    }
                } 
                elseif($product_in_engelsa)
                {
                    if($product_in_engelsa->stock >= $quantity)
                    {
                        $stokoni_flags[] = 0;
                        continue;
                    }
                    else
                    {
                        return 'ROTURASTOCK';
                    }
                }
            }   
        }
        
        //Set in_stockoni flag
        if(count($stokoni_flags) > 0 && array_search(0, $stokoni_flags) === false)
        {
            $this->_data['order_stokoni_status'][$order[0].$order[41]] = 1;
        }
        
        //Check order status
        if($order[48] == 'C')
        {
            return 'PAGADO';
        }
        if($order[48] == 'X')
        {
            return 'CANCELADO';
        }
        if(stripos('paypal', $order[47]) !== false)
        {
            return 'PAYPAL';
        }
        if(stripos('pay pal', $order[47]) !== false)
        {
            return 'PAYPAL';
        }
        if(stripos('pay-pal', $order[47]) !== false)
        {
            return 'PAYPAL';
        }
        
        // Default NO
        
        return 'NO';
    }
    
    private function get_stokoni_status($order)
    {
        if(isset($this->_data['order_stokoni_status'][$order[0].$order[41]]))
        {
            return $this->_data['order_stokoni_status'][$order[0].$order[41]];
        }
        
        return null;
    }
    
    /**
     * Convert currency to default currency
     * @param array $order
     * @return array Order
     */
    protected function convert_currency($order)
    {
        $data = array();
        
        $data['currency']   = $order[50];
        $data['price']      = $order[40];
        
        $data = $this->_CI->currency->convertCurrency($data);
        
        $order[50]   = $data['currency'];
        $order[40]   = $data['price'];
        
        $j = 11;
        
        for($i = 51; $i <= 60; $i++)
        {
            $data['currency']   = $order[$i];
            $data['price']      = $order[$j];
            
            if(empty($data['price']) || empty($data['currency']))
            {
                continue;
            }
            
            $data = $this->_CI->currency->convertCurrency($data);
            
            $order[$j]   = $data['price'];
            
            $j += 3;
        }
        
        return $order;
    }
}