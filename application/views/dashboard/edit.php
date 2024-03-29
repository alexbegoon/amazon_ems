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
                <td>Shipping method</td>
                <td><?php echo htmlentities($order->shipping_phrase);?></td>
            </tr>
            <tr>
                <td>Ingresos</td>
                <td><input type="text" name="ingresos" value="<?php echo round($order->ingresos,2);?>" /></td>
            </tr>
            <tr>
                <td>Gasto</td>
                <td><input type="text" name="gasto" value="<?php echo round($order->gasto,2);?>" /></td>
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
            <table id="order_items_list">
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
                    <td><input <?php echo $required;?> style="width:40px;" type="text" value="<?php $k='precio'.$i; echo round($order->$k,2);?>" name="<?php echo $k;?>" /></td>
                    <td><a href="javascript:void(0);" title="Delete" class="remove" onclick="Amazoni.clear_product_in_order(<?php echo $i;?>);return false;"></a></td>
                </tr>
                <?php } else { 
                    if ($i == 1) { 
                        $required = 'required="required"';
                    } else { 
                        $required = '';
                    }
                ?>
                <tr>
                    <td><b>Sku<?php echo $i;?></b></td>
                    <td><input <?php echo $required;?> style="width:auto;"  type="text" value="<?php $l = 'sku'.$i; $k='sku'.$i; echo !empty($order->$k)?$order->$k:null;?>" name="<?php echo $k;?>" /></td>
                    <td><b>Cantidad<?php echo $i;?></b></td>
                    <td><input <?php echo $required;?> style="width:20px;" type="text" value="<?php $k='cantidad'.$i; echo !empty($order->$l)?$order->$k:null;?>" name="<?php echo $k;?>" /></td>
                    <td><b>Precio<?php echo $i;?></b></td>
                    <td><input <?php echo $required;?> style="width:40px;" type="text" value="<?php $k='precio'.$i; echo !empty($order->$l)?round($order->$k,2):null;?>" name="<?php echo $k;?>" /></td>
                    <td><a href="javascript:void(0);" title="Delete" class="remove" onclick="Amazoni.clear_product_in_order(<?php echo $i;?>);return false;"></a></td>
                </tr>
                <?php } ?>
                <?php } ?>
            </table>
        </div>
        <div>
            <p>Actions history:</p>
            <?php if (empty($info)) : ?>
            <p class="highlight">This order have error. Please ask Support.</p>
            <?php endif;?>
            <table>
                <?php if (!empty($info)) : ?>
                <tr>
                    <th>SKU</th>
                    <th>Quantity</th>
                    <th>Provider</th>
                    <th>Message</th>
                    <th>Time</th>
                    <th title="Means that product has been ordered to provider">Ordered</th>
                </tr>
                
                
                <?php foreach($info as $row) : ?>
                
                <?php
                
                    $msg = 'OK';
                    $attr = '';
                    
                    if($row->out_of_stock == 1)
                    {
                        $msg = 'Out of stock';
                        $attr = 'class="highlight"';
                    }
                    
                    if($row->provider_reserve_quantity > 0)
                    {
                        $msg = 'Sold from provider reserve';
                        $attr = 'class="highlight"';
                    }
                    
                    if($row->canceled == 1)
                    {
                        $msg = 'Canceled';
                        $attr = 'class="highlight"';
                    }
                    
                    if($row->csv_exported == 1)
                    {
                        $ordered = 'Yes';
                        $ordered_class = 'green';
                    }
                    else
                    {
                        $ordered = 'No';
                        $ordered_class = 'red';
                    }
                ?>
                
                
                <tr <?php echo $attr;?>>
                    <td><?php echo $row->sku_in_order;?></td>
                    <td><?php echo $row->quantity;?></td>
                    <td><?php echo $row->provider_name;?></td>
                    <td><?php echo $msg;?></td>
                    <td><?php echo $row->timestamp;?></td>
                    <td title="Means that product has been ordered to provider" class="<?php echo $ordered_class;?>"><?php echo $ordered;?></td>
                </tr>
                
                
                <?php endforeach;?>
                
                <?php endif;?>
            </table>
        </div>
        <div>
            <p>Statuses history:</p>
            <?php if (empty($status_history)) : ?>
            <p class="highlight">History not stored. Please ask support.</p>
            <?php endif;?>
            <table>
                <?php if (!empty($status_history)) : ?>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Status</th>
                </tr>
                
                
                <?php foreach($status_history as $row) : ?>
                
                <?php 
                
                if((int)$row->user_id === 0)
                {
                    $history_user = 'Amazoni4 System';
                }
                else
                {
                    $history_user = $this->ion_auth->user($row->user_id)->row()->first_name .
                                    ' '.
                                    $this->ion_auth->user($row->user_id)->row()->last_name;
                }
                
                
                ?>
                
                <tr>
                    <td><?php echo $row->created_on;?></td>
                    <td><?php echo $history_user?></td>
                    <td class="<?php echo strtolower($row->status);?>"><?php echo $row->status;?></td>
                </tr>
                
                
                <?php endforeach;?>
                
                <?php endif;?>
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
    <input type="hidden" name="pedido" value="<?php echo $order->pedido;?>">
    <input type="hidden" name="web" value="<?php echo $order->web;?>">
    <input type="hidden" name="fechaentrada" value="<?php echo $order->fechaentrada;?>">
    </form>
    <script>
        $(function(){
            
            $('#order_status').combobox();
            
            // We cant choice status after cancelation
            if($('#order_status').val() == 'CANCELADO')
            {
                $('#order_status').find('option')
                                    .remove()
                                    .end()
                                    .append('<option value="CANCELADO">CANCELADO</option>')
                                    .val('CANCELADO');
            }
            
            Amazoni.order_items_modified = false;
            
            // Detect order item reorganization
            $('table#order_items_list input').change(function(){
                if(!Amazoni.order_items_modified)
                {
                    $('#edit-form').append('<input type="hidden" name="items_modified" value="true" />');
                    Amazoni.order_items_modified = true;
                }
            });
            
            $('table#order_items_list a.remove').click(function(){
                if(!Amazoni.order_items_modified)
                {
                    $('#edit-form').append('<input type="hidden" name="items_modified" value="true" />');
                    Amazoni.order_items_modified = true;
                }
            });
            
            
        });
    </script>
</div>
