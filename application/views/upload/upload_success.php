<article>
    <h1><?php echo $title;?></h1>
<h3>Your file was successfully uploaded!</h3>

<?php if(!empty($orders)) { ?>
<form method="POST" action="<?php echo base_url().'index.php/upload/store';?>">


<h3>Please check orders and push Store button</h3>


<br>
<p><?php echo anchor($url, 'Upload Another File!'); ?></p>
<br>
<input type="submit" value="Store orders" />
<p><?php echo count($orders);?> orders was parsed</p>
<table class="orders">
            <tr>
                <th>Pedido</th>
                <th>Stokoni</th>
                <th>Nombre</th>
                <th>Fechaentrada</th>
                <th>Direccion</th>
                <th>Estado</th>
                <th>Cpostal</th>
                <th>Telefono</th>
                <th>Pais</th>
                <th>Procesado</th>
                <th>Ingresos</th>
                <th>Gasto</th>
                <th>Web</th>
            </tr>
            <?php foreach ($orders as $order) { ?>
            
            <?php 
                        
            
                if($order->in_stokoni == 1)
                {
                    $in_stokoni = '&nbsp;Stokoni&nbsp;';
                }
                else 
                {
                    $in_stokoni = '';
                }
            
                $procesado_class = strtolower($order->procesado);

                if (strpos($order->procesado, 'ENVIADO_') !== false) {
                    $procesado_class = 'enviado';
                }

                if (strpos($order->procesado, 'PREPARACION_') !== false) {
                    $procesado_class = 'preparacion';
                }
                
                $order_details = '';
                
                $order_details = "<table class=thin_table><tr><th>SKU</th><th>Price</th><th>Qty</th></tr>";
                for ($i = 1; $i <= 10; $i++)
                {
                    $sku        = 'sku'.$i;
                    $price      = 'precio'.$i;
                    $qty        = 'cantidad'.$i;
                    if (!empty($order->$sku)) {
                        $order_details .= '<tr>';
                        $order_details .= '<td>'.$order->$sku.'</td>';
                        $order_details .= '<td>'.number_format($order->$price,2).'&euro;</td>';
                        $order_details .= '<td>'.$order->$qty.'</td>';
                        $order_details .= '</tr>';
                    }
                }
                
                $order_details .= '</table>';
            ?>
            
            <tr onclick="open_modal_with_content('<?php echo $order_details;?>');" title="Click for the details">
                <!-- Pedido -->
                <td class="bold"><?php echo $order->pedido ;?></td>
                <!-- Stokoni -->
                <td class="bold"><b class="stokoni"><?php echo $in_stokoni ;?></b></td>
                <!-- Nombre -->
                <td><?php echo  htmlentities($order->nombre) ;?></td>
                <!-- Fechaentrada -->
                <td><?php echo $order->fechaentrada ;?></td>
                <!-- Direccion -->
                <td><?php echo htmlentities($order->direccion) ;?></td>
                <!-- Estado -->
                <td><?php echo htmlentities($order->estado) ;?></td>
                <!-- Cpostal -->
                <td><?php echo $order->codigopostal;?></td>
                <!-- Telefono -->
                <td><?php echo $order->telefono;?></td>
                <!-- Pais -->
                <td><?php echo $order->pais ;?></td>
                <!-- Procesado -->
                <td class="<?php echo $procesado_class;?>"><?php echo $order->procesado ;?></td>
                <!-- Ingresos -->
                <td class="ingreso"><?php echo number_format($order->ingresos, 2);?>&euro;</td>
                <!-- Gasto -->
                <td class="gasto"><?php echo number_format($order->gasto, 2);?>&euro;</td>
                <!-- Web -->
                <td class="<?php echo strtolower($order->web);?>"><?php echo $order->web ;?></td>
                
            </tr>
            
            <? } ?>
        </table>
<p><?php echo count($orders);?> orders was parsed</p>
<input type="submit" value="Store orders" />
<br>
<br>
<p><?php echo anchor($url, 'Upload Another File!'); ?></p>
<input type="hidden" name="url" value="<?php echo current_url();?>" />
</form>
<?php } else { ?>

<ul>
<?php foreach ($upload_data as $item => $value):?>
<li><?php echo $item;?>: <?php echo $value;?></li>
<?php endforeach; ?>
</ul>
<p><?php echo anchor($url, 'Upload Another File!'); ?></p>

<?php } ?>

</article>
<div id="modal_window" class="modal_window" title="">
</div>