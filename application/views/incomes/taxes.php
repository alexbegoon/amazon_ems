<article>
    <h1><?php echo $title;?></h1>
    <form id="taxes-form" method="post" action="<?php echo current_url();?>">
        <div class="filters">
            <input type="button" value="Back" id="incomes_back" />
            <input type="button" value="Add..." onclick="AJAX_add('<?php echo current_url();?>')" />
        </div>
        <?php if (!empty($taxes)) { 
            ?>
        <table class="thin_table">
            <tr>
                <th colspan="2">Action</th>
                <th>Name</th>
                <th>Percentage</th>
                <th>Fixed cost per operation</th>
            </tr>
            <?php foreach ($taxes as $tax) {
                ?>
            <tr>
                <td>
                    <a href="#" class="remove" onclick="AJAX_delete('<?php echo current_url();?>', <?php echo $tax->id;?>);return false;"></a>
                </td>
                <td>
                    <a href="#" class="edit" onclick="AJAX_edit('<?php echo current_url();?>', <?php echo $tax->id;?>);return false;"></a>
                </td>
                <td><?php echo $tax->name;?></td>
                <td><?php echo $tax->percent;?>%</td>
                <td><?php echo $tax->fixed_cost;?></td>
            </tr>
                <?php
            }?>
        </table>
            <?php
        } else { 
            ?>
        <p>Taxes not found.</p>
            <?php
        }?>
    </form>
</article>
<div id="modal_window" class="modal_window" title="">
</div>