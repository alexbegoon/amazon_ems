<?php
/**
 * Description of process_orders
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
?>
<article>
    <h1><?php echo $title;?></h1>
    <div>
        <table class="thin_table">
            <?php if(!empty($process_rows)):?>
            <tr>
                <th>Edit</th>
                <th>Order ID</th>
                <th>Order Name</th>
                <th>Web</th>
                <th>Status</th>
                <th>Verifying Message</th>
            </tr>
            
            <?php foreach ($process_rows['orders'] as $key => $order):?>
            <tr>
                <td><a href="#" class="edit" onclick="edit(<?php echo $order->id;?>);return false;"></a></td>
                <td><?php echo $order->id;?></td>
                <td class="bold"><?php echo $order->pedido;?></td>
                <td class="<?php echo strtolower($order->web); ?>"><?php echo $order->web;?></td>
                <td><?php echo $order->procesado;?></td>
                <td><?php echo $process_rows['statuses'][$key];?></td>
            </tr>
            <?php endforeach;?>
            <?php endif;?>
            <?php if(empty($process_rows)):?>
            <p>
                No actions registered. Orders not modified.
            </p>
            <?php endif;?>
        </table>
    </div>
</article>