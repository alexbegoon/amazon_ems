<article>
    <h1><?php echo $title;?></h1>
    <?php echo form_open(current_url(), array('id' => strtolower($title).'-form'))?>
        <div class="filters">
            <input type="button" value="Back" id="incomes_back" />
            <input type="button" id="add-<?php echo strtolower($title);?>" value="Add..." onclick="AJAX_add('<?php echo current_url();?>')" />
        </div>
        
        <?php if (!empty($providers)) { 
            ?>
        <table>
            <tr>
                <th colspan="2">Action</th>
                <th>Provider name</th>
                <th>Provider website</th>
                <th>Provider description</th>
            </tr>
            <?php foreach ($providers as $provider) {
                ?>
            <tr>
                <td>
                    <a href="#" class="remove" onclick="AJAX_delete('<?php echo current_url();?>', <?php echo $provider->id;?>);return false;"></a>
                </td>
                <td>
                    <a href="#" class="edit" onclick="AJAX_edit('<?php echo current_url();?>', <?php echo $provider->id;?>);return false;"></a>
                </td>
                <td><?php echo $provider->name;?></td>
                <td><?php echo anchor($provider->website, $provider->website, array('target' => '_blank'));?></td>
                <td><?php echo $provider->description;?></td>
            </tr>
                <?php
            }?>
        </table>
            <?php
        } else { 
            ?>
        <p>Providers not found.</p>
            <?php
        }?>
    </form>
</article>
<div id="modal_window" class="modal_window" title="">
</div>