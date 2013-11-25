<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Description of edit_template
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
?>
<article>
    <h1><?php echo $title; ?></h1>
    <?php echo form_open(current_url()); ?>
    
        <p>Subject:</p>
        <textarea name="subject" id="subject_textarea" cols="100" rows="1"><?php echo $template->subject; ?></textarea>
        
        <br>
        <p>Body:</p>
        <textarea name="body" id="body_textarea" cols="100" rows="10"><?php echo $template->body; ?></textarea>
        
        <br>
        
        <div>
            <p>You can use in the template the next pseudo-variables:</p>
            <p>
                {name}
                <br>
                {website}
                <br>
                {info_email}
                <br>
                {product_link}
            </p>
        </div>
        
        <br>
        <br>
        <br>
        
        <input type="submit" value="Save" />
    </form>
</article>