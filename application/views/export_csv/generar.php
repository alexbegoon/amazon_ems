<article>
    <h1><?php echo $title;?></h1>
    <?php if(!empty($summary)) { ?>
    <div class="menu-wrapper">
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/export_csv/'.$method;?>">
                <img src="<?php echo base_url().'assets/imgs/Button-Download-icon.png';?>" alt="<?php echo $title;?>" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/export_csv/'.$method;?>">
                <span><?php echo humanize($title);?></span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-item-img" href="javascript:window.print();">
                <img src="<?php echo base_url().'assets/imgs/1-Normal-Printer-icon.png';?>" alt="<?php echo $title;?>" />
            </a>
            <a class="menu-item" href="javascript:window.print();">
                <span>Print orders</span>
            </a>
        </div>
    </div>
    <div>
        <table class="thin_table">
            <tr>
                <th>Pedido</th>
                <th>Ingreso</th>
                <th>Coste</th>
            </tr>
            <?php foreach($summary as $item) { ?>
                <tr>
                    <td class="bold"><?php echo $item->pedido;  ?></td>
                    <td class="ingreso"><?php echo number_format($item->ingresos,2);?>&euro;</td>
                    <td class="gasto"><?php echo number_format($item->gasto,2);   ?>&euro;</td>
                </tr>    
            <?php } ?>
                <tr class="total">
                    <td>Total:</td>
                    <td class="ingreso"><?php echo number_format($item->total_ingresos,2);?>&euro;</td>
                    <td class="gasto"><?php echo number_format($item->total_gasto,2);?>&euro;</td>
                </tr>    
        </table>
    </div>
    <div class="printer_document">
        <div>
            <?php foreach ($orders_for_printer as $order) { ?>
            <?php 
            
                $tmpl = array (
                    'table_open' => '<table class="order_table">'
                );

                $this->table->set_template($tmpl);
                
                switch ($order->web)
                {
                    case 'AMAZON' :
                        $this->lang = new CI_Lang();
                        $this->lang->load('print_order', 'english');
                        
                        $order_title = strtoupper($this->lang->line('print_order_packing_slip'));
                        $order_footer = null;
                        
                        $this->table->add_row('Amazon Marketplace');
                        $this->table->add_row('Seller: Buyin.es');
                        $this->table->add_row('');
                        $this->table->add_row($this->lang->line('print_order_id').': '.$order->id);
                        $this->table->add_row($this->lang->line('print_order_customer_name').': '.$order->name);
                        $this->table->add_row($this->lang->line('print_order_shipping_address').': '.$order->address);
                        $this->table->add_row($this->lang->line('print_order_zip_code').': '.$order->zip);
                        $this->table->add_row($this->lang->line('print_order_city').': '.$order->city);
                        $this->table->add_row($this->lang->line('print_order_country_label').': '.$order->country);
                        $this->table->add_row('');
                        
                        for($i = 1; $i <= 10; $i++)
                        { 
                            $product_name   = 'product_name_'.$i;
                            $unit           = 'unit_'.$i;
                            $sku            = 'sku'.$i;
                            
                            if(isset($order->$product_name))
                            {
                                $this->table->add_row(
                                        $this->lang->line('print_order_sku').': '.
                                        $order->$sku.'<br>'.
                                        $this->lang->line('print_order_product_label').': '.
                                        $order->$product_name.'<br>'.
                                        $this->lang->line('print_order_units').': '.
                                        $order->$unit.'<br>'
                                            );  
                                
                            } 
                    
                        }
                        break;
                        
                    case 'AMAZON-USA' : 
                        $this->lang = new CI_Lang();
                        $this->lang->load('print_order', 'english');
                        
                        $order_title = strtoupper($this->lang->line('print_order_packing_slip'));
                        $order_footer = null;
                        
                        $this->table->add_row('Amazon Marketplace');
                        $this->table->add_row('Seller: Buyin.es');
                        $this->table->add_row('');
                        $this->table->add_row($this->lang->line('print_order_id').': '.$order->id);
                        $this->table->add_row($this->lang->line('print_order_customer_name').': '.$order->name);
                        $this->table->add_row($this->lang->line('print_order_shipping_address').': '.$order->address);
                        $this->table->add_row($this->lang->line('print_order_zip_code').': '.$order->zip);
                        $this->table->add_row($this->lang->line('print_order_city').': '.$order->city);
                        $this->table->add_row($this->lang->line('print_order_country_label').': '.$order->country);
                        $this->table->add_row('');
                        
                        for($i = 1; $i <= 10; $i++)
                        { 
                            $product_name   = 'product_name_'.$i;
                            $unit           = 'unit_'.$i;
                            $sku            = 'sku'.$i;
                            
                            if(isset($order->$product_name))
                            {
                                $this->table->add_row(
                                        $this->lang->line('print_order_sku').': '.
                                        $order->$sku.'<br>'.
                                        $this->lang->line('print_order_product_label').': '.
                                        $order->$product_name.'<br>'.
                                        $this->lang->line('print_order_units').': '.
                                        $order->$unit.'<br>'
                                            );  
                                
                            } 
                    
                        }
                        break;
                    case 'COSMETICAONLINE' :
                        $this->lang = new CI_Lang();
                        $this->lang->load('print_order', 'spanish');
                        
                        $order_title = strtoupper($this->lang->line('print_order_packing_slip'));
                        
                        $order_footer = 'Gracias por confiar en BuyIn. Para cualquier duda en relación al pedido que acaba de recibir, puede ponerse en contacto con nosotros en el número de teléfono de atención al cliente 902.005.676 o si lo prefiere remitiéndonos un email a la dirección info@buyin.es
                                        <br><br>
                                        Esperamos que disfrute de su compra y tenga un buen día !!
                                        <br><br>
                                        Atención al cliente Buyin';
                        
                        $this->table->add_row('BuyIn');
                        $this->table->add_row('www.buyin.es');
                        $this->table->add_row('');
                        $this->table->add_row($this->lang->line('print_order_id').': '.$order->id);
                        $this->table->add_row($this->lang->line('print_order_customer_name').': '.$order->name);
                        $this->table->add_row($this->lang->line('print_order_shipping_address').': '.$order->address);
                        $this->table->add_row($this->lang->line('print_order_zip_code').': '.$order->zip);
                        $this->table->add_row($this->lang->line('print_order_city').': '.$order->city);
                        $this->table->add_row($this->lang->line('print_order_payment_method_label').': '.$order->payment_method);
                        $this->table->add_row('');
                        
                        for($i = 1; $i <= 10; $i++)
                        { 
                            $product_name   = 'product_name_'.$i;
                            $unit           = 'unit_'.$i;
                            $sku            = 'sku'.$i;
                            
                            if(isset($order->$product_name))
                            {
                                $this->table->add_row(
                                        $this->lang->line('print_order_sku').': '.
                                        $order->$sku.'<br>'.
                                        $this->lang->line('print_order_product_label').': '.
                                        $order->$product_name.'<br>'.
                                        $this->lang->line('print_order_units').': '.
                                        $order->$unit.'<br>'
                                );  
                                
                            } 
                    
                        }
                        break;
                        
                    case 'BUYIN' :
                        $this->lang = new CI_Lang();
                        $this->lang->load('print_order', 'spanish');
                        
                        $order_title = strtoupper($this->lang->line('print_order_packing_slip'));
                        
                        $order_footer = 'Gracias por confiar en BuyIn. Para cualquier duda en relación al pedido que acaba de recibir, puede ponerse en contacto con nosotros en el número de teléfono de atención al cliente 902.005.676 o si lo prefiere remitiéndonos un email a la dirección info@buyin.es
                                        <br><br>
                                        Esperamos que disfrute de su compra y tenga un buen día !!
                                        <br><br>
                                        Atención al cliente Buyin';
                        
                        $this->table->add_row('BuyIn');
                        $this->table->add_row('www.buyin.es');
                        $this->table->add_row('');
                        $this->table->add_row($this->lang->line('print_order_id').': '.$order->id);
                        $this->table->add_row($this->lang->line('print_order_customer_name').': '.$order->name);
                        $this->table->add_row($this->lang->line('print_order_shipping_address').': '.$order->address);
                        $this->table->add_row($this->lang->line('print_order_zip_code').': '.$order->zip);
                        $this->table->add_row($this->lang->line('print_order_city').': '.$order->city);
                        $this->table->add_row($this->lang->line('print_order_payment_method_label').': '.$order->payment_method);
                        $this->table->add_row('');
                        
                        for($i = 1; $i <= 10; $i++)
                        { 
                            $product_name   = 'product_name_'.$i;
                            $unit           = 'unit_'.$i;
                            $sku            = 'sku'.$i;
                            
                            if(isset($order->$product_name))
                            {
                                $this->table->add_row(
                                        $this->lang->line('print_order_sku').': '.
                                        $order->$sku.'<br>'.
                                        $this->lang->line('print_order_product_label').': '.
                                        $order->$product_name.'<br>'.
                                        $this->lang->line('print_order_units').': '.
                                        $order->$unit.'<br>'
                                );  
                                
                            } 
                    
                        }
                        break;
                        
                    case 'KOSMETIK' :
                        $this->lang = new CI_Lang();
                        $this->lang->load('print_order', 'german');
                        
                        $order_title = strtoupper($this->lang->line('print_order_packing_slip'));
                        
                        $order_footer = 'Sie können sich gerne mit uns in Kontakt setzen, indem Sie unseren online Chat von Kosmetik Online Shop nutzen (unten, rechts) oder uns unter der Telefonnummer 0034.958490405 anrufen. Sie können uns auch eine email an info@kosmetikonline-shop.com mit Ihrer Bestellnummer und Fragen senden. Danke für Ihre Aufmerksamkeit, Ihr Kundendienst.';
                        
                        $this->table->add_row('BuyIn');
                        $this->table->add_row('www.buyin.es');
                        $this->table->add_row('');
                        $this->table->add_row($this->lang->line('print_order_id').': '.$order->id);
                        $this->table->add_row($this->lang->line('print_order_customer_name').': '.$order->name);
                        $this->table->add_row($this->lang->line('print_order_shipping_address').': '.$order->address);
                        $this->table->add_row($this->lang->line('print_order_zip_code').': '.$order->zip);
                        $this->table->add_row($this->lang->line('print_order_city').': '.$order->city);
                        $this->table->add_row($this->lang->line('print_order_country_label').': '.$order->country);
                        $this->table->add_row('');
                        
                        for($i = 1; $i <= 10; $i++)
                        { 
                            $product_name   = 'product_name_'.$i;
                            $unit           = 'unit_'.$i;
                            $sku            = 'sku'.$i;
                            
                            if(isset($order->$product_name))
                            {
                                $this->table->add_row(
                                        $this->lang->line('print_order_sku').': '.
                                        $order->$sku.'<br>'.
                                        $this->lang->line('print_order_product_label').': '.
                                        $order->$product_name.'<br>'.
                                        $this->lang->line('print_order_units').': '.
                                        $order->$unit.'<br>'
                                );  
                                
                            } 
                    
                        }
                        break;
                        
                    case 'COSMETIQUES' :
                        $this->lang = new CI_Lang();
                        $this->lang->load('print_order', 'french');
                        
                        $order_title = strtoupper($this->lang->line('print_order_packing_slip'));
                        
                        $order_footer = 'Vous pouvez prendre contact avec nous à travers du chat en ligne du portail www.cosmetiquesonline.net ( le chat se trouve en bas à droite) ou bien en nous contactant sur le numéro de téléphone 0821773284 (notre horaire de travail est de 8:00h à 13:00h). Vous pouvez aussi nous envoyer un e-mail à l\'adresse electronique info@cosmetiquesonline.net , en nous indiquant votre numéro de commande et votre incident. Merci pour votre attention, Service clientèle';
                        
                        $this->table->add_row('BuyIn');
                        $this->table->add_row('www.buyin.es');
                        $this->table->add_row('');
                        $this->table->add_row($this->lang->line('print_order_id').': '.$order->id);
                        $this->table->add_row($this->lang->line('print_order_customer_name').': '.$order->name);
                        $this->table->add_row($this->lang->line('print_order_shipping_address').': '.$order->address);
                        $this->table->add_row($this->lang->line('print_order_zip_code').': '.$order->zip);
                        $this->table->add_row($this->lang->line('print_order_city').': '.$order->city);
                        $this->table->add_row($this->lang->line('print_order_country_label').': '.$order->country);
                        $this->table->add_row('');
                        
                        for($i = 1; $i <= 10; $i++)
                        { 
                            $product_name   = 'product_name_'.$i;
                            $unit           = 'unit_'.$i;
                            $sku            = 'sku'.$i;
                            
                            if(isset($order->$product_name))
                            {
                                $this->table->add_row(
                                        $this->lang->line('print_order_sku').': '.
                                        $order->$sku.'<br>'.
                                        $this->lang->line('print_order_product_label').': '.
                                        $order->$product_name.'<br>'.
                                        $this->lang->line('print_order_units').': '.
                                        $order->$unit.'<br>'
                                );  
                                
                            } 
                    
                        }
                        break;
                        
                    case 'PRIXPARFUM' :
                        $this->lang = new CI_Lang();
                        $this->lang->load('print_order', 'french');
                        
                        $order_title = strtoupper($this->lang->line('print_order_packing_slip'));
                        
                        $order_footer = 'Vous pouvez prendre contact avec nous à travers du chat en ligne du portail www.cosmetiquesonline.net ( le chat se trouve en bas à droite) ou bien en nous contactant sur le numéro de téléphone 0821773284 (notre horaire de travail est de 8:00h à 13:00h). Vous pouvez aussi nous envoyer un e-mail à l\'adresse electronique info@cosmetiquesonline.net , en nous indiquant votre numéro de commande et votre incident. Merci pour votre attention, Service clientèle';
                        
                        $this->table->add_row('BuyIn');
                        $this->table->add_row('www.buyin.es');
                        $this->table->add_row('');
                        $this->table->add_row($this->lang->line('print_order_id').': '.$order->id);
                        $this->table->add_row($this->lang->line('print_order_customer_name').': '.$order->name);
                        $this->table->add_row($this->lang->line('print_order_shipping_address').': '.$order->address);
                        $this->table->add_row($this->lang->line('print_order_zip_code').': '.$order->zip);
                        $this->table->add_row($this->lang->line('print_order_city').': '.$order->city);
                        $this->table->add_row($this->lang->line('print_order_country_label').': '.$order->country);
                        $this->table->add_row('');
                        
                        for($i = 1; $i <= 10; $i++)
                        { 
                            $product_name   = 'product_name_'.$i;
                            $unit           = 'unit_'.$i;
                            $sku            = 'sku'.$i;
                            
                            if(isset($order->$product_name))
                            {
                                $this->table->add_row(
                                        $this->lang->line('print_order_sku').': '.
                                        $order->$sku.'<br>'.
                                        $this->lang->line('print_order_product_label').': '.
                                        $order->$product_name.'<br>'.
                                        $this->lang->line('print_order_units').': '.
                                        $order->$unit.'<br>'
                                );  
                                
                            } 
                    
                        }
                        break;
                        
                    case 'COSMETICS' :
                        $this->lang = new CI_Lang();
                        $this->lang->load('print_order', 'english');
                        
                        $order_title = strtoupper($this->lang->line('print_order_packing_slip'));
                        
                        $order_footer = 'Thank you for your trust in us. If you have any questions in relation with the order you have received, do not hesitate to contact us, we will be pleased to help you and we will try to give you the best solution. Phone: +34.958.49.04.05 email: info@cosmetics-makeup.net
                                        <br><br>
                                        Best regards, Customer Service';
                        
                        $this->table->add_row('BuyIn');
                        $this->table->add_row('www.buyin.es');
                        $this->table->add_row('');
                        $this->table->add_row($this->lang->line('print_order_id').': '.$order->id);
                        $this->table->add_row($this->lang->line('print_order_customer_name').': '.$order->name);
                        $this->table->add_row($this->lang->line('print_order_shipping_address').': '.$order->address);
                        $this->table->add_row($this->lang->line('print_order_zip_code').': '.$order->zip);
                        $this->table->add_row($this->lang->line('print_order_city').': '.$order->city);
                        $this->table->add_row($this->lang->line('print_order_country_label').': '.$order->country);
                        $this->table->add_row('');
                        
                        for($i = 1; $i <= 10; $i++)
                        { 
                            $product_name   = 'product_name_'.$i;
                            $unit           = 'unit_'.$i;
                            $sku            = 'sku'.$i;
                            
                            if(isset($order->$product_name))
                            {
                                $this->table->add_row(
                                        $this->lang->line('print_order_sku').': '.
                                        $order->$sku.'<br>'.
                                        $this->lang->line('print_order_product_label').': '.
                                        $order->$product_name.'<br>'.
                                        $this->lang->line('print_order_units').': '.
                                        $order->$unit.'<br>'
                                );  
                                
                            } 
                    
                        }
                        break;
                        
                }
                
                $table = $this->table->generate();
            ?>
            <div class="A4">
                <div>
                    <br><br>
                    <h2><?php echo $order_title;?></h2>
                    <br>
                    <?php echo $table;?>
                </div>
                <div class="footer">
                    <p>
                        <?php 
                        if(isset($order_footer))
                        echo $order_footer;?>
                    </p>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php } else { ?>
    <div>
        <p>Have no such orders.</p>
    </div>
    <?php } ?>
</article>