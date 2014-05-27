<?php

/**
 * Description of update_stock
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
?>
<article>
    <h1><?php echo $title; ?></h1>
    <div class="filters">
        <?php foreach ($providers as $provider => $data):?>
        <p>Update <?php echo humanize($provider);?> stock:</p>
        <input type="button" id="update_<?php echo humanize($provider);?>" onclick="Amazoni.update_stock('<?php echo $data['url'];?>','<?php echo humanize($provider);?>');" value="Update <?php echo humanize($provider);?>" />
        <br>
        <div class="ajax-loader-2" id="loader_<?php echo humanize($provider);?>"></div>
        <br>
        <?php endforeach;?>
    </div>
</article>