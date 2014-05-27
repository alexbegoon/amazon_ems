<?php
/**
 * Description of roturastock_report
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
?>
<article>
    <h1><?php echo $title; ?></h1>
    <?php if(count($orders) > 0):?>
    <div>
        <div class="pagination">
        <?php echo $pagination;?>
        </div>
        <table class="thin_table">
            <tr>
                <th>Fecha</th>
                <th>Id</th>
                <th>Pedido</th>
                <th>Procesado</th>
                <th>Ver</th>
            </tr>
            <?php foreach ($orders as $order):?>
            <?php 
                $procesado_class = strtolower($order->order_status);

                if (strpos($order->order_status, 'ENVIADO_') !== false) {
                    $procesado_class = 'enviado';
                }

                if (strpos($order->order_status, 'PREPARACION_') !== false) {
                    $procesado_class = 'preparacion';
                }
            
            
            ?>
            <tr id="<?php echo $order->order_id;?>">
                <td><?php echo $order->date_when_out_of_stock;?></td>
                <td><?php echo $order->order_id;?></td>
                <td class="bold"><?php echo $order->order_name;?></td>
                <td class="procesado <?php echo $procesado_class;?>"><?php echo $order->order_status;?></td>
                <td><a href="#" class="edit" onclick="edit(<?php echo $order->order_id;?>);return false;"></a></td>
            </tr>
            <?php endforeach;?>
        </table>
        <div class="pagination">
        <?php echo $pagination;?>
        </div>
    </div>
    <?php else: ?>
    <p>Orders not found.</p>
    <?php endif;?>
</article>
