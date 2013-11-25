<div class="edit-ajax-container">
        <table>
            <tr>
                <td>Country*: </td>
                <td>
                    <select id="select_country" name="country_code" value="" required="required" multiply="multiply" form="shipping-costs-form">
                        <?php echo $countries_list;?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Shipping company*: </td>
                <td>
                    <select id="select_company" name="id_shipping_company" required="required" form="shipping-costs-form">
                        <?php echo $shipping_companies_list;?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Web*: </td>
                <td>
                        <?php echo $web_list;?>
                </td>
            </tr>
            <tr>
                <td>Shipping type*: </td>
                <td>
                        <?php echo $shipping_types_list;?>
                </td>
            </tr>
            <tr>
                <td>Cost*:</td>
                <td><input id="price" type="text" name="price" value="" maxlength="30" required="required" form="shipping-costs-form" /></td>
            </tr>
            <tr>
                <td>Description:</td>
                <td><textarea name="description" cols="50" maxlength="255" form="shipping-costs-form"></textarea></td>
            </tr>
            <tr>
                <td>RegExp:</td>
                <td><textarea name="regexp" cols="50" maxlength="255" form="shipping-costs-form"></textarea></td>
            </tr>
        </table>
        <br>
        <div class="edit-buttons">
            <input type="submit" id="edit-save" value="Save" form="shipping-costs-form" />
        </div>
        <input type="hidden" name="task" value="save" form="shipping-costs-form" />
    <script>
        $(function() {
            $( "#select_country" ).combobox();
            $( "#select_company" ).combobox();
            $( "#web_fields_list" ).combobox();
            $( "#shipping_types_list" ).combobox();
            
            $('#shipping-costs-form').submit (function() { 
                if (!$.isNumeric($('#price').val())){
                    $('#price').addClass('wrong');
                    $('#price').focus();
                    return false;
                } else {
                    $(this).removeClass('wrong');
                    return true;
                } });
        });
    </script>
</div>