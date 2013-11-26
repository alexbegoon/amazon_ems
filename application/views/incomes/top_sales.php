<article>
    <h1><?php echo $title;?></h1>
    <?php echo form_open(current_url());?>
    <?php $search = $this->input->post("search");?>
    <div class="filters">
        <input type="button" value="Back" id="incomes_back" />
        <label for="search">Buscar: </label>
        <input id="search" type="text" name="search" value="<?php echo $search;?>" />
        <input type="submit" value="Buscar" />
    </div>
    <div id="radios">
        <?php echo $period_radios; ?>
    </div>
    <div class="incomes_wrapper">
        <div class="provider_list">
            <div id="radios2">
                <?php echo $provider_radios; ?>
            </div>
        </div>
        <br>
        <br>
        <div class="top_sales_list">
            <?php if(!empty($products_list)) { ?>
            <p><?php echo $total_rows;?> products found.</p>
            <div class="pagination">
                <?php echo $pagination;?>
            </div>
            <table class="thin_table pointer">
                <tr>
                    <th>SKU</th>
                    <th>Product name</th>
                    <th>Total sold</th>
                    <th>Total quantity</th>
                    <th>Provider name</th>
                    <th>Date of last purchase</th>
                </tr>
                <?php foreach ($products_list as $product) { ?>
                <tr onclick="show_top_sales_details('<?php echo $product->sku;?>')">
                    <td><?php echo $product->sku;?></td>
                    <td><?php echo htmlentities(stripslashes($product->product_name));?></td>
                    <td><?php echo number_format($product->total_sold,2);?>&euro;</td>
                    <td><?php echo $product->total_quantity;?></td>
                    <td><?php echo $product->provider_name;?></td>
                    <td><?php echo $product->last_date_purchase;?></td>
                </tr>            
                <?php } ?>
            </table>
            <div class="pagination">
                <?php echo $pagination;?>
            </div>
            <p><?php echo $total_rows;?> products found.</p>
            <?php } else { ?>
            <p>Have no products.</p>
            <? } ?>
        </div>
    </div>
    
    </form>
    <script>
        // Radios UI
        $(function() {
            (function( $ ){
            //plugin buttonset vertical
            $.fn.buttonsetv = function() {
              $(':radio, :checkbox', this).wrap('<div style="margin: 1px"/>');
              $(this).buttonset();
              $('label:first', this).removeClass('ui-corner-left').addClass('ui-corner-top');
              $('label:last', this).removeClass('ui-corner-right').addClass('ui-corner-bottom');
              mw = 0; // max witdh
              $('label', this).each(function(index){
                 w = $(this).width();
                 if (w > mw) mw = w; 
              })
              $('label', this).each(function(index){
                $(this).width(mw);
              })
            };
            })( jQuery );
            $( "#radios" ).buttonset();
            $( "#radios2" ).buttonsetv();
            $("#radios input").click(function(){
                $("form").submit();
            });
            $("#radios2 input").click(function(){
                $("form").submit();
            });
        });
    </script>
</article>