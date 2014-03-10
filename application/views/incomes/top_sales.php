<article>
    <h1><?php echo $title;?></h1>
    <?php echo form_open(current_url());?>
    <?php $search = $this->input->post("search");
            $date_from = $this->input->post("date_from");
            $date_to = $this->input->post("date_to");
    ?>
    <div class="filters">
        <input type="button" value="Back" id="incomes_back" />
        <label for="search">Buscar: </label>
        <input id="search" type="text" name="search" value="<?php echo $search;?>" />
        <label for="date_picker">Date from: </label>
        <input id="date_picker" type="text" name="date_from" value="<?php echo $date_from;?>" />
        <label for="date_picker_2">Date to: </label>
        <input id="date_picker_2" type="text" name="date_to" value="<?php echo $date_to;?>" />
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
            <br>
            <div id="web_list">
                <?php echo $web_list;?>
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
                    <th><a href="javascript:void(0);" id="order_by_total_sold" onclick="Amazoni.order_link(this);">Total sold</a></th>
                    <th><a href="javascript:void(0);" id="order_by_total_quantity" onclick="Amazoni.order_link(this);">Total quantity</a></th>
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
            <?php } ?>
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
            $( "#web_list" ).buttonsetv();
            $("#radios input").click(function(){
                $('#date_picker, #date_picker_2').val(null);
                $("form").submit();
            });
            $("#radios2 input").click(function(){
                $("form").submit();
            });
            $( "#web_list input" ).click(function(){
                $("form").submit();
            });
            
            
            // Datepicker
            $('#date_picker, #date_picker_2').datepicker({
                dateFormat: 'yy-mm-dd',
                onSelect: function(  ) {
                    var dateFrom = $('#date_picker').datepicker("getDate");
                    var dateTo   = $('#date_picker_2').datepicker("getDate");
                    var rMin = new Date(dateFrom); 
                    var rMax = new Date(dateTo);
                    if(this.id == 'date_picker')
                    {
                        $('#date_picker_2').datepicker("option","minDate",new Date(rMin.getTime() + 86400000));
                        $('#date_picker').datepicker("option","maxDate",rMin);
                    }
                    else
                    {
                        $('#date_picker_2').datepicker("option","minDate",rMax);
                        $('#date_picker').datepicker("option","maxDate",new Date(rMax.getTime() - 86400000));
                    }
                    
                    $('#date_picker, #date_picker_2').attr('required','required');
                    
                    $('#radios input').removeAttr('checked');
                    $( "#radios" ).buttonset('refresh');
                },
                onClose: function(){
                    
                    $('#date_picker, #date_picker_2').attr('required','required');
                    
                    $('#radios input').removeAttr('checked');
                    $( "#radios" ).buttonset('refresh');
                    
                    if( $('#date_picker').val() == '' && $('#date_picker').val() == '' )
                    {
                        $('#date_picker, #date_picker_2').removeAttr('required');
                    }
                    
                }
            });
            
        });
    </script>
</article>