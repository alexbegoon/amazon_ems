<div class="edit-ajax-container">
    <?php if (!empty($orders)) {
        $total = 0;
        ?>
        <table>
            <tr>
                <th>Id</th>
                <th>Pedido</th>
                <th>Web</th>
                <th>Subtotal</th>
            </tr>
            <?php foreach ($orders as $order) {
                ?>
            <tr>
                <td><?php echo $order->id?></td>
                <td class="bold"><?php echo $order->pedido?></td>
                <td><?php echo $order->web?></td>
                <td><?php echo $order->ingresos?></td>
            </tr>   
                <?php  
                $total += $order->ingresos;
            }?>
            <tr>
                <td colspan="4" style="background: #e8eaeb;"></td>
            </tr>
            
            <tr class="total">
                <td></td>
                <td></td>
                <td>Total:</td>
                <td><?php echo $total?></td>
            </tr>
        </table>
        <?php
    } ?>
</div>