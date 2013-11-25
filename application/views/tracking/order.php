<?php echo form_open(base_url().'index.php/tracking/save_tracking' , array('id' => 'tracking_order_form'));?>
    <div id="ajax-msg">
            <?php echo validation_errors(); ?>
    </div>

<?php if(count($orders) == 1 && is_array($orders)) { ?>
    <div class="tracking_order">
        <label for="">Número de pedido</label>
        <input type="text" name="pedido" value="<?php echo $orders[0]->pedido?>" required="required" readonly />
        <br>
        <br>
        <label for="">Nombre del Cliente</label>
        <input type="text" name="nombre" value="<?php echo $orders[0]->nombre?>" required="required" />
        <br>
        <br>
        <label for="">Email del cliente</label>
        <input type="email" name="correo" value="<?php echo $orders[0]->correo?>" required="required" />
        <br>
        <br>
        <label for="">Tracking del Cliente</label>
        <input type="text" name="tracking" value="<?php echo $orders[0]->tracking?>" required="required" />
        <br>
        <br>
        <label for="">Web del Cliente</label>
        <input type="text" name="web" value="<?php echo $orders[0]->web?>" required="required" readonly />
        <br>
        <br>
        <label for="select_company">Compañía de Transporte</label>
        <select id="select_company" name="id_shipping_company" value="" required="required">
            <?php echo $shipping_companies_list;?>
        </select>
        <br>
        <br>
        <input type="hidden" name="id" value="<?php echo $orders[0]->id?>" />
        <input type="submit" value="Enviar Tracking" />
    </div>
    <script>
        $( "#select_company" ).combobox();
    </script>
<?php } elseif(count($orders) > 1) { ?>
    <table class="thin_table pointer" id="order_list">
        <tr>
            <th>Pedido</th>
            <th>Web</th>
            <th>Nombre</th>
            <th>Email</th>
        </tr>
    <?php $i=0;?>
    <?php foreach($orders as $order) { ?>
    <?php $i++;?>
        <script>
            var order_<?php echo $i;?> = '<?php echo json_encode($order)?>';
        </script>
        <tr onclick='tracking_form(order_<?php echo $i;?>);'>
            <td class="bold"><?php echo $order->pedido?></td>
            <td><?php echo $order->web?></td>
            <td><?php echo htmlentities($order->nombre)?></td>
            <td><?php echo $order->correo?></td>
        </tr>
    <?php } ?>
    </table>
<?php } else { ?>
    <p>Order not found</p>
<?php } ?>
</form>
    <script>
        function tracking_form(order)
        {
            var data = $.parseJSON(order);
            
            $('#order_list').remove();
            
            if(!data.tracking)
            {
                data.tracking = '';
            }
            
            var str = '';
            
            str = str + '<div class="tracking_order">';
            
            str = str + '<label for="">Número de pedido</label>';
            str = str + '<input type="text" name="pedido" value="'+data.pedido+'" required="required" readonly />';
            str = str + '<br><br>';
            str = str + '<label for="">Nombre del Cliente</label>';
            str = str + '<input type="text" name="nombre" value="'+data.nombre+'" required="required" />';
            str = str + '<br><br>';
            str = str + '<label for="">Email del cliente</label>';
            str = str + '<input type="email" name="correo" value="'+data.correo+'" required="required" />';
            str = str + '<br><br>';
            str = str + '<label for="">Tracking del Cliente</label>';
            str = str + '<input type="text" name="tracking" value="'+data.tracking+'" required="required" />';
            str = str + '<br><br>';
            str = str + '<label for="">Web del Cliente</label>';
            str = str + '<input type="text" name="web" value="'+data.web+'" required="required" readonly />';
            str = str + '<br><br>';
            str = str + '<label for="select_company">Compañía de Transporte</label>';
            str = str + '<select id="select_company" name="id_shipping_company" value="" required="required">';
            str = str + '<?php echo $shipping_companies_list;?>';
            str = str + '</select>';
            str = str + '<br><br>';
            str = str + '<input type="hidden" name="id" value="'+data.id+'" />';
            str = str + '<input type="submit" value="Enviar Tracking" />';
            str = str + '<script>';
            str = str + '$( "#select_company" ).combobox();';
            str = str + '</scr'+'ipt>';
            str = str + '</div>';
            
            $('#tracking_order_form').append(str);
        }
        
        $(function() {
            var search = $('#pedido').val();

            if (search !== '') {
                var table = $('table');

                table.find('tr').each(function(index, row) {

                    var allCells = $(row).find('td');

                    if(allCells.length > 0) {
                        var found = false;

                        allCells.each(function(index, td) {

                            var regExp = new RegExp(search, 'i');

                            if (regExp.test($(td).text())) {
        //                        //console.log($(td));
                                $(td).html(function(index, oldHTML) {
                                    return oldHTML.replace(search, '<b style="background-color:#ffff00;">$&</b>');
                                });
                            }
                        });
                    }
                });    
            }

          });
    </script>
<?php if(validation_errors() != '') { ?>
<script>
    $("#ajax-msg").fadeIn();
</script>
<?php } ?>
