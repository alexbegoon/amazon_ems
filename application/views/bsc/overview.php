<?php
$date_from                  = $this->input->post("date_from");
$date_to                    = $this->input->post("date_to");
$products_option_selected   = $this->input->post("products_mode");

$products_options = array(
    
    '1' => 'All products.',
    '2' => 'Products Pending.',
    '3' => 'Products Ordered.'
    
);


?>
<article>
    <h1><?php echo $title;?></h1>
    <?php echo form_open(current_url());?>
    <div class="filters">
        <label for="products_mode">Mode: </label>
        <?php echo form_dropdown('products_mode', $products_options, $products_option_selected,'id="products_mode"'); ?>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <label for="providers_list">Provider: </label>
        <?php echo $providers_list; ?>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <label for="date_picker">Date from: </label>
        <input id="date_picker" type="text" name="date_from" value="<?php echo $date_from;?>" />
        <label for="date_picker_2">Date to: </label>
        <input id="date_picker_2" type="text" name="date_to" value="<?php echo $date_to;?>" />
        <input type="submit" value="Process" />
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <label for="to_excel" title="Export selected products to the MS Excel list">To Excel: </label>
        <button id="to_excel"><span class="excel_icon"></span></button>
        <br>
        <br>
    </div>
    <div>
        <div id="radios">
            <?php echo $period_radios; ?>
        </div>
        <br>
    </div>
    
    
    <?php if($overview) :?>
    <?php //var_dump($overview);die;?>
    <p><?php echo $unique_products_count;?> unique products found.</p>
    <div class="pagination">
    <?php echo $pagination;?>
    </div>
    <table class="thin_table">
        <tr>
            <th>SKU</th>
            <th>Product name</th>
            <th>Provider</th>
            <th>Stock</th>
            <th>Price</th>
            <th>Last sold(date)</th>
            <th>Units Sold Stokoni</th>
            <th>Trend</th>
            <th>Units Sold BuyIn</th>
            <th>Trend</th>
            <th>Units Sold Amazon</th>
            <th>Trend</th>
            <th>Have Best Price?</th>
            <th>Total Trend</th>
            <th>Checkbox</th>
            <th>Quantity needed</th>
            <th>Price</th>
            <th>Ordered?</th>
            <th>Date</th>
        </tr>
    <?php foreach($overview as $row) :?>
        
        <?php 
        
        $checkbox = array(
                            'name'        => 'product_id[]',
                            'class'       => 'product_selector',
                            'id'          => 'checkbox_product_id_'.$row['id'],
                            'value'       => $row['id'],
                            'checked'     => $row['is_checked']
                            );


        ?>
        
        <tr id="product_id_<?php echo $row['id']?>" data-sku="<?php echo $row['sku'] ;?>">
            <td class="bold"><?php echo $row['sku'] ;?></td>
            <td><?php echo $row['product_name'] ;?></td>
            <td><?php echo $row['provider_name'] ;?></td>
            <td><?php echo $row['stock'] ;?></td>
            <td title="<?php echo $row['last_price'] ? 'The last price was: '.number_format($row['last_price'], 2).'&euro;' : null;?>"><?php echo number_format($row['price'], 2);?>&euro;</td>
            <td><?php echo $row['date_of_last_purchase'] ;?></td>
            <td><?php echo $row['units_sold_warehouse'] ;?></td>
            <td class="<?php
                    echo ($row['warehouse_trend'] > 0) ? 'green' : ''; 
                    echo ($row['warehouse_trend'] < 0) ? 'red' : ''; 
            ?>"><?php 
            
                    echo ($row['warehouse_trend'] > 0) ? '<span class="a_up"></span>' : ''; 
                    echo ($row['warehouse_trend'] < 0) ? '<span class="a_down"></span>' : ''; 
                    echo round($row['warehouse_trend'],2) ;?>%
            
            </td>
            <td><?php echo $row['units_sold_buyin'] ;?></td>
            <td class="<?php
                    echo ($row['buyin_trend'] > 0) ? 'green' : ''; 
                    echo ($row['buyin_trend'] < 0) ? 'red' : ''; 
            ?>"><?php 
            
                    echo ($row['buyin_trend'] > 0) ? '<span class="a_up"></span>' : ''; 
                    echo ($row['buyin_trend'] < 0) ? '<span class="a_down"></span>' : ''; 
                    echo round($row['buyin_trend'],2) ;?>%
            
            </td>
            <td><?php echo $row['units_sold_amazon'] ;?></td>
            <td class="<?php
                    echo ($row['amazon_trend'] > 0) ? 'green' : ''; 
                    echo ($row['amazon_trend'] < 0) ? 'red' : ''; 
            ?>"><?php 
            
                    echo ($row['amazon_trend'] > 0) ? '<span class="a_up"></span>' : ''; 
                    echo ($row['amazon_trend'] < 0) ? '<span class="a_down"></span>' : ''; 
                    echo round($row['amazon_trend'],2) ;?>%
            
            </td>
            <td class="<?php echo $row['is_best_price'] == 1 ? 'green' : 'red';?>"><?php echo $row['is_best_price'] == 1 ? 'Yes' : 'No';?></td>
            <td class="<?php
                    echo ($row['total_trend'] > 0) ? 'green' : ''; 
                    echo ($row['total_trend'] < 0) ? 'red' : ''; 
            ?>"><?php 
            
                    echo ($row['total_trend'] > 0) ? '<span class="a_up"></span>' : ''; 
                    echo ($row['total_trend'] < 0) ? '<span class="a_down"></span>' : ''; 
                    echo round($row['total_trend'],2) ;?>%
            </td>
            <td><?php echo form_checkbox($checkbox); ;?></td>
            <td>
                <input type="number" class="quantity_needed" min="0" data-id="<?php echo $row['id']?>" name="quantity_needed_<?php echo $row['id']?>" id="quantity_needed_<?php echo $row['id']?>" value="<?php echo $row['quantity_needed']?>" />
                <span class="apply_icon"></span>
            </td>
            <td>
                <input type="number" class="target_price" data-id="<?php echo $row['id']?>" step="any" min="0" name="target_price_<?php echo $row['id']?>" id="target_price_<?php echo $row['id']?>" value="<?php echo $row['target_price'] > 0 ? round($row['target_price'],2) : null;?>" />
                <span class="apply_icon"></span>
            </td>
            <td class="<?php echo $row['provider_ordered'] == 1 ? 'green' : 'red';?>"><?php echo $row['provider_ordered'] == 1 ? 'Yes' : 'No';?></td>
            <td><?php echo $row['provider_order_date'];?></td>
        </tr>
    <?php endforeach;?>
    </table>
    <div class="pagination">
    <?php echo $pagination;?>
    </div>
    <p><?php echo $unique_products_count;?> unique products found.</p>
    <?php endif;?>
    <?php echo form_close();?>
