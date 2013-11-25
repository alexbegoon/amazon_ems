<article>
    <h1><?php echo $title;?></h1>
    <?php echo form_open(current_url(), 'id="shipping-costs-form"');?>
        <?php $post_data = $this->input->post();?>
        <div class="filters">
            <input type="button" value="Back" id="incomes_back" />
            <input type="button" value="Add..." onclick="AJAX_add('<?php echo current_url();?>')" />
            <label for="search">Buscar: </label>
            <input id="search" type="text" name="search" value="<?php echo $post_data['search'];?>">
            <label for="select_country_filter">Country: </label>
            <select id="select_country_filter" name="filter_country_code" >
                <?php echo $countries_list;?>
            </select>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <label for="select_company_filter">Shipping company: </label>
            <select id="select_company_filter" name="filter_id_shipping_company" >
                <?php echo $shipping_companies_list;?>
            </select>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" value="Buscar">
        </div>
        <div class="incomes_wrapper">
            <div class="left_filters">
                <br>
                <br>
                <p>Web:</p>
                <div id="radios_1">
                    <?php echo $web_field_radio_list; ?>
                </div>
                <br>
                <br>
                <p>Shipping type:</p>
                <div id="radios_2">
                    <?php echo $shipping_type_radio_list; ?>
                </div>
                
            </div>
            <div class="shipping_prices">
                <br>
                <br>
                <?php if (!empty($costs)) { 
                    ?>
                <p><?php echo $total_rows;?> shipping costs found.</p>
                <div class="pagination">
                    <?php echo $pagination;?>
                </div>
                <table class="thin_table">
                    <tr>
                        <th colspan="2">Action</th>
                        <th>Web</th>
                        <th>Country</th>
                        <th>Country code</th>
                        <th>Cost</th>
                        <th>Shipping type</th>
                        <th>Shipping company</th>
                    </tr>
                    <?php foreach ($costs as $cost) {
                        ?>
                    <tr title="Modified: <?php echo $cost->timestamp;?>">
                        <td>
                            <a href="#" title="Delete" class="remove" onclick="AJAX_delete('<?php echo current_url();?>', <?php echo $cost->id;?>);return false;"></a>
                        </td>
                        <td>
                            <a href="#" title="Edit" class="edit" onclick="AJAX_edit('<?php echo current_url();?>', <?php echo $cost->id;?>);return false;"></a>
                        </td>
                        <td><?php echo $cost->web;?></td>
                        <td><?php echo $cost->name . '&nbsp;&nbsp;' . get_country_flag_img($cost->country_code); ?></td>
                        <td><?php echo $cost->country_code;?></td>
                        <td><?php echo $cost->price;?>&euro;</td>
                        <td><?php echo $cost->shipping_type_name;?></td>
                        <td><?php echo $cost->company_name;?></td>
                    </tr>
                        <?php
                    }?>
                </table>
                <div class="pagination">
                    <?php echo $pagination;?>
                </div>
                <p><?php echo $total_rows;?> shipping costs found.</p>
                    <?php
                } else { 
                    ?>
                <p>Shipping costs not found.</p>
                    <?php
                }?>
            </div>
        </div>
    </form>
</article>
<div id="modal_window" class="modal_window" title="">
</div>
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
            $( "#radios_1" ).buttonsetv();
            $( "#radios_2" ).buttonsetv();
            
            $("#radios_1 input").click(function(){
                $("form").submit();
            });
            $("#radios_2 input").click(function(){
                $("form").submit();
            });
            $( "#select_country_filter" ).combobox();
            $( "#select_company_filter" ).combobox();
            
            
        });
</script>