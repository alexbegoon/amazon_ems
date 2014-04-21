<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Description of virtuemart_model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Virtuemart_model extends CI_Model
{
 
    private $_db_connections = array(), $_version = array();


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        
        //Load model
        $this->load->model('incomes/web_field_model');
    }
    
    /**
     * Create the DB connection to website
     * 
     * @param string $web
     * @return object
     */
    private function create_db_connection($web)
    {
        if(isset($this->_db_connections[$web]))
        {
            return $this->_db_connections[$web];
        }
        $site = $this->web_field_model->get_web_field($web);
        
        if($site && !empty($site->hostname) && strlen($site->hostname) > 12 )
        {
            $active_record = TRUE;
            
            $config['hostname'] = $site->hostname;
            $config['username'] = $site->username;
            $config['password'] = $site->password;
            $config['database'] = $site->database;
            $config['dbdriver'] = 'pdo';
            $config['dbprefix'] = $site->dbprefix;
            $config['pconnect'] = FALSE;
            $config['db_debug'] = TRUE;
            $config['cache_on'] = FALSE;
            $config['cachedir'] = './application/cache/database';
            $config['char_set'] = $site->char_set;
            $config['dbcollat'] = $site->dbcollat;
            $config['swap_pre'] = '';
            $config['autoinit'] = TRUE;
            $config['stricton'] = FALSE;  
 
            $this->_db_connections[$web] = $this->load->database($config, TRUE, $active_record);
            return $this->_db_connections[$web];
        }
        
        return FALSE;
        
    }

    /**
     * Return virtuemart version of website
     * @param string $web
     * @return string Virtuemart version
     */
    public function check_version($web)
    {
        if(isset($this->_version[$web]))
        {
            return $this->_version[$web];
        }
        
        $db   = $this->create_db_connection($web);
        $site = $this->web_field_model->get_web_field($web);
        
        if(!$site)
        {
            return false;
        }
        
        $query = 'SELECT 
                        CASE EXISTS (
                                        SELECT * 
                                        FROM information_schema.tables
                                        WHERE table_schema = \''.$site->database.'\' 
                                        AND table_name = \''.$site->dbprefix.'vm_orders\' 
                                        LIMIT 1 
                                    )
                            WHEN 1 THEN \'1.0.0.0\' 
                            WHEN 0 THEN \'2.0.0.0\' 
                        END as `version` 
                    ';
                
        if(!is_a($db, 'CI_DB_pdo_driver'))
        {
            return false;
        }
        
        $result = $db->query($query);
        
        if($result)
        {
            $this->_version[$web] = $result->row()->version;
            return $this->_version[$web];
        }
            
        return false;
    }
    
    public function get_user_by_order_name($web, $order_name)
    {
        $db = $this->create_db_connection($web);
        
        if(!$db)
        {
            return false;
        }
        
        $virtuemart_version = $this->check_version($web);
        
        if($virtuemart_version == '2.0.0.0')
        {
            $query = $db->select('users.`name`, users.`username`, users.`email`, users.`language_tag`')
                        ->from('users as users')
                        ->join('virtuemart_orders as orders', 'orders.virtuemart_user_id = users.id', 'inner')
                        ->where('orders.order_number',$order_name)
                        ->get();
            
            if($query)
            {
                return $query->row();
            }
        }
        
        return false;
    }
    
    public function get_language_tag_of_order($web, $order_name)
    {
        $user = $this->get_user_by_order_name($web, $order_name);
        
        if($user)
        {
            return $user->language_tag;
        }
        
        return false;
    }
    
    /**
     * Return two chars prefix, that helps to choose correct language of the email template
     * @param type $web
     * @param type $order_name
     * @return string
     */
    public function get_template_prefix($web, $order_name)
    {
        $language_tag = $this->get_language_tag_of_order($web, $order_name);
        
        $rules = $this->get_languages_rules();
        
        if($language_tag)
        {
            if($rules[$language_tag])
            {
                return $rules[$language_tag];
            }
        }
        
        $web_shop = $this->web_field_model->get_web_field($web);
        
        if($web_shop)
        {
            return $web_shop->template_language;
        }
        
        // Default
        return 'es';
    }

    
    private function get_languages_rules()
    {
        return array( 
            'nl-NL' => 'nl',
            'en-AU' => 'en',
            'en-US' => 'en',
            'en-GB' => 'en',
            'es-ES' => 'es',
            'fr-FR' => 'fr',
            'de-DE' => 'de',
            'nn-NO' => 'nn',
            'pt-PT' => 'pt',
            'sv-SE' => 'sv',
            'it-IT' => 'it'
        );
    }
    
    public function get_order($web, $order_name)
    {
        $db = $this->create_db_connection($web);
        
        if(!$db)
        {
            return false;
        }
        
        $virtuemart_version = $this->check_version($web);
        
        if($virtuemart_version == '2.0.0.0')
        {
            $query = ' SELECT `orders`.*, `order_items`.*, 
                       `userinfos`.`name` as `customer_name`, 
                       \''.$web.'\' as `web` 
                       FROM `'.$db->dbprefix('virtuemart_orders').'` as `orders` 
                       LEFT JOIN `'.$db->dbprefix('virtuemart_order_items').'` as `order_items` 
                       USING(`virtuemart_order_id`)     
                       LEFT JOIN `'.$db->dbprefix('virtuemart_userinfos').'` as `userinfos`    
                       USING(`virtuemart_user_id`)  
                       WHERE `orders`.`order_number` = \''.$order_name.'\' 
            ';
            
        }
        elseif($virtuemart_version == '1.0.0.0')
        {
            $query = ' SELECT `orders`.*, `order_items`.*, 
                       CONCAT_WS(\' \',`userinfos`.`first_name`,`userinfos`.`last_name`) as `customer_name`, 
                       `order_items`.`product_id` as `virtuemart_product_id`, 
                       `orders`.`user_id` as `virtuemart_user_id` 
                       FROM `'.$db->dbprefix('vm_orders').'` as `orders` 
                       LEFT JOIN `'.$db->dbprefix('vm_order_item').'` as `order_items` 
                       USING(`order_id`) 
                       LEFT JOIN `'.$db->dbprefix('vm_order_user_info').'` as `userinfos`    
                       USING(`order_id`) 
                       WHERE `orders`.`order_id` = \''.$order_name.'\' 
            ';
            
        }
                
        $result = $db->query($query);
        
        if($result)
        {
            return $result->result();
        }
                
        return false;
    }
    
    public function is_product_exists($web, $product_id)
    {
        $db = $this->create_db_connection($web);
        
        if(!$db)
        {
            return false;
        }
        
        $virtuemart_version = $this->check_version($web);
        
        if($virtuemart_version == '2.0.0.0')
        {
            $query = ' SELECT *   
                       FROM `'.$db->dbprefix('virtuemart_products').'` as `products` 
                       WHERE `products`.`virtuemart_product_id` = \''.(int)$product_id.'\' 
            ';
        }
        elseif($virtuemart_version == '1.0.0.0')
        {
            $query = ' SELECT *   
                       FROM `'.$db->dbprefix('vm_product').'` as `products` 
                       WHERE `products`.`product_id` = \''.(int)$product_id.'\' 
            ';
            
        }
                
        $result = $db->query($query);
        
        if($result->num_rows() == 1)
        {
            return true;
        }
                
        return false;
    }
    
    /**
     * Return single product
     * @param string $web
     * @param int $product_id
     * @return mixed
     */
    public function get_product($web, $product_id)
    {
        $db = $this->create_db_connection($web);
        
        if(!$db)
        {
            return false;
        }
        
        $virtuemart_version = $this->check_version($web);
        
        if($virtuemart_version == '2.0.0.0')
        {
            $query = ' SELECT *   
                       FROM `'.$db->dbprefix('virtuemart_products').'` as `products` 
                       WHERE `products`.`virtuemart_product_id` = \''.(int)$product_id.'\' 
            ';
        }
        elseif($virtuemart_version == '1.0.0.0')
        {
            $query = ' SELECT *   
                       FROM `'.$db->dbprefix('vm_product').'` as `products` 
                       WHERE `products`.`product_id` = \''.(int)$product_id.'\' 
            ';
        }
                
        $result = $db->query($query);
        
        if($result->num_rows() == 1)
        {
            return $result->row();
        }
                
        return false;
    }
    
    /**
     * Return all customer reviews of this web shop
     * @param string $web
     * @param string $start_date_from
     * @return mixed
     */
    public function get_all_reviews($web, $start_date_from = '1970-01-01 00:00:00')
    {
        $db = $this->create_db_connection($web);
        
        if(!$db)
        {
            return false;
        }
        
        $virtuemart_version = $this->check_version($web);
        
        if($virtuemart_version == '2.0.0.0')
        {
            $query = ' SELECT *   
                       FROM `'.$db->dbprefix('virtuemart_rating_reviews').'` as `reviews` 
                       WHERE `reviews`.`created_on` > \''.$start_date_from.'\' 
            ';
        }
        elseif($virtuemart_version == '1.0.0.0')
        {
            $query = ' SELECT *, `product_id` as `virtuemart_product_id`, 
                       from_unixtime(`time`, \'%Y-%m-%d %h:%i:%s\') as `created_on`, `user_rating` as `review_rating`, 
                       `userid` as `created_by`, `review_id` as `virtuemart_rating_review_id` 
                       FROM `'.$db->dbprefix('vm_product_reviews').'` as `reviews` 
                       WHERE `reviews`.`time` > \''.$start_date_from.'\' 
            ';
        }
                
        $result = $db->query($query);
        
        if($result->num_rows() > 0)
        {
            return $result->result();
        }
                
        return false;
    }
}