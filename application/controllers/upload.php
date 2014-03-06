<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Upload files
 * 
 * @link http://ellislab.com/codeigniter/user-guide/libraries/file_uploading.html official manual
 * 
 * 
 */
class Upload extends CI_Controller {

        private $_upload_path           = './upload/';
        private $_allowed_types         = 'txt|zip|htm|html|HTM|HTML';
        private $_max_size              = '10000';
        private $_max_width             = '1024';
        private $_max_height            = '768';
    
	function __construct()
	{
		parent::__construct();
                
                // Authorization check
                if (!$this->ion_auth->logged_in())
                {
                   redirect('auth/login');
                }
                
                ini_set('upload_max_filesize', '10M');
                
		$this->load->helper(array('form', 'url'));
                
                // Load model
                $this->load->model('upload/upload_model');
                $this->load->model('amazon/amazon_model');
	}

	function index()
	{
                $this->load->template('upload/upload_form', array('error' => ' ', 
                                                           'title' => 'Upload Files',
                                                           'url'   => 'upload/do_upload',
                                                           'help_info' => '' ));
	}

	function do_upload()
	{
		$config['upload_path']      = $this->_upload_path;
		$config['allowed_types']    = $this->_allowed_types;
		$config['max_size']         = $this->_max_size;
//		$config['max_width']        = $this->_max_width;
//		$config['max_height']       = $this->_max_height;

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors(), 'title' => 'Upload Files');
                        $error['url'] = 'upload/'.$this->router->method;
                        $error['help_info'] = '';
                        
