<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Unit testing controller
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

class Unit_testing extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        
        // Authorization check
        if (!$this->ion_auth->logged_in())
        {
           redirect('auth/login');
        }
        
        // Only admin have access
        if (!$this->ion_auth->is_admin()) 
        {
            show_404();
            die;
        }
        
        $this->load->library('unit_test');
    }
    
    public function index()
    {
        $data['title'] = humanize($this->router->class);
        $data['test_name'] = '';
        $data['result'] = '';
        
        // Load view 
        $this->load->template(strtolower($this->router->class).'/index', $data);
    }
    
    public function shipping_costs_model()
    {
        
        $this->load->model('incomes/shipping_costs_model');
        
        $test_name = humanize($this->router->method);
        $data['test_name'] = $test_name;
        $data['title'] = humanize($this->router->class);
        
        $test_values = array(
            
            array(
                'web' => '',
                'country_code' => '',
                'shipping_phrase' => '',
                'expected_result' => ''
                ),
            array(
                'web' => '',
                'country_code' => '',
                'shipping_phrase' => '',
                'expected_result' => ''
                ),
            array(
                'web' => '',
                'country_code' => '',
                'shipping_phrase' => '',
                'expected_result' => ''
                ),
            array(
                'web' => '',
                'country_code' => '',
                'shipping_phrase' => '',
                'expected_result' => ''
                ),
            array(
                'web' => '',
                'country_code' => '',
                'shipping_phrase' => '',
                'expected_result' => ''
                ),
            array(
                'web' => '',
                'country_code' => '',
                'shipping_phrase' => '',
                'expected_result' => ''
                ),
            array(
                'web' => '',
                'country_code' => '',
                'shipping_phrase' => '',
                'expected_result' => ''
                ),
            array(
                'web' => '',
                'country_code' => '',
                'shipping_phrase' => '',
                'expected_result' => ''
                ),
            array(
                'web' => '',
                'country_code' => '',
                'shipping_phrase' => '',
                'expected_result' => ''
                ),
            array(
                'web' => '',
                'country_code' => '',
                'shipping_phrase' => '',
                'expected_result' => ''
                ),
            array(
                'web' => '',
                'country_code' => '',
                'shipping_phrase' => '',
                'expected_result' => ''
                ),
            array(
                'web' => '',
                'country_code' => '',
                'shipping_phrase' => '',
                'expected_result' => ''
                )
            
            
        );
        
        foreach ($test_values as $test_value)
        {
            
            $data['result'] .= $this->unit->run( 
                    $this->shipping_costs_model->get_shipping_price(
                            $test_value['web'],
                            $test_value['country_code'],
                            $test_value['shipping_phrase']
                            ), 
                    $test_value['expected_result'],
                    $test_name,
                    $notes);
        }
        
        
        // Load view 
        $this->load->template(strtolower($this->router->class).'/index', $data);
    }
    
    public function shipping_companies_model()
    {
        
        $this->load->model('incomes/shipping_companies_model');
        
        $test_name = humanize($this->router->method);
        $data['test_name'] = $test_name;
        $data['title'] = humanize($this->router->class);
        
        $test_values = array(
            
            array(
                'shipping_phrase' => 'standard_shipping|SEUR/MRW|Tarifa 24/48 horas Peninsula|5.95|1',
                'expected_result' => false
                ),
            array(
                'shipping_phrase' => 'standard_shipping|Fedex|Livraison à domicile (Fedex  3-4 jours)|8.90|10',
                'expected_result' => 'Fedex'
                ),
            array(
                'shipping_phrase' => 'standard_shipping|GLS|GLS 3-4 jours|6.90|13',
                'expected_result' => 'GLS'
                ),
            array(
                'shipping_phrase' => 'standard_shipping|Fedex|European Union 72/96 hours|6.90|5',
                'expected_result' => 'Fedex'
                ),
            array(
                'shipping_phrase' => 'standard_shipping|Fedex|Fedex Israel|29.90|11',
                'expected_result' => 'Fedex'
                ),
            array(
                'shipping_phrase' => 'standard_shipping|GLS|GLS 3-4 jours|6.90|13standard_standard_shipping|Fedex|Fedex Israel|29.90|11shipping|GLS|GLS 3-4 jours|6.90|13standard_shipping|GLS|GLS 3-4 jours|6.90|13',
                'expected_result' => 'GLS'
                ),
            array(
                'shipping_phrase' => 'standard_shipping|Fedex|European Union 72/96 hours|6.90|5',
                'expected_result' => 'Fedex'
                ),
            array(
                'shipping_phrase' => 'standard_shipping|SEUR/MRW|Tarifa 24/48 horas Peninsula|5.95|1',
                'expected_result' => false
                ),
            array(
                'shipping_phrase' => 'standard_shipping|Fedex|European Union 72/96 hours|6.90|5',
                'expected_result' => 'Fedex'
                ),
            array(
                'shipping_phrase' => 'mondialrelay|MONDIALRELAY|074896|3.90|1',
                'expected_result' => 'Mondial Relay'
                ),
            array(
                'shipping_phrase' => 'GLS|Fedex|mondialrelay|MONDIALRELAY|074896|3.90|1',
                'expected_result' => 'Mondial Relay'
                ),
            array(
                'shipping_phrase' => 'Fedexfedexglsgls|MONDIALRELAY|074896|3.90|1MONDIALRELAYMONDIALRELAY',
                'expected_result' => 'Mondial Relay'
                ),
            array(
                'shipping_phrase' => 'test',
                'expected_result' => false
                )
            
        );
        
        foreach ($test_values as $test_value)
        {
            
            $data['result'] .= $this->unit->run( 
                    $this->shipping_companies_model->find_company_by_key_phrase(
                            $test_value['shipping_phrase']
                            )->company_name, 
                    $test_value['expected_result'],
                    $test_name,
                    'Expected: '.$test_value['expected_result'].
                    ',<br> Received: '.
                    $this->shipping_companies_model->find_company_by_key_phrase(
                            $test_value['shipping_phrase']
                            )->company_name);
        }
        
        
        // Load view 
        $this->load->template(strtolower($this->router->class).'/index', $data);
    }
    
    
    public function shipping_types_model()
    {
        
        $this->load->model('incomes/shipping_types_model');
        
        $test_name = humanize($this->router->method);
        $data['test_name'] = $test_name;
        $data['title'] = humanize($this->router->class);
        
        $test_values = array(
            
            array(
                'phrase' => 'standard_shipping|SEUR/MRW|Tarifa 24/48 horas Peninsula|5.95|1',
                'expected_result' => 'Standard'
                ),
            array(
                'phrase' => 'standard_shipping|Fedex|Livraison à domicile (Fedex  3-4 jours)|8.90|10',
                'expected_result' => 'Standard'
                ),
            array(
                'phrase' => 'standard_shipping|GLS|GLS 3-4 jours|6.90|13',
                'expected_result' => 'Standard'
                ),
            array(
                'phrase' => 'standard_shipping|Fedex|European Union 72/96 hours|6.90|5',
                'expected_result' => 'Standard'
                ),
            array(
                'phrase' => 'standard_shipping|Fedex|Fedex Israel|29.90|11',
                'expected_result' => 'Standard'
                ),
            array(
                'phrase' => 'standard_shipping|GLS|GLS 3-4 jours|6.90|13standard_standard_shipping|Fedex|Fedex Israel|29.90|11shipping|GLS|GLS 3-4 jours|6.90|13standard_shipping|GLS|GLS 3-4 jours|6.90|13',
                'expected_result' => 'Standard'
                ),
            array(
                'phrase' => 'free_shipping|Ofertas PC Stock|Envío gratuito|0|1|70.23|390.15|0.00|0|',
                'expected_result' => 'Free Shipping'
                ),
            array(
                'phrase' => 'standard_shipping|SEUR/MRW|Tarifa 24/48 horas Peninsula|5.95|1',
                'expected_result' => 'Standard'
                ),
            array(
                'phrase' => 'standard_shipping|Fedex|European Union 72/96 hours|6.90|5',
                'expected_result' => 'Standard'
                ),
            array(
                'phrase' => 'mondialrelay|MONDIALRELAY|074896|3.90|1',
                'expected_result' => false
                ),
            array(
                'phrase' => 'GLS|Fedex|mondialrelay|MONDIALRELAY|074896|3.90|1',
                'expected_result' => false
                ),
            array(
                'phrase' => 'Fedexfedexglsgls|MONDIALRELAY|074896|3.90|1MONDIALRELAYMONDIALRELAY',
                'expected_result' => false
                ),
            array(
                'phrase' => 'testFedexfedexglsgls|MONDIALRELAY|074896|3.90|1MONDIALRELAYMONDIALRELAY',
                'expected_result' => 'Test type'
                ),
            array(
                'phrase' => 'test',
                'expected_result' => 'Test type'
                )
            
        );
        
        foreach ($test_values as $test_value)
        {
            
            $data['result'] .= $this->unit->run( 
                    $this->shipping_types_model->find_type_by_key_phrase(
                            $test_value['phrase']
                            )->shipping_type_name, 
                    $test_value['expected_result'],
                    $test_name,
                    'Expected: '.$test_value['expected_result'].
                    ',<br> Received: '.
                    $this->shipping_types_model->find_type_by_key_phrase(
                            $test_value['phrase']
                            )->shipping_type_name);
        }
        
        
        // Load view 
        $this->load->template(strtolower($this->router->class).'/index', $data);
    }
    
    public function virtuemart_model()
    {
        $this->load->model('virtuemart/virtuemart_model');
        
        $test_name = humanize($this->router->method);
        $data['test_name'] = $test_name;
        $data['title'] = humanize($this->router->class);
        $data['result'] = '';
        
        $test_values = array(
            
           
            array(
                'phrase' => 'BUYIN',
                'expected_result' => '2.0.0.0'
                ),
            array(
                'phrase' => 'COSMETICS',
                'expected_result' => '1.0.0.0'
                ),
            array(
                'phrase' => 'TUFARMACIAONLINE',
                'expected_result' => '2.0.0.0'
                ),
            array(
                'phrase' => 'KOSMETIK',
                'expected_result' => '1.0.0.0'
                ),
            array(
                'phrase' => 'TEST',
                'expected_result' => false
                ),
            
            
        );
        
        foreach ($test_values as $test_value)
        {
            
            $data['result'] .= $this->unit->run( 
                    $this->virtuemart_model->check_version(
                            $test_value['phrase']
                            ), 
                    $test_value['expected_result'],
                    $test_name,
                    'Expected: '.$test_value['expected_result'].
                    ',<br> Received: '.
                    $this->virtuemart_model->check_version(
                            $test_value['phrase']
                            ));
        }
        
        // Load view 
        $this->load->template(strtolower($this->router->class).'/index', $data);
    }
    
}