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
                <?php if(strpos(current_url(),'generar_stokoni_summary') !== FALSE):?>
                <th>Stokoni</th>
                <?php endif;?>
                <th>Ingreso</th>
                <th>Coste</th>
            </tr>
            <?php foreach($summary as $item) { ?>
                <tr>
                    <td class="bold"><?php echo $item->pedido;  ?></td>
                    <?php if(strpos(current_url(),'generar_stokoni_summary') !== FALSE):?>
                    <td>STOKONI</td>
                    <?php endif;?>
                    <td class="ingreso"><?php echo number_format($item->ingresos,2);?>&euro;</td>
                    <td class="gasto"><?php echo number_format($item->gasto,2);   ?>&euro;</td>
                </tr>    
            <?php } ?>
                <tr class="total">
                    <?php if(strpos(current_url(),'generar_stokoni_summary') !== FALSE):?>
                    <td></td>
                    <?php endif;?>
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
                
                $this->lang = new CI_Lang();
                if(isset($order->other_info->language))
                {
                    $this->lang->load('print_order', $order->other_info->language);
                }
                $order_title = strtoupper($this->lang->line('print_order_packing_slip'));
                if(isset($order->other_info->print_order_footer))
                {
                    $order_footer = stripslashes($order->other_info->print_order_footer);
                }
                if(isset($order->other_info->title))
                {
                    $this->table->add_row($order->other_info->title);
                }
                
                if(!empty($order->other_info->print_order_title))
                {
                    $this->table->add_row(stripslashes($order->other_info->print_order_title));
                }
                if(isset($order->other_info->url))
                {
                    $this->table->add_row($order->other_info->url);
                }
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