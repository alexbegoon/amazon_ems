<article>
    <h1><?php echo $title;?></h1>

<?php if (!empty($error) && $error != ' ') { ?>
<div id="ajax-msg" style="display: none;">
    <?php echo $error;?>
</div>
<?php } ?>

<?php echo form_open_multipart($url, array('id' => "upload_file"));?>

<input type="file" name="userfile" size="20" />

<br /><br />

<div>
<?php echo $help_info;?>
</div>
    
<br /><br />
<input type="submit" value="Upload" />

</form>
</article>
<script>
    $(function() {
        $("#ajax-msg").fadeIn();
    }); 
</script>