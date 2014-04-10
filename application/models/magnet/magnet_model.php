<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of magnet
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Magnet_model extends CI_Model
{
    const TEMPLATES_PATH = 'views/magnet/email_templates/';
    
    private $_templates = array();
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        
        $this->load->helper('file');
        
        $this->load->model('virtuemart/virtuemart_model');
        $this->load->model('incomes/web_field_model');
        
        $this->load->library('parser');
        $this->load->library('email');
    }
    
    public function get_template($language_code)
    {
        if(isset($this->_templates[$language_code]))
        {
            return $this->_templates[$language_code];
        }
        
        $email = new stdClass();
        
        $email->body    = read_file(APPPATH . self::TEMPLATES_PATH . 'email_body_' . $language_code . '.php');
        $email->subject = read_file(APPPATH . self::TEMPLATES_PATH . 'email_subject_' . $language_code . '.php');
                
        $this->_templates[$language_code] = $email;
        
        return $email;
    }
    
    public function update_template($language_code)
    {
        $post_data = $this->input->post();
       
        if(isset($post_data['body']))
        {
            write_file(APPPATH . self::TEMPLATES_PATH . 'email_body_' . $language_code . '.php', $post_data['body']);
        }
        
        if(isset($post_data['subject']))
        {
            write_file(APPPATH . self::TEMPLATES_PATH . 'email_subject_' . $language_code . '.php', $post_data['subject']);
        }
    }
    
    private function prepare_link ($web, $product_id, $user_id, $product_name, $order_number)
    {
        $virtuemart_version = $this->virtuemart_model->check_version($web);
        $web_site           = $this->web_field_model->get_web_field($web);
        $link               = '';
        $lang               = $this->get_sef_language($web, $order_number);
        
        if(empty($product_id) || empty($user_id) || empty($product_name))
        {
            return false;
        }
        
        $token = $this->get_token($product_id, $user_id);
                
        if($virtuemart_version == '2.0.0.0')
        {
            $link     = 'http://';
            $link    .= $web_site->url . '/';
            $link    .= ''.$lang.'/?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product_id.'&userId='.$user_id.'&token='.$token.'#reviewform';
        }
        elseif($virtuemart_version == '1.0.0.0')
        {
            $link     = 'http://';
            $link    .= $web_site->url . '/';
            $link    .= 'index.php?page=shop.product_details&flypage=flypage.tpl&product_id='.$product_id.'&userId='.$user_id.'&option=com_virtuemart&token='.$token.'#reviewform';
        }
        
        if(!empty($link))
        {
            $link = anchor($link, $product_name);
        }
        
        return $link;
    }
    
    private function get_sef_language($web, $order_number)
    {
        $language_tag = $this->virtuemart_model->get_language_tag_of_order($web, $order_number);
        $rules = $this->get_language_rules();
        
        if(!empty($language_tag))
        {
            return $rules[$language_tag];
        }
        
        // Default
        return 'es';
    }
    
    private function get_language_rules()
    {
        return array(
            // Language Tag = > URL Language Code
            
            'nl-NL' => 'nl',
            'en-AU' => 'au',
            'en-GB' => 'en',
            'en-US' => 'us',
            'es-ES' => 'es',
            'fr-FR' => 'fr',
            'de-DE' => 'de',
            'it-IT' => 'it',
            'nn-NO' => 'no',
            'pt-PT' => 'pt',
            'sv-SE' => 'se'
        );
    }
    
    private function get_token($product_id, $user_id)
    {
        return md5($product_id.$user_id.'BUYIN');
    }
    
    /**
     * This function return an array of orders, that should receive the email
     * @return array array of objects
     */
    private function get_orders()
    {
        
        $date_from = date('Y-m-d', time() - (30 * 24 * 60 * 60) ); 
        $date_to   = date('Y-m-d', time() - (9 * 24 * 60 * 60) );
        
        $query = ' SELECT `pedido`, `nombre`, `nombre` as `name`, 
                          `fechaentrada` as `date`, `pedido` as `order_number`, 
                          `correo` as `email`, `web`, `id`  
                   FROM `pedidos` 
                   WHERE `fechaentrada` BETWEEN \''.$date_from.'\' AND  \''.$date_to.'\'  
                   AND   `magnet_msg_received` = 0 
                   AND (`procesado` = \'ENVIADO_GLS\' 
                         OR `procesado` = \'ENVIADO_GRUTINET\' 
                         OR `procesado` = \'ENVIADO_FEDEX\' 
                         OR `procesado` = \'ENVIADO_MEGASUR\' 
                         OR `procesado` = \'ENVIADO_MARABE\' 
                         OR `procesado` = \'ENVIADO_PACK\' 
                         OR `procesado` = \'ENVIADO_TOURLINE\' 
                        )
                    ';
        
        $result = $this->db->query($query);
        
        if($result)
        {
            return $result->result();
        }
        
        return false;
        
    }
    
    private function marked_as_received_msg($order_id)
    {
        
//        $query = ' UPDATE `pedidos` 
//                   SET `magnet_msg_received` = 1 
//                   WHERE `id` = '.(int)$order_id.' 
//        ';
//        
//        $this->db->query($query);
        
    }
    
    public function send_email_messages()
    {
        
        $return = array();
        
        try
        {
            $orders = $this->get_orders();
        
            if(count($orders) > 0 && is_array($orders))
            {
                foreach ($orders as $order)
                {
                    
                    $recipient = filter_var($order->email, FILTER_VALIDATE_EMAIL);
//                    $recipient = 'alexbassmusic@gmail.com';
                    
                    if(!$recipient)
                    {
                        continue;
                    }
                    
                    $virtuemart_order = $this->virtuemart_model->get_order($order->web, $order->order_number);
                    
                    if(!is_array($virtuemart_order))
                    {
                        continue;
                    }
                    
                    foreach ($virtuemart_order as $order_item)
                    {
                        $product_id     = null;
                        $user_id        = null;
                        $product_name   = null;
                        
                        if($this->virtuemart_model->is_product_exists($order->web, $order_item->virtuemart_product_id))
                        {
                            $product_id    = $order_item->virtuemart_product_id; 
                            $user_id       = $order_item->virtuemart_user_id;
                            $product_name  = $order_item->order_item_name;
                        }
                        
                        if(!empty($product_id) && !empty($user_id) && !empty($product_name))
                        {
                            break;
                        }
                    }
                    
                    $link = $this->prepare_link($order->web, $product_id, $user_id, $product_name, $order->order_number);
                    
                    if(empty($link))
                    {
                        continue;
                    }
                    
                    $email = $this->prepare_email($order->web, $order->name, $link, $order->order_number);
                    
                    $email_sent = $this->send_email_to($recipient, $email);
                    
                    if($email_sent)
                    {
                        $return[] = $recipient;
                        $return[] = $email;
                        $this->marked_as_received_msg($order->id);
                    }
                }
                
                return $return;
            }
            else
            {
                $msg = 'Magnet. Have no orders to send';
            
                log_message('INFO', $msg);
            }
                        
        }
        catch(Exception $e)
        {
            log_message('ERROR', 'Magnet Exception. ' . $e->getMessage());
        }
    }
            
    private function prepare_email($web, $customer_name, $link, $order_name)
    {
        
        $web_site = $this->web_field_model->get_web_field($web);
        
        $email = new stdClass();
        
        if($web_site)
        {
            $template_language = $this->virtuemart_model->get_template_prefix($web, $order_name);
            
            $template = $this->get_template($template_language);
            
            $email->from  = $web_site->email;
            $email->title = $web_site->title;
            
            $subject['name']    = humanize($customer_name);
            $subject['website'] = $web_site->url;
            $subject['product_link'] = $link;
            $subject['info_email'] = mailto($web_site->email);
            
            $email->subject = $this->parser->parse_string($template->subject, $subject, TRUE);
            
            $body['name']    = humanize($customer_name);
            $body['website'] = anchor('http://'.$web_site->url, $web_site->url);
            $body['product_link'] = $link;
            $body['info_email'] = mailto($web_site->email);
            
            $email->body    = $this->parser->parse_string($template->body, $body, TRUE);
            
            return $email;
        }
        
        return false;
    }
    
    private function send_email_to($recipient, $email)
    {
        $config['charset'] = 'utf-8';
        $this->email->initialize($config);
        $this->email->from($email->from, $email->title);
        $this->email->reply_to($email->from, $email->title);
        $this->email->set_mailtype('html');
        $this->email->to($recipient);
        $this->email->subject($email->subject);
        $this->email->message($email->body);      
        if($this->email->send())
        {
            return true;
        }
        else
        {
            $msg = 'Magnet. Cant send the message. '. $this->email->print_debugger();
            log_message('INFO', $msg);
            return false;
        }
        
    }
    
}