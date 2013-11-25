<article>
    <h1><?php echo $title;?></h1>
    <div class="filters">
        <input type="button" value="Back" id="incomes_back">
        <input type="button" value="Add..." onclick="AJAX_add('<?php echo current_url();?>')">
    </div>
    <?php if(empty($web_fields)) { ?>
    <p>Have no web fields</p>
    <?php } else { ?>
    <table class="thin_table">
        <tr class="odd-row">
            <th colspan="2" class="first">Action</th>
            <th>WEB</th>
            <th>Title</th>
            <th>URL</th>
            <th>E-mail</th>
            <th>Template language</th>
            <th>Providers</th>
        </tr>
        <?php foreach($web_fields as $web_field) { ?>
        <tr>
            <td>
                <a href="#" title="Delete" class="remove" onclick="AJAX_delete('<?php echo current_url();?>', '<?php echo $web_field->web;?>');return false;"></a>
            </td>
            <td>
                <a href="#" title="Edit" class="edit" onclick="AJAX_edit('<?php echo current_url();?>', '<?php echo $web_field->web;?>');return false;"></a>
            </td>
            <td>
                <?php echo $web_field->web;?>
            </td>
            <td>
                <?php echo $web_field->title;?>
            </td>
            <td>
                <?php echo $web_field->url;?>
            </td>
            <td>
                <?php echo $web_field->email;?>
            </td>
            <td>
                <img src="<?php echo base_url().'assets/imgs/small_flags/'.$web_field->language_code.'.png'?>" alt="<?php echo $web_field->language;?>" width="20" height="20" />
                <?php echo $web_field->language;?>
            </td>
            <td>
                <?php echo $web_field->providers;?>
            </td>
        </tr>
        <?php } ?>
    </table>
    <?php } ?>
</article>