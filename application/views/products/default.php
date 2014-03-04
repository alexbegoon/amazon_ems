<article>
    <h1><?php echo $title;?></h1>
    <?php echo form_open(current_url(), 'id="products-form"');?>
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
    <div class="incomes_wrapper">
        <div class="incomes_summary">
            <?php if($providers_statistic):?>
            <h2>Provider Stats</h2>
            <table class="thin_table">
                <tr>
                    <th>Provider</th>
                    <th>Total Products</th>
                    <th>Products with Stock</th>
                </tr>
                <?php foreach($providers_statistic as $r):?>
                <tr style="cursor: pointer;" onclick="Amazoni.show_provider_statistic('<?php echo $r->provider_name;?>');">
                    <td><?php echo $r->provider_name;?></td>
                    <td><?php echo $r->total_products;?></td>
                    <td><?php echo $r->total_products_with_stock;?></td>
                </tr>
                <?php endforeach;?>
            </table>
            <?php endif;?>
        </div>
        <div class="incomes_orders">
        <?php if(count($products) > 0 && !empty($products)) { ?>
        <p><?php echo $total_products;?> products found</p>
        <div class="pagination">
        <?php echo $pagination;?>
        </div>
        <table class="thin_table">
            <tr>
                <th>SKU</th>
                <th>Product name</th>
                <th><a href="javascript:void(0);" id="order_by_price" onclick="Amazoni.order_link(this);">Price</a></th>
                <th><a href="javascript:void(0);" id="order_by_stock" onclick="Amazoni.order_link(this);">Stock</a></th>
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
    </div>
    </div>
    
    </form>
    <script>
        $(function(){
            
            $('#providers_list').combobox();
                
        });
    </script>
</article>