<?php
/**
 * Description of order
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
?>
<div>
    <table class="thin_table">
        <tr>
            <th>NOMBRE</th>
            <th>EAN DEL PRODUCTO</th>
            <th>CANTIDAD</th>
            <th>PRECIO</th>
        </tr>
        <?php $total = 0;?>
        <?php $subtotal = 0;?>
    <?php foreach($order as $row):?>
        <tr>
            <td><?php echo $row->product_name;?></td>
            <td class="bold"><?php echo $row->sku;?></td>
            <td><?php echo $row->quantity;?></td>
            <td><?php echo $row->price;?> &euro;</td>
        </tr>
            <?php $total += $row->price;?>
        <?php $subtotal += $row->price;?>
    <?php endforeach;?>
        <?php if(!empty($extra_items)):?>
    <tr>
        <td></td>
        <td></td>
        <td class="bold">SubTotal:</td>
        <td class="bold"><?php echo number_format($subtotal,2);?> &euro;</td>
    </tr>
    <?php endif;?>
    <?php if(!empty($extra_items)):?>
                <?php $subtotal=0;?>
        <tr>
            <td class="header-td" colspan="4">PRODUCTOS ADICIONALES</td>
        </tr>
        <?php foreach($extra_items as $i): ?>
        <tr>
            <td><?php echo $i->product_name;?></td>
            <td class="bold"><?php echo $i->sku;?></td>
            <td><?php echo $i->quantity;?></td>
            <td><?php echo $i->price;?> &euro;</td>
        </tr>
        <?php $total += $i->price;?>
        <?php $subtotal += $i->price;?>
        <?php endforeach;?>
        <tr>
            <td></td>
            <td></td>
            <td class="bold">SubTotal:</td>
            <td class="bold"><?php echo number_format($subtotal,2);?> &euro;</td>
        </tr>
    <?php endif;?>
        <tr>
            <td></td>
            <td></td>
            <td class="bold">Total:</td>
            <td class="bold"><?php echo number_format($total,2);?> &euro;</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td><a title="Send order to provider" href="<?php echo base_url('index.php/providers/send_order/'.$id."/".$return_url);?>" onclick="Amazoni.confirm_order_sending(this);return false;" ><span class="email_send_icon pointer_cursor"></span></a></td>
            <td><a title="Download order in Excel format" href="<?php echo base_url('index.php/providers/download_order/'.$id);?>"><span class="excel_icon pointer_cursor"></span></a></td>
        </tr>
    </table>
</div>