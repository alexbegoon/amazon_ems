<article>
    <h1><?php echo $title;?></h1>
    <?php echo form_open(current_url(), array('id' => strtolower($title).'-form'))?>
        <div class="filters">
            <input type="button" value="Back" id="incomes_back" />
            <input type="button" id="add-<?php echo strtolower($title);?>" value="Add..." onclick="AJAX_add('<?php echo current_url();?>')" />
            <a class="link_as_button" href="<?php echo base_url('index.php/providers/compare');?>">Compare new provider</a>
        </div>
        
        <?php if (!empty($providers)) { 
            ?>
        <table>
            <tr>
                <th colspan="2">Action</th>
                <th>Provider name</th>
                <th>Provider website</th>
                <th>Provider description</th>
                <th>Email</th>
                <th>Subject</th>
                <th>Content</th>
                <th>Email Copy</th>
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
                <td><?php echo $provider->emails_list;?></td>
                <td><?php echo $provider->email_subject;?></td>
                <td><?php echo $provider->email_content;?></td>
                <td><?php echo $provider->cc_emails_list;?></td>
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