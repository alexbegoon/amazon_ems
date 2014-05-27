<?php
/**
 * Description of verify_products
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
?>
<article>
    <h1><?php echo $title; ?></h1>
    <?php if (count($orders) > 0){?>
        <div>
            <input type="button" value="Start Verifying" class="start_verifying" />
            <br>
            <br>
            <table class="thin_table" id="orders_table">
                <tr>
                    <th>Order ID</th>
                    <th>Order Name</th>
                    <th>Order Status</th>
                    <th>Web</th>
                    <th>Current Action</th>
                </tr>
                <?php foreach($orders as $order):?>
                <tr id="order_id_<?php echo $order->id; ?>" data-orderid="<?php echo $order->id; ?>">
                    <td><?php echo $order->id; ?></td>
                    <td class="bold"><?php echo $order->pedido; ?></td>
                    <td><?php echo $order->procesado; ?></td>
                    <td class="<?php echo strtolower($order->web); ?>"><?php echo $order->web; ?></td>
                    <td id="status_<?php echo $order->id; ?>"></td>
                </tr>
                <?php endforeach;?>
            </table>
            <br>
            <br>
            <input type="button" value="Start Verifying" class="start_verifying" />
        </div>
    <?php } else {?>
    <p>Orders to verify not found.</p>
    <?php }?>
</article>
<script type="text/javascript">
$(function(){
    Amazoni.order_loader = null;
    var verify_products_accepted = <?php echo $this->session->userdata('verify_products_accepted') ? 'false' : 'true' ;?>;
    var verifying_started = false;
    var orders = [];
    
    var verified_orders_JSON = '<?php echo json_encode($verified_orders);?>';
    var verified_orders = $.parseJSON(verified_orders_JSON);
    
    if(verified_orders)
    {
        $(verified_orders).each(function(item,value){
            if($('#status_'+value['order_id']).length !== 0)
            {
                $('#status_'+value['order_id']).html(value['status']).attr('title', 'Verified on: '+value['date']);
            }
        });
    }
    
    
    $('.start_verifying').click(function(e){
        
        if(verifying_started === false)
        {
            if(verify_products_accepted)
            {
                var rows = $('#orders_table > tbody  > tr');
                var i = 0;
                rows.each(function() {
                    var order_id = $(this).attr('data-orderid');
                    var status_cell = $('#status_'+order_id);

                    if($.isNumeric(order_id))
                    {
                       orders[i++] = {status_cell:status_cell, order_id:order_id};
                    }
                });
                
                ajax_request(orders);
            }
            else
            {
                alert('You have already verified these orders.');
            }
        }
        else
        {
            alert('You have already verified these orders.');
        }
        
        verifying_started = true;
        
    });
    
    
    function text_loader(cell)
    {
        cell.html('Checking');
        cell.data('cellInterval', setInterval(function() {
                                        cell.append('.');

                                        if(cell.html() === 'Checking....')
                                        {
                                            cell.html('Checking');
                                        }
                                    }, 500));
    }
    
    function ajax_request(orders, i)
    {
        if(!$.isNumeric(i))
        {
            i = 0;
        }
        
        if(!orders[i])
        {
            return false;
        }
        
        var order_id = orders[i]['order_id'];
        var status_cell = orders[i]['status_cell'];
        
        if($.isNumeric(order_id))
        {
            $.ajax({
                type: 'GET',
                url: '<?php echo base_url('index.php/dashboard/verify_order')?>/'+order_id,
                beforeSend: function(jqXHR){
                    text_loader(status_cell);                            
                },
                success: function(response){
                    status_cell.html(response);
                    if(response === 'Done')
                    {
                        status_cell.addClass('success');
                    }
                },
                error: function(jqXHR,textStatus,errorThrown){
                    status_cell.html('Error');
                    status_cell.addClass('error');
                    status_cell.attr('title', textStatus + ' ' +errorThrown);
                },
                complete: function(jqXHR,textStatus){
                    clearInterval(status_cell.data('cellInterval'));
                    
                    ajax_request(orders, ++i);
                }
            });
        }
    }
    
});
</script>