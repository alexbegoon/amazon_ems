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
            <?php foreach($order->products as $product) : ?>
            <?php if (!empty($product->sku)) : ?>
            <tr>
                <td><?php echo $product->quantity;?></td>
                <td>
                    <span>
                        <?php echo !empty($product->product_name) ? $product->product_name : '***WARNING ! Amazoni dont know such product. SKU: '.$product->sku.'. Go to "Products" link ( http://www.buyin.eu/amazoni4/index.php/products/page/ ) and setup this product.***';?>
                    </span>
                    <br>
                    <span>
                        <?php echo $this->lang->line('print_order_sku');?> : 
                        <?php echo '#'.$product->sku;?>
                    </span>
                </td>
                <td>
                    <span>
                        <?php echo number_format($product->price, 2);?>&euro;
                    </span>
                </td>
                <td>
                    <span>
                        <?php echo number_format(($product->price * $product->quantity), 2);?>&euro;
                    </span>
                </td>
            </tr>
            <?php endif;?>
            <?php endforeach;?>
            <tr>
                <td colspan="3" style="text-align:right;vertical-align:bottom;">
                    <br>
                    <?php echo $this->lang->line('print_order_total');?>: 
                </td>
                <td>
                    <?php echo $this->lang->line('print_order_shipping_fee');?>: <?php echo number_format($order->shipping_cost <= 0 ? 0 : $order->shipping_cost, 2); ?>&euro;
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