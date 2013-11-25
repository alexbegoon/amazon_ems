<article>
    <h1><?php echo $title;?></h1>
    <?php //print_r($orders); ?>
    
    <form id="engelsa-form" method="post" action="<?php echo base_url().'index.php/engelsa/page/';?>">
    <?php $filter = $this->input->post("filter");?>
        <div class="filters">
            <div class="ui-widget">
                <label for="search">Buscar: </label>
                <input id="search" type="text" name="filter[search]" value="<?php echo $filter['search'];?>" />
                <label for="combobox">Marca: </label>
                <select id="combobox" name="filter[nombre_marca]">
                    <?php echo $brand_options; ?>
                </select>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="submit" value="Search" />
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
                <td><?php echo trim($product->descripcion, '"') ;?></td>
                <!-- Precio -->
                <td><?php echo $product->precio ;?></td>
                <!-- Stock -->
                <td><?php echo $product->stock ;?></td>
                <!-- Nombre Marca -->
                <td><?php echo str_replace('"', '', trim($product->nombre_marca, '"')) ;?></td>
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
    
</article>