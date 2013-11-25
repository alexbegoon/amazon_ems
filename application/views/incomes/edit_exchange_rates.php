<div class="edit-ajax-container">
    <form method="POST" action="<?php echo current_url();?>" id="ajax_form">
        <table>
            <tr>
                <td>Currency: </td>
                <td>
                    <select id="select_currency" name="currency_id" required="required">
                        <?php echo $currencies_list;?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Rate: </td>
                <td>
                    <input id="rate" type="text" name="rate" value="<?php echo $rate->rate;?>" maxlength="30" required="required" />
                </td>
            </tr>
        </table>
        <br>
        <div class="edit-buttons">
            <input type="submit" id="edit-save" value="Save">
        </div>
        <input type="hidden" name="task" value="edit" />
        <input type="hidden" name="id" value="<?php echo $rate->id;?>" />
    </form>
    <script>
        $(function() {
            
            $( "#select_currency" ).combobox();
            
            $('#ajax_form').submit (function() { 
                if (!$.isNumeric($('#rate').val())){
                    $('#rate').addClass('wrong');
                    $('#rate').focus();
                    return false;
                } else {
                    $('#rate').removeClass('wrong');
                } 
            });
        });
    </script>
</div>