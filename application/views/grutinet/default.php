<article>
    <h1><?php echo $title;?></h1>
    <?php echo form_open(base_url().'index.php/grutinet/page/', array('id' => 'grutinet-form'));?>
    <?php $filter = $this->input->post("filter");?>
        <div class="filters">
            <div class="ui-widget">
                <label for="search">Buscar: </label>
                <input id="search" type="text" name="filter[search]" value="<?php echo $filter['search'];?>" />
                <label for="combobox">Marca: </label>
                <?php echo $brand_options; ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="submit" value="Buscar" />
            </div>
        </div>
        <div>
            <p><?php echo $total_products;?> products found</p>
        </div>
        <?php if (!empty($products)) { ?>
        <div class="pagination">
        <?php echo $pagination;?>
        </div>
        <table class="thin_table">
            <tr>
                <th>EAN</th>
                <th>Descripcion</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Nombre Marca</th>
            </tr>
            <?php foreach ($products as $product) { ?>
            <tr>
                <!-- EAN -->
                <td><?php echo $product->ean ;?></td>
                <!-- Descripcion -->
                <td><?php echo htmlentities($product->product_name) ;?></td>
                <!-- Precio -->
                <td><?php echo number_format($product->price, 2);?>&euro;</td>
                <!-- Stock -->
                <td><?php echo $product->stock ;?></td>
                <!-- Nombre Marca -->
                <td><?php echo htmlentities($product->brand_name);?></td>
            </tr>
            <?php } ?>
        </table>
        <div class="pagination">
        <?php echo $pagination;?>
        </div>
        <div>
            <p><?php echo $total_products;?> products found</p>
        </div>
        <?php } ?>
    </form>
    <script>
        $(function(){
            $('#brand_name_list').combobox();
        });
    </script>
</article>