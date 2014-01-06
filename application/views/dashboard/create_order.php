<div class="edit-ajax-container">
    <div id="ajax-msg">
        <?php echo validation_errors(); ?>
    </div>
    <?php echo form_open('/dashboard/save', 'id="create_order_form"'); ?>
    <?php if(!empty($order) && count($order)>3) { ?>
    <div class="left-top">
        <table>
            <tr>
                <td>Pedido</td>
                <td><input required="required" type="text" name="pedido" value="<?php echo $order['pedido'];?>" /></td>
            </tr>
            <tr>
                <td>Nombre</td>
                <td><input required="required" type="text" name="nombre" value="<?php echo $order['nombre'];?>" /></td>
            </tr>
            <tr>
                <td>Fechaentrada</td>
                <td><input required="required" type="text" name="fechaentrada" id="fechaentrada"  value="<?php echo $order['fechaentrada'];?>" /></td>
            </tr>
            <tr>
                <td>Direccion</td>
                <td><textarea name="direccion" rows="4"><?php echo htmlentities($order['direccion']);?></textarea></td>
            </tr>
            <tr>
                <td>Telefono</td>
                <td><input required="required" type="text" name="telefono" value="<?php echo $order['telefono'];?>" /></td>
            </tr>
            <tr>
                <td>Cpostal</td>
                <td><input required="required" type="text" name="codigopostal" value="<?php echo $order['codigopostal'];?>" /></td>
            </tr>
            <tr>
                <td>
                    <label for="select_web">Web</label>
                </td>
                <td>
                    <?php echo $web_fields_list;?> 
                </td>
            </tr>
            <tr>
                <td>
                    <label for="shipping_cost_id">Shipping</label>
                </td>
                <td>
                    <select required="required" name="shipping_cost_id" id="shipping_cost_id"></select> 
                </td>
            </tr>
            <tr>
                <td>Pais</td>
                <td>
                    <select name="pais" id="select_pais" required="required"></select>
                </td>
            </tr>
            <tr>
                <td>Estado</td>
                <td><input required="required" type="text" name="estado" value="<?php echo htmlentities($order['estado']);?>" /></td>
            </tr>
            <tr>
                <td>Forma de pago</td>
                <td><input type="text" name="formadepago" value="<?php echo htmlentities($order['formadepago']);?>" /></td>
            </tr>
            <tr>
                <td>Ingresos</td>
                <td><input type="text" name="ingresos" value="<?php echo $order['ingresos'];?>" /></td>
            </tr>
            <tr>
                <td>Gasto</td>
                <td><input readonly title="Will be calculated automatically" type="text" name="gasto" value="<?php echo $order['gasto'];?>" /></td>
            </tr>
        </table>
    </div>   
    <div class="right-top">
        <table>
            <tr>
                <td>
                    <label for="order_status" >Processado</label>
                    <select id="order_status" name="procesado">
                        <?php getStatusOptions($order['procesado']);?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="comentarios">Comenatarios</label><br>
                    <textarea id="comentarios" name="comentarios" rows="5" cols="40"><?php echo htmlentities($order['comentarios']);?></textarea>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="tracking">Tracking</label><br>
                    <input id="tracking" type="text" name="tracking" value="<?php echo htmlentities($order['tracking']);?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="correo">Correo</label><br>
                    <input required="required" id="correo" type="email" name="correo" value="<?php echo htmlentities($order['correo']);?>" />
                </td>
            </tr>
        </table>        
    </div>
    <div class="bottom">
        <div>
            <table>
                <?php for ($i=1; $i<=10; $i++) { ?>
                <?php $k='sku'.$i; ?>
                <?php if ($i == 1) { 
                            $required = 'required="required"';
                        } else { 
                            $required = '';
                        }
?>
                <tr>
                    <td><b>Sku<?php echo $i;?></b></td>
                    <td><input <?php echo $required;?> style="width:auto;"  type="text" value="<?php $k='sku'.$i; echo isset($order[$k])?$order[$k]:'';?>" name="<?php echo $k;?>" /></td>
                    <td><b>Cantidad<?php echo $i;?></b></td>
                    <td><input <?php echo $required;?> style="width:20px;" type="text" value="<?php $k='cantidad'.$i; echo isset($order[$k])?$order[$k]:'';?>" name="<?php echo $k;?>" /></td>
                    <td><b>Precio<?php echo $i;?></b></td>
                    <td><input <?php echo $required;?> style="width:40px;" type="text" value="<?php $k='precio'.$i; echo isset($order[$k])?$order[$k]:'';?>" name="<?php echo $k;?>" /></td>
                    <td><a href="javascript:void(0);" title="Delete" class="remove" onclick="Amazoni.clear_product_in_order(<?php echo $i;?>);return false;"></a></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
        <?php } else { ?>
        <div class="left-top">
        <table>
            <tr>
                <td>Pedido</td>
                <td><input required="required" type="text" name="pedido" value="" /></td>
            </tr>
            <tr>
                <td>Nombre</td>
                <td><input required="required" type="text" name="nombre" value="" /></td>
            </tr>
            <tr>
                <td>Fechaentrada</td>
                <td><input required="required" type="text" name="fechaentrada" id="fechaentrada"  value="" /></td>
            </tr>
            <tr>
                <td>Direccion</td>
                <td><textarea name="direccion" rows="4"></textarea></td>
            </tr>
            <tr>
                <td>Telefono</td>
                <td><input required="required" type="text" name="telefono" value="" /></td>
            </tr>
            <tr>
                <td>Cpostal</td>
                <td><input required="required" type="text" name="codigopostal" value="" /></td>
            </tr>
            <tr>
                <td>
                    <label for="select_web">Web</label>
                </td>
                <td>
                    <?php echo $web_fields_list;?> 
                </td>
            </tr>
            <tr>
                <td>Pais</td>
                <td>
                    <select name="pais" id="select_pais" required="required"></select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="shipping_cost_id">Shipping</label>
                </td>
                <td>
                    <select required="required" name="shipping_cost_id" id="shipping_cost_id"></select> 
                </td>
            </tr>
            <tr>
                <td>Estado</td>
                <td><input required="required" type="text" name="estado" value="" /></td>
            </tr>
            <tr>
                <td>Forma de pago</td>
                <td><input type="text" name="formadepago" value="" /></td>
            </tr>
            <tr>
                <td>Ingresos</td>
                <td><input type="text" name="ingresos" value="" /></td>
            </tr>
            <tr>
                <td>Gasto</td>
                <td><input type="text" name="gasto" value="" readonly title="Will be calculated automatically"/></td>
            </tr>
        </table>
    </div>   
    <div class="right-top">
        <table>
            <tr>
                <td>
                    <label for="order_status" >Processado</label>
                    <select id="order_status" name="procesado">
                        <?php getStatusOptions();?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="comentarios">Comenatarios</label><br>
                    <textarea id="comentarios" name="comentarios" rows="5" cols="40"></textarea>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="tracking">Tracking</label><br>
                    <input id="tracking" type="text" name="tracking" value="" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="correo">Correo</label><br>
                    <input required="required" id="correo" type="email" name="correo" value="" />
                </td>
            </tr>
        </table>
    </div>
    <div class="bottom">
        <div>
            <table>
                <?php for ($i=1; $i<=10; $i++) { ?>
                <?php $k='sku'.$i; ?>
                <?php if ($i == 1) { 
                    $required = 'required="required"';
                } else { 
                    $required = '';
                }
?>
                <tr>
                    <td><b>Sku<?php echo $i;?></b></td>
                    <td><input <?php echo $required;?> style="width:auto;"  type="text" value="" name="<?php $k='sku'.$i;echo $k;?>" /></td>
                    <td><b>Cantidad<?php echo $i;?></b></td>
                    <td><input <?php echo $required;?> style="width:20px;" type="text" value="" name="<?php $k='cantidad'.$i;echo $k;?>" /></td>
                    <td><b>Precio<?php echo $i;?></b></td>
                    <td><input <?php echo $required;?> style="width:40px;" type="text" value="" name="<?php $k='precio'.$i;echo $k;?>" /></td>
                    <td><a href="javascript:void(0);" title="Delete" class="remove" onclick="Amazoni.clear_product_in_order(<?php echo $i;?>);return false;"></a></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
        <?php } ?>
        
    <div class="bottom">
        <div class="edit-buttons">
            <input type="submit" id="edit-save" value="Save" />
            &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="button" id="edit-close" value="Cancel" />
        </div>
    </div> 
    </form>
    <script>
        $(function() {
            $('#order_status').combobox();
            $('#select_pais').combobox();
            $('#select_web').combobox();
            $('#shipping_cost_id').combobox();
            
            
            $('#fechaentrada').datepicker({ 
                gotoCurrent: true,
                dateFormat: 'yy-mm-dd',
                showAnim: "puff"
            });
            
            
            $('#select_web').combobox({
                select: function( event, ui ) {
                    
                    $('#select_pais').find('option')
                                    .remove()
                                    .end();
                            
                    $.getJSON( url_before_index + "index.php/dashboard/update_country_list/" + $('#select_web').val(), function( data ) {
                            
                            $.each(data.country_list, function( index, value ) {
                                $('#select_pais').append(value);
                            });
                            
                    });    
                }
            });
            
            $('#select_pais').combobox({
                select: function( event, ui ) {
                    
                    $('#shipping_cost_id').find('option')
                                    .remove()
                                    .end();
                    $.post( url_before_index + "index.php/dashboard/get_available_shipping/",{country_code:$('#select_pais').val(),web:$('#select_web').val()}, function( data ) {
                         console.log(data);
                         $.each(data.shipping, function( index, value ) {
                                $('#shipping_cost_id').append(value);
                            });

                      });        
                }
            });
            
            
            <?php if(validation_errors()){?>
            $("#ajax-msg").fadeIn();
            <?php } ?>
            
            
          });
    </script>
</div>