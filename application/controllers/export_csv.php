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
            force_download($file->name, $file->data);
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
}