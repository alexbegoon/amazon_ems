<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of reviews_model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Reviews_model extends CI_Model
{
    private $_reviews_total = 0;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function count_reviews()
    {
        return $this->_reviews_total;
    }
    
    /**
     * Sync reviews with all web shops
     */
    public function sync_reviews()
    {
        // Load models
        $this->load->model('incomes/web_field_model');
        $this->load->model('virtuemart/virtuemart_model');
        $this->load->model('products/products_model');
        
        $web_fields = $this->web_field_model->get_all_web_fields();
        
        $reviews = array();
        
        foreach ($web_fields as $web_field)
        {
            if($web_field->sync_enabled == '1')
            {      
                 $reviews[$web_field->web] = $this->virtuemart_model->get_all_reviews($web_field->web, date('Y-m-d H:i:s', time() - 30 * 24 * 60 * 60));
                 
                 foreach ($reviews[$web_field->web] as $review)
                 {
                     $data = array();
                     
                     $data['web']                       = $web_field->web;
                     $data['comment']                   = $review->comment;
                     $data['rating']                    = $review->review_rating;
                     $data['created']                   = $review->created_on;
                     $data['virtuemart_product_id']     = $review->virtuemart_product_id;
                     $data['product_name']              = $this->products_model->get_product(
                                                          $this->virtuemart_model->get_product(
                                                            $web_field->web, $review->virtuemart_product_id
                                                          )->product_sku,
                                                          $web_field->web
                                                          )->product_name;
                     $data['product_sku']               = $this->virtuemart_model->get_product(
                                                            $web_field->web, $review->virtuemart_product_id
                                                          )->product_sku;
                     $data['provider_product_sku']      = $this->products_model->get_product(
                                                          $this->virtuemart_model->get_product(
                                                            $web_field->web, $review->virtuemart_product_id
                                                          )->product_sku,
                                                          $web_field->web
                                                          )->sku;
                     $data['virtuemart_rating_review_id']  = $review->virtuemart_rating_review_id;
                     $data['amazoni_product_id']        = $this->products_model->get_product(
                                                          $this->virtuemart_model->get_product(
                                                            $web_field->web, $review->virtuemart_product_id
                                                          )->product_sku,
                                                          $web_field->web
                                                          )->id;
                     $data['timestamp']                 = null;
                        
//                     var_dump($data);
                     
                     $this->db->insert($this->db->dbprefix('customer_reviews'), $data); 
                 }
            }
        }
    }
    
    public function get_all_reviews($page)
    {
        $post_data = $this->input->post();
                    
        if(!empty($post_data['web']))
        {
            $this->db->where('web', $post_data['web']);
        }
        
        if(isset($post_data['rating']))
        {
            $this->db->where('rating', $post_data['rating']);
        }
        
                  $this->db->order_by('created', 'DESC');
        $result = $this->db->get($this->db->dbprefix('customer_reviews'), $page, 50);
                  
        if(!empty($post_data['web']))
        {
            $this->db->where('web', $post_data['web']);
        }
        
        if(isset($post_data['rating']))
        {
            $this->db->where('rating', $post_data['rating']);
        }
        
        $result2 = $this->db->get($this->db->dbprefix('customer_reviews'));
                
        if($result)
        {
            $this->_reviews_total = $result2->num_rows();
            
            $reviews = array();
            
            foreach ($result->result() as $review)
            {
                $review->link = $this->get_product_link($review->web, $review->virtuemart_product_id);
                $reviews[] = $review;
            }
            
            return $reviews;
        }        
    }
    
    public function get_rating_filter()
    {
        $html = '';
        
        $buttons = array(
            
            array(
                'name' => 'rating',
                'type' => 'radio',
                'id'   => 'rating_0',
                'value'=> '0'
            ),
            array(
                'name' => 'rating',
                'type' => 'radio',
                'id'   => 'rating_1',
                'value'=> '1'
            ),
            array(
                'name' => 'rating',
                'type' => 'radio',
                'id'   => 'rating_2',
                'value'=> '2'
            ),
            array(
                'name' => 'rating',
                'type' => 'radio',
                'id'   => 'rating_3',
                'value'=> '3'
            ),
            array(
                'name' => 'rating',
                'type' => 'radio',
                'id'   => 'rating_4',
                'value'=> '4'
            ),
            array(
                'name' => 'rating',
                'type' => 'radio',
                'id'   => 'rating_5',
                'value'=> '5'
            )
        );
        
        foreach ($buttons as $button)
        {
            $html .= form_radio($button, null, null, set_radio($button['name'], $button['value']));
            $html .= form_label(get_rating_stars($button['value']), $button['id']);
        }
        
        return $html;
    }
    
    private function get_product_link($web, $product_id)
    {
        $web_site = $this->web_field_model->get_web_field($web);
        
        if(empty($web_site))
        {
            return false;
        }
        //http://www.cosmetiquesonline.net/index.php?option=com_virtuemart&page=shop.product_details&flypage=flypage.tpl&product_id=35652
        $uri = 'http://';
        $uri .= preg_replace('/http:\/\/|https:\/\//', '', $web_site->url);
        
        switch ($web_site->virtuemart_version)
        {
            case '2.0.0.0' : $uri .= '/index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.(int)$product_id;
                break;
            case '1.0.0.0' : $uri .= '/index.php?option=com_virtuemart&page=shop.product_details&flypage=flypage.tpl&product_id='.(int)$product_id;
                break;
            default : return false;
        }
        
        return anchor_popup($uri, 'View current product flypage in shop', 'target="_blank"');
    }
}