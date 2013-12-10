<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__).'/sync_general.php';
/**
 * Syncronization DB
 *
 * @author      Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

class Sync extends Sync_general
{
    
    public function __construct($web)
    {
        parent::__construct($web);
    }
        
    /**
     * Extract orders from e-shop
     * 
     */
    protected function extractOrders() {
        
        $prefix     = $this->_config->output_prefix;
        $start_date = $this->_config->start_time;
        $now        = time();
        
        if ($now - strtotime($start_date) > 60 * 60 * 24 * 7) {
            $start_date = date('Y-m-d H:i:s', $now - 60 * 60 * 24 * 5); // 5 days ago
        }
        
        $query = 'SELECT `orders`.`order_number` AS `pedido`, `user`.`name` AS `nombre`, 
                         `orders`.`created_on` AS `fechaentrada`, `orders`.`created_on` AS `fechadepago`, 
                         `userinfo`.`address_1` AS `direccion`, `userinfo`.`phone_1` AS `telefono`, 
                         `userinfo`.`zip` AS `codigopostal`, `country`.`country_2_code` AS `pais`, 
                         `userinfo`.`city` AS `estado`, \'NO\' AS `procesado`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 0,1) AS `sku1`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 0,1) AS `precio1`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 0,1) AS `cantidad1`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 1,1) AS `sku2`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 1,1) AS `precio2`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 1,1) AS `cantidad2`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 2,1) AS `sku3`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 2,1) AS `precio3`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 2,1) AS `cantidad3`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 3,1) AS `sku4`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 3,1) AS `precio4`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 3,1) AS `cantidad4`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 4,1) AS `sku5`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 4,1) AS `precio5`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 4,1) AS `cantidad5`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 5,1) AS `sku6`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 5,1) AS `precio6`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 5,1) AS `cantidad6`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 6,1) AS `sku7`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 6,1) AS `precio7`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 6,1) AS `cantidad7`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 7,1) AS `sku8`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 7,1) AS `precio8`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 7,1) AS `cantidad8`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 8,1) AS `sku9`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 8,1) AS `precio9`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 8,1) AS `cantidad9`,
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 9,1) AS `sku10`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 9,1) AS `precio10`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'virtuemart_order_items` AS `items` WHERE `items`.`virtuemart_order_id` = `orders`.`virtuemart_order_id` LIMIT 9,1) AS `cantidad10`,
                         `orders`.`order_total` AS `ingresos`, \''.$this->_config->web.'\' AS `web`, `orders`.`customer_note` AS `comentarios`, 
                         NULL AS `tracking`, `userinfo`.`email` AS `correo`, 0 AS `gasto`, NULL AS `localidad`, 
                         `paymethod`.`payment_name` AS `formadepago`, `orders`.`order_status` , '.$this->get_shipment_methods_expression().', 
                         `currencies`.`currency_code_3` as `order_currency`, 
                         `currencies`.`currency_code_3` as `order_item_currency_1`, 
                         `currencies`.`currency_code_3` as `order_item_currency_2`, 
                         `currencies`.`currency_code_3` as `order_item_currency_3`, 
                         `currencies`.`currency_code_3` as `order_item_currency_4`, 
                         `currencies`.`currency_code_3` as `order_item_currency_5`, 
                         `currencies`.`currency_code_3` as `order_item_currency_6`, 
                         `currencies`.`currency_code_3` as `order_item_currency_7`, 
                         `currencies`.`currency_code_3` as `order_item_currency_8`, 
                         `currencies`.`currency_code_3` as `order_item_currency_9`, 
                         `currencies`.`currency_code_3` as `order_item_currency_10`
            
                  FROM `'.$prefix.'virtuemart_orders` as `orders` 
                  LEFT JOIN `'.$prefix.'virtuemart_paymentmethods_es_es` AS `paymethod` 
                  USING (`virtuemart_paymentmethod_id`) 
                  LEFT JOIN `'.$prefix.'virtuemart_order_userinfos` AS `userinfo` 
                  USING (`virtuemart_order_id`) 
                  LEFT JOIN `'.$prefix.'users` AS `user` 
                  ON `orders`.`virtuemart_user_id` = `user`.`id` 
                  LEFT JOIN `'.$prefix.'virtuemart_countries` AS `country` 
                  ON `country`.`virtuemart_country_id` = `userinfo`.`virtuemart_country_id` 
                  LEFT JOIN `'.$prefix.'virtuemart_currencies` AS `currencies` 
                  ON `orders`.`order_currency` = `currencies`.`virtuemart_currency_id` 
                  '.$this->get_shipment_methods_joins().' 
                  WHERE `orders`.`created_on` > \''.$start_date.'\' AND `userinfo`.`address_type` = \'BT\'
        ';
        try {
            $stmt = $this->output_dbo->query($query);
            if ($this->_config->test_mode) {
               $this->_orders = $stmt->fetchAll(PDO::FETCH_ASSOC); 
            } else {
               $this->_orders = $stmt->fetchAll(PDO::FETCH_NUM); 
            }
            
        } catch(PDOException $ex) {
            echo $ex->getMessage();
        }
    }  
    
    protected function get_shipment_methods_joins()
    {
     
        $string = '';
        $prefix     = $this->_config->output_prefix;
        
        if(count($this->_config->languages) > 0 && is_array($this->_config->languages))
        {
            foreach ($this->_config->languages as $key => $value)
            {
                $string .= ' LEFT JOIN `'.$prefix.'virtuemart_shipmentmethods_';
                $string .= $key.'` AS `shipmentmethods_'.$key.'` ';
                $string .= ' USING(`virtuemart_shipmentmethod_id`) ';
            }
        }
        
        return $string;
    }
    
    protected function get_shipment_methods_expression()
    {
        
        $string = '';
        
        if(count($this->_config->languages) > 0 && is_array($this->_config->languages))
        {
            $string = 'CONCAT_WS(\'|\' ';
            
            foreach ($this->_config->languages as $key => $value)
            {
                $string .= ', `shipmentmethods_'.$key.'`.`shipment_name`, ';
                $string .= ' `shipmentmethods_'.$key.'`.`shipment_desc`, ';
                $string .= ' `shipmentmethods_'.$key.'`.`slug` ';
            }
            
            $string .= ' ) as `shipping_phrase`';
        }
        
        return $string;
    }
}