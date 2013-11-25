<div class="edit-ajax-container">
    <div id="ajax-msg"><?php echo validation_errors(); ?><?php echo $errors; ?></div>
    <?php echo form_open($action , array('id' => 'stokoni_add_product'))?>
        <table>
            <tr>
                <td>EAN</td>
                <td><input type="text" name="ean" required="required" value="<?php echo $product['ean'];?>" /></td>
            </tr>
            <tr>
                <td>Nombre</td>
                <td><input type="text" name="nombre" required="required" value="<?php echo $product['nombre'];?>" /></td>
            </tr>
            <tr>
                <td>Coste</td>
                <td><input type="text" name="coste" required="required" value="<?php echo $product['coste'];?>" /></td>
            </tr>
            <tr>
                <td>Stock</td>
                <td><input type="text" name="stock" required="required" value="<?php echo $product['stock'];?>" /></td>
            </tr>
            <tr>
                <td>Proveedor</td>
                <td><?php echo $providers_list;?></td>
            </tr>
            <tr>
                <td>Vendidas</td>
                <td><input type="text" name="vendidas" value="<?php echo $product['vendidas'];?>"/></td>
            </tr>
            <tr>
                <td>fecha De Compra</td>
                <td><input type="text" name="fechaDeCompra" id="fechaDeCompra" required="required" value="<?php echo $product['fechaDeCompra'];?>"/></td>
            </tr>
        </table>
        <br>
        <div class="edit-buttons">
            <input type="submit" value="Save">
        </div>
        <input type="hidden" name="id" value="<?php echo isset($product['id']) ? $product['id'] : 0;?>"/>
    </form>
    <script>
        $(function() {
            
            $('#providers_list_2').combobox();
            
            $('#fechaDeCompra').datepicker({
                
                dateFormat: "yy-mm-dd",
                showAnim: "puff"
                
            });
            
            <?php if(validation_errors() || $errors) { 
                ?>
            {
                $('#ajax-msg').fadeIn();
            }
                <?php
            }?>
            
                
        });
    </script>
</div>