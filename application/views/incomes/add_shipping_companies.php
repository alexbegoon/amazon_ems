<div class="edit-ajax-container">
    <form method="POST" action="<?php echo current_url();?>" id="ajax_form">
        <table>
            <tr>
                <td>Company name:</td>
                <td><input type="text" name="company_name" value="" maxlength="255" required="required" /></td>
            </tr>
            <tr>
                <td>Company code:</td>
                <td><input type="text" name="company_code" value="" maxlength="30" required="required" /></td>
            </tr>
            <tr>
                <td>Website:</td>
                <td><input type="text" name="company_website" value="http://" maxlength="50" required="required" /></td>
            </tr>
            <tr>
                <td>Description:</td>
                <td><textarea name="company_description" rows="3" cols="25" maxlength="255" ></textarea></td>
            </tr>
            <tr>
                <td>RegExp:</td>
                <td><textarea name="company_regexp" rows="3" cols="25" maxlength="255"></textarea></td>
            </tr>
        </table>
        <br>
        <div class="edit-buttons">
            <input type="submit" id="edit-save" value="Save">
        </div>
        <input type="hidden" name="task" value="save" />
    </form>
</div>