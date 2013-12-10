<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__).'/sync_general.php';
/**
 * Syncronization DB with e-shop
 *
 * @author      Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

class SyncOld extends Sync_general
{
    
    public function __construct($web)
    {
        parent::__construct($web);
//      $this->setGasto();
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
        
        $query = 'SELECT `orders`.`order_id` AS `pedido`, CONCAT_WS(\' \',`vm_order_user_info`.`first_name`, `vm_order_user_info`.`last_name`) AS `nombre`, 
                          FROM_UNIXTIME(`orders`.`cdate`, \'%Y-%m-%d\') AS `fechaentrada`, 
                          FROM_UNIXTIME(`orders`.`cdate`, \'%Y-%m-%d\') AS `fechadepago`, 
                         `vm_order_user_info`.`address_1` AS `direccion`, `vm_order_user_info`.`phone_1` AS `telefono`, 
                         `vm_order_user_info`.`zip` AS `codigopostal`, `country`.`country_2_code` AS `pais`, 
                         `vm_order_user_info`.`city` AS `estado`, \'NO\' AS `procesado`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 0,1) AS `sku1`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 0,1) AS `precio1`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 0,1) AS `cantidad1`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 1,1) AS `sku2`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 1,1) AS `precio2`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 1,1) AS `cantidad2`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 2,1) AS `sku3`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 2,1) AS `precio3`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 2,1) AS `cantidad3`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 3,1) AS `sku4`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 3,1) AS `precio4`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 3,1) AS `cantidad4`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 4,1) AS `sku5`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 4,1) AS `precio5`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 4,1) AS `cantidad5`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 5,1) AS `sku6`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 5,1) AS `precio6`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 5,1) AS `cantidad6`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 6,1) AS `sku7`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 6,1) AS `precio7`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 6,1) AS `cantidad7`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 7,1) AS `sku8`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 7,1) AS `precio8`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 7,1) AS `cantidad8`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 8,1) AS `sku9`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 8,1) AS `precio9`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 8,1) AS `cantidad9`, 
                         (SELECT `items`.`order_item_sku`      FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 9,1) AS `sku10`, 
                         (SELECT `items`.`product_final_price` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 9,1) AS `precio10`, 
                         (SELECT `items`.`product_quantity`    FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 9,1) AS `cantidad10`, 
                         ROUND(`orders`.`order_total`, 2) AS `ingresos`, \''.$this->_config->web.'\' AS `web`, `orders`.`customer_note` AS `comentarios`, 
                         NULL AS `tracking`, `vm_order_user_info`.`user_email` AS `correo`, 0 AS `gasto`, NULL AS `localidad`, 
                         `paymethod`.`payment_method_name` AS `formadepago`, `orders`.`order_status`, `orders`.`ship_method_id` as `shipping_phrase`, 
                         `orders`.`order_currency` as `order_currency`, 
                         (SELECT `items`.`order_item_currency` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 0,1) AS `order_item_currency_1`, 
                         (SELECT `items`.`order_item_currency` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 1,1) AS `order_item_currency_2`, 
                         (SELECT `items`.`order_item_currency` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 2,1) AS `order_item_currency_3`,     
                         (SELECT `items`.`order_item_currency` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 3,1) AS `order_item_currency_4`,     
                         (SELECT `items`.`order_item_currency` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 4,1) AS `order_item_currency_5`, 
                         (SELECT `items`.`order_item_currency` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 5,1) AS `order_item_currency_6`, 
                         (SELECT `items`.`order_item_currency` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 6,1) AS `order_item_currency_7`, 
                         (SELECT `items`.`order_item_currency` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 7,1) AS `order_item_currency_8`,     
                         (SELECT `items`.`order_item_currency` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 8,1) AS `order_item_currency_9`,     
                         (SELECT `items`.`order_item_currency` FROM `'.$prefix.'vm_order_item` AS `items` WHERE `items`.`order_id` = `orders`.`order_id` LIMIT 9,1) AS `order_item_currency_10`    
                         
                  FROM `'.$prefix.'vm_orders` as `orders` 
                  LEFT JOIN `'.$prefix.'vm_order_payment` AS `payment` 
                  USING (`order_id`)    
                  LEFT JOIN `'.$prefix.'vm_payment_method` AS `paymethod` 
                  ON `payment`.`payment_method_id` = `paymethod`.`payment_method_id`  
                  LEFT JOIN `'.$prefix.'vm_order_user_info` AS `vm_order_user_info` 
                  USING(`order_id`) 
                  LEFT JOIN `'.$prefix.'vm_country` AS `country` 
                  ON `country`.`country_3_code` = `vm_order_user_info`.`country` 
                  WHERE `orders`.`cdate` > UNIX_TIMESTAMP(\''.$start_date.'\') 
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
                  
    private function setGasto(){
        
        $start_date = '2013-08-01';
        
        $query = ' SELECT * 
                   FROM `pedidos` 
                   WHERE `fechaentrada` > \''.$start_date.'\' 
                            AND ( `gasto` = 0 OR `gasto` IS NULL) 
        ';
        
        try {
                $stmt = $this->input_dbo->query($query);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC); 
                
                if ($result) {
                                                          
                    $query = 'UPDATE `pedidos`  
                              SET `gasto` = ?  
                              WHERE `id` = ? 
                    ';
                    
                    $stmt = $this->input_dbo->prepare($query);
                    
                    foreach ($result as $item) {
                        
                        $gasto = $this->getGasto($item);
                        $stmt->execute(array($gasto, $item['id']));
                    }
                    
                } else {
                    
                    return false;
                    
                }

            } catch(PDOException $ex) {
                echo $ex->getMessage();
            }
        
    }
}