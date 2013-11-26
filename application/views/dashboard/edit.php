<div class="edit-ajax-container">
    <div id="ajax-msg">
        <?php echo validation_errors(); ?>
    </div>
    <?php echo form_open('/dashboard/save', 'id="edit-form"'); ?>
    <div class="left-top">
        <table>
            <tr>
                <td>Id</td>
                <td><?php echo $order->id;?></td>
            </tr>
            <tr>
                <td>Pedido</td>
                <td><?php echo $order->pedido;?></td>
            </tr>
            <tr>
                <td>Fechaentrada</td>
                <td><?php echo $order->fechaentrada;?></td>
            </tr>
            <tr>
                <td>Nombre</td>
                <td><input type="text" name="nombre" value="<?php echo $order->nombre;?>" /></td>
            </tr>
            <tr>
                <td>Direccion</td>
                <td><textarea name="direccion" rows="4"><?php echo htmlentities($order->direccion);?></textarea></td>
            </tr>
            <tr>
                <td>Telefono</td>
                <td><input type="text" name="telefono" value="<?php echo $order->telefono;?>" /></td>
            </tr>
            <tr>
                <td>Cpostal</td>
                <td><input type="text" name="codigopostal" value="<?php echo $order->codigopostal;?>" /></td>
            </tr>
            <tr>
                <td>Pais</td>
                <td><input type="text" name="pais" value="<?php echo $order->pais;?>" /></td>
            </tr>
            <tr>
                <td>Estado</td>
                <td><input type="text" name="estado" value="<?php echo htmlentities($order->estado);?>" /></td>
            </tr>
            <tr>
                <td>Forma de pago</td>
                <td><?php echo htmlentities($order->formadepago);?></td>
            </tr>
            <tr>
                <td>Ingresos</td>
                <td><input type="text" name="ingresos" value="<?php echo $order->ingresos;?>" /></td>
            </tr>
            <tr>
                <td>Gasto</td>
                <td><input type="text" name="gasto" value="<?php echo $order->gasto;?>" /></td>
            </tr>
        </table>
    </div>   
    <div class="right-top">
        <table>
            <tr>
                <td>
                    <label for="order_status" >Processado</label>
                    <select id="order_status" name="procesado">
                        <?php getStatusOptions($order->procesado);?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="comentarios">Comenatarios</label><br>
                    <textarea id="comentarios" name="comentarios" rows="5" cols="40"><?php echo htmlentities($order->comentarios);?></textarea>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="tracking">Tracking</label><br>
                    <input id="tracking" type="text" name="tracking" value="<?php echo htmlentities($order->tracking);?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="correo">Correo</label><br>
                    <input id="correo" type="email" name="correo" value="<?php echo htmlentities($order->correo);?>" />
                </td>
            </tr>
        </table>
        <br>
        <?php
        if($order->in_stokoni == 1)
        {
            $in_stokoni = '&nbsp;Stokoni&nbsp;';
        }
        else 
        {
            $in_stokoni = '';
        }
        ?>
        <b class="stokoni_in_edit_window"><?php echo $in_stokoni;?></b>
        <br>
        <a href="javascript:void(0);" onclick="Amazoni.get_order_for_print(<?php echo $order->id;?>)">
            <img src="<?php echo base_url().'assets/imgs/1-Normal-Printer-icon.png';?>" alt="Print Order" />
            Print Order
        </a>
    </div>
    <div class="bottom">
        <div>
            <table>
                <?php for ($i=1; $i<=10; $i++) { ?>
                <?php $k='sku'.$i; ?>
                <?php if (!empty($order->$k)) { ?>
                <?php if ($i == 1) { 
                            $required = 'required="required"';
                        } else { 
                            $required = '';
                        }
                ?>
                <tr>
                    <td><b>Sku<?php echo $i;?></b></td>
                    <td><input <?php echo $required;?> style="width:auto;"  type="text" value="<?php $k='sku'.$i; echo $order->$k;?>" name="<?php echo $k;?>" /></td>
                    <td><b>Cantidad<?php echo $i;?></b></td>
                    <td><input <?php echo $required;?> style="width:20px;" type="text" value="<?php $k='cantidad'.$i; echo $order->$k;?>" name="<?php echo $k;?>" /></td>
                    <td><b>Precio<?php echo $i;?></b></td>
                    <td><input <?php echo $required;?> style="width:40px;" type="text" value="<?php $k='precio'.$i; echo $order->$k;?>" name="<?php echo $k;?>" /></td>
                    <td><a href="javascript:void(0);" title="Delete" class="remove" onclick="Amazoni.clear_product_in_order(<?php echo $i;?>);return false;"></a></td>
                </tr>
                <?php } else { ?>
                <tr>
                    <td><b>Sku<?php echo $i;?></b></td>
                    <td><input <?php echo $required;?> style="width:auto;"  type="text" value="<?php $l = 'sku'.$i; $k='sku'.$i; echo !empty($order->$k)?$order->$k:null;?>" name="<?php echo $k;?>" /></td>
                    <td><b>Cantidad<?php echo $i;?></b></td>
                    <td><input <?php echo $required;?> style="width:20px;" type="text" value="<?php $k='cantidad'.$i; echo !empty($order->$l)?$order->$k:null;?>" name="<?php echo $k;?>" /></td>
                    <td><b>Precio<?php echo $i;?></b></td>
                    <td><input <?php echo $required;?> style="width:40px;" type="text" value="<?php $k='precio'.$i; echo !empty($order->$l)?$order->$k:null;?>" name="<?php echo $k;?>" /></td>
                    <td><a href="javascript:void(0);" title="Delete" class="remove" onclick="Amazoni.clear_product_in_order(<?php echo $i;?>);return false;"></a></td>
                </tr>
                <?php } ?>
                <?php } ?>
            </table>
        </div>
    </div>
    <div class="bottom">
        <div class="edit-buttons">
            <input type="submit" id="edit-save" value="Save" />
            &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="button" id="edit-close" value="Cancel" />
        </div>
    </div>
    <input type="hidden" name="id" value="<?php echo (int)$order->id;?>">
    <input type="hidden" name="fechaentrada" value="<?php echo $order->fechaentrada;?>">
    </form>
    <script>
        $(function(){
            $('#order_status').combobox();
        });
    </script>
</div>
