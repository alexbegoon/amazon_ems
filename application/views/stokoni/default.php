<article>
    <h1><?php echo $title;?></h1>
    <?php echo form_open(base_url().'index.php/stokoni/page/', array('id' => 'stokoni-form')); ?>
    <?php $filter = $this->input->post("filter");?>
        <div class="filters">
            <div class="ui-widget">
                <input type="button" value="Add..." onclick="AJAX_add('<?php echo base_url().'index.php/stokoni/add_product/'?>')" />
                <label for="search">Buscar: </label>
                <input id="search" type="text" name="filter[search]" value="<?php echo $filter['search'];?>" />
                <?php echo $providers_list;?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="submit" value="Search" />
            </div>
        </div>
        <div class="incomes_wrapper">
            <div class="incomes_summary">
                <?php 
                    $this->table->set_heading('Provider', 'Total Money', 'Total Sold', 'Total Stock', 'Total Vendidas');
                    $tmpl = array ( 'table_open' => '<table class="thin_table">' );
                    $this->table->set_template($tmpl);
                    
                    foreach ($summary as $row)
                    {
                        $this->table->add_row(
                            $row->provider,
                            number_format($row->sub_total_money,2)."&euro;",
                            number_format($row->sub_total_sold,2)."&euro;",
                            $row->sub_total_stock,
                            $row->sub_total_vendidas                                
                                );
                    }
                    if(isset($summary[0]))
                    {
                        $this->table->add_row(
                            'Total: ',
                            number_format($summary[0]->total_money,2)."&euro;",
                            number_format($summary[0]->total_sold,2)."&euro;",
                            $summary[0]->total_stock,
                            $summary[0]->total_vendidas                                
                                );
                    }
                    
                    echo $this->table->generate();
                ?>
            </div>
            <div class="incomes_orders">
                <div>
                    <p><?php echo $total_products;?> products found</p>
                </div>
                <?php if (!empty($products)) { ?>
                <div class="pagination">
                <?php echo $pagination;?>
                </div>
                <table class="thin_table">
                    <tr>
                        <th>Action</th>
                        <th>EAN</th>
                        <th>Nombre</th>
                        <th>Coste</th>
                        <th>Stock</th>
                        <th>Proveedor</th>
                        <th>Vendidas</th>
                    </tr>
                    <?php foreach ($products as $product) { ?>
                    <tr id="<?php echo $product->id ;?>">
                        <!-- Action -->
                        <td><a href="#" class="edit" onclick="AJAX_edit('<?php echo base_url().'index.php/stokoni/edit'?>',<?php echo $product->id;?>);return false;"></a></td>
                        <!-- EAN -->
                        <td class="ean"><?php echo $product->ean ;?></td>
                        <!-- Nombre -->
                        <td class="nombre"><?php echo htmlentities($product->nombre);?></td>
                        <!-- Coste -->
                        <td class="coste"><?php echo $product->coste;?></td>
                        <!-- Stock -->
                        <td class="stock"><?php echo $product->stock;?></td>
                        <!-- Proveedor -->
                        <td class="proveedor"><?php echo $product->proveedor;?></td>
                        <!-- Vendidas -->
                        <td class="vendidas"><?php echo $product->vendidas;?></td>
                    </tr>
                    <? } ?>
                </table>
                <div class="pagination">
                <?php echo $pagination;?>
                </div>
                <div>
                    <p><?php echo $total_products;?> products found</p>
                </div>
        <?php } ?>
            </div>    
        </div>
        
    </form>
    
</article>
<script>
    $(function(){
        
        $('#providers_list').combobox();
        
    });
</script>