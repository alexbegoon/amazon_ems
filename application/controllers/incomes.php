<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Incomes controller
 *
 * @author      Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Incomes extends CI_Controller {
    
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
         
        // Load model
        $this->load->model('incomes/incomes_model');
        $this->load->model('incomes/web_field_model');
        
    }
    
    public function index($page = null)
    {   
        
        redirect('incomes/page/');
        
    }
    
    public function page($page = null)
    {
        
        // Post data
        $filter = $this->input->post("filter");
        
        // Load data 
        $data['title'] = ucfirst($this->router->class);
        $data['current_month'] = date('F Y', time());
        
        if (!empty($filter['month'])) {
           $data['month_options'] = getMonthsOptions($filter['month']); 
        } else {
           $data['month_options'] = getMonthsOptions(date('m', time()));  
        }
        
        if (!empty($filter['year'])) {
           $data['year_options'] = getYearsOptions($filter['year']);
        } else {
           $data['year_options'] = getYearsOptions(date('Y', time()));  
        }
        
        if (!empty($filter['incomes_summary_year'])) {
           $data['incomes_summary_year_options'] = getYearsOptions($filter['incomes_summary_year']);
        } else {
           $data['incomes_summary_year_options'] = getYearsOptions(date('Y', time()));  
        }
        
        $data['summary'] = $this->incomes_model->getSummary($filter['month'], $filter['incomes_summary_year']);
        
        $data['orders']  = $this->incomes_model->getOrders($page, $filter['month'], $filter['incomes_summary_year']);
        
        $data['total_rows'] = $this->incomes_model->countOrders();
        
        $data['other_costs'] = $this->incomes_model->get_other_costs();
        
        // Pagination
        
        $config['base_url'] = base_url().'index.php/incomes/page/';
        $config['total_rows'] = $this->incomes_model->countOrders();
        $config['per_page'] = 50; 

        $this->pagination->initialize($config); 
        $data['pagination'] = $this->pagination->create_links();
                
        // Load view 
        $this->load->template('incomes/index', $data);
        
    }
                
    public function shipping_companies()
    {
        $post_data = $this->input->post();
        
        $data = array();
        $data['title']          = ucfirst(str_replace('_', ' ', $this->router->method));
        
        // Load model
        $this->load->model($this->router->class.'/'.$this->router->method.'_model');
        
        if (!empty($post_data['task'])) {
            
            switch ($post_data['task']) {
                case 'add' : $this->load->view('incomes/add_'.$this->router->method, $data);
                    break;
                case 'delete'   : $data['response'] = $this->shipping_companies_model->remove((int)$post_data['id']);
                                  $this->load->view('incomes/ajax_response', $data);
                    break;
                case 'edit'     : $data['company'] = $this->shipping_companies_model->getCompany((int)$post_data['id']);
                                  $this->load->view('incomes/edit_'.$this->router->method, $data);
                                  $isUpdated = $this->shipping_companies_model->edit((int)$post_data['id']);
                                  if ($isUpdated){
                                        redirect(current_url());
                                  }
                    break;
                case 'save'     : $this->shipping_companies_model->add(); // Show default after save
                default: 
                    // Load data
                    $data['companies']      = $this->shipping_companies_model->getCompanies();
                    $data['post_data']      = $post_data;

                    // Load view  
                    $this->load->template('incomes/'.$this->router->method, $data);
            }
            
        } else {
            
            // Load data
            $data['companies']      = $this->shipping_companies_model->getCompanies();
            $data['post_data']      = $post_data;

            // Load view  
            $this->load->template('incomes/'.$this->router->method, $data);
            
        }
    }
    
    public function shipping_types()
    {
        $post_data = $this->input->post();
        
        $data = array();
        $data['title']          = ucfirst(str_replace('_', ' ', $this->router->method));
        
        // Load model
        $this->load->model($this->router->class.'/'.$this->router->method.'_model');
        
        if (!empty($post_data['task'])) {
            
            switch ($post_data['task']) {
                case 'add' : $this->load->view('incomes/add_'.$this->router->method, $data);
                    break;
                case 'delete'   : $data['response'] = $this->shipping_types_model->remove((int)$post_data['id']);
                                  $this->load->view('incomes/ajax_response', $data);
                    break;
                case 'edit'     : $data['shipping_type'] = $this->shipping_types_model->get_shipping_type((int)$post_data['id']);
                                  $this->load->view('incomes/edit_'.$this->router->method, $data);
                                  $isUpdated = $this->shipping_types_model->edit((int)$post_data['id']);
                                  if ($isUpdated)
                                  {
                                        redirect(current_url());
                                  }
                    break;
                case 'save'     : $this->shipping_types_model->add(); // Show default after save
                default: 
                    // Load data
                    $data['shipping_types']      = $this->shipping_types_model->get_shipping_types();
                    $data['post_data']      = $post_data;

                    // Load view  
                    $this->load->template('incomes/'.$this->router->method, $data);
            }
            
        } else {
            
            // Load data
            $data['shipping_types']      = $this->shipping_types_model->get_shipping_types();
            $data['post_data']           = $post_data;

            // Load view  
            $this->load->template('incomes/'.$this->router->method, $data);
            
        }
    }
    
    public function shipping_costs($page = 0)
    {
        
        $post_data = $this->input->post();
        
        $data = array();
        $data['title']          = ucfirst(str_replace('_', ' ', $this->router->method));
        
        // Load model
        $this->load->model($this->router->class.'/'.$this->router->method.'_model');
        $this->load->model('incomes/web_field_model');
        $this->load->helper('html');
        
        // Prepare data
        $data['web_field_radio_list']     = $this->web_field_model->get_radio_inputs_web('filter_web');
        $data['shipping_type_radio_list'] = $this->shipping_costs_model->get_radio_inputs_shipping_types('filter_shipping_type_id');
        $data['countries_list'] = $this->shipping_costs_model->getCountries(isset($post_data['filter_country_code']) ? $post_data['filter_country_code'] : null);
        $data['shipping_companies_list'] = $this->shipping_costs_model->getShippingCompanies(isset($post_data['filter_id_shipping_company']) ? $post_data['filter_id_shipping_company'] : null);
        $data['costs']          = $this->shipping_costs_model->getCosts($page);
        $data['post_data']      = $post_data;
        $data['total_rows'] = $this->shipping_costs_model->count_total();
        
        // Pagination
        
        $config['base_url'] = base_url().'index.php/incomes/shipping_costs/';
        $config['total_rows'] = $this->shipping_costs_model->count_total();
        $config['per_page'] = 50;
        
        $this->pagination->initialize($config); 
        $data['pagination'] = $this->pagination->create_links();
        
        if (!empty($post_data['task'])) {
            
            switch ($post_data['task']) {
                case 'add' :        
                                $data['countries_list']             = $this->shipping_costs_model->getCountries();
                                $data['shipping_companies_list']    = $this->shipping_costs_model->getShippingCompanies();
                                $data['web_list']                   = $this->web_field_model->get_web_fields_list(null, 'web', 'id="web_fields_list" form="shipping-costs-form"');
                                $data['shipping_types_list']        = $this->shipping_costs_model->get_shipping_types_list(null, 'shipping_type_id', 'id="shipping_types_list" form="shipping-costs-form"');
                                $this->load->view('incomes/add_'.$this->router->method, $data);
                    break;
                case 'delete'   : $data['response'] = $this->shipping_costs_model->remove((int)$post_data['id']);
                                  $this->load->view('incomes/ajax_response', $data);
                    break;
                case 'edit'     : 
                                  $data['cost'] = $this->shipping_costs_model->getPrice((int)$post_data['id']);  
                                  $data['countries_list'] = $this->shipping_costs_model->getCountries($data['cost']->country_code);
                                  $data['shipping_companies_list'] = $this->shipping_costs_model->getShippingCompanies($data['cost']->id_shipping_company);
                                  $data['web_list']                = $this->web_field_model->get_web_fields_list($data['cost']->web, 'web', 'id="web_fields_list" form="shipping-costs-form"');
                                  $data['shipping_types_list']     = $this->shipping_costs_model->get_shipping_types_list($data['cost']->shipping_type_id, 'shipping_type_id', 'id="shipping_types_list" form="shipping-costs-form"');
                                  $this->load->view('incomes/edit_'.$this->router->method, $data);
                                  $isUpdated = $this->shipping_costs_model->edit((int)$post_data['id']);
                                  if ($isUpdated){
                                        redirect(current_url());
                                  }
                    break;
                case 'update'   :
                                  $this->shipping_costs_model->edit((int)$post_data['id']);
                    
                                  // Load data
                                  $data['costs']      = $this->shipping_costs_model->getCosts($page);
                                  $data['post_data']      = $post_data;

                                    // Load view  
                                    $this->load->template('incomes/'.$this->router->method, $data);
                    break;
                case 'save'     : $this->shipping_costs_model->add(); // Show default after save
                default: 
                    // Load data
                    $data['costs']      = $this->shipping_costs_model->getCosts($page);
                    $data['post_data']      = $post_data;

                    // Load view  
                    $this->load->template('incomes/'.$this->router->method, $data);
            }
            
        } else {
            
            // Load view  
            $this->load->template('incomes/'.$this->router->method, $data);
            
        }
        
    }
    
    public function taxes()
    {
        $post_data = $this->input->post();
        
        $data = array();
        $data['title']          = ucfirst(str_replace('_', ' ', $this->router->method));
        
        // Load model
        $this->load->model($this->router->class.'/'.$this->router->method.'_model');
        
        if (!empty($post_data['task'])) {
            
           switch ($post_data['task']) {
                case 'add' :    
                                  $this->load->view('incomes/add_'.$this->router->method, $data);
                    break;
                case 'delete'   : $data['response'] = $this->taxes_model->remove((int)$post_data['id']);
                                  $this->load->view('incomes/ajax_response', $data);
                    break;
                case 'edit'     : 
                                  $data['tax'] = $this->taxes_model->getTax((int)$post_data['id']);
                                  $this->load->view('incomes/edit_'.$this->router->method, $data);
                                  $isUpdated = $this->taxes_model->edit((int)$post_data['id']);
                                  if ($isUpdated){
                                        redirect(current_url());
                                  }
                    break;
                case 'save'     : $this->taxes_model->add(); // Show default after save
                default: 
                    // Load data
                    $data['taxes']          = $this->taxes_model->getTaxes();
                    $data['post_data']      = $post_data;

                    // Load view  
                    $this->load->template('incomes/'.$this->router->method, $data);
            }
            
        } else {
            
            // Load data
            $data['taxes']          = $this->taxes_model->getTaxes();
            $data['post_data']      = $post_data;

            // Load view  
            $this->load->template('incomes/'.$this->router->method, $data);
            
        }
        
    }
    
    public function exchange_rates()
    {
        
        $post_data = $this->input->post();
        
        $data = array();
        $data['title']          = ucfirst(str_replace('_', ' ', $this->router->method));
        
        // Load model
        $this->load->model($this->router->class.'/'.$this->router->method.'_model');
        
        if (!empty($post_data['task'])) {
            
           switch ($post_data['task']) {
                case 'add' :    
                                  $data['currencies_list'] = $this->exchange_rates_model->getCurrenciesList();
                                  $this->load->view('incomes/add_'.$this->router->method, $data);
                    break;
                case 'delete'   : $data['response'] = $this->exchange_rates_model->remove((int)$post_data['id']);
                                  $this->load->view('incomes/ajax_response', $data);
                    break;
                case 'edit'     : 
                                  $data['rate'] = $this->exchange_rates_model->getRate((int)$post_data['id']);
                                  $data['currencies_list'] = $this->exchange_rates_model->getCurrenciesList((int)$data['rate']->currency_id);
                                  $this->load->view('incomes/edit_'.$this->router->method, $data);
                                  $isUpdated = $this->exchange_rates_model->edit((int)$post_data['id']);
                                  if ($isUpdated){
                                        redirect(current_url());
                                  }
                    break;
                case 'save'     : $this->exchange_rates_model->add(); // Show default after save
                default: 
                    // Load data
                    $data['rates']          = $this->exchange_rates_model->getRates();
                    $data['post_data']      = $post_data;

                    // Load view  
                    $this->load->template('incomes/'.$this->router->method, $data);
            }
            
        } else {
            
            // Load data
            $data['rates']          = $this->exchange_rates_model->getRates();
            $data['post_data']      = $post_data;

            // Load view  
            $this->load->template('incomes/'.$this->router->method, $data);
            
        }
        
        
    }
    
    public function providers()
    {
        $post_data = $this->input->post();
        
        $data = array();
        $data['title']          = humanize($this->router->method);
        
        // Load model
        $this->load->model($this->router->class.'/'.$this->router->method.'_model');
        
        if (!empty($post_data['task'])) {
            
           switch ($post_data['task']) {
                case 'add' :    
                                  $this->load->view('incomes/add_'.$this->router->method, $data);
                    break;
                case 'delete'   : $data['response'] = $this->providers_model->remove((int)$post_data['id']);
                                  $this->load->view('incomes/ajax_response', $data);
                    break;
                case 'edit'     : 
                                  $data['provider'] = $this->providers_model->getProvider((int)$post_data['id']);
                                  $this->load->view('incomes/edit_'.$this->router->method, $data);
                                  $isUpdated = $this->providers_model->edit((int)$post_data['id']);
                                  if ($isUpdated){
                                        redirect(current_url());
                                  }
                    break;
                case 'save'     : $this->providers_model->add(); // Show default after save
                default: 
                    // Load data
                    $data['providers']      = $this->providers_model->getProviders();
                    $data['post_data']      = $post_data;

                    // Load view  
                    $this->load->template('incomes/'.$this->router->method, $data);
            }
            
        } else {
            
            // Load data
            $data['providers']      = $this->providers_model->getProviders();
            $data['post_data']      = $post_data;

            // Load view  
            $this->load->template('incomes/'.$this->router->method, $data);
            
        }
    }
    
    public function top_sales($page = 0)
    {
        $post_data = $this->input->post();
        
        $data = array();
        $data['title']          = humanize($this->router->method);
        
        // Load model
        $this->load->model($this->router->class.'/'.$this->router->method.'_model');
        
        //Prepare data
        $data['period_radios']      = $this->top_sales_model->get_radio_inputs_periods();
        $data['provider_radios']    = $this->top_sales_model->get_radio_inputs_providers();
        $data['web_list']           = $this->web_field_model->get_radio_inputs_web('web');
        $data['products_list']      = $this->top_sales_model->get_products_list($post_data, $page);
        $data['total_rows']         = $this->top_sales_model->total_rows();
        
        // Pagination
        
        $config['base_url'] = base_url().'index.php/incomes/top_sales/';
        $config['total_rows'] = $this->top_sales_model->total_rows();
        $config['per_page'] = 50; 

        $this->pagination->initialize($config); 
        $data['pagination'] = $this->pagination->create_links();
        
        // Load view  
        $this->load->template('incomes/'.$this->router->method, $data);
    }
    
    public function top_sales_product_details($sku)
    {
        if(empty($sku) && !is_string)
        {
            return false;
        }
        
        // Load model
        $this->load->model($this->router->class.'/top_sales_model');
        
        // Load library
        $this->load->library('table');
        
        $product_details = $this->top_sales_model->get_product_details($sku);
        
        // Load view  
        $this->load->view('incomes/'.$this->router->method, array('product_details' => $product_details));
    }


    public function web_field()
    {
        $post_data = $this->input->post();
        
        $data = array();
        $data['title']          = humanize($this->router->method);
        
        // Load model
        $this->load->model($this->router->class.'/'.$this->router->method.'_model');
        
        if (!empty($post_data['task']))
        {
           switch ($post_data['task'])
           {
                case 'add' :      $data['test_mode']            = $this->web_field_model->get_test_mode_toggle();
                                  $data['sync_toggle']          = $this->web_field_model->get_sync_toggle();
                                  $data['providers_accordion']  = $this->web_field_model->get_providers_accordion();
                                  $data['languages_list']       = $this->web_field_model->get_language_list();
                                  $this->load->view('incomes/add_'.$this->router->method, $data);
                    break;
                case 'delete'   : $data['response'] = $this->web_field_model->remove($post_data['id']);
                                  $this->load->view('incomes/ajax_response', $data);
                    break;
                case 'edit'     : 
                                  $data['test_mode']                = $this->web_field_model->get_test_mode_toggle($post_data['id']);  
                                  $data['sync_toggle']              = $this->web_field_model->get_sync_toggle($post_data['id']);  
                                  $data['providers_accordion']      = $this->web_field_model->get_providers_accordion($post_data['id']);
                                  $language                         = $this->web_field_model->get_template_language($post_data['id']);
                                  $data['languages_list']           = $this->web_field_model->get_language_list($language);
                                  $data['web_field']                = $this->web_field_model->get_web_field($post_data['id']);
                                  $this->load->view('incomes/edit_'.$this->router->method, $data);
                                  $isUpdated = $this->web_field_model->edit($post_data['id']);
                                  if ($isUpdated)
                                  {
                                        redirect(current_url());
                                  }
                    break;
                case 'save'     : $this->web_field_model->add(); // Show default after save
                default: 
                    // Load data
                    $data['web_fields']      = $this->web_field_model->get_all_web_fields();
                    $data['post_data']       = $post_data;

                    // Load view  
                    $this->load->template('incomes/'.$this->router->method, $data);
            }
        } 
        else
        {
            // Load data
            $data['web_fields']      = $this->web_field_model->get_all_web_fields();
            $data['post_data']       = $post_data;

            // Load view  
            $this->load->template('incomes/'.$this->router->method, $data);
        }
    }
}