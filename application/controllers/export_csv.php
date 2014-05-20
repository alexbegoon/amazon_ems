<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Export csv files controller
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Export_csv extends CI_Controller {
    
    public function __construct()
    {
         parent::__construct();
         
         // Authorization check
         if (!$this->ion_auth->logged_in())
         {
            redirect('auth/login');
         }
         
//         // Only admin have access
//         if (!$this->ion_auth->is_admin()) 
//         {
//             show_404();
//             die;
//         }
         
         // Load helpers
         $this->load->helper('download');
         $this->load->helper('file');
         
         // Load model
         $this->load->model('export_csv/export_csv_model');
         
         // Load library
         $this->load->library('table');
    }
    
    public function index()
    {
        // Prepare data
        $data['title'] = humanize($this->router->class);
        
        // Load view 
        $this->load->template('export_csv/index', $data);
    }
    
    public function fedex()
    {
        // Prepare data
        $data['title'] = humanize($this->router->class);
        
        // Model tasks
        $file = $this->export_csv_model->prepare_file('fedex');
        
        // Export file
        if(!empty($file))
        {
            force_download($file->name, $file->data);
        }
        
        // Load view 
        $this->load->template('export_csv/index', $data);
    }
    
    public function gls()
    {
        // Prepare data
        $data['title'] = humanize($this->router->class);
        
        // Model tasks
        $file = $this->export_csv_model->prepare_file('gls');
        
        // Export file
        if(!empty($file))
        {
            force_download($file->name, $file->data);
        }
        
        // Load view 
        $this->load->template('export_csv/index', $data);
    }
    
    public function fedex_gls()
    {
        // Prepare data
        $data['title'] = humanize($this->router->class);
        
        // Model tasks
        $file = $this->export_csv_model->prepare_file('fedex_gls');
        
        // Export file
        if(!empty($file))
        {
            if(write_file(APPPATH . 'logs/CSV_export_'. date('d-m-Y_H-i-s', time()).'.csv', $file->data))
            {
                $this->export_csv_model->batch_update_orders_statuses();
                force_download($file->name, $file->data);
            }
        }
        
        // Load view 
        $this->load->template('export_csv/fedex_gls', $data);
    }
    
    public function fedex_gls_summary()
    {
        // Prepare data
        $data['title'] = humanize($this->router->method);
        $data['method'] = str_replace('_summary', '', $this->router->method);
        
        // Model tasks
        $data['summary'] = $this->export_csv_model->get_summary($this->router->method);
        
        // Load view 
        $this->load->template('export_csv/fedex_gls', $data);
    }
    
    public function export_engelsa_summary()
    {
        // Prepare data
        $data['title'] = humanize($this->router->method);
        $data['method'] = str_replace('_summary', '', $this->router->method);
        
        // Model tasks
        $data['summary'] = $this->export_csv_model->get_summary($this->router->method);
        
        // Load view 
        $this->load->template('export_csv/fedex_gls', $data);
    }
    
    public function export_pinternacional_summary()
    {
        // Prepare data
        $data['title'] = humanize($this->router->method);
        $data['method'] = str_replace('_summary', '', $this->router->method);
        
        // Model tasks
        $data['summary'] = $this->export_csv_model->get_summary($this->router->method);
        
        // Load view 
        $this->load->template('export_csv/fedex_gls', $data);
    }
    
    public function export_coqueteo_summary()
    {
        // Prepare data
        $data['title'] = humanize($this->router->method);
        $data['method'] = str_replace('_summary', '', $this->router->method);
        
        // Model tasks
        $data['summary'] = $this->export_csv_model->get_summary($this->router->method);
        
        // Load view 
        $this->load->template('export_csv/fedex_gls', $data);
    }
    
    public function export_engelsa()
    {
        // Prepare data
        $data['title'] = humanize($this->router->class);
        
        // Model tasks
        $file = $this->export_csv_model->prepare_file($this->router->method);
        
        // Export file
        if(!empty($file))
        {
            if(write_file(APPPATH . 'logs/'.$this->router->method. '_' .date('d-m-Y_H-i-s', time()).'.csv', $file->data))
            {
                $this->export_csv_model->batch_update_orders_statuses();
                force_download($file->name, $file->data);
            }
        }
        
        // Load view 
        $this->load->template('export_csv/fedex_gls', $data);
    }
    
    public function export_pinternacional()
    {
        // Prepare data
        $data['title'] = humanize($this->router->class);
        
        // Model tasks
        $file = $this->export_csv_model->prepare_file($this->router->method);
        
        // Export file
        if(!empty($file))
        {
            if(write_file(APPPATH . 'logs/'.$this->router->method. '_' .date('d-m-Y_H-i-s', time()).'.csv', $file->data))
            {
                $this->export_csv_model->batch_update_orders_statuses();
                force_download($file->name, $file->data);
            }
        }
        
        // Load view 
        $this->load->template('export_csv/fedex_gls', $data);
    }
    
    public function export_coqueteo()
    {
        // Prepare data
        $data['title'] = humanize($this->router->class);
        
        // Model tasks
        $file = $this->export_csv_model->prepare_file($this->router->method);
        
        // Export file
        if(!empty($file))
        {
            if(write_file(APPPATH . 'logs/'.$this->router->method. '_' .date('d-m-Y_H-i-s', time()).'.csv', $file->data))
            {
                $this->export_csv_model->batch_update_orders_statuses();
                force_download($file->name, $file->data);
            }
        }
        
        // Load view 
        $this->load->template('export_csv/fedex_gls', $data);
    }
    
    public function generar_gls()
    {        
        // Prepare data
        $data['title'] = humanize($this->router->method);
        $data['method'] = str_replace('_summary', '', $this->router->method);
        
        // Model tasks
        $file = $this->export_csv_model->prepare_file($this->router->method);
        
        // Export file
        if(!empty($file))
        {
            force_download($file->name, $file->data);
        }
        
        // Load view 
        $this->load->template('export_csv/generar', $data);
    }
    
    public function generar_fedex()
    {
        // Prepare data
        $data['title'] = humanize($this->router->method);
        $data['method'] = str_replace('_summary', '', $this->router->method);
        
        // Model tasks
        $file = $this->export_csv_model->prepare_file($this->router->method);
                
        // Export file
        if(!empty($file))
        {
            force_download($file->name, $file->data);
        }
        
        // Load view 
        $this->load->template('export_csv/generar', $data);
    }
    
    public function generar_pack()
    {
        // Prepare data
        $data['title'] = humanize($this->router->method);
        $data['method'] = str_replace('_summary', '', $this->router->method);
        
        // Model tasks
        $file = $this->export_csv_model->prepare_file($this->router->method);
                
        // Export file
        if(!empty($file))
        {
            force_download($file->name, $file->data);
        }
        
        // Load view 
        $this->load->template('export_csv/generar', $data);
    }
    
    public function generar_tourline()
    {
        // Prepare data
        $data['title'] = humanize($this->router->method);
        $data['method'] = str_replace('_summary', '', $this->router->method);
        
        // Model tasks
        $file = $this->export_csv_model->prepare_file($this->router->method);
                
        // Export file
        if(!empty($file))
        {
            force_download($file->name, $file->data);
        }
        
        // Load view 
        $this->load->template('export_csv/generar', $data);
    }
    
    public function generar_gls_summary()
    {
        // Prepare data
        $data['title'] = humanize($this->router->method);
        $data['method'] = str_replace('_summary', '', $this->router->method);
        
        // Model tasks
        $data['summary'] = $this->export_csv_model->get_summary($this->router->method);
        $data['orders_for_printer'] = $this->export_csv_model->get_orders_for_printer($this->router->method);
        
        // Load view 
        $this->load->template('export_csv/generar', $data);
    }
    
    public function generar_fedex_summary()
    {
        // Prepare data
        $data['title'] = humanize($this->router->method);
        $data['method'] = str_replace('_summary', '', $this->router->method);
        
        // Model tasks
        $data['summary'] = $this->export_csv_model->get_summary($this->router->method);
        $data['orders_for_printer'] = $this->export_csv_model->get_orders_for_printer($this->router->method);
        
        // Load view 
        $this->load->template('export_csv/generar', $data);
    }
    
    public function generar_pack_summary()
    {
        // Prepare data
        $data['title'] = humanize($this->router->method);
        $data['method'] = str_replace('_summary', '', $this->router->method);
        
        // Model tasks
        $data['summary'] = $this->export_csv_model->get_summary($this->router->method);
        $data['orders_for_printer'] = $this->export_csv_model->get_orders_for_printer($this->router->method);
        
        // Load view 
        $this->load->template('export_csv/generar', $data);
    }
    
    public function generar_tourline_summary()
    {
        // Prepare data
        $data['title'] = humanize($this->router->method);
        $data['method'] = str_replace('_summary', '', $this->router->method);
        
        // Model tasks
        $data['summary'] = $this->export_csv_model->get_summary($this->router->method);
        $data['orders_for_printer'] = $this->export_csv_model->get_orders_for_printer($this->router->method);
        
        // Load view 
        $this->load->template('export_csv/generar', $data);
    }
    
    public function generar_stokoni_summary()
    {
        // Prepare data
        $data['title'] = humanize($this->router->method);
        $data['method'] = str_replace('_summary', '', $this->router->method);
        
        // Model tasks
        $data['summary'] = $this->export_csv_model->get_summary($this->router->method);
        $data['orders_for_printer'] = $this->export_csv_model->get_orders_for_printer($this->router->method);
        
        // Load view 
        $this->load->template('export_csv/generar', $data);
    }
    
    public function generar_stokoni()
    {
        // Prepare data
        $data['title'] = humanize($this->router->method);
        $data['method'] = str_replace('_summary', '', $this->router->method);
        
        // Model tasks
        $file = $this->export_csv_model->prepare_file($this->router->method);
                
        // Export file
        if(!empty($file))
        {
            force_download($file->name, $file->data);
        }
        
        // Load view 
        $this->load->template('export_csv/generar', $data);
    }
    
    public function generar_new_products_coqueteo_summary()
    {
        $date_from                  = $this->input->post("date_from");
        $date_to                    = $this->input->post("date_to");
        
        // Prepare data
        $data['title'] = humanize($this->router->method);
        $data['method'] = str_replace('_summary', '', $this->router->method);
        $data['button_name'] = 'Download coqueteo new products report';
        $data['button_link'] = base_url() . 'index.php/export_csv/generar_new_products_coqueteo';
        
        // Filters
        $input = array(
              'name'        => 'date_from',
              'id'          => 'date_picker',
              'value'       => $date_from,
            );
        $data['filters'][]   = form_label('Date from: ', 'date_picker');
        $data['filters'][]   = form_input($input);
        $input = array(
              'name'        => 'date_to',
              'id'          => 'date_picker_2',
              'value'       => $date_to,
            );
        $data['filters'][]   = form_label('Date to: ', 'date_picker_2');
        $data['filters'][]   = form_input($input);
        
        // Load view 
        $this->load->template('export_csv/new_coqueteo', $data);
    }
    
    public function generar_new_products_coqueteo()
    {
        // Prepare data
        $data['title'] = humanize($this->router->method);
        $data['method'] = str_replace('_summary', '', $this->router->method);
        
        // Model tasks
        $file = $this->export_csv_model->prepare_file($this->router->method);
                
        // Export file
        if(!empty($file))
        {
            force_download($file->name, $file->data);
        }
        
        // Load view 
        $this->load->template('export_csv/generar', $data);
    }
}