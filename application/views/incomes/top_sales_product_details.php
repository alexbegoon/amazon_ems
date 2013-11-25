<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    if($product_details)
    {
        
        $tmpl = array (
                    'table_open' => '<table class="thin_table">');
        

        $this->table->set_template($tmpl);
        
        $this->table->set_heading('WEB', 'Country', 'Total sold', 
                                    'Total quantity', 
                                    'Date of last purchase', 
                                    'SKU', 'Product name');
        
        foreach ($product_details as $row)
        {
            $this->table->add_row(
                    
                    $row->web,
                    $row->country,
                    number_format($row->total_sold, 2).'&euro;',
                    $row->total_quantity,
                    $row->last_date_purchase,
                    $row->sku,
                    $row->product_name  
                );
        }
        
        echo $this->table->generate();
    }

?>