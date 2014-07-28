<div class="edit-ajax-container">
    <?php echo form_open(current_url(), array('id' => 'ajax_form'));?>
        <table>
            <tr>
                <td>Name: </td>
                <td>
                    <input id="name" type="text" name="name" value="" maxlength="255" required="required" />
                </td>
            </tr>
            <tr>
                <td>Website: </td>
                <td>
                    <input id="website" type="text" name="website" value="http://" maxlength="100" />
                </td>
            </tr>
            <tr>
                <td>Description:</td>
                <td><input id="description" type="text" name="description" value="" maxlength="255" /></td>
            </tr>
            <tr title="A comma-delimited list of emails. Example: one@example.com, two@example.com, three@example.com">
                <td>Email:</td>
                <td><textarea id="emails_list" name="emails_list" placeholder="one@example.com, two@example.com, three@example.com"></textarea></td>
            </tr>
            <tr>
                <td>Subject:</td>
                <td><textarea id="email_subject" name="email_subject"></textarea></td>
            </tr>
            <tr>
                <td>Content:</td>
                <td><textarea id="email_content" name="email_content"></textarea></td>
            </tr>
            <tr title="A comma-delimited list of emails. Example: one@example.com, two@example.com, three@example.com">
                <td>Email Copy:</td>
                <td><textarea id="cc_emails_list" name="cc_emails_list" placeholder="one@example.com, two@example.com, three@example.com"></textarea></td>
            </tr>
            <tr>
                <td>Send CSV file in the email:</td>
                    <input type="hidden" name="send_csv" value="0" />
                <td><input type="checkbox" id="send_csv" name="send_csv" value="1"></td>
            </tr>
            <tr>
                <td>CSV file format:</td>
                <td><textarea id="csv_format" name="csv_format"></textarea></td>
            </tr>
            <tr>
                <td>Send XLS file in the email:</td>
                    <input type="hidden" name="send_xls" value="0" />
                <td><input type="checkbox" id="send_xls" name="send_xls" value="1"></td>
            </tr>
            <tr>
                <td>XLS file format:</td>
                <td><textarea id="csv_format" name="xls_format"></textarea></td>
            </tr>
        </table>
        <br>
        <div class="edit-buttons">
            <input type="submit" id="edit-save" value="Save">
        </div>
        <input type="hidden" name="task" value="save" />
    </form>
</div>