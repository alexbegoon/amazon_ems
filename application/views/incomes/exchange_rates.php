<article>
    <h1><?php echo $title;?></h1>
    <form id="ratees-form" method="post" action="<?php echo current_url();?>">
        <div class="filters">
            <input type="button" value="Back" id="incomes_back" />
            <input type="button" value="Add..." onclick="AJAX_add('<?php echo current_url();?>')" />
        </div>
        
        <?php if (!empty($rates)) { 
            ?>
        <table class="thin_table">
            <tr>
                <th colspan="2">Action</th>
                <th>Name</th>
                <th>Rate</th>
                <th>Equals</th>
            </tr>
            <?php foreach ($rates as $rate) {
                ?>
            <tr title="<?php echo 'Modified: '.$rate->timestamp?>">
                <td>
                    <a href="#" class="remove" onclick="AJAX_delete('<?php echo current_url();?>', <?php echo $rate->id;?>);return false;"></a>
                </td>
                <td>
                    <a href="#" class="edit" onclick="AJAX_edit('<?php echo current_url();?>', <?php echo $rate->id;?>);return false;"></a>
                </td>
                <td><?php echo $rate->currency_name . ' ('.$rate->currency_code_3.')';?></td>
                <td><?php echo $rate->rate;?></td>
                <td>1 Euro = <?php echo number_format($rate->rate, 2);?> * 1<?php echo '('.$rate->currency_code_3.')';?></td>
            </tr>
                <?php
            }?>
        </table>
            <?php
        } else { 
            ?>
        <p>Exchange rates not found.</p>
            <?php
        }?>
    </form>
</article>
<div id="modal_window" class="modal_window" title="">
</div>