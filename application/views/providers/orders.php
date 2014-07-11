<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Description of orders
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
?>
<article>
    <h1><?php echo $title;?></h1>
    <?php echo form_open(current_url(), 'id="provider-orders-form"');?>
    <div class="filters">
        <label for="providers_list">Provider:</label>
        <?php echo $providers_dropdown;?>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <label for="date_picker">Date from: </label>
        <input type="text" name="date_from" value="<?php echo $post_data['date_from']; ?>" id="date_picker">
        <label for="date_picker_2">Date to: </label>
        <input type="text" name="date_to" value="<?php echo $post_data['date_to']; ?>" id="date_picker_2">
        <input type="submit" value="Buscar">
    </div>
    <?php if($total_orders > 0) :?>
    <p><?php echo $total_orders;?> orders found.</p>
    <div class="pagination">
    <?php echo $pagination;?>
    </div>
    <table class="thin_table">
        <tr>
            <th>Order ID</th>
            <th>Provider</th>
            <th>Created on</th>
            <th>Created by</th>
            <th>Sent to provider</th>
            <th colspan="5">Action</th>
        </tr>
        <?php foreach($orders as $order):?>
        <tr>
            <td><?php echo $order->id;?></td>
            <td><?php echo $order->provider_name;?></td>
            <td><?php echo $order->created_on;?></td>
            <td><?php echo $this->ion_auth->user($order->created_by)->row()->first_name .
            ' '.
            $this->ion_auth->user($order->created_by)->row()->last_name;?></td>
            <td><?php echo $order->sent_to_provider == 0 ? '<span class="red">No</span>' : $order->sending_date ;?></td>
            <td title="Send order to provider"><a href="<?php echo base_url('index.php/providers/send_order/'.$order->id.'/'.base64_url_encode(current_url()));?>" onclick="Amazoni.confirm_order_sending(this);return false;" ><span class="email_send_icon pointer_cursor"></span></a></td>
            <td title="View order"><span onclick="Amazoni.get_provider_order('<?php echo $order->id;?>', '<?php echo base64_url_encode(current_url());?>')" class="invoice_icon pointer_cursor"></span></td>
            <td title="Download order in Excel format"><a href="<?php echo base_url('index.php/providers/download_order/'.$order->id);?>"><span class="excel_icon pointer_cursor"></span></a></td>
            <td title="Add more products to the order"><a href="javascript:void(0);" onclick="Amazoni.add_products_to_provider_order(<?php echo $order->id; ?>);"><span class="shop-cart-add-icon"></span></a></td>
            <td><a href="javascript:void(0);" onclick="Amazoni.claim_provider_order(<?php echo $order->id; ?>);"><b class="error_icon" title="Report about problem with provider report"></b></a></td>
        </tr>
        <?php endforeach;?>
    </table>
    <div class="pagination">
    <?php echo $pagination;?>
    </div>
    <p><?php echo $total_orders;?> orders found.</p>
    <?php endif;?>
    <?php if($total_orders <= 0):?>
    <p>Orders not found.</p>
    <?php endif;?>
    <?php echo form_close();?>
</article>
<script type="text/javascript">
$(function(){
    $('#providers_list').combobox();
    
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
                },
                onClose: function(){
                    
                    $('#date_picker, #date_picker_2').attr('required','required');
                                        
                    if( $('#date_picker').val() == '' && $('#date_picker').val() == '' )
                    {
                        $('#date_picker, #date_picker_2').removeAttr('required');
                    }
                    
                }
            });
});
</script>