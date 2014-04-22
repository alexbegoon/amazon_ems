<?php
/**
 * Description of error
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
?>
<div>
    <?php foreach($errors as $error) :?>
    <p class="error-message"><?php echo $error;?></p>
    <?php endforeach;?>
</div>