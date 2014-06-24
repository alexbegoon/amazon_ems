<?php
/**
 * Description of compare
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
?>
<article>
    <h1><?php echo $title;?></h1>
    <?php if (!empty($error) && $error != ' ') { ?>
    <div id="ajax-msg" style="display: none;">
        <?php echo $error;?>
    </div>
    <?php } ?>
    <div>
        <p>
            Please upload .XLS file with format as shown bellow.
        </p>
        <table class="thin_table">
            <tr>
                <th>EAN</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Stock</th>
            </tr>
            <tr>
                <td>3349668520312</td>
                <td>Product A</td>
                <td>13.4</td>
                <td>1</td>
            </tr>
            <tr>
                <td>0009668520312</td>
                <td>Product B</td>
                <td>10</td>
                <td>10</td>
            </tr>
            <tr>
                <td>668520312</td>
                <td>Product C</td>
                <td>23.43</td>
                <td>0</td>
            </tr>
            <tr>
                <td>...</td>
                <td>...</td>
                <td>...</td>
                <td>...</td>                
            </tr>
        </table>
    </div>
    <br>
    <?php echo form_open_multipart(base_url('index.php/upload/compare_new_provider_file')); ?>
    <input type="file" name="userfile" size="40" />
    <br /><br />
    <input type="submit" value="Upload" />
    <?php echo form_close();?>
</article>
<script>
    $(function() {
        $("#ajax-msg").fadeIn();
    }); 
</script>