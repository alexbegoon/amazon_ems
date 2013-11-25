<div class="edit-ajax-container">
    <form method="POST" action="<?php echo current_url();?>" id="ajax_form">
        <table>
            <tr>
                <td>Name: </td>
                <td>
                    <input id="name" type="text" name="name" value="" maxlength="30" required="required" />
                </td>
            </tr>
            <tr>
                <td>Percentage: </td>
                <td>
                    <input id="percent" type="text" name="percent" value="" maxlength="30" required="required" />
                </td>
            </tr>
            <tr>
                <td>Fixed cost per operation:</td>
                <td><input id="fixed_cost" type="text" name="fixed_cost" value="" maxlength="30" required="required" /></td>
            </tr>
        </table>
        <br>
        <div class="edit-buttons">
            <input type="submit" id="edit-save" value="Save">
        </div>
        <input type="hidden" name="task" value="save" />
    </form>
    <script>
        $(function() {            
            $('#ajax_form').submit (function() { 
                if (!$.isNumeric($('#percent').val())){
                    $('#percent').addClass('wrong');
                    $('#percent').focus();
                    return false;
                } else {
                    $('#percent').removeClass('wrong');
                } 
                if (!$.isNumeric($('#fixed_cost').val())){
                    $('#fixed_cost').addClass('wrong');
                    $('#fixed_cost').focus();
                    return false;
                } else {
                    $('#fixed_cost').removeClass('wrong');
                } 
            });
        });
    </script>
</div>