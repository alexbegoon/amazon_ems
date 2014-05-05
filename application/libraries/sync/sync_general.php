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
        $this->_CI->load->model('products/products_model');
        $this->_CI->load->model('dashboard/dashboard_model');
        $this->_CI->load->model('virtuemart/virtuemart_model');
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
                
        if( !empty($web->installed_languages) && $this->_config->virtuemart_version === '2.0.0.0' )
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
        
        if (!$this->_config->test_mode) 
        {
            $this->update_statuses();
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
                    
                    $order[45]  = $this->getGasto($order, false); //calculate Gasto
                    $order[9]   = $this->getProcesado($order);
                    unset($order[48]); // Unset order status. We need no this in pedido table
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
                        `formadepago`, `in_stokoni`, `shipping_phrase`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ");
                    
                        $stmt->execute($order);
                        
                        $this->_CI->products_model->store_history($order[41],$order[0],$this->input_dbo->lastInsertId(),$order[9],$order[2]);
                        
                        if($order[9] == 'CANCELADO')
                        {
                            $this->_CI->dashboard_model->cancel_order((int)$this->input_dbo->lastInsertId());
                        }
                        
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
                
        $hash = strtolower(substr(preg_replace('/\d/','',md5((string)(rand(0,100).rand(0,100).rand(0,100).rand(0,100)))),0,10));
        
        $query = 'SELECT id as '.$hash.' FROM pedidos 
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
     * @return mixed 0 or gasto float
     */
    private function getGasto($order, $safe_mode = true) {
        
        if (is_array($order) && !empty($order)) {
                        
            if (empty($order['id'])) {
                $order_product = array();
                $j = 0;
                for ($i = 10; $i <= 37; $i += 3){
                        if(empty($order[$i]))
                        {
                            continue;
                        }
                        $order_product[$j]['sku']       = $order[$i];
                        $order_product[$j]['quantity']  = (int)$order[$i+2];
                        $order_product[$j]['price']     = (float)$order[$i+1];
                        $order_product[$j]['order_id']  = trim($order[0]);
                        $j++;   
                }
                
                $shipping_cost  = $this->getShippingCost($order[7],$order[41],$order[49]);
                
                return $this->_CI->products_model->calculate_gasto($order_product,$shipping_cost,$order[41],$safe_mode);
                
            } else {
                $order_product = array();
                $j = 0;
                for ($i = 1; $i <= 10; $i++){
                        if(empty($order['sku'.$i]))
                        {
                            continue;
                        }
                        $order_product[$j]['sku']       = $order['sku'.$i];
                        $order_product[$j]['quantity']  = (int)$order['cantidad'.$i];
                        $order_product[$j]['price']     = (float)$this->getPriceFromEngelsa($sku);
                        $order_product[$j]['order_id']  = trim($order['pedido']);      
                        $j++;
                }
                $shipping_cost  = $this->getShippingCost($order['pais'],$order['web'],$order['shipping_phrase']);
                
                return $this->_CI->products_model->calculate_gasto($order_product,$shipping_cost,$order['web'],$safe_mode);
            }
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
        
        if(isset($this->_CI->products_model->products_sales_history_data[$order[41]][$order[0]]['out_of_stock']))
        {
            if($this->_CI->products_model->products_sales_history_data[$order[41]][$order[0]]['out_of_stock'] == true)
            {
                return 'ROTURASTOCK';
            }
        }
        
        //Check order status
        if($order[48] == 'C')
        {
            return 'PAGADO';
        }
        if($order[48] == 'X' && preg_match('/Carte|MasterCard|VISA|Maestro|Tarjeta|Carte Bleue/i',$order[47]) == 1)
        {
            return 'PTE_PAGO';
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
    
    private function update_statuses()
    {
        // Get orders that require the refresh
        
        $statuses = array(
                            'NO'
            );
        $this->_CI->db->select('id, pedido, web, procesado');
        $dbprefix = $this->_CI->db->dbprefix;
        $this->_CI->db->set_dbprefix(null);
        $this->_CI->db->from('pedidos');
        $this->_CI->db->set_dbprefix($dbprefix);
        $this->_CI->db->where_in('procesado', $statuses);
        $this->_CI->db->where('fechaentrada >=', date('Y-m-d', time() - 7 * SECONDS_PER_DAY));
        $this->_CI->db->order_by('id', 'desc');
        $query = $this->_CI->db->get();
        
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $order) 
            {
                $vm_order = $this->_CI->virtuemart_model->get_order($order->web, $order->pedido);
                
                if(count($vm_order) > 0)
                {
                    switch ($vm_order[0]->order_status)
                    {
                        case 'P' : 
                            $this->_CI->dashboard_model->set_status((int)$order->id, 'PAGADO');
                            break;
                        case 'X' :
                            $this->_CI->dashboard_model->cancel_order((int)$order->id);
                            break;
                    }
                }
            }
        }
    }
}