<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tracking model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Tracking_model extends CI_Model
{
    
    private $_tracking_data = array(), $_amazon_file_data = array(), $_list_of_tracking_to_our_websites = array();
    private $_web_fields = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        
        // Load parser class
        $this->load->library('parser');
        
        // Load file helper
        $this->load->helper('file');
        $this->load->helper('my_string_helper');
        
        // Load models
        $this->load->model('dashboard/dashboard_model');
        $this->load->model('incomes/shipping_companies_model');
        $this->load->model('virtuemart/virtuemart_model');
    }
    
    public function save_tracking()
    {
        $post_data = $this->input->post();
                
        if($this->update_order($post_data))
        {
           return $this->prepare_email($post_data);
        }
        
        return false;
    }
    
    /**
     * Save order
     * 
     */
    private function update_order($post_data)
    {
        unset($post_data['id_shipping_company']);
        unset($post_data['state']);
        unset($post_data['conflict']);
        return $this->dashboard_model->save($post_data);
    }
    
    private function prepare_email($post_data)
    {
        $this->load->library('email');
        
        $web_field = $this->get_web_field($post_data);
                
        if(empty($web_field) || empty($post_data['correo']))
        {
            return false;
        }
        
        $this->email->from($web_field->email, $web_field->title);
        $this->email->reply_to($web_field->email, $web_field->title);
        $this->email->set_mailtype('html');
        $this->email->to($post_data['correo']);
//        $this->email->to('alexander.begoon@gmail.com');
        $this->email->subject($this->get_subject($post_data));
        $this->email->message($this->get_message($post_data));                   
        if($this->email->send())
        {
            return true;
        }
        else
        {
            log_message('ERROR', $this->email->print_debugger());
            echo 'Error. Cant send message.';
        }
    }
    
    private function get_subject($post_data)
    {        
        $shipping_company = $this->shipping_companies_model->getCompany($post_data['id_shipping_company']);
        
        $web_field = $this->get_web_field($post_data);
        
        $data = array();
        
        $data['nombrecliente']          = $post_data['nombre'];
        $data['entradapagina']          = $web_field->title;
        $data['tracking']               = $post_data['tracking'];
        $data['pedido']                 = $post_data['pedido'];
        $data['transporte_nombre']      = $shipping_company->company_code;
        $data['transporte_direccion']   = 'http://' . str_replace('http://', '', str_replace('https://','',$shipping_company->company_website));
        $data['pagina']                 = $web_field->url;
        $data['emailrespuesta']         = $web_field->email;
        
        if(empty($web_field))
        {
            return false;
        }
        
        $template_language = $this->virtuemart_model->get_template_prefix($post_data['web'], $post_data['pedido']);
        
        if( !$this->is_template_exists($template_language, 'tracking/email_templates/subject_') )
        {
            $template_language = 'es';
        }
        
        return $this->parser->parse('tracking/email_templates/subject_'.$template_language, $data, true);
    }
    
    private function get_message($post_data)
    {
        $web_field = $this->get_web_field($post_data);
        
        if(empty($web_field))
        {
            return false;
        }
        $shipping_company = $this->shipping_companies_model->getCompany($post_data['id_shipping_company']);
        
        $data = array();
        
        $data['nombrecliente']          = $post_data['nombre'];
        $data['entradapagina']          = $web_field->title;
        $data['tracking']               = $post_data['tracking'];
        $data['pedido']                 = $post_data['pedido'];
        $data['transporte_nombre']      = $shipping_company->company_code;
        $data['transporte_direccion']   = 'http://' . str_replace('http://', '', str_replace('https://','',$shipping_company->company_website));
        $data['pagina']                 = $web_field->url;
        $data['emailrespuesta']         = $web_field->email;
        
        $template_language = $this->virtuemart_model->get_template_prefix($post_data['web'], $post_data['pedido']);
        
        if( !$this->is_template_exists($template_language, 'tracking/email_templates/email_') )
        {
            $template_language = 'es';
        }
        
        return $this->parser->parse('tracking/email_templates/email_'.$template_language, $data, true);
    }
    
    private function is_template_exists($template_language, $file_path)
    {
        if(read_file(FCPATH.APPPATH.'views/'.$file_path.$template_language.'.php'))
        {
            return true;
        }
        
        return false;
    }

    private function get_web_field($post_data)
    {
        if(isset($this->_web_fields[$post_data['web']]))
        {
            return $this->_web_fields[$post_data['web']];
        }
        if(!empty($post_data['web']))
        {
            // Load model
            $this->load->model('incomes/web_field_model');
            
            $web = $this->web_field_model->get_web_field($post_data['web']);
            
            if($web)
            {
                $this->_web_fields[$post_data['web']] = $web;
                return $this->_web_fields[$post_data['web']];
            }
        }
        
        return false;
    }
    
    public function get_email_template($lang)
    {
        return read_file(FCPATH.'application/views/tracking/email_templates/email_'.$lang.'.php');
    }
    
    public function get_subject_template($lang)
    {
        return read_file(FCPATH.'application/views/tracking/email_templates/subject_'.$lang.'.php');
    }
    
    public function save_template($post_data)
    {
        write_file(FCPATH.'application/views/tracking/email_templates/subject_'.$post_data['lang'].'.php', $post_data['subject']);
        write_file(FCPATH.'application/views/tracking/email_templates/email_'.$post_data['lang'].'.php', $post_data['message']);
    }
    
    public function process_tracking_file($file_path)
    {
        $data = $this->parse_file($file_path);
        
        $amazon_file_data = array();
        
        foreach ($data as $row)
        {
            if($row['state'] == '5')
            {
                $orders = $this->dashboard_model->get_order_by_pedido($row['pedido'], true);
            
                if(count($orders) == 1 && is_array($orders))
                {
                    if(stripslashes($orders[0]->nombre) == stripslashes($row['nombre']))
                    {
                        $params = array(    'id'                   => (int)$orders[0]->id,
                                            'correo'               => $orders[0]->correo,
                                            'web'                  => $orders[0]->web,
                                            'id_shipping_company'  => (int)$this->shipping_companies_model->get_company_by_code('GLS')->id,
                                            'nombre'               => $orders[0]->nombre,
                                            'pedido'               => $orders[0]->pedido,
                                            'tracking'             => $row['tracking'],
                                            'state'                => $row['state']
                                        );

                        if($orders[0]->web == 'AMAZON')
                        {
                            $amazon_file_data[] = array('order-id'          => $orders[0]->pedido, 
                                                        'ship-date'         => date('Y-m-d', time()),
                                                        'carrier-code'      => 'GLS',
                                                        'tracking-number'   => $row['tracking'],
                                                        'ship-method'       => 'Standard'
                                                        );
                            if($this->update_order($params))
                            {
                                $this->prepare_email($params);
                            }
                        }
                        else
                        {
                            if($this->update_order($params))
                            {
                                $this->_list_of_tracking_to_our_websites[] = $params;
                                $this->prepare_email($params);
                            }
                        }
                    }
                    else
                    {
                        if($orders[0]->web != 'AMAZON' && !empty($orders[0]->web) && strpos($orders[0]->web, 'AMAZON') === false)
                        {
                            $params = array(    'id'                   => (int)$orders[0]->id,
                                                'correo'               => $orders[0]->correo,
                                                'web'                  => $orders[0]->web,
                                                'id_shipping_company'  => (int)$this->shipping_companies_model->get_company_by_code('GLS')->id,
                                                'nombre'               => $orders[0]->nombre,
                                                'pedido'               => $orders[0]->pedido,
                                                'tracking'             => $row['tracking'],
                                                'state'                => $row['state'],
                                                'conflict'             => true
                               );
                            $this->_list_of_tracking_to_our_websites[] = $params;
                        } 
                        elseif($orders[0]->web == 'AMAZON' && getInnerSubstring($orders[0]->pedido,'-') == $row['pedido']) 
                        {
                            $params = array(    'id'                   => (int)$orders[0]->id,
                                                'correo'               => $orders[0]->correo,
                                                'web'                  => $orders[0]->web,
                                                'id_shipping_company'  => (int)$this->shipping_companies_model->get_company_by_code('GLS')->id,
                                                'nombre'               => $orders[0]->nombre,
                                                'pedido'               => $orders[0]->pedido,
                                                'tracking'             => $row['tracking'],
                                                'state'                => $row['state'],
                                                'conflict'             => true
                               );
                            $this->_list_of_tracking_to_our_websites[] = $params;
                        }
                    }
                }

                if(count($orders) > 1 && is_array($orders))
                {
                    foreach($orders as $order)
                    {
                        if(stripslashes($order->nombre) == stripslashes($row['nombre']))
                        {
                            $params = array(    'id'                   => (int)$order->id,
                                                'correo'               => $order->correo,
                                                'web'                  => $order->web,
                                                'id_shipping_company'  => (int)$this->shipping_companies_model->get_company_by_code('GLS')->id,
                                                'nombre'               => $order->nombre,
                                                'pedido'               => $order->pedido,
                                                'tracking'             => $row['tracking'],
                                                'state'                => $row['state'],
                                                'conflict'             => false
                               );
                            
                            if($order->web == 'AMAZON')
                            {
                                $amazon_file_data[] = array('order-id'          => $order->pedido, 
                                                            'ship-date'         => date('Y-m-d', time()),
                                                            'carrier-code'      => 'GLS',
                                                            'tracking-number'   => $row['tracking'],
                                                            'ship-method'       => 'Standard'
                                                            );
                                if($this->update_order($params))
                                {
                                    $this->prepare_email($params);
                                }
                            }
                            else
                            {
                                if($this->update_order($params))
                                {
                                    $this->_list_of_tracking_to_our_websites[] = $params;
                                    $this->prepare_email($params);
                                }
                            }
                        }
                        else
                        {
                            if($order->web != 'AMAZON' && !empty($order->web) && strpos($order->web, 'AMAZON') === false)
                            {
                                $params = array(    'id'                   => (int)$order->id,
                                                    'correo'               => $order->correo,
                                                    'web'                  => $order->web,
                                                    'id_shipping_company'  => (int)$this->shipping_companies_model->get_company_by_code('GLS')->id,
                                                    'nombre'               => $order->nombre,
                                                    'pedido'               => $order->pedido,
                                                    'tracking'             => $row['tracking'],
                                                    'state'                => $row['state'],
                                                    'conflict'             => true
                                   );
                                $this->_list_of_tracking_to_our_websites[] = $params;
                            }
                            elseif($order->web == 'AMAZON' && getInnerSubstring($order->pedido, '-') == $row['pedido'])
                            {
                                $params = array(    'id'                   => (int)$order->id,
                                                    'correo'               => $order->correo,
                                                    'web'                  => $order->web,
                                                    'id_shipping_company'  => (int)$this->shipping_companies_model->get_company_by_code('GLS')->id,
                                                    'nombre'               => $order->nombre,
                                                    'pedido'               => $order->pedido,
                                                    'tracking'             => $row['tracking'],
                                                    'state'                => $row['state'],
                                                    'conflict'             => true
                                   );
                                $this->_list_of_tracking_to_our_websites[] = $params;
                            }
                        }
                    }
                }
            }
        }
        
        $this->_amazon_file_data = $amazon_file_data;
        $this->prepare_amazon_file($amazon_file_data);
    }
    
    private function parse_file($file_path)
    {
        $file = read_file($file_path);
        $rows = explode("\n", $file);
        
        foreach ($rows as $row)
        {
            $row = explode('|', $row);
            
            isset($row[0]) ? $row[0] : $row[0] = '';
            isset($row[8]) ? $row[8] : $row[8] = '';
            isset($row[3]) ? $row[3] : $row[3] = '';
            isset($row[10]) ? $row[10] : $row[10] = '';
            
            $this->_tracking_data[] = array('tracking'  => trim($row[0]),
                                            'pedido'    => trim($row[8]),
                                            'state'     => trim($row[3]),
                                            'nombre'    => trim($row[10])
                                            );
        }
        
        return $this->_tracking_data;
    }
    
    private function prepare_amazon_file($amazon_file_data)
    {
        $header = array('order-id', 'ship-date', 'carrier-code', 'tracking-number', 'ship-method');
        
        $file_body = implode("\t", $header) . "\r\n";
        
        foreach ($amazon_file_data as $row)
        {
            $file_body .= $row['order-id'] . "\t" . 
                          $row['ship-date'] . "\t" . 
                          $row['carrier-code'] . "\t" . 
                          $row['tracking-number'] . "\t" . 
                          $row['ship-method'] .  "\r\n";
        }
        
        return write_file(FCPATH.'upload/amazonTracking-'.date('Y-m-d', time()).'.txt', $file_body);
    }
    
    public function get_list_of_tracking_reads()
    {
        return $this->_tracking_data;
    }
    
    public function get_list_of_tracking_send_to_our_websites()
    {
        return $this->_list_of_tracking_to_our_websites;
    }
    
    public function get_list_of_orders_that_go_to_the_amazon_file()
    {
        return $this->_amazon_file_data;
    }
    
    public function is_amazon_file_exists()
    {
        $string = read_file(FCPATH.'upload/amazonTracking-'.date('Y-m-d', time()).'.txt');
        
        if(!empty($string))
        {
            return true;
        }
        
        return false;
    }
    
    public function get_file_amazon_tracking()
    {
        if($this->is_amazon_file_exists())
        {
            $this->load->helper('download');
            $data = read_file(FCPATH.'upload/amazonTracking-'.date('Y-m-d', time()).'.txt'); // Read the file's contents
            $name = 'amazonTracking-'.date('Y-m-d', time()).'.txt';
            force_download($name, $data);
        }
        
        return false;
    }
}