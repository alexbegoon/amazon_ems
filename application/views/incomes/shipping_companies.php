<article>
    <h1><?php echo $title;?></h1>
    <form id="shipping-company-form" method="post" action="<?php echo base_url().'index.php/incomes/shipping_companies/';?>">
        <div class="filters">
            <input type="button" value="Back" id="incomes_back" />
            <input type="button" id="add-ship-company" value="Add..." onclick="AJAX_add('<?php echo base_url().'index.php/incomes/shipping_companies/';?>')" />
        </div>
        <?php if (!empty($companies)) { 
            ?>
        <table>
            <tr>
                <th colspan="2">Action</th>
                <th>Company name</th>
                <th>Company website</th>
                <th>Company description</th>
                <th>Company RegExp</th>
            </tr>
            <?php foreach ($companies as $company) {
                ?>
            <tr>
                <td>
                    <a href="#" class="remove" onclick="AJAX_delete('<?php echo base_url().'index.php/incomes/shipping_companies/';?>', <?php echo $company->id;?>);return false;"></a>
                </td>
                <td>
                    <a href="#" class="edit" onclick="AJAX_edit('<?php echo base_url().'index.php/incomes/shipping_companies/';?>', <?php echo $company->id;?>);return false;"></a>
                </td>
                <td><?php echo $company->company_name;?></td>
                <td><a target="_blank" href="<?php echo $company->company_website;?>"><?php echo $company->company_website;?></a></td>
                <td><?php echo $company->company_description;?></td>
                <td><?php echo $company->company_regexp;?></td>
            </tr>
                <?php
            }?>
        </table>
            <?php
        } else { 
            ?>
        <p>Shipping companies not found.</p>
            <?php
        }?>
    </form>
</article>
<div id="modal_window" class="modal_window" title="">
</div>