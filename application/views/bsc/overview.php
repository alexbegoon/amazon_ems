<?php
$date_from = $this->input->post("date_from");
$date_to = $this->input->post("date_to");
?>
<article>
    <h1><?php echo $title;?></h1>
    <?php echo form_open(current_url());?>
    <div class="filters">
        <label for="providers_list">Provider: </label>
        <?php echo $providers_list; ?>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <label for="date_picker">Date from: </label>
        <input id="date_picker" type="text" name="date_from" value="<?php echo $date_from;?>" />
        <label for="date_picker_2">Date to: </label>
        <input id="date_picker_2" type="text" name="date_to" value="<?php echo $date_to;?>" />
        <input type="submit" value="Process" />
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
            <th>Last price</th>
            <th>Last sold(date)</th>
            <th>Units Sold BuyIn</th>
            <th>Trend</th>
            <th>Units Sold Amazon</th>
            <th>Trend</th>
            <th>Amazon Best Price</th>
            <th>Total Trend</th>
            <th>Checkbox</th>
            <th>Quantity needed</th>
            <th>Price</th>
            <th>Order</th>
            <th>Date</th>
        </tr>
    <?php foreach($overview as $row) :?>
        
        <?php 
        
        $checkbox = array(
                            'name'        => 'product_id[]',
                            'id'          => 'checkbox_product_id_'.$row['id'],
                            'value'       => $row['id'],
                            'checked'     => FALSE,
                            );


        ?>
        
        <tr id="product_id_<?php echo $row['id']?>" data-sku="<?php echo $row['sku'] ;?>">
            <td class="bold"><?php echo $row['sku'] ;?></td>
            <td><?php echo $row['product_name'] ;?></td>
            <td><?php echo $row['provider_name'] ;?></td>
            <td><?php echo $row['stock'] ;?></td>
            <td><?php echo number_format($row['price'], 2);?>&euro;</td>
            <td title="<?php echo $row['last_price_date'] ? 'Date of last price: '.$row['last_price_date'] : 'No data';?>"><?php echo $row['last_price'] ? number_format($row['last_price'], 2).'&euro;' : 'No data';?></td>
            <td><?php echo $row['date_of_last_purchase'] ;?></td>
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
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    <?php endforeach;?>
    </table>
    <div class="pagination">
    <?php echo $pagination;?>
    </div>
    <?php endif;?>
    <?php echo form_close();?>
</article>
<script>
    $(function(){
        $( "#radios" ).buttonset();
        $('#providers_list').combobox();
        
        $("#radios input").click(function(){
            $('#date_picker, #date_picker_2').val(null);
            $("form").submit();
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
    });
</script>