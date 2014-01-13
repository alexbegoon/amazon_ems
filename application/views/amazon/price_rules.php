<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of price_rules
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */


?>
<article>
    <h1><?php echo $title;?></h1>
    <?php echo form_open(base_url().'index.php/amazon/price_rules/', array('id' => 'price_rules_form'));?>
    <div>
        <div class="filters">
            <input type="button" value="Add..." onclick="AJAX_add('<?php echo base_url().'index.php/amazon/add_price_rule';?>')">
            <?php echo anchor_popup(base_url().'index.php/sync_process/sync_data_with_amazon', 'Update Prices');?>
        </div>
        <div>
            <div>
                <p>
                    Method: "((((Stokoni Price * Select Currency * Select profit margin ) + (Select transport margin + Select extra margin))) * Select Marketplace Margin)* Taxes"
                </p>
            </div>
            <br>
            <div class="pagination">
            <?php echo $pagination;?>
            </div>
            <table class="thin_table">
                <tr>
                    <th colspan="2">Action</th>
                    <th>Web</th>
                    <th>Provider</th>
                    <th>Profit Margin</th>
                    <th>Extra Margin</th>
                    <th>Transport Margin</th>
                    <th>Marketplace Margin</th>
                    <th>Taxes</th>
                    <th>Currency</th>
                </tr>
                <?php foreach($price_rules as $rule) : ?>
                <tr id="<?php echo $rule->id;?>">
                    <td><a href="#" title="Delete" class="remove" onclick="AJAX_delete('<?php echo base_url().'index.php/amazon/delete_price_rule';?>', <?php echo $rule->id;?>);return false;"></a></td>
                    <td><a href="#" title="Edit" class="edit" onclick="AJAX_edit('<?php echo base_url().'index.php/amazon/edit_price_rule';?>', <?php echo $rule->id;?>);return false;"></a></td>
                    <td class="web"><?php echo $rule->web; ?></td>
                    <td class="provider_name"><?php echo $rule->provider_name; ?></td>
                    <td class="sum"><?php echo $rule->sum; ?></td>
                    <td class="multiply"><?php echo $rule->multiply; ?></td>
                    <td class="transport"><?php echo $rule->transport; ?></td>
                    <td class="marketplace"><?php echo $rule->marketplace; ?></td>
                    <td class="tax"><?php echo $rule->tax; ?></td>
                    <td class="currency_name"><?php echo $rule->currency_symbol . ' (' . $rule->currency_code_3 . ') - '. $rule->currency_name; ?></td>
                </tr>
                <?php endforeach;?>
            </table>
            <div class="pagination">
            <?php echo $pagination;?>
            </div>
        </div>
    </div>
    </form>
</article>