<div class="edit-ajax-container">
    <?php echo form_open(current_url(), array('id' => 'ajax_form'));?>
        <table>
            <tr>
                <td>WEB</td>
                <td><input type="text" name="web" value="<?php echo $web_field->web;?>" required="required" readonly="readonly" /></td>
            </tr>
            <tr>
                <td>Title</td>
                <td><input type="text" name="title" value="<?php echo $web_field->title;?>" required="required" /></td>
            </tr>
            <tr>
                <td>URL</td>
                <td><input type="text" name="url" value="<?php echo $web_field->url;?>" required="required" placeholder="www.mysite.com" /></td>
            </tr>
            <tr>
                <td>E-mail</td>
                <td><input type="email" name="email" value="<?php echo $web_field->email;?>" required="required" placeholder="admin@admin.com" /></td>
            </tr>
            <tr>
                <td>Template language</td>
                <td><?php echo $languages_list;?></td>
            </tr>
        </table>
        <br>
        <p>DB connection setup:</p>
        <table>
            <tr>
                <td>Host name</td>
                <td><input type="text" value="<?php echo $web_field->hostname;?>" name="hostname" required="required" /></td>
            </tr>
            <tr>
                <td>Database name</td>
                <td><input type="text" value="<?php echo $web_field->database;?>" name="database" required="required" /></td>
            </tr>
            <tr>
                <td>DB prefix</td>
                <td><input type="text" value="<?php echo $web_field->dbprefix;?>" name="dbprefix" /></td>
            </tr>
            <tr>
                <td>The character set</td>
                <td><input type="text" value="<?php echo $web_field->char_set;?>" name="char_set" /></td>
            </tr>
            <tr>
                <td>The character collation</td>
                <td><input type="text" value="<?php echo $web_field->dbcollat;?>" name="dbcollat" /></td>
            </tr>
            <tr>
                <td>Username</td>
                <td><input type="text" value="<?php echo $web_field->username;?>" name="username" required="required"  /></td>
            </tr>
            <tr>
                <td>Password</td>
                <td><input type="password" value="<?php echo $web_field->password;?>" name="password" required="required"  /></td>
            </tr>
        </table>
        <br>
        <p>Sync process setup:</p>
        <table>
            <tr>
                <td>Sync process enable</td>
                <td>
                    <div id="radios">
                        <?php echo $sync_toggle;?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Test mode</td>
                <td>
                    <div id="radios2">
                        <?php echo $test_mode;?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Virtuemart version</td>
                <td>
                    <?php echo get_virtuemart_versions($web_field->virtuemart_version); ?>
                </td>
            </tr>
            <tr>
                <td>Start datetime</td>
                <td>
                    <input type="text" value="<?php echo $web_field->start_time;?>" name="start_time" id="start_time" />
                </td>
            </tr>
        </table>
        <br>
        <p>Languages setup:</p>
        <table>
            <tr>
                <td>Installed languages on the web-site:</td>
                <td>
                    <input maxlength="255" id="installed_languages" type="text" placeholder="es_es, en_gb" value="<?php echo $web_field->installed_languages;?>" name="installed_languages" onchange="validate();" />
                </td>
            </tr>
        </table>
        <br>
        <p>Providers setup:</p>
        <div id="providers">
            <?php echo $providers_accordion;?>
        </div>
        <br>
        <div class="edit-buttons">
            <input type="submit" id="edit-save" value="Save">
        </div>
        <input type="hidden" name="task" value="edit" />
        <input type="hidden" name="id" value="<?php echo $web_field->web;?>" />
    </form>
    <script>
        $(function(){
            
            $('#providers').accordion({
                collapsible: true
            });
            
            $('#languages_list, #virtuemart_versions_list').combobox();
            $('#radios, #radios2').buttonset();
            $('#start_time').datepicker({ dateFormat: "yy-mm-dd 00:00:00" });
            
        });
        
        function validate(){
            
            var installed_languages = $('#installed_languages').val();
            
            var patt = /^\s*$|^\s*\w{2}_\w{2}\s*$|^\w{2}_\w{2}(\s*,\s*\w{2}_\w{2}\s*)*$/;
            
            if(!patt.test(installed_languages))
            {
                alert(installed_languages + "\n\n" + 'Wrong installed languages string');
                $('#installed_languages').val('');
                $('#installed_languages').focus();
            }
            
        }
    </script>
</div>