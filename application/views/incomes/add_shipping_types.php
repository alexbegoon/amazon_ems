<div class="edit-ajax-container">
    <?php echo form_open(current_url(), array('id' => 'ajax_form'));?>
        <table>
            <tr>
                <td>Type name</td>
                <td><input type="text" name="shipping_type_name" required="required" /></td>
            </tr>
            <tr>
                <td>Description</td>
                <td><textarea name="shipping_type_description" required="required" maxlength="255" cols="50"></textarea></td>
            </tr>
            <tr>
                <td>Keywords</td>
                <td><textarea name="shipping_type_keywords" required="required" maxlength="64" cols="50"></textarea></td>
            </tr>
            <tr>
                <td>RegExp</td>
                <td><textarea name="shipping_type_regexp" required="required" maxlength="255" cols="50"></textarea></td>
            </tr>
        </table>
        <br>
        <div class="edit-buttons">
            <input type="submit" id="edit-save" value="Save">
        </div>
        <input type="hidden" name="task" value="save">
    </form>
    <script>
    </script>
</div>