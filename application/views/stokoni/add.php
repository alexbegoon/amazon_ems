<div class="edit-ajax-container">
    <?php echo form_open(base_url().'index.php/stokoni/add_product/', array('id' => 'stokoni_add_product'))?>
        <table>
            <tr>
                <td>EAN</td>
                <td><input type="text" name="ean" required="required"/></td>
            </tr>
            <tr>
                <td>Nombre</td>
                <td><input type="text" name="nombre" required="required" /></td>
            </tr>
            <tr>
                <td>Coste</td>
                <td><input type="text" name="coste" required="required" /></td>
            </tr>
            <tr>
                <td>Stock</td>
                <td><input type="text" name="stock" required="required" /></td>
            </tr>
            <tr>
                <td>Proveedor</td>
                <td><?php echo $providers_list;?></td>
            </tr>
            <tr>
                <td>fecha De Compra</td>
                <td><input type="text" name="fechaDeCompra" id="fechaDeCompra" required="required" /></td>
            </tr>
        </table>
        <br>
        <div class="edit-buttons">
            <input type="submit" value="Save">
        </div>
        <input type="hidden" name="vendidas" value="0"/>
    </form>
    <script>
        $(function() {
            
            $('#providers_list_2').combobox();
            
            $('#fechaDeCompra').datepicker({
                
                dateFormat: "yy-mm-dd",
                showAnim: "puff"
                
            });
        });
    </script>
</div>