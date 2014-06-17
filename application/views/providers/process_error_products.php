<?php
/**
 * Description of process_error_products
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
?>
<article>
    <h1><?php echo $title;?></h1>
    <div>
        <table class="thin_table">
            <tr>
                <th>Product</th>
                <th>Available Quantity</th>
                <th>Quantity Need</th>
                <th>Status</th>
            </tr>
            <?php foreach ($process_rows as $row):?>
            <tr>
                <td><?php echo $row['product_name'];?></td>
                <td><?php echo $row['product_available_quantity'];?></td>
                <td><?php echo $row['product_quantity_needed'];?></td>
                <td><?php echo $row['status'];?></td>
            </tr>
            <?php endforeach;?>
        </table>
    </div>
</article>