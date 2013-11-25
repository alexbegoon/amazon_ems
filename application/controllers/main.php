<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Main controller
 *
 * @author SancheZZ
 */
class Main extends CI_Controller {
    
    public function __construct()
    {
         parent::__construct();
         
         // Authorization check
         if (!$this->ion_auth->logged_in())
         {
            redirect('auth/login');
         }
    }
       
    public function index()
    {
        // Authorization check
        if (!$this->ion_auth->logged_in())
        {
            redirect('auth/login');
        }
        
        // Default controller Dashboard
        
        redirect('dashboard');
        
        
//        $data = array();
//        $data['title'] = 'What\'s next?';
//        
//        // Load view  
//        $this->load->template('templates/default', $data);
    }
}