			$this->load->template('upload/upload_form', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data(), 'title' => 'Upload Files');
                        $data['url']    = 'upload/';
			$this->load->template('upload/upload_success', $data);
		}
	}
        
        /**
         * Store parsed orders to pedidos table
         * 
         * 
         */
        function store()
        {
            $data['title']  = 'Store Orders';
            $post_data = $this->input->post();
            
            $store_response = $this->upload_model->storeOrders();
            if($store_response !== false)
            {
                $data['affected_rows'] = $store_response['affected_rows'];
                $data['response']   = 'Orders successfully stored';
                $data['url']        = $post_data['url'];
                $this->load->template('upload/upload_store', $data);
            }
            else
            {
                $data['affected_rows'] = 0;
                $data['response'] = 'Error!!! Cant store orders';
                $data['url']      = $post_data['url'];
                $this->load->template('upload/upload_store', $data);
            }
        }
                
        function upload_amazon_europe_orders()
        {
                $config['upload_path']      = $this->_upload_path;
		$config['allowed_types']    = $this->_allowed_types;
		$config['max_size']         = $this->_max_size;

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
                        $error['title'] = 'Upload Amazon Europe Orders';
                        $error['url'] = 'upload/'.$this->router->method;
                        $error['help_info'] = '';
                        
			$this->load->template('upload/upload_form', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
                        $data['title']  = 'Upload Amazon Europe Orders';
                        $data['url']    = 'upload/'.$this->router->method;
                        $data['orders'] = $this->upload_model->getOrders($data['upload_data']['full_path'], 'Europe');
                        
                        
			$this->load->template('upload/upload_success', $data);
		}
        }
        
        function upload_amazon_usa_sellercentral_data()
        {
                $config['upload_path']      = $this->_upload_path;
                $config['allowed_types']    = $this->_allowed_types;
                $config['max_size']         = $this->_max_size;

                $this->load->library('upload', $config);

                if ( ! $this->upload->do_upload())
                {
                        $error = array('error' => $this->upload->display_errors());
                        $error['title'] = humanize($this->router->method);
                        $error['url'] = 'upload/'.$this->router->method;
                        $error['help_info'] = '';

                        $this->load->template('upload/upload_form', $error);
                }
                else
                {
                        $data = array('upload_data' => $this->upload->data());
                        $data['title']  = humanize($this->router->method);
                        $data['url']    = 'upload/'.$this->router->method;
                        $data['data']   = $this->amazon_model->parse_sellercentral_data($data['upload_data'], 'AMAZON-USA');


                        $this->load->template('upload/upload_sellercentral_success', $data);
                }
        }
        function upload_amazon_uk_sellercentral_data()
        {
            $config['upload_path']      = $this->_upload_path;
            $config['allowed_types']    = $this->_allowed_types;
            $config['max_size']         = $this->_max_size;

            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload())
            {
                    $error = array('error' => $this->upload->display_errors());
                    $error['title'] = humanize($this->router->method);
                    $error['url'] = 'upload/'.$this->router->method;
                    $error['help_info'] = '';

                    $this->load->template('upload/upload_form', $error);
            }
            else
            {
                    $data = array('upload_data' => $this->upload->data());
                    $data['title']  = humanize($this->router->method);
                    $data['url']    = 'upload/'.$this->router->method;
                    $data['data']   = $this->amazon_model->parse_sellercentral_data($data['upload_data'], 'AMAZON-CO-UK');


                    $this->load->template('upload/upload_sellercentral_success', $data);
            }
        }
        function upload_amazon_de_sellercentral_data()
        {
            $config['upload_path']      = $this->_upload_path;
            $config['allowed_types']    = $this->_allowed_types;
            $config['max_size']         = $this->_max_size;            
            
            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload())
            {
                    $error = array('error' => $this->upload->display_errors());
                    $error['title'] = humanize($this->router->method);
                    $error['url'] = 'upload/'.$this->router->method;
                    $error['help_info'] = '';

                    $this->load->template('upload/upload_form', $error);
            }
            else
            {
                    $data = array('upload_data' => $this->upload->data());
                    $data['title']  = humanize($this->router->method);
                    $data['url']    = 'upload/'.$this->router->method;
                    $data['data']   = $this->amazon_model->parse_sellercentral_data($data['upload_data'], 'AMAZON-DE');


                    $this->load->template('upload/upload_sellercentral_success', $data);
            }
        }
        
        function store_sellercentral_data()
        {
            $data['title']  = 'Store Sellercentral Data';
            $post_data = $this->input->post();
            
            $store_response = $this->amazon_model->store_sellercentral_data();
            if($store_response !== false)
            {
                $data['affected_rows'] = 'All';
                $data['response']   = 'Data successfully stored';
                $data['url']        = $post_data['url'];
                $this->load->template('upload/upload_store', $data);
            }
            else
            {
                $data['affected_rows'] = 0;
                $data['response'] = 'Error!!! Cant store sellercentral data';
                $data['url']      = $post_data['url'];
                $this->load->template('upload/upload_store', $data);
            }
        }
                
        function upload_amazon_usa_orders()
        {
                $config['upload_path']      = $this->_upload_path;
		$config['allowed_types']    = $this->_allowed_types;
		$config['max_size']         = $this->_max_size;

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
                        $error['title'] = 'Upload Amazon USA Orders';
                        $error['url'] = 'upload/'.$this->router->method;
                        $error['help_info'] = '';
                        
			$this->load->template('upload/upload_form', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
                        $data['title']  = 'Upload Amazon USA Orders';
                        $data['url']    = 'upload/'.$this->router->method;
                        $data['orders'] = $this->upload_model->getOrders($data['upload_data']['full_path'], 'USA');
                        
                        
			$this->load->template('upload/upload_success', $data);
		}
        }
        
        function overwrite_gasto()
        {
            //print_r($this->upload_model->overwriteGasto()) ;
        }
        
        function gls_tracking_file()
        {
            $config['upload_path']      = $this->_upload_path;
            $config['allowed_types']    = '*';
            $config['max_size']         = $this->_max_size;

            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload())
            {
                $data = array('error' => $this->upload->display_errors());
                $data['title'] = 'Tracking';
                $this->load->template('tracking/index', $data);
            }
            else
            {
                // Load model
                $this->load->model('tracking/tracking_model');
                
                $data = array('upload_data' => $this->upload->data());
                $data['title'] = 'Tracking';
                
                $this->tracking_model->process_tracking_file($data['upload_data']['full_path']);
                
                $data['summary']['parse_file']      = $this->tracking_model->get_list_of_tracking_reads();
                $data['summary']['tracking_send']   = $this->tracking_model->get_list_of_tracking_send_to_our_websites();
                $data['summary']['amazon_file']     = $this->tracking_model->get_list_of_orders_that_go_to_the_amazon_file();
                
                $data['is_amazon_file_exists']      = $this->tracking_model->is_amazon_file_exists();
                
                $this->load->template('tracking/index', $data);
            }
        }
        
        function products()
        {
            $config['upload_path']      = $this->_upload_path;
            $config['allowed_types']    = '*';
            $config['max_size']         = $this->_max_size;
                        
            $this->load->library('upload', $config);
            
            //Load model
            $this->load->model('products/products_model');
            
            if ( ! $this->upload->do_upload())
            {
                $error = array('error' => $this->upload->display_errors());
                $error['title'] = humanize($this->router->method);
                $error['url'] = 'upload/'.$this->router->method;
                $error['help_info'] = $this->products_model->get_help_info();

                $this->load->template('upload/upload_form', $error);
            }
            else 
            {
                $data = array('upload_data' => $this->upload->data());
                $data['title'] = humanize($this->router->method);
                
                
                
                $data['upload_summary'] = $this->products_model->upload_products($data);
                
                $this->load->template('products/upload_success', $data);
            }
        }
}
?>
