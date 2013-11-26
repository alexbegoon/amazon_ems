<article>
    <h1><?php echo $title;?></h1>
    <?php echo form_open(base_url().'index.php/products/page/', 'id="products-form"');?>
    <?php $post_data = $this->input->post();?>
    <div class="filters">
        <div class="ui-widget">
            <input type="button" value="Upload..." onclick="window.location = '<?php echo base_url().'index.php/upload/products';?>';"/>
            <label for="search">Buscar: </label>
            <input id="search" type="text" name="search" value="<?php echo $post_data['search'];?>" />
            <label for="providers_list">Proveedor: </label>
            <?php echo $providers_list; ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" value="Search" />
        </div>
    </div>
        <?php if(count($products) > 0 && !empty($products)) { ?>
        <p><?php echo $total_products;?> products found</p>
        <div class="pagination">
        <?php echo $pagination;?>
        </div>
        <table class="thin_table">
            <tr>
                <th>SKU</th>
                <th>Product name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Provider</th>
            </tr>
            <?php foreach ($products as $product) { ?>
            <tr>
                <td><?php echo $product->sku;?></td>
                <td><?php echo htmlentities($product->product_name);?></td>
                <td><?php echo number_format($product->price,2);?>&euro;</td>
                <td><?php echo $product->stock;?></td>
                <td><?php echo $product->provider_name;?></td>
            </tr>    
            <?php } ?>
        </table>
        <div class="pagination">
        <?php echo $pagination;?>
        </div>
        <p><?php echo $total_products;?> products found</p>
        <?php } else { ?>
        <p>Products not found</p>
        <?php } ?>
    
    </form>
    <script>
        $(function(){
            
            $('#providers_list').combobox();
                
        });
    </script>
</article>