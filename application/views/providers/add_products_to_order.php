<?php
/**
 * Description of report_error
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
?>
<div>
    <?php echo form_open( base_url('index.php/providers/process_error_products') );?>
    <table class="thin_table" id="error_report_table">
        <tr><td><table><tr><td>Product*:</td><td><input type="text" class="autocompleate" name="products[]" placeholder="Start typing SKU of the product here..." value="" required="required" size="64" /></td><td>Available Quantity*:                        </td><td><input type="number" min="0" step="1" class="" name="available_quantity[]"  placeholder="0, 1, 2..." value="" required="required" autocomplete="off"/></td></tr><tr title="Reason is not required. But, it may help in the future, for analyzing provider quality"><td>Reason:                        </td><td colspan="3"><textarea name="reasons[]" maxlength="255" id="reason" cols="50" rows="3" placeholder="product is out of stock, the unit in bad conditions, not for sale and etc..."></textarea></td></tr></table></td></tr>    
    </table>    
    <p>
        <a href="javascript:void(0);" id="add_more">Add more...</a>
    </p>
    <div class="edit-buttons">
        <input type="hidden" name="order_id" value="<?php echo $id; ?>" />
        <input type="submit" id="edit-save" value="Process...">
    </div>
    <?php echo form_close();?>
</div>
<script type="text/javascript">
$(function(){
    
    Amazoni.init_autocompleate = function()
    {
        $( ".autocompleate" ).autocomplete({
            source: url_before_index + "index.php/products/search/?orderid=<?php echo $id;?>",
            minLength: 3,
        });

        /* For zebra striping */
        $("#error_report_table tr:nth-child(odd)").addClass("odd-row");
        /* For cell text alignment */
        $("#error_report_table td:first-child, table th:first-child").addClass("first");
        /* For removing the last border */
        $("#error_report_table td:last-child, table th:last-child").addClass("last");
    }
    
    Amazoni.init_autocompleate();
    
    $('#add_more').click(function(){
        $('#error_report_table > tbody:last').append('<tr><td><table><tr><td>Product*:</td><td><input type="text" class="autocompleate" name="products[]" placeholder="Start typing SKU of the product here..." value="" required="required" size="64" /></td><td>Available Quantity*:                        </td><td><input type="number" min="0" step="1" class="" name="available_quantity[]" placeholder="0, 1, 2..." value="" required="required" autocomplete="off"/></td></tr><tr title="Reason is not required. But, it may help in the future, for analyzing provider quality"><td>Reason:                        </td><td colspan="3"><textarea name="reasons[]" maxlength="255" id="reason" cols="50" rows="3" placeholder="product is out of stock, the unit in bad conditions, not for sale and etc..."></textarea></td></tr></table></td></tr>    ');
        Amazoni.init_autocompleate();
    });
    
    $('form').submit(function(){
        $('input.autocompleate').val(function( index, value ){
            
            var pattern = /id:\s\d+$/;
            
            if(pattern.test(value))
            {
                return value;
            }
                    
            return '';
        });
    });
});
</script>