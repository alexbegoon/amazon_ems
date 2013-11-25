<div class="edit-ajax-container">
    <?php echo form_open(current_url(), array('id' => 'ajax_form'));?>
        <table>
            <tr>
                <td>Name: </td>
                <td>
                    <input id="name" type="text" name="name" value="<?=$provider->name?>" maxlength="255" required="required" />
                </td>
            </tr>
            <tr>
                <td>Website: </td>
                <td>
                    <input id="website" type="text" name="website" value="<?=$provider->website?>" maxlength="100" />
                </td>
            </tr>
            <tr>
                <td>Description:</td>
                <td><input id="description" type="text" name="description" value="<?=$provider->description?>" maxlength="255" /></td>
            </tr>
        </table>
        <br>
        <div class="edit-buttons">
            <input type="submit" id="edit-save" value="Save">
        </div>
        <input type="hidden" name="task" value="edit" />
        <input type="hidden" name="id" value="<?php echo $provider->id;?>" />
    </form>
</div>