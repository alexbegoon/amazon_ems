<article>
<h1><?php echo $title;?></h1>
<?php echo form_open(base_url().'index.php/upload/store_sellercentral_data' , array('id' => 'store_sellercentral_data'));?>
<h3>Your file was successfully uploaded!</h3>
<?php if(!empty($data)):?>
<br>
<p><?php echo $data['total_rows'];?> products was parsed. First 100 products at the bottom table.</p>
<br>
<input type="submit" value="Store data" />
<br>
<p><?php echo anchor($url, 'Upload Another File!'); ?></p>
<br>
<div>
    <table class="thin_table">
        <tr>
            <th>EAN</th>
            <th>Merchant SKU</th>
            <th>ASIN/ISBN</th>
            <th>Product name</th>
            <th>Status</th>
            <th>Sales Rank</th>
            <th>Sales Rank Category</th>
            <th>Web</th>
        </tr>
        <?php $i = 1;?>
        <?php foreach($data['first_rows'] as $row):?>
        <?php if($i >= 100){break;}?>
        <?php
        $td_attr = '';
        
        if(empty($row->ean))
        {
            $td_attr .= 'style="background: yellow;"';
        }

        ?>
        <tr>
            <td <?php echo $td_attr;?> class="bold"><?php echo $row->ean;?></td>
            <td <?php echo $td_attr;?>><?php echo $row->merchant_sku;?></td>
            <td <?php echo $td_attr;?>><?php echo $row->asin_isbn;?></td>
            <td <?php echo $td_attr;?>><?php echo $row->product_name;?></td>
            <td <?php echo $td_attr;?>><?php echo $row->status;?></td>
            <td <?php echo $td_attr;?>><?php echo $row->sales_rank;?></td>
            <td <?php echo $td_attr;?>><?php echo $row->sales_rank_category_name;?></td>
            <td <?php echo $td_attr;?>><?php echo $row->web;?></td>
        </tr>
        <?php $i++;?>
        <?php endforeach;?>
    </table>
</div>
<br>
<input type="submit" value="Store data" />
<br>
<p><?php echo anchor($url, 'Upload Another File!'); ?></p>
<?php endif;?>
<input type="hidden" name="url" value="<?php echo current_url();?>" />
</form>
</article>