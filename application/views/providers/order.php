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
    <?php foreach($order as $row):?>
        <tr>
            <td><?php echo $row->product_name;?></td>
            <td class="bold"><?php echo $row->sku;?></td>
            <td><?php echo $row->quantity;?></td>
            <td><?php echo $row->price;?> &euro;</td>
        </tr>
            <?php $total += $row->price;?>
    <?php endforeach;?>
        <tr>
            <td></td>
            <td></td>
            <td class="bold">Total:</td>
            <td class="bold"><?php echo $total;?> &euro;</td>
        </tr>
    </table>
</div>