<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of add_price_rule
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
?>
<?php echo form_open(base_url().'index.php/amazon/save_rule/', array('id' => 'add_rule_form'));?>
<div id="ajax-msg"><?php echo validation_errors(); ?><?php echo $errors; ?></div>
<div class="edit-ajax-container">
        <table>
            <tr>
                <td>Select Provider*: </td>
                <td>
                    <?php echo $providers_list;?>
                </td>
            </tr>
            <tr>
                <td>Select Website*: </td>
                <td>
                    <?php echo $web_list;?>
                </td>
            </tr>
            <tr>
                <td>Select Currency*: </td>
                <td>
                    <select id="select_currency" name="currency_id" value="" required="required">
                        <?php echo $currency_list;?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Multiplication factor*: </td>
                <td>
                    <input type="number" step="any" name="multiply" value="1" required="required"/>
                </td>
            </tr>
            <tr>
                <td>Sum factor*: </td>
                <td>
                    <input type="number" step="any" name="sum" value="0" required="required"/>
                </td>
            </tr>
        </table>
        <br /><br />
        <div class="edit-buttons">
            <input type="submit" value="Save">
        </div>
</div>
</form>
<script>
    $(function(){
        
        $('#providers_list').combobox();
        $('#web_fields_list').combobox();
        $('#select_currency').combobox();
        
        <?php if(validation_errors() || $errors) { 
                ?>
                {
                    $('#ajax-msg').fadeIn();
                }
                <?php
              }?>
        
    });
</script>