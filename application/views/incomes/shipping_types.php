<article>
    <h1><?php echo $title;?></h1>
    <?php echo form_open(current_url(), 'id="shipping-types-form"');?>
    </form>
    <div class="filters">
        <input type="button" value="Back" id="incomes_back" />
        <input type="button" value="Add..." onclick="AJAX_add('<?php echo current_url();?>')" />
    </div>
    <div>
        <?php if(!empty($shipping_types)) { ?>
            <table class="thin_table">
                <tr>
                    <th colspan="2">Action</th>
                    <th>Type name</th>
                    <th>Description</th>
                    <th>Keywords</th>
                    <th>RegExp</th>
                </tr>
                <?php foreach ($shipping_types as $shipping_type) { ?>
                <tr title="Modified: <?php echo $shipping_type->timestamp;?>">
                    <td>
                        <a href="#" title="Delete" class="remove" onclick="AJAX_delete('<?php echo current_url();?>', <?php echo $shipping_type->shipping_type_id;?>);return false;"></a>
                    </td>
                    <td>
                        <a href="#" title="Edit" class="edit" onclick="AJAX_edit('<?php echo current_url();?>', <?php echo $shipping_type->shipping_type_id;?>);return false;"></a>
                    </td>
                    <td><?php echo $shipping_type->shipping_type_name;?></td>
                    <td><?php echo $shipping_type->shipping_type_description;?></td>
                    <td><?php echo $shipping_type->shipping_type_keywords;?></td>
                    <td><?php echo $shipping_type->shipping_type_regexp;?></td>
                </tr>        
                <?php } ?>
            </table>
        <?php } else { ?>
        <p>Shipping types not found</p>
        <?php } ?>
    </div>
</article>