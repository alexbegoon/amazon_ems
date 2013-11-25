<div class="edit-ajax-container">
    <form method="POST" action="<?php echo current_url();?>" id="ajax_form">
        <table>
            <tr>
                <td>Company name:</td>
                <td><input type="text" name="company_name" value="<?php echo $company->company_name;?>" maxlength="255" required="required" /></td>
            </tr>
            <tr>
                <td>Company code:</td>
                <td><input type="text" name="company_code" value="<?php echo $company->company_code;?>" maxlength="30" required="required" /></td>
            </tr>
            <tr>
                <td>Website:</td>
                <td><input type="text" name="company_website" value="<?php echo $company->company_website;?>" maxlength="50" required="required" /></td>
            </tr>
            <tr>
                <td>Description:</td>
                <td><textarea name="company_description" rows="3" cols="25" maxlength="255"><?php echo $company->company_description;?></textarea></td>
            </tr>
            <tr>
                <td>RegExp:</td>
                <td><textarea name="company_regexp" rows="3" cols="25" maxlength="255"><?php echo $company->company_regexp;?></textarea></td>
            </tr>
        </table>
        <br>
        <div class="edit-buttons">
            <input type="submit" id="edit-save" value="Save">
        </div>
        <input type="hidden" name="task" value="edit" />
        <input type="hidden" name="id" value="<?php echo $company->id;?>" />
    </form>
</div>