<article>
    <h1><?php echo $title;?></h1>
    <div class="filters">
        <input type="button" value="Back" id="tracking_back">
    </div>
    <?php echo form_open(base_url().'index.php/tracking/save_template' , array('id' => 'save_template_form'));?>
    <p>Subject:</p>
    <textarea name="subject" cols="100" rows="1"><?php echo $subject;?></textarea>
    <p>Message:</p>
    <textarea name="message" cols="100" rows="20"><?php echo $template;?></textarea>
    <div>
        <p>
            You can use in the template the next pseudo-variables:
        </p>
        <p>
            {nombrecliente}
            <br>
            {pedido}
            <br>
            {entradapagina}
            <br>
            {tracking}
            <br>
            {transporte_nombre}
            <br>
            {transporte_direccion}
            <br>
            {pagina}
            <br>
            {emailrespuesta}
        </p>
    </div>
    <br>
    <input type="hidden" name="lang" value="<?php echo $lang?>">
    <input type="submit" value="Save template">
    </form>
</article>