</article>
<script>
    $(function(){
        $( "#radios" ).buttonset();
        $('#providers_list').combobox();
        $('#products_mode').combobox();
        
        $("#radios input").click(function(){
            $('#date_picker, #date_picker_2').val(null);
            $("form").submit();
        });
        
        // Export to excel
        
        $('form *').click(function(){
            
            if($('#to_excel_toggle').length)
            {
                $('#to_excel_toggle').val(0);
            }
        });
        
        $('#to_excel').click(function(e){
            e.preventDefault();
            
            $('form').append('<input type="hidden" id="to_excel_toggle" name="to_excel" value="1">');
            
            $('form').submit();
        });
        
        // Datepicker
            $('#date_picker, #date_picker_2').datepicker({
                dateFormat: 'yy-mm-dd',
                onSelect: function(  ) {
                    var dateFrom = $('#date_picker').datepicker("getDate");
                    var dateTo   = $('#date_picker_2').datepicker("getDate");
                    var rMin = new Date(dateFrom); 
                    var rMax = new Date(dateTo);
                    if(this.id == 'date_picker')
                    {
                        $('#date_picker_2').datepicker("option","minDate",new Date(rMin.getTime() + 86400000));
                        $('#date_picker').datepicker("option","maxDate",rMin);
                    }
                    else
                    {
                        $('#date_picker_2').datepicker("option","minDate",rMax);
                        $('#date_picker').datepicker("option","maxDate",new Date(rMax.getTime() - 86400000));
                    }
                    
                    $('#date_picker, #date_picker_2').attr('required','required');
                    
                    $('#radios input').removeAttr('checked');
                    $( "#radios" ).buttonset('refresh');
                },
                onClose: function(){
                    
                    $('#date_picker, #date_picker_2').attr('required','required');
                    
                    $('#radios input').removeAttr('checked');
                    $( "#radios" ).buttonset('refresh');
                    
                    if( $('#date_picker').val() == '' && $('#date_picker').val() == '' )
                    {
                        $('#date_picker, #date_picker_2').removeAttr('required');
                    }
                    
                }
            });
            
            // Quantity needed	and Target Price AJAX
            
             $('.quantity_needed, .target_price').on('blur',function(){
                
                var el = $(this);
                
                if(!this.checkValidity())
                {
                    alert(this.validationMessage);
                    el.val(null);
                    el.focus();
                }
             });
            
            $('.quantity_needed, .target_price').on('change',function(){
                
                var el = $(this);
                
                if(!this.checkValidity() || !$.isNumeric(el.val()))
                {
                    var validation_msg = this.validationMessage || 'Please, enter a valid number';
                    alert(validation_msg);
                    el.val(null);
                    el.focus();
                }
                else
                {
                    el.parent().toggleClass('small-ajax-loader');
                    
                    var id = el.attr('data-id');
                    var key = el.attr("class");
                    var value = el.val();
                    var data = {};
                    data[key] = value;
                    
                    $.ajax({
                            type: "POST",
                            url: url_before_index + "index.php/bsc/update_product/" + id,
                            data: data
                          }).success(function( response ) {
                                if(response === 'success')
                                {
                                    el.parent().removeClass('small-ajax-loader');
                                    el.css("backgroundColor", "#99ff99"); // hack for Safari
                                    el.animate({ backgroundColor: '#99ff99' }, 1500);
                                    setTimeout(function(){el.animate({backgroundColor: '#fff'}, 1000)},500);
                                }
                          }).error(function( jqXHR, textStatus, errorThrown ) {
                                alert(textStatus + ':  ' + errorThrown);
                                el.parent().removeClass('small-ajax-loader');
                                el.css("backgroundColor", "#ff9999"); // hack for Safari
                                el.animate({ backgroundColor: '#ff9999' }, 1500);
                                setTimeout(function(){
                                    el.animate({backgroundColor: '#fff'}, 1000);
                                    el.val(null);
                                },500);
                          });
                }
            });
            
    });
</script>