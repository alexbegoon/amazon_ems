<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of magnet
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class magnet extends CI_Controller
{
    public function __construct()
    {
         parent::__construct();
         
         // Authorization check
         if (!$this->ion_auth->logged_in())
         {
            redirect('auth/login');
         }
         
         // Load model
         $this->load->model('magnet/magnet_model');
    }
    
    public function index()
    {   
        $data['title'] = humanize($this->router->class);
        
        // Load view 
        $this->load->template('magnet/index', $data);
        
    }
    
    public function edit_template($language_code)
    {
        
        $data['title'] = humanize($this->router->method);
        
        $this->magnet_model->update_template($language_code);
        
        $data['template'] = $this->magnet_model->get_template($language_code);
        
        // Load view 
        $this->load->template('magnet/edit_template', $data);
    }
    
    public function send_email_messages()
    {
        
        $data['title'] = humanize($this->router->method);
        
        $data['emails'] = $this->magnet_model->send_email_messages();
                
        // Load view 
        $this->load->template('magnet/send_email_messages', $data);
    }
}