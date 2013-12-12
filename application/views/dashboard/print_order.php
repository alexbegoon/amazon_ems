<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Description of print_order
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
$this->lang = new CI_Lang();
$this->lang->load('print_order', strtolower($order->web_field->language));
?>
<div>
    <div class="A4">
        <span style="font-size:14px;"><?php echo $this->lang->line('print_order_packing_slip');?>:</span>
        <br>
        <h1 style="font-size:24px;font-weight:bold;">
            <?php echo humanize($order->nombre); ?>
            <br>
            <?php echo $order->direccion; ?>
            <br>
            <?php echo $order->codigopostal; ?>&nbsp;
            <?php echo $order->estado; ?>
            <br>
            <?php echo $order->country; ?>
        </h1>
        <hr>
        <span style="font-weight:bold;"><?php echo $this->lang->line('print_order_id');?>: <?php echo $order->pedido;?></span>
        <br>
        <span><?php echo $order->web_field->title;?></span>
        <hr>
        <table class="single_order_table">
            <tr>
                <th><?php echo $this->lang->line('print_order_units');?></th>
                <th><?php echo $this->lang->line('print_order_product_label');?></th>
                <th><?php echo $this->lang->line('print_order_price');?></th>
                <th><?php echo $this->lang->line('print_order_subtotal_price');?></th>
            </tr>
            <?php for($i=1;$i<=10;$i++) : ?>
            <?php if (!empty($order->{'sku'.$i})) : ?>
            <tr>
                <td><?php echo $order->{'cantidad'.$i};?></td>
                <td>
                    <span>
                        <?php echo !empty($order->products[$i]->product_name) ? $order->products[$i]->product_name : '***WARNING ! Amazoni dont know such product. SKU: '.$order->{'sku'.$i}.'. Go to "Products" link ( http://www.buyin.eu/amazoni4/index.php/products/page/ ) and setup this product.***';?>
                    </span>
                    <br>
                    <span>
                        <?php echo $this->lang->line('print_order_sku');?> : 
                        <?php echo $order->{'sku'.$i};?>
                    </span>
                </td>
                <td>
                    <span>
                        <?php echo number_format($order->{'precio'.$i}, 2);?>&euro;
                    </span>
                </td>
                <td>
                    <span>
                        <?php echo number_format(($order->{'cantidad'.$i} * $order->{'precio'.$i}), 2);?>&euro;
                    </span>
                </td>
            </tr>
            <?php endif;?>
            <?php endfor;?>
            <tr>
                <td colspan="3" style="text-align:right;vertical-align:bottom;">
                    <br>
                    <?php echo $this->lang->line('print_order_total');?>: 
                </td>
                <td>
                    <?php echo $this->lang->line('print_order_shipping_fee');?>: <?php echo number_format($order->shipping_cost, 2); ?>&euro;
                    <hr>
                    <?php echo number_format($order->ingresos, 2);?>&euro;
                </td>
            </tr>
        </table>
        <div class="footer">
            <p>
                <?php echo $this->lang->line('print_order_footer');?>
            </p>
        </div>
    </div>
</div>