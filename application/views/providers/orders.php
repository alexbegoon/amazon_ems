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
            <th colspan="3">Action</th>
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
            <td title="Send order to provider"><span class="email_send_icon pointer_cursor"></span></td>
            <td title="View order"><span onclick="Amazoni.get_provider_order('<?php echo $order->id;?>')" class="invoice_icon pointer_cursor"></span></td>
            <td title="Download order in Excel format"><span class="excel_icon pointer_cursor"></span></td>
